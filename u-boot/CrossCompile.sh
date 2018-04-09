#! /bin/sh
#
PWD=`pwd`
TOOLCHAIN_NAME="gcc-linaro-arm-linux-gnueabihf-4.9-2014.05_linux"
export SYSROOT=`pwd`/$TOOLCHAIN_NAME/bin
export SYSROOTARM=`pwd`/output/host/usr/arm-buildroot-linux-gnueabihf/sysroot
export CROSS_COMPILE=arm-linux-gnueabihf-
export PATH=$SYSROOT:$PATH
if [ $# -ge 1 ]
	then
	cd $1
fi	
PROMPT_COMMAND='PS1="\[\033[0;31m\]CROSS CC:\[\033[0;30m\]$PS1";unset PROMPT_COMMAND' bash