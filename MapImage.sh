#!/bin/sh

SAVE_LC=$LC_ALL
export LC_ALL=C

BUILD=`cat board/FT/TXT/BUILD`
IMAGEFILE=../ft-TXT_Build_$BUILD.img
echo ">>> $IMAGEFILE <<<"
#-- map partitions
DRIVE1=/dev/mapper/`kpartx -s -l $IMAGEFILE | head -n 1 | awk '{print $1}'`
DRIVE2=/dev/mapper/`kpartx -s -l $IMAGEFILE | head -n 2 | tail -n 1 | awk '{print $1}'`

echo ">>>>>>>>>>>>>>>>>>"
echo $DRIVE1
echo $DRIVE2
echo ">>>>>>>>>>>>>>>>>>"

kpartx -asv $IMAGEFILE
#-- mount partitions
umount /tmp/boot
umount /tmp/rootfs

rm -rf /tmp/boot  
rm -rf /tmp/rootfs

mkdir /tmp/boot  
mkdir /tmp/rootfs

mount ${DRIVE1} /tmp/boot
mount ${DRIVE2} /tmp/rootfs

bash

umount ${DRIVE1}
umount ${DRIVE2}
                                                        
#-- unmap partitions
kpartx -dsv $IMAGEFILE

export LC_ALL=$SAVE_LC
                                                                                                                