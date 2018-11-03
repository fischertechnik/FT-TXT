#!/bin/sh

# ============================================================================
# Update TXT System
# ============================================================================
# (C) 2018 Michael Sögtrop - all rights reserved
# ============================================================================

# Usage
# Copy update.sh and update-xxxx.sig to /tmp
# Execute method 1
#   su
#   <enter root password>
#   cd /tmp
#   /tmp/update.sh  (attention, always give full path)
#
# Execute method 2
#   sudo /usr/sbin/exec_signed.sh update.sh update_xxxx-sig

set -e

# ========== Check if this is the first or I/O redirected start of the script ==========

if [ $# = 0 ]
then

  # ========== kill ROBOPro app ==========

  killall -9 run.sh || true

  # ========== extract ShowProgress ==========

  payloadtoolsbeg_match=$(grep -n -m 1 '^PAYLOADTOOLSBEG:$' $0 | cut -d ':' -f 1)
  payloadtoolsend_match=$(grep -n -m 1 '^PAYLOADTOOLSEND:$' $0 | cut -d ':' -f 1)
  payloadtools_start=$((payloadtoolsbeg_match + 1))
  payloadtools_length=$((payloadtoolsend_match - payloadtoolsbeg_match - 1))
  tail -n +$payloadtools_start $0 | head -n $payloadtools_length | openssl enc -base64 -d | gzip -d - >> ./ShowProgressOld || { echo "tar failed" 1>&2; sleep 300; exit 1; }
  chmod u+x ShowProgressOld

  # ========== Restart this script with I/O redirected to ShowProgress ==========

  # screen -m -d -S <name> starts a detached deamon session with name <name>
  
  screen -m -d -S update /bin/sh -c "/bin/sh $0 restart 2>&1 | /tmp/ShowProgressOld"

  # ========== Terminate shell which sharted this script ==========
  
  exit 0

fi

# ========== I/O redirected branch of the script ==========

set -x

# Set number of steps in ShowProgress
echo "!!C10"

# ========== Wait for shell disconnect before killing ssh ==========

# Set current step info in ShowProgress
echo "!!S1"
echo "!!TWait for disconnect"

sleep 2

# ========== Stop services ==========

# Set current step info in ShowProgress
echo "!!S2"
echo "!!TStop services"

/etc/init.d/bt_ap stop || true
/etc/init.d/wlan_ap stop || true
/etc/init.d/S99_dhcpd stop || true
/etc/init.d/S98usb_g_ether stop || true
/etc/init.d/S98_bt_nap stop || true
/etc/init.d/S80dhcp-server stop || true
/etc/init.d/S80dhcp-relay stop || true
/etc/init.d/S60openvpn stop || true
/etc/init.d/S50sshd stop || true
/etc/init.d/S40network stop || true
/etc/init.d/S30dbus stop || true
/etc/init.d/S26gdk-pixbuf stop || true
/etc/init.d/S25pango stop || true
/etc/init.d/S21rngd stop || true
/etc/init.d/S20urandom stop || true
/etc/init.d/S10udev stop || true
/etc/init.d/S03uim-sysfs.sh stop || true
/etc/init.d/M99_vncserver stop || true
/etc/init.d/M01logging stop || true

# ========== kill all non required processes ==========

echo "!!S3"
echo "!!TKill processes"

# Loop over all processes
find /proc -type d -name "[0-9]*" -maxdepth 1 | while read -r proc
do 
  # See if the exe link is valid (process is not backed by kernel image)
  if readlink $proc/exe > /dev/null
  then
    exefile=$(readlink -fn $proc/exe)
    pid=$(basename $proc)
    echo "$pid $exefile"
    # Check if process is required or can be killed
    case $exefile in
      /bin/busybox) ;;         # bash for the script to run
      /usr/bin/screen) ;;      # screen used to make this script an IO detached deamon
      */ShowProgressOld) ;;    # show progress app
      *)                       # everythign else
        # kill process
        echo "kill $pid $exefile"
        kill -9 $pid
        ;;
    esac
  fi
done

# ========== Creare mini system in RAM ==========

echo "!!S4"
echo "!!TCreate RAM System"

# remount root fs as read only (to avoid messing it up)
mount -r -o remount /

# Change umask so that users can access newly creates folders
# Existing files and links are copied with existing priviledges, but some folders might be top open
# TODO: Check folder permissions!
umask 002

mkdir /tmp/tmproot
chmod 775 /tmp/tmproot
mount none /tmp/tmproot -t tmpfs
mount

