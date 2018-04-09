#!/bin/bash
WRKDIR=`pwd`
cd ..
rm -rf buildroot
git clone git://git.buildroot.net/buildroot ./buildroot
cd buildroot
git checkout 2018.02 -b FT-TXTwrk
patch -p1 < ../FT-TXT/patches/001-enable-kernel-external-dts-fix.patch
patch -p1 < ../FT-TXT/patches/002-add-CrossCompile-Script.patch
patch -p1 < ../FT-TXT/patches/003-psplash-for-fischertechnik-TXT.patch
patch -p1 < ../FT-TXT/patches/004-sdl-robopro.patch
#
chmod a+x *.sh
cp ../FT-TXT/configs/* ./configs
git add .
git commit -m "FT-TXT wrk changes and setup"
make BR2_EXTERNAL=../FT-TXT FT-TXT_4.1_defconfig
cd $WRKDIR
#------
