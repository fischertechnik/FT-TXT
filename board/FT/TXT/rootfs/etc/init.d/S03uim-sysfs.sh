#! /bin/sh

NODE=`cd /sys; find . | grep kim | grep install`
if [ $NODE ]
then
    echo UIM SYSFS Node Found at /sys/$NODE
else
    echo UIM SYSFS Node Not Found 
    exit 0
fi 

uim="/usr/sbin/uim"
uim_args="-f `dirname /sys/$NODE`"

test -x "$uim" || exit 0

case "$1" in
  start)
    echo -n "Starting uim-sysfs daemon"
    start-stop-daemon --start --quiet --pidfile /var/run/uim.pid --make-pidfile --exec $uim -- $uim_args >/dev/null 2>/dev/null & 
    echo "."
    ;;
  stop)
    echo -n "Stopping uim-sysfs daemon"
    start-stop-daemon --stop --quiet --pidfile /var/run/uim.pid
    echo "."
    ;;
  *)
    echo "Usage: /etc/init.d/S03uim-sysfs.sh {start|stop}"
    exit 1
esac

exit 0
