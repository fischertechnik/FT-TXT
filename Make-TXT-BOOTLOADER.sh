#!/bin/bash
#---- 
WRKDIR=`pwd`
DODIR="u-boot"
TOOLCHAIN_TARNAME="gcc-linaro-arm-linux-gnueabihf-4.9-2014.05_linux.tar.xz"
TOOLCHAIN_NAME="gcc-linaro-arm-linux-gnueabihf-4.9-2014.05_linux"
cd ..
rm -rf $DODIR
mkdir $DODIR
cd $DODIR 
wget http://releases.linaro.org/archive/14.05/components/toolchain/binaries/$TOOLCHAIN_TARNAME
tar -xvf $TOOLCHAIN_TARNAME
###tar -xzvf $WRKDIR/u-boot.tar.gz
cp -a $WRKDIR/$DODIR/* ./

#----- compiler einrichten
export SYSROOT=`pwd`/$TOOLCHAIN_NAME/bin
export SYSROOTARM=`pwd`/output/host/usr/arm-buildroot-linux-gnueabihf/sysroot
export CROSS_COMPILE=arm-linux-gnueabihf-
export PATH=$SYSROOT:$PATH

make u-boot_clean
make u-boot

mkdir bin
cp board-support/u-boot-2013.10-ti2013.12.01/MLO ./bin
cp board-support/u-boot-2013.10-ti2013.12.01/u-boot.img ./bin

cp board-support/u-boot-2013.10-ti2013.12.01/tools/env/fw_printenv $WRKDIR/board/FT/TXT/rootfs/sbin/

echo "======================="
ls -alh ./bin
echo "======================="

cd $WRKDIR