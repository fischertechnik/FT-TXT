#!/bin/sh
BUILD=`cat board/FT/TXT/BUILD`
IMAGEFILE=../ft-TXT_Build_$BUILD.img
ROOTFSMNT=/tmp/XXXRootFs

echo "Image: $IMAGEFILE"

#-- build imagefile
rm $IMAGEFILE*
sync
dd if=/dev/zero ibs=1M count=100  > $IMAGEFILE
dd if=/dev/zero ibs=1M count=255 | tr "\000" "\377" >> $IMAGEFILE
sync
#-- create partitions
fdisk $IMAGEFILE << EOF
o
n
p


+130M
n
p



t
1
c
a
1
p
w
EOF
echo "============="
sync
sync
#-- map partitions
DRIVE1=/dev/mapper/`kpartx -s -l $IMAGEFILE | head -n 1| awk '{print $1}'`
DRIVE2=/dev/mapper/`kpartx -s -l $IMAGEFILE | tail -n 2 | head -n 1| awk '{print $1}'`
sleep 1
echo ">>>>>>>>>>>>>>>>>>"
echo $DRIVE1
echo $DRIVE2
echo ">>>>>>>>>>>>>>>>>>"
kpartx -asv $IMAGEFILE
#-- mount partitions
umount /tmp/boot 2>/dev/null
umount /tmp/rootfs 2>/dev/null

rm -rf /tmp/boot  
rm -rf /tmp/rootfs

mkdir /tmp/boot  
mkdir /tmp/rootfs

dd if=/dev/zero of=${DRIVE1} bs=1M count=1
mkfs.vfat -F 32 -n "BOOT" ${DRIVE1}
mount ${DRIVE1} /tmp/boot

du -h ${DRIVE1}

cp ../buildroot/output/images/am335x-kno_txt.dtb /tmp/boot
cp ../buildroot/output/images/uImage /tmp/boot
cp ../buildroot/output/images/rootfs.ubi /tmp/boot
cp ./board/FT/TXT/rootfs/etc/ft-logo.bmp /tmp/boot/bootlogo.bmp
cp ../u-boot/bin/MLO /tmp/boot
cp ../u-boot/bin/u-boot.img /tmp/boot

ls -alh /tmp/boot

dd if=/dev/zero of=${DRIVE2} bs=1M count=1
mkfs.ext3 -L "ROOTF" ${DRIVE2}
mount ${DRIVE2} /tmp/rootfs

du -h ${DRIVE2}

umount ${ROOTFSMNT} 2>/dev/null
rm -rf ${ROOTFSMNT}
mkdir -p ${ROOTFSMNT}
echo "Mount ====>> " ${ROOTFSMNT}
mount -o loop ../buildroot/output/images/rootfs.ext2 ${ROOTFSMNT}

WRKDIR=`pwd`
cd  ${ROOTFSMNT}
tar cf - . | (cd /tmp/rootfs && tar xBf -)

cd ${WRKDIR}
umount ${ROOTFSMNT}

sync
sync
umount ${DRIVE1}
umount ${DRIVE2}
                                                        
#-- unmap partitions
kpartx -d -s -v $IMAGEFILE
#-- packen des Imagefiles
rm $IMAGEFILE.zip 2>/dev/null
zip -j $IMAGEFILE.zip $IMAGEFILE
