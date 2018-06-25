#!/bin/sh

# ============================================================================
# Switch / to a RAM disk and eventually unmount /
# Part 2 - final steps after restarting SSHD
# ============================================================================
# (C) 2018 Michael Sögtrop - all rights reserved
# ============================================================================

set -e
set -x

# ========== Final unmounting after restaring SSHD ==========

umount /oldroot/tmp
umount /oldroot/dev/pts
umount -l /oldroot/dev

# ========== Clear oldroot ==========

mount -rw -o remount /oldroot
rm -rf /oldroot/*

# ========== UNTAR payload ==========

match=$(grep -n -m 1 '^PAYLOAD:$' $0 | cut -d ':' -f 1)
payload_start=$((match + 1))
tail -n +$payload_start $0 | tar -C /oldroot/ -xzvf - || { echo "tar failed" 1>&2 ; exit 1 ; }

# sync here in case the power controller does something strange during the FW update
sync

# ========== Flash boot, kernel, ... ==========

flash_erase /dev/mtd8 0 0
nandwrite -p /dev/mtd8 /oldroot/lib/boot/uImage

flash_erase /dev/mtd4 0 0
nandwrite -p /dev/mtd4 /oldroot/lib/boot/am335x-kno_txt.dtb 

flash_erase /dev/mtd0 0 0
nandwrite -p /dev/mtd0 /oldroot/lib/boot/MLO

flash_erase /dev/mtd5 0 0
nandwrite -p /dev/mtd5 /oldroot/lib/boot/u-boot.img

rm /oldroot/lib/boot/am335x-kno_txt.dtb
rm /oldroot/lib/boot/MLO
rm /oldroot/lib/boot/u-boot.img
rm /oldroot/lib/boot/uImage
rm /oldroot/lib/boot/UpdateBootloader.sh

# ========== Update firmware ==========

/oldroot/sbin/FwUpdTxt || { echo "Firmware update failed" 1>&2 ; exit 2 ; }
rm /oldroot/sbin/FwUpdTxt

# ========== Everything fine ==========

exit 0
