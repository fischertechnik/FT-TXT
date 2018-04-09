#!/bin/sh
#---
apt-get update
#---
apt-get -y install linux-headers-$(uname -r)
apt-get -y install build-essential
apt-get -y install libgmp-dev libmpc-dev libmpc-dev
apt-get -y install liblzo2-dev libssl-dev dh-autoreconf libzip-dev autoconf2.64 patchutils
apt-get -y install openssh-server
apt-get -y install sqlite3 libsqlite3-dev
apt-get -y install libconfig-dev
apt-get -y install putty
apt-get -y install kpartx
apt-get -y install mesa-common-dev
apt-get -y install libgl1-mesa-dev
apt-get -y install joe git gitk cvs
apt-get -y install cvs2svn
apt-get -y install gdb bison flex gettext libncurses5-dev texinfo autoconf automake libtool
apt-get -y install libpng12-dev libglib2.0-dev libgtk2.0-dev gperf libxt-dev libxp6-dev 
apt-get -y install zerofree screen zip bc
apt-get -y install gawk python-dev
apt-get -y install curl openssh-server ca-certificates postfix
apt-get -y install libvncserver-dev
apt-get -y install tinc
apt-get -y install fakeroot
apt-get -y install dkms
apt-get -y install kdiff3
apt-get -y install mercurial
#----- 32 bit libraries
dpkg --add-architecture i386
apt-get update
apt-get -y install libc6:i386 libstdc++6:i386
apt-get -y install gcc-multilib
apt-get -y install device-tree-compiler lzma lzop libncurses5:i386 zlib1g:i386
#--- CodeLite
apt-key adv --fetch-keys http://repos.codelite.org/CodeLite.asc
apt-add-repository 'deb http://repos.codelite.org/ubuntu/ xenial universe'
apt-get update
apt-get -y install codelite wxcrafter
apt-get -y install cryptsetup
