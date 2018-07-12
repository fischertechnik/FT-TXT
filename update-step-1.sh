#!/bin/sh

# ============================================================================
# Switch / to a RAM disk and eventually unmount /
# Part 1 - Everything until restarting SSHD
# ============================================================================
# (C) 2018 Michael Sögtrop - all rights reserved
# ============================================================================

set -e
set -x

# ========== kill all non required processes ==========

# First stop some init.d processes

/etc/init.d/bt_ap stop || true
/etc/init.d/wlan_ap stop || true

# /etc/init.d/S99_dhcpd stop || true
# /etc/init.d/S98usb_g_ether stop || true
/etc/init.d/S98_bt_nap stop || true
/etc/init.d/S80dhcp-server stop || true
/etc/init.d/S80dhcp-relay stop || true
/etc/init.d/S60openvpn stop || true
# /etc/init.d/S50sshd stop || true
# /etc/init.d/S40network stop || true
/etc/init.d/S30dbus stop || true
/etc/init.d/S26gdk-pixbuf stop || true
/etc/init.d/S25pango stop || true
/etc/init.d/S21rngd stop || true
/etc/init.d/S20urandom stop || true
# /etc/init.d/S10udev stop || true
# /etc/init.d/S03uim-sysfs.sh stop || true

/etc/init.d/M99_vncserver stop || true
/etc/init.d/M01logging stop || true

# Then kill all processes which are not backed by the kernel image, with a few exceptions

# Loop over all processes
find /proc -type d -name "[0-9]*" -maxdepth 1 | while read -r proc
do 
  # See if the exe link is valid (process is not backed by kernel image)
  if readlink $proc/exe > /dev/null
  then
    exefile=$(readlink -fn $proc/exe)
    pid=$(basename $proc)
    # Check if process is required or can be killed
    case $exefile in
      /bin/busybox) ;;
      /usr/sbin/sshd) ;;
      /usr/sbin/dhcpd) ;;
      # /sbin/udevd) ;; # Not needed because we copy /dev. udevd is there to (dynamically) populate /dev
      *)
        # kill process
        echo $proc is $exefile kill $pid
        kill -9 $pid
        ;;
    esac
  fi
done

# If these are kept:
#  /bin/busybox) ;;
#  /usr/sbin/sshd) ;;
#  /usr/sbin/dhcpd) ;;
# The system continues to run reliably
# Maybe udev is still required if there is some USB hickup

# ========== Restart System from RAM disk ==========

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

# cp -dp or cp -a because these redirect tje symlinks to the destination folder, but we need them relative to root
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

# /usr/libexec contains just a few files, sudo, and sftp server
find /usr/libexec -type f -exec sh -c 'echo FILE {}; mkdir -p /tmp/tmproot/$(dirname {}) ; cp -p {} /tmp/tmproot{}' \;
find /usr/libexec -type l -exec sh -c 'echo LINK {}; mkdir -p /tmp/tmproot/$(dirname {}) ; ln -s $(readlink -f {}) /tmp/tmproot{}' \;

# /usr/sbin additional required files
cp -p /usr/sbin/sshd /tmp/tmproot/usr/sbin
cp -p /usr/sbin/dhcpd /tmp/tmproot/usr/sbin
cp -p /usr/sbin/ubi* /tmp/tmproot/usr/sbin
cp -p /usr/sbin/flash_erase /tmp/tmproot/usr/sbin
cp -p /usr/sbin/nandwrite /tmp/tmproot/usr/sbin

# /usr/bin additional required files
cp -p /usr/bin/ssh-keygen /tmp/tmproot/usr/bin
cp -p /usr/bin/scp /tmp/tmproot/usr/bin
cp -p /usr/bin/sudo /tmp/tmproot/usr/bin
cp -p /usr/bin/openssl /tmp/tmproot/usr/bin

# /usr/lib additional required files
mkdir -p /tmp/tmproot/usr/lib
ln -s /usr/lib /tmp/tmproot/usr/lib/arm-linux-gnueabihf
cp -p /usr/lib/libcrypto.so.1.0.0 /tmp/tmproot/usr/lib/
ln -s /usr/lib/libcrypto.so.1.0.0 /tmp/tmproot/usr/lib/libcrypto.so
cp -p /usr/lib/libz.so.1.2.8 /tmp/tmproot/usr/lib/
ln -s /usr/lib/libz.so.1.2.8 /tmp/tmproot/usr/lib/libz.so.1
ln -s /usr/lib/libz.so.1.2.8 /tmp/tmproot/usr/lib/libz.so
cp -p /usr/lib/libstdc++.so.6.0.20 /tmp/tmproot/usr/lib/
ln -s /usr/lib/libstdc++.so.6.0.20 /tmp/tmproot/usr/lib/libstdc++.so.6

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

# Restart terminals
killall getty

# Restart DHCP
/etc/init.d/S99_dhcpd stop
/etc/init.d/S99_dhcpd start

# Restart SSHD - This kills the connection and ends this script
nohup /etc/init.d/S50sshd restart > /dev/null 2>&1 < /dev/null &

exit 0
