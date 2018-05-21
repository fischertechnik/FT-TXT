#!/bin/sh

#---
apt-get update

#---
apt-get -y install openssh-server
apt-get -y install mesa-common-dev
apt-get -y install libgl1-mesa-dev
apt-get -y install joe git gitk cvs
apt-get -y install cvs2svn
apt-get -y install postfix
apt-get -y install libvncserver-dev
apt-get -y install tinc
apt-get -y install kdiff3
apt-get -y install mercurial

#----- 32 bit libraries
dpkg --add-architecture i386
apt-get update
apt-get -y install libc6:i386 libstdc++6:i386
apt-get -y install gcc-multilib
apt-get -y install libncurses5:i386 zlib1g:i386

#--- CodeLite
apt-key adv --fetch-keys http://repos.codelite.org/CodeLite.asc
apt-add-repository 'deb http://repos.codelite.org/ubuntu/ xenial universe'
apt-get update
apt-get -y install codelite wxcrafter
apt-get -y install cryptsetup

