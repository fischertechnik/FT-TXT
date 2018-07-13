#!/bin/sh

sudo /etc/init.d/wlan_cl scan -eSSID -esignal: >/opt/knobloch/.KE_ClientsList.txt
