#!/bin/sh

DRIVE=$1

# Check SD size

DRIVEBYTES=`blockdev --getsize64 $DRIVE`
echo "Size of drive is $DRIVEBYTES bytes"
case $DRIVEBYTES in
  7742685184) echo "Disk size OK" ;;

  *) echo "Unexpected SD card size - please double check and update script! => exit" ; exit 1 ;;
esac

# Unmount paritions

umount ${DRIVE}1
umount ${DRIVE}2
umount ${DRIVE}3

# Check image file

BUILD=`cat board/FT/TXT/BUILD`
IMAGEFILE=../ft-TXT_Build_$BUILD.img

echo "Build number = $BUILD"
echo "Image = $IMAGEFILE"

if [ ! -f "$IMAGEFILE" ]
then
  echo "Image file $IMAGEFILE does not exist! => exit" ; exit 2
fi

# Clear first MByte of disk

dd if=/dev/zero of=$DRIVE bs=1024 count=1024

# Write image

sudo dd if=$IMAGEFILE of=$DRIVE

