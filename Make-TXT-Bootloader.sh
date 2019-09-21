#!/bin/bash

# ATTENTION: This script uses a downloaded 32 bit ARM gcc.
# On a 64 bit Linux, 32 bit support must be installed via
# sudo dpkg --add-architecture i386
# sudo apt-get update
# sudo apt-get install libc6:i386 libncurses5:i386 libstdc++6:i386 zlib1g:i386

# NOTE: It might be more reasonable to use the corresponding cross GCC Linux package

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
export SYSROOTARM=$WRKDIR/output/host/usr/arm-buildroot-linux-gnueabihf/sysroot
export CROSS_COMPILE=arm-linux-gnueabihf-
export PATH=$SYSROOT:$PATH

make u-boot_clean
make u-boot

mkdir bin
cp board-support/u-boot-2013.10-ti2013.12.01/MLO ./bin
cp board-support/u-boot-2013.10-ti2013.12.01/u-boot.img ./bin

cp board-support/u-boot-2013.10-ti2013.12.01/tools/env/fw_printenv $WRKDIR/board/FT/TXT/rootfs/sbin/

cp board-support/u-boot-2013.10-ti2013.12.01/MLO $WRKDIR/board/FT/TXT/rootfs/lib/boot
cp board-support/u-boot-2013.10-ti2013.12.01/u-boot.img $WRKDIR/board/FT/TXT/rootfs/lib/boot

echo "======================="
ls -alh ./bin
echo "======================="

cd $WRKDIR