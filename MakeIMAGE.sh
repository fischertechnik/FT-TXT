#!/bin/sh
BUILD=`cat board/FT/TXT/BUILD`
IMAGEFILE=../ft-TXT_Build_$BUILD.img

echo "Image: $IMAGEFILE"

#-- build imagefile
dd if=/dev/zero ibs=1M count=1 > $IMAGEFILE
dd if=/dev/zero ibs=1M count=255 | tr "\000" "\377" >> $IMAGEFILE
#-- create partitions
sfdisk --in-order --Linux --unit M $IMAGEFILE << EOF
,90,0x0c,*
,,,-
EOF
#-- map partitions
DRIVE1=/dev/mapper/`kpartx -s -l $IMAGEFILE | head -n 1| awk '{print $1}'`
DRIVE2=/dev/mapper/`kpartx -s -l $IMAGEFILE | tail -n 2 | head -n 1| awk '{print $1}'`

echo $DRIVE1
echo $DRIVE2


echo -n "\nPress any key to continue... "
read keypressed

kpartx -a -s -v $IMAGEFILE
#-- mount partitions
umount /tmp/boot
umount /tmp/rootfs

rm -rf /tmp/boot  
rm -rf /tmp/rootfs

mkdir /tmp/boot  
mkdir /tmp/rootfs

dd if=/dev/zero of=${DRIVE1} bs=1M count=1
mkfs.vfat -F 32 -n "boot" ${DRIVE1}
mount ${DRIVE1} /tmp/boot

cp ./buildroot/output/images/am335x-kno_txt.dtb /tmp/boot
cp ./buildroot/output/images/uImage /tmp/boot
cp ./buildroot/output/images/rootfs.ubi /tmp/boot
cp ./board/FT/TXT/rootfs/etc/ft-logo.bmp /tmp/boot/bootlogo.bmp
cp ../u-boot/MLO /tmp/boot
cp ../u-boot/u-boot.img /tmp/boot

ls -alh /tmp/boot

dd if=/dev/zero of=${DRIVE2} bs=1M count=1
mkfs.ext3 -L "rootfs" ${DRIVE2}
mount ${DRIVE2} /tmp/rootfs
echo "Copy rootfs start"
tar -C /tmp/rootfs --checkpoint --checkpoint-action=dot  -xzf ../buildroot/output/images/rootfs.tar.gz
echo ""
echo "Rootfs copied"
sync
sync
umount ${DRIVE1}
umount ${DRIVE2}
                                                        
#-- unmap partitions
kpartx -d -s -v $IMAGEFILE
#-- packen des Imagefiles
rm $IMAGEFILE.zip
zip -j $IMAGEFILE.zip $IMAGEFILE
