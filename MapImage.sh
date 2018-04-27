#!/bin/sh

SAVE_LC=$LC_ALL
export LC_ALL=C

BUILD=`cat board/FT/TXT/BUILD`
IMAGEFILE=../ft-TXT_Build_$BUILD.img
echo ">>> $IMAGEFILE <<<"
#-- map partitions
DRIVE1=/dev/mapper/`kpartx -s -l $IMAGEFILE | head -n +1 | awk '{print $1}'`
DRIVE2=/dev/mapper/`kpartx -s -l $IMAGEFILE | tail -n +2 | head -n +1 | awk '{print $1}'`

echo ">>>>>>>>>>>>>>>>>>"
echo $DRIVE1
echo $DRIVE2
echo ">>>>>>>>>>>>>>>>>>"
sleep 1
kpartx -asv $IMAGEFILE
sleep 1
#-- mount partitions
umount /tmp/boot 2>/dev/null
umount /tmp/rootfs 2>/dev/null

rm -rf /tmp/boot  
rm -rf /tmp/rootfs

mkdir /tmp/boot  
mkdir /tmp/rootfs

mount ${DRIVE1} /tmp/boot
mount ${DRIVE2} /tmp/rootfs

PROMPT_COMMAND='PS1="\[\033[0;31m\]MAP IMAGE:\[\033[0;30m\]$PS1";unset PROMPT_COMMAND' bash

umount ${DRIVE1}
umount ${DRIVE2}
                                                        
#-- unmap partitions
kpartx -dsv $IMAGEFILE

export LC_ALL=$SAVE_LC
                                                                                                                