mkdir /tmp/tmproot/oldroot
mkdir /tmp/tmproot/proc
mkdir /tmp/tmproot/sys

# cp -a of /dev doesn't copy file contents but special files
cp -a /dev /tmp/tmproot/dev

# cp -dp or cp -a doesn't work because these redirect the symlinks to the destination folder, but we need them relative to root
find /bin -type f -exec sh -c 'echo FILE {}; mkdir -p /tmp/tmproot/$(dirname {}) ; cp -p {} /tmp/tmproot{}' \;
find /bin -type l -exec sh -c 'echo LINK {}; mkdir -p /tmp/tmproot/$(dirname {}) ; ln -s $(readlink -f {}) /tmp/tmproot{}' \;

find /sbin -type f -exec sh -c 'echo FILE {}; mkdir -p /tmp/tmproot/$(dirname {}) ; cp -p {} /tmp/tmproot{}' \;
find /sbin -type l -exec sh -c 'echo LINK {}; mkdir -p /tmp/tmproot/$(dirname {}) ; ln -s $(readlink -f {}) /tmp/tmproot{}' \;

# In /etc we exclude a few large folders
ETCEX="-path /etc/joe -prune -o -path /etc/udev -prune -o -path /etc/keymaps -prune -o -path /etc/rc_keymaps -prune -o -path /etc/udev -prune -o"
find /etc $ETCEX -type f -exec sh -c 'echo FILE {}; mkdir -p /tmp/tmproot/$(dirname {}) ; cp -p {} /tmp/tmproot{}' \;
find /etc $ETCEX -type l -exec sh -c 'echo LINK {}; mkdir -p /tmp/tmproot/$(dirname {}) ; ln -s $(readlink -f {}) /tmp/tmproot{}' \;

# In /lib only direct children are important
find /lib -type f -maxdepth 1 -exec sh -c 'echo FILE {}; mkdir -p /tmp/tmproot/$(dirname {}) ; cp -p {} /tmp/tmproot{}' \;
find /lib -type l -maxdepth 1 -exec sh -c 'echo LINK {}; mkdir -p /tmp/tmproot/$(dirname {}) ; ln -s $(readlink -f {}) /tmp/tmproot{}' \;

# In /usr/bin and /usr/sbin we copy only links to /bin/busybox
find /usr/bin -type l -exec sh -c 'echo LINK {}; if [ "$(readlink -f {})"=="/bin/busybox" ] ; then mkdir -p /tmp/tmproot/$(dirname {}) ; ln -s $(readlink -f {}) /tmp/tmproot{} ; fi' \;
find /usr/sbin -type l -exec sh -c 'echo LINK {}; if [ "$(readlink -f {})"=="/bin/busybox" ] ; then mkdir -p /tmp/tmproot/$(dirname {}) ; ln -s $(readlink -f {}) /tmp/tmproot{} ; fi' \;

# In /var we exclude a few large folders
VAREX="-path /var/psg -prune -o -path /var/www -prune -o"
find /var $VAREX -type f -exec sh -c 'echo FILE {}; mkdir -p /tmp/tmproot/$(dirname {}) ; cp -p {} /tmp/tmproot{}' \;
find /var $VAREX -type l -exec sh -c 'echo LINK {}; mkdir -p /tmp/tmproot/$(dirname {}) ; ln -s $(readlink -f {}) /tmp/tmproot{}' \;
# This special folder is required by sshd with exactly these rights
mkdir -m 755 /tmp/tmproot/var/empty 

# /usr/sbin additional required files
cp -p /usr/sbin/ubi* /tmp/tmproot/usr/sbin
cp -p /usr/sbin/flash_erase /tmp/tmproot/usr/sbin
cp -p /usr/sbin/nandwrite /tmp/tmproot/usr/sbin

# /usr/lib additional required files
mkdir -p /tmp/tmproot/usr/lib
ln -s /usr/lib /tmp/tmproot/usr/lib/arm-linux-gnueabihf
# libz is required to untar zipped new system
cp -p /usr/lib/libz.so.1.2.8 /tmp/tmproot/usr/lib/          || cp -p /usr/lib/libz.so.1.2.11 /tmp/tmproot/usr/lib/
ln -s /usr/lib/libz.so.1.2.8 /tmp/tmproot/usr/lib/libz.so.1 || ln -s /usr/lib/libz.so.1.2.11 /tmp/tmproot/usr/lib/libz.so.1
ln -s /usr/lib/libz.so.1.2.8 /tmp/tmproot/usr/lib/libz.so   || ln -s /usr/lib/libz.so.1.2.11 /tmp/tmproot/usr/lib/libz.so
# libstdc++ is required for firmware update
# in new system libstdc++.so.6 in in /lib and copied by default
cp -p /usr/lib/libstdc++.so.6.0.20 /tmp/tmproot/usr/lib/               || ls /lib/libstdc++.so.6
ln -s /usr/lib/libstdc++.so.6.0.20 /tmp/tmproot/usr/lib/libstdc++.so.6 || ls /lib/libstdc++.so.6

