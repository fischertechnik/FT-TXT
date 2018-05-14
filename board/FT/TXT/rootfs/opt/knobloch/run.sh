#!/bin/sh

export TSLIB_TSDEVICE=/dev/input/event0
export TSLIB_TSEVENTTYPE=INPUT
export TSLIB_CONFFILE=/etc/ts.conf
export TSLIB_CALIBFILE=/etc/pointercal
export SDL_MOUSEDRV=TSLIB
export SDL_MOUSEDEV=$TSLIB_TSDEVICE

cd /opt/knobloch
while true; do
  ./TxtControlMain /dev/ttyO2 65000 1
  sleep 2
done

