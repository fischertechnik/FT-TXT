#!/bin/sh
#---
apt-get update
#---
apt-get -y install linux-headers-$(uname -r)
apt-get -y install build-essential
apt-get -y install libgmp-dev libmpc-dev libmpc-dev
apt-get -y install liblzo2-dev libssl-dev dh-autoreconf libzip-dev autoconf2.64 patchutils
apt-get -y install sqlite3 libsqlite3-dev
apt-get -y install libconfig-dev
apt-get -y install putty
apt-get -y install kpartx
apt-get -y install gdb bison flex gettext libncurses5-dev texinfo autoconf automake libtool
apt-get -y install libpng12-dev libglib2.0-dev libgtk2.0-dev gperf libxt-dev libxp6-dev 
apt-get -y install zerofree screen zip bc
apt-get -y install gawk python-dev
apt-get -y install curl 
apt-get -y install ca-certificates
apt-get -y install fakeroot
apt-get -y install dkms
apt-get -y install device-tree-compiler lzma lzop 

