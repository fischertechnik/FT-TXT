#!/bin/sh
echo $2 | sudo /usr/bin/tee /etc/timezone
#export TZ=$2
#echo $TZ
sudo /usr/bin/unlink /etc/localtime 
sudo /bin/ln -s /usr/share/zoneinfo/Etc/$2 /etc/localtime
sudo /bin/date -u -s $1
sudo /sbin/hwclock -uw
