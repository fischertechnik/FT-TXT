# Information on system processes

For the system update it is important to have as few processes running from executables in the file system as possible, because these files would exist twice during the update.

## Listing of files which are loaded from an executable (rather than the kernel image)

```
ls -l /proc/*/exe
lrwxrwxrwx    1 root     root             0 Jan  1 01:01 1/exe -> /bin/busybox
lrwxrwxrwx    1 root     root             0 Jan  1 01:01 779/exe -> /usr/sbin/uim
lrwxrwxrwx    1 root     root             0 Jan  1 01:01 783/exe -> /sbin/udevd
lrwxrwxrwx    1 root     root             0 Jan  1 01:01 815/exe -> /usr/sbin/rngd
lrwxrwxrwx    1 root     root             0 Jan  1 01:01 833/exe -> /usr/bin/dbus-daemon
lrwxrwxrwx    1 root     root             0 Jan  1 01:01 878/exe -> /usr/sbin/sshd
lrwxrwxrwx    1 root     root             0 Jan  1 01:01 906/exe -> /usr/sbin/bluetoothd
lrwxrwxrwx    1 root     root             0 Jan  1 01:01 915/exe -> /usr/bin/pand
lrwxrwxrwx    1 root     root             0 Jan  1 01:01 918/exe -> /usr/sbin/agent
lrwxrwxrwx    1 root     root             0 Jan  1 01:01 920/exe -> /bin/busybox
lrwxrwxrwx    1 root     root             0 Jan  1 01:01 921/exe -> /usr/sbin/bt-nap
lrwxrwxrwx    1 root     root             0 Jan  1 01:01 924/exe -> /usr/bin/rfcomm
lrwxrwxrwx    1 root     root             0 Jan  1 01:01 957/exe -> /usr/sbin/hostapd
lrwxrwxrwx    1 root     root             0 Jan  1 01:01 962/exe -> /usr/sbin/dhcpd
lrwxrwxrwx    1 root     root             0 Jan  1 01:01 967/exe -> /usr/bin/screen
lrwxrwxrwx    1 root     root             0 Jan  1 01:01 968/exe -> /bin/busybox
lrwxrwxrwx    1 root     root             0 Jan  1 01:01 969/exe -> /bin/busybox
lrwxrwxrwx    1 root     root             0 Jan  1 01:01 970/exe -> /bin/busybox
lrwxrwxrwx    1 root     root             0 Jan  1 01:01 971/exe -> /bin/busybox
lrwxrwxrwx    1 ROBOPro  ROBOPro          0 Jan  1 01:01 972/exe -> /opt/knobloch/TxtControlMain
lrwxrwxrwx    1 root     root             0 Jan  1 01:01 1007/exe -> /usr/sbin/sshd
lrwxrwxrwx    1 root     root             0 Jan  1 01:01 1011/exe -> /bin/busybox
lrwxrwxrwx    1 root     root             0 Jan  1 03:21 self/exe -> /bin/busybox
```

dead links (processes loaded from kernel) are removed from the above list

## Listing or processes without kernel processes

PS matched the above list (again kernel processes in [] are removed)

```
ps ax
    1 root     init
  779 root     /usr/sbin/uim -f /sys/./devices/kim
  783 root     /sbin/udevd -d
  815 root     rngd
  833 dbus     dbus-daemon --system
  878 root     /usr/sbin/sshd
  906 root     bluetoothd -dd
  915 root     pand --listen --role NAP --master --devup /etc/bluetooth/dev-up --devdown /etc/bluetooth/dev-down
  918 root     agent -c NoInputNoOutput 874061
  920 root     {bt_ser} /bin/sh /etc/init.d/bt_ser
  921 root     bt-nap -N pan0
  924 root     rfcomm listen /dev/rfcomm0 22
  957 root     hostapd /etc/hostapd.conf
  962 root     dhcpd -s server -cf /etc/dhcp/dhcpd.conf -lf /tmp/dhcp_leases.list
  967 root     {screen} SCREEN -A -m -d -S ROBOPRO /opt/knobloch/run.sh
  968 root     /sbin/getty -L ttyO0 115200 linux
  969 root     /sbin/getty -L tty2 115200 linux
  970 root     /sbin/getty -L tty3 115200 linux
  971 ROBOPro  {run.sh} /bin/sh /opt/knobloch/run.sh
  972 ROBOPro  ./TxtControlMain /dev/ttyO2 65000 1
 1007 root     sshd: root@pts/1
 1011 root     -sh
```

## Notes on some not so obvious non-kernel processes

* **rngd** = Check and feed random data from hardware device to kernel 
* **pand** = The pand PAN daemon allows your computer to connect to ethernet networks using Bluetooth
* **rfcomm** = rfcomm is used to set up, maintain, and inspect the RFCOMM configuration of the Bluetooth subsystem in the Linux kernel.
* **hostadpd** = hostapd is a daemon which allows communication between different 802.11 wireless access points running in Host AP mode

## List of processes with open files

The list of processes with open files looks good

```
lsof
815	/usr/sbin/rngd	/tmp/rngd.pid
962	/usr/sbin/dhcpd	/tmp/dhcp_leases.list

920	/bin/busybox	/etc/init.d/bt_ser
971	/bin/busybox	/opt/knobloch/run.sh

972	/opt/knobloch/TxtControlMain	/usr/share/fonts/liberation/LiberationSans-BoldItalic.ttf
972	/opt/knobloch/TxtControlMain	/usr/share/fonts/liberation/LiberationSans-BoldItalic.ttf
972	/opt/knobloch/TxtControlMain	/usr/share/fonts/liberation/LiberationMono-Bold.ttf
972	/opt/knobloch/TxtControlMain	/usr/share/fonts/liberation/LiberationSans-Regular.ttf
972	/opt/knobloch/TxtControlMain	/usr/share/fonts/liberation/LiberationSans-Bold.ttf
972	/opt/knobloch/TxtControlMain	/usr/share/fonts/liberation/LiberationSans-Regular.ttf
972	/opt/knobloch/TxtControlMain	/usr/share/fonts/liberation/LiberationSans-Bold.ttf
972	/opt/knobloch/TxtControlMain	/usr/share/fonts/liberation/LiberationSans-Bold.ttf
972	/opt/knobloch/TxtControlMain	/usr/share/fonts/liberation/LiberationSans-Italic.ttf
972	/opt/knobloch/TxtControlMain	/usr/share/fonts/liberation/LiberationSans-Regular.ttf
972	/opt/knobloch/TxtControlMain	/usr/share/fonts/liberation/LiberationSansNarrow-Regular.ttf
972	/opt/knobloch/TxtControlMain	/usr/share/fonts/liberation/LiberationSansNarrow-Regular.ttf
972	/opt/knobloch/TxtControlMain	/usr/share/fonts/liberation/LiberationSans-Bold.ttf
972	/opt/knobloch/TxtControlMain	/usr/share/fonts/liberation/LiberationSansNarrow-Regular.ttf
972	/opt/knobloch/TxtControlMain	/dev/input/event1

1007	/usr/sbin/sshd	pipe:[7585]
1007	/usr/sbin/sshd	pipe:[7585]
```
