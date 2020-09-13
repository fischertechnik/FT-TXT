#!/bin/bash
WRKDIR=`pwd`

#----- clean toolchain folder
rm -rf /opt/FT/TXT

#----- remove old buildroot folder
cd ..
sudo rm -rf buildroot
git clone git://git.buildroot.net/buildroot ./buildroot
cd buildroot

#----- get fresh copy from git and patch
git checkout 2020.05 -b FT-TXTwrk
#==== Patches
for i in ../FT-TXT/patches/*.patch
do
        echo "==Apply patch : <$i> =="
        patch -p1 < $i
done
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