# /usr/share additional required files
mkdir -p /tmp/tmproot/usr/share/txt-utils
cp -p /usr/share/txt-utils/power-off /tmp/tmproot/usr/share/txt-utils/

# some empty directories
mkdir /tmp/tmproot/tmp
chmod 1777 /tmp/tmproot/tmp

mkdir -p /tmp/tmproot/opt/knobloch
chown ROBOPro:ROBOPro /tmp/tmproot/opt/knobloch
chmod 775 /tmp/tmproot/opt/knobloch

mkdir /tmp/tmproot/root
chmod 700 /tmp/tmproot/root

# config for sudo debugging
# echo "Debug sudo /var/log/sudo_debug all@debug" > /tmp/tmproot/etc/sudo.conf
# echo "Debug sudoers.so /var/log/sudo_debug all@debug" >> /tmp/tmproot/etc/sudo.conf
# chmod 600 /tmp/tmproot/etc/sudo.conf

# ========== Switch to RAM system ==========

echo "!!S5"
echo "!!TSwitch RAM System"

# Switch root
pivot_root /tmp/tmproot/ /tmp/tmproot/oldroot
cd /

# Mount new special file systems
mount none /proc -t proc
mount none /sys -t sysfs
mount none /dev/pts -t devpts

# Unmount old special file systems
umount /oldroot/proc
umount /oldroot/sys
umount /oldroot/dev/shm
# /run does not exist in old system
umount /oldroot/run || true

# Restart terminals
killall getty

# Move mount /oldroot/tmp to /tmp
# This way the filename of the script ($0) remains the same if it was in /tmp before
mount --move /oldroot/tmp/ /tmp

# Unmount remaining oldroot file systems
# Note oldroot/tmp cannot be unmounted, because the current script is in it
# /dev/pts contains pseudo terminals used by screen, so it also cannot be unmounted (use -l=lazy option)
umount -l /oldroot/dev/pts
umount -l /oldroot/dev

# ========== Delete old system ==========

echo "!!S6"
echo "!!TDelete old system"

mount -rw -o remount /oldroot
rm -rf /oldroot/*

# ========== Install new system ==========

echo "!!S7"
echo "!!TInstall new system"

payloadtar_match=$(grep -n -m 1 '^PAYLOADTAR:$' $0 | cut -d ':' -f 1)
payloadtar_start=$((payloadtar_match + 1))
tail -n +$payloadtar_start $0 | tar -C /oldroot/ -xzvf - || { echo "tar failed" 1>&2 ; sleep 300; exit 1; }

# sync here in case the power controller does something strange during the FW update
sync

# ========== Flash boot, kernel, ... ==========

echo "!!S8"
echo "!!TFlash boot loader"

flash_erase /dev/mtd8 0 0
nandwrite -p /dev/mtd8 /oldroot/lib/boot/uImage

flash_erase /dev/mtd4 0 0
nandwrite -p /dev/mtd4 /oldroot/lib/boot/am335x-kno_txt.dtb 

flash_erase /dev/mtd0 0 0
nandwrite -p /dev/mtd0 /oldroot/lib/boot/MLO

flash_erase /dev/mtd5 0 0
nandwrite -p /dev/mtd5 /oldroot/lib/boot/u-boot.img

# remove unneeded files
rm /oldroot/lib/boot/am335x-kno_txt.dtb
rm /oldroot/lib/boot/MLO
rm /oldroot/lib/boot/u-boot.img
rm /oldroot/lib/boot/uImage
rm /oldroot/lib/boot/UpdateBootloader.sh

# ========== Update IO firmware ==========

echo "!!S9"
echo "!!TUpdate IO firmware"

/oldroot/sbin/FwUpdTxt || { echo "Firmware update failed" 1>&2 ; sleep 300; exit 2; }
rm /oldroot/sbin/FwUpdTxt

# The firmware update is really nasty - it terminate before it is finished
# Poweroff after 10s delays fails
sleep 15

# ========== Shutdown ==========

echo "!!S10"
echo "!!TShutdown"

sync
sync
sleep 1
echo 1 > /sys/class/leds/off-uc/brightness

# ========== End of script ==========
