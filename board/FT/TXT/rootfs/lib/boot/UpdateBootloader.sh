#!/bin/sh

echo "Update u-Boot"

flash_erase /dev/mtd8 0 0
nandwrite -p /dev/mtd8 /lib/boot/uImage

flash_erase /dev/mtd4 0 0
nandwrite -p /dev/mtd4 /lib/boot/am335x-kno_txt.dtb 

flash_erase /dev/mtd0 0 0
nandwrite -p /dev/mtd0 /lib/boot/MLO

flash_erase /dev/mtd5 0 0
nandwrite -p /dev/mtd5 /lib/boot/u-boot.img

