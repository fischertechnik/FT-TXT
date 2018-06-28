#!/bin/bash
WRKDIR=`pwd`
cd ../buildroot

#----- build file buildroot/.config

make BR2_EXTERNAL=../FT-TXT FT-TXT_4.1_defconfig

#----- build system and image

# Note: Don't use -j here, the BR2_JLEVEL=0 parameter sets the threadcount parameter in subprojects to 2x number of CPUs
# Parallel make at the top level is (stupid enough) not supported
make

cd $WRKDIR
#------
