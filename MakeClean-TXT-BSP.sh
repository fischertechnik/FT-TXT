#!/bin/bash
WRKDIR=`pwd`

#----- clean toolchain folder
rm -rf /opt/FT/TXT

#----- remove old buildroot folder
cd ..
rm -rf buildroot
git clone git://git.buildroot.net/buildroot ./buildroot
cd buildroot

#----- get fresh copy from git and patch
git checkout 2018.02 -b FT-TXTwrk
patch -p1 < '../FT-TXT/patches/001-enable-kernel-external-dts-fix.patch'
patch -p1 < '../FT-TXT/patches/002-add-CrossCompile-Script.patch'
patch -p1 < '../FT-TXT/patches/003-psplash-for-fischertechnik-TXT.patch'
patch -p1 < '../FT-TXT/patches/004-sdl-robopro.patch'
patch -p1 < '../FT-TXT/patches/005-tslib-auf-alteversion-ROBOPRO.patch'
patch -p1 < '../FT-TXT/patches/006-ssh-allow-rootlogin.patch'
patch -p1 < '../FT-TXT/patches/007-Crosscompile-password-expiration-time.patch'
patch -p1 < '../FT-TXT/patches/0008-Liberatin-Fonts-1.06.0.20100721-verwenden.patch'

#----- copy config files
chmod a+x *.sh
cp ../FT-TXT/configs/* ./configs

#----- commit changes
git add .
git commit -m "FT-TXT wrk changes and setup"

#----- build file buildroot/.config
make BR2_EXTERNAL=../FT-TXT FT-TXT_4.1_defconfig

#----- build system and image
# Note: Don't use -j here, the BR2_JLEVEL=0 parameter sets the threadcount parameter in subprojects to 2x number of CPUs
# Parallel make at the top level is (stupid enough) not supported
make

cd $WRKDIR
#------
