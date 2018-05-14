#!/bin/sh
if [ $1 -lt 12 ]
then
    sudo /bin/sed -i -e "s/channel=.*/channel=$1/" /etc/hostapd.conf
    echo "Set Wlan Channel $1"
else
    echo "Wrong Channel"
fi



