# U-BOOT

## U-BOOT Documentation

* [U-Boot Manual](https://www.denx.de/wiki/DULG/Manual) (a newer version that what is on the TXT)
* [Nand commands](https://www.denx.de/wiki/DULG/UBootCmdGroupNand)
* See also on disk "buildroot-2018/u-boot/board-support/u-boot-2013.10-ti2013.12.01/doc"

## How are the initial u-boot parameters set?

Initial u-boot parameters and are set in :

    buildroot-2018/u-boot/board-support/u-boot-2013.10-ti2013.12.01/include/autoconf.mk

Initial u-boot environment variables are set in this file in **CONFIG_EXTRA_ENV_SETTINGS**

### Flash environment variables / functions

```
flash_erase=nand erase.chip\0

flash_all=run flash_erase; run flash_u-boot; run flash_spl; run flash_rootfs; run flash_dtb; run flash_uImage; run flash_bootlogo; setenv bootcmd run nandboot; setenv preboot run nandpreboot; saveenv\0

flash_rootfs=mtdparts default;nand erase.part NAND.rootfs; fatload mmc 0:1 0x80200000 rootfs.ubi; nand write 0x80200000 NAND.rootfs 0x${filesize}\0

flash_spl=mtdparts default; nand erase.part NAND.SPL1; nand erase.part NAND.SPL2; nand erase.part NAND.SPL3; nand erase.part NAND.SPL4; mw.b 0x80200000 0xff 0x200000; fatload mmc 0:1 0x80200000 MLO; nand write 0x80200000 NAND.SPL1 0x${filesize}; nand write 0x80200000 NAND.SPL2 0x${filesize}; nand write 0x80200000 NAND.SPL3 0x${filesize}; nand write 0x80200000 NAND.SPL4 0x${filesize}\0

flash_u-boot=mtdparts default; nand erase.part NAND.U-boot; mw.b 0x80200000 0xff 0x100000; fatload mmc 0:1 0x80200000 u-boot.img; nand write 0x80200000 NAND.U-boot 0x${filesize}\0

flash_dtb=mtdparts default; nand erase.part NAND.dtb; mw.b 0x80200000 0xff 0x40000; fatload mmc 0:1 0x80200000 am335x-kno_txt.dtb; nand write 0x80200000 NAND.dtb 0x${filesize}\0

flash_uImage=mtdparts default; nand erase.part NAND.uImage; mw.b 0x80200000 0xff 0x500000; fatload mmc 0:1 0x80200000 uImage; nand write 0x80200000 NAND.uImage 0x${filesize}\0

flash_bootlogo=mtdparts default; nand erase.part NAND.bootlogo; mw.b 0x80200000 0xff 0x40000; fatload mmc 0:1 0x80200000 bootlogo.bmp; nand write 0x80200000 NAND.bootlogo 0x${filesize}\0
```

### Boot control environment variables / functions

Note: bootcmd is changed by flash_all tp nand_boot.

```
bootcmd=run sdboot\0

nandboot=run reset_wl18xx; mtdparts default; mtdparts default; nand read 0x80200000 NAND.uImage; nand read 0x80F00000 NAND.dtb; fdt addr 0x80F00000; run opp;setenv bootargs fbtft_device.name=txt_ili9341 fbtft_device.fps=10 console=ttyO0,115200 ubi.mtd=10 root=ubi0:rootfs rootfstype=ubifs rootwait quiet; bootm 0x80200000 - 0x80F00000\0

sdboot=run reset_wl18xx; fatload mmc 0 0x80200000 uImage; fatload mmc 0 0x80F00000 am335x-kno_txt.dtb; fdt addr 0x80F00000; run opp;setenv bootargs fbtft_device.name=txt_ili9341 fbtft_device.fps=10 console=ttyO0,115200 root=/dev/mmcblk0p2 rw rootwait quiet;bootm 0x80200000 - 0x80F00000\0

ramboot=run reset_wl18xx; fatload mmc 0 0x80200000 uImage; fatload mmc 0 0x80F00000 am335x-kno_txt.dtb; fdt addr 0x80F00000; run opp;fatload mmc 0 0x81000000 initrd.gz; setenv initrdsize 0x${filesize}; setenv bootargs fbtft_device.name=txt_ili9341 fbtft_device.fps=10 console=ttyO0,115200 initrd=0x81000000,${initrdsize} root=/dev/ram0 rw init=/sbin/init; bootm 0x80200000 - 0x80F00000\0

nandpreboot=mtdparts default; nand read 0x80200000 NAND.bootlogo; lcd l\0

preboot=ext4load mmc 0:2 0x80200000 /etc/ft-logo.bmp; lcd l\0

reset_wl18xx=gpio clear 97; gpio clear 98\0
```

### USB FLash commands

```
usbflash_all=run flash_erase; run usbflash_u-boot; run usbflash_spl; run usbflash_rootfs; run usbflash_dtb; run usbflash_uImage; run usbflash_bootlogo; setenv bootcmd run nandboot; setenv preboot run nandpreboot; saveenv\0

usbflash_rootfs=mtdparts default;usb start;nand erase.part NAND.rootfs; fatload usb 0:1 0x80200000 rootfs.ubi; nand write 0x80200000 NAND.rootfs 0x${filesize}\0

usbflash_spl=mtdparts default;usb start; nand erase.part NAND.SPL1; nand erase.part NAND.SPL2; nand erase.part NAND.SPL3; nand erase.part NAND.SPL4; mw.b 0x80200000 0xff 0x200000; fatload usb 0:1 0x80200000 MLO; nand write 0x80200000 NAND.SPL1 0x${filesize}; nand write 0x80200000 NAND.SPL2 0x${filesize}; nand write 0x80200000 NAND.SPL3 0x${filesize}; nand write 0x80200000 NAND.SPL4 0x${filesize}\0

usbflash_u-boot=mtdparts default;usb start; nand erase.part NAND.U-boot; mw.b 0x80200000 0xff 0x100000; fatload usb 0:1 0x80200000 u-boot.img; nand write 0x80200000 NAND.U-boot 0x${filesize}\0

usbflash_dtb=mtdparts default;usb start; nand erase.part NAND.dtb; mw.b 0x80200000 0xff 0x40000; fatload usb 0:1 0x80200000 am335x-kno_txt.dtb; nand write 0x80200000 NAND.dtb 0x${filesize}\0

usbflash_uImage=mtdparts default;usb start; nand erase.part NAND.uImage; mw.b 0x80200000 0xff 0x500000; fatload usb 0:1 0x80200000 uImage; nand write 0x80200000 NAND.uImage 0x${filesize}\0

usbflash_bootlogo=mtdparts default;usb start; nand erase.part NAND.bootlogo; mw.b 0x80200000 0xff 0x40000; fatload usb 0:1 0x80200000 bootlogo.bmp; nand write 0x80200000 NAND.bootlogo 0x${filesize}\0
```

## How can u-boot parameters be shown or modified from the TXT-console?

The TXT Linux contains two programs to print and modify u-boot parameters:

    fw_printenv
    fw_setenv

Both command are only available to root.
Here are examples to set the boot order:

    fw_setenv bootcmd "run nandboot"
    fw_setenv bootcmd "run sdboot;run nandboot"

## Flash TXT from SD-card

### Prepare SD card with image

Preape a SD card image with:

    sudo MakeIMAGE.sh
    sudo CopyImageToSD.sh

CopyImageToSD.sh checks if the size of the destination device is a known SD card size, but:

**There is absolutely no guarentee that the script won't erase your entire disk !!!**

### Attach Serial USB Converter

I use a PLX 2303 adapter with for independent wires with the following coding:

* white wire - RXD
* green wire - TXD
* black wire - GND
* red wire   - VCC 

PLX 2303 adapters from other suppliers might have a different coding.

The connections at the TXT 2x5 pin head are as follows:

* upper left = RCD = white wire
* lower left = TXD = green wire
* upper right = GND = black wire

Use putty serial mode with the following parameters:

* serial port = /dev/ttyUSB0 (on Linux, might be different)
* baud rate = 115200
* data bits = 8
* parity = none
* stop bits = 1

Note: for cut and paste in putty in Linux

* from putty to Linux: select with left mouse in putty, paste with middle mouse in Linux editor (e.g. gedit)
* from Linux to putty: put into clipboatrd with Ctrl+C, paste with middle mouse button in putty window

### Boot TXT and flash

Insert the SD card and power on the TXT.

**As soon as you see messages on the serial console, press enter!**

At the u-boot prompt, run these commands:

    run flash_all    (takes ~ 1 minute)
    run nandboot 

For erasing the flash:

    run flash_erase

The above commands are not standard u-boot commands. They are defined in the environment. You can check the definition with the u-boot command:

    printenv

### Modify U-BOOT NAND parititions

In case the partition table needs to be modified from the default, use the below u-boot commands (can be cut and paste to u-boot all in one):

    setenv flash_all 'run flash_erase; run flash_u-boot; run flash_spl; run flash_rootfs; run flash_dtb; run flash_uImage; run flash_bootlogo; setenv bootcmd run nandboot; setenv preboot run nandpreboot; saveenv'
    setenv flash_erase 'nand erase.chip; run mtdparts_txt'
    setenv flash_bootlogo 'nand erase.part NAND.bootlogo; mw.b 0x80200000 0xff 0x40000; fatload mmc 0:1 0x80200000 bootlogo.bmp; nand write 0x80200000 NAND.bootlogo 0x${filesize}'
    setenv flash_dtb 'nand erase.part NAND.dtb; mw.b 0x80200000 0xff 0x40000; fatload mmc 0:1 0x80200000 am335x-kno_txt.dtb; nand write 0x80200000 NAND.dtb 0x${filesize}'
    setenv flash_rootfs 'nand erase.part NAND.rootfs; fatload mmc 0:1 0x80200000 rootfs.ubi; nand write 0x80200000 NAND.rootfs 0x${filesize}'
    setenv flash_spl 'nand erase.part NAND.SPL1; nand erase.part NAND.SPL2; nand erase.part NAND.SPL3; nand erase.part NAND.SPL4; mw.b 0x80200000 0xff 0x200000; fatload mmc 0:1 0x80200000 MLO; nand write 0x80200000 NAND.SPL1 0x${filesize}; nand write 0x80200000 NAND.SPL2 0x${filesize}; nand write 0x80200000 NAND.SPL3 0x${filesize}; nand write 0x80200000 NAND.SPL4 0x${filesize}'
    setenv flash_u-boot 'nand erase.part NAND.U-boot; mw.b 0x80200000 0xff 0x100000; fatload mmc 0:1 0x80200000 u-boot.img; nand write 0x80200000 NAND.U-boot 0x${filesize}'
    setenv flash_uImage 'nand erase.part NAND.uImage; mw.b 0x80200000 0xff 0x500000; fatload mmc 0:1 0x80200000 uImage; nand write 0x80200000 NAND.uImage 0x${filesize}'
    setenv mtdparts_txt 'setenv mtdparts mtdparts=nand.0:128k(NAND.SPL1),128k(NAND.SPL2),128k(NAND.SPL3),128k(NAND.SPL4),256k(NAND.dtb),1m(NAND.U-boot),128k(NAND.U-boot-env),128k(NAND.U-boot-env-backup),6m(NAND.uImage),256k(NAND.bootlogo),-(NAND.rootfs)'

Then enter these commands one by one, checking the results:

    printenv
    saveenv
    run flash_all

# UBI-FS

## Get information
 
 From TXT ssh root console:

    #/usr/sbin/ubinfo
    UBI version:                    1
    Count of UBI devices:           1
    UBI control device major/minor: 10:59
    Present UBI devices:            ubi0

    # /usr/sbin/ubinfo -d 0
    ubi0
    Volumes count:                           1
    Logical eraseblock size:                 129024 bytes, 126.0 KiB
    Total amount of logical eraseblocks:     966 (124637184 bytes, 118.9 MiB)
    Amount of available logical eraseblocks: 0 (0 bytes)
    Maximum count of volumes                 128
    Count of bad physical eraseblocks:       0
    Count of reserved physical eraseblocks:  20
    Current maximum erase counter value:     2
    Minimum input/output unit size:          2048 bytes
    Character device major/minor:            250:0
    Present volumes:                         0

    # ls -l /sys/devices/virtual/ubi/ubi0
    total 0
    -r--r--r--    1 root     root          4096 Jan  1 00:52 avail_eraseblocks
    -r--r--r--    1 root     root          4096 Jan  1 00:52 bad_peb_count
    -r--r--r--    1 root     root          4096 Jan  1 00:52 bgt_enabled
    -r--r--r--    1 root     root          4096 Jan  1 00:52 dev
    -r--r--r--    1 root     root          4096 Jan  1 00:52 eraseblock_size
    -r--r--r--    1 root     root          4096 Jan  1 00:52 max_ec
    -r--r--r--    1 root     root          4096 Jan  1 00:52 max_vol_count
    -r--r--r--    1 root     root          4096 Jan  1 00:52 min_io_size
    -r--r--r--    1 root     root          4096 Jan  1 00:52 mtd_num
    drwxr-xr-x    2 root     root             0 Jan  1 00:52 power
    -r--r--r--    1 root     root          4096 Jan  1 00:52 reserved_for_bad
    lrwxrwxrwx    1 root     root             0 Jan  1 00:52 subsystem -> ../../../../class/ubi
    -r--r--r--    1 root     root          4096 Jan  1 00:52 total_eraseblocks
    drwxr-xr-x    3 root     root             0 Jan  1 00:52 ubi0_0
    -rw-r--r--    1 root     root          4096 Jan  1 00:52 uevent
    -r--r--r--    1 root     root          4096 Jan  1 00:52 volumes_count

    # cat /sys/devices/virtual/ubi/ubi0/mtd_num 
    10

    # ls -l /sys/devices/virtual/ubi/ubi0/ubi0_0/
    total 0
    -r--r--r--    1 root     root          4096 Jan  1 00:52 alignment
    -r--r--r--    1 root     root          4096 Jan  1 00:52 corrupted
    -r--r--r--    1 root     root          4096 Jan  1 00:52 data_bytes
    -r--r--r--    1 root     root          4096 Jan  1 00:52 dev
    lrwxrwxrwx    1 root     root             0 Jan  1 00:52 device -> ../../ubi0
    -r--r--r--    1 root     root          4096 Jan  1 00:52 name
    drwxr-xr-x    2 root     root             0 Jan  1 00:52 power
    -r--r--r--    1 root     root          4096 Jan  1 00:52 reserved_ebs
    lrwxrwxrwx    1 root     root             0 Jan  1 00:52 subsystem -> ../../../../../class/ubi
    -r--r--r--    1 root     root          4096 Jan  1 00:52 type
    -rw-r--r--    1 root     root          4096 Jan  1 00:52 uevent
    -r--r--r--    1 root     root          4096 Jan  1 00:52 upd_marker
    -r--r--r--    1 root     root          4096 Jan  1 00:52 usable_eb_size

    # cat /sys/devices/virtual/ubi/ubi0/ubi0_0/data_bytes 
    121540608

# ROOT access

## Root password

The TXT initially has a random unknown root password.
The root password is generated on first boot (and during major updates) and immediately discarded.

The ROBOPro user can at any time create a new random root password using the command:

    sudo /usr/sbin/new_root_password.sh 60

On old system, display of the root password in the TxtControlApp can be enabled via:

    cd /opt/knobloch
    echo "showroot=1" > .TxtAccess.ini

The root password is shown on the display. The number is the number seconds the password is displayed. The script ensures, that only root has access to the display while the root password is shown.

So anybody who can see the screen can create a new root password.
If you don't want this, set your own root password and remove the new_root_password.sh from sudo.

## Signed scripts

The TXT has a preinstalled set of RSA signature keys in /etc/rootauth.

Using the script

    ROBOPro ALL=(root) NOPASSWD: /usr/sbin/exec_signed.sh <script> <sig>

The ROBOPro user can execute scripts signed with one of the keys as root. Fischertechnik uses this mechanism to install updates as root.

The private keys for the preinstalled keys are stored on OpenPGP signature cards. There are several keys for backup reasons.

# System update process

## Bootloader u-boot

The bootloader and kernel are copied to the TXT filesystem under

    /lib/boot/u-boot.img            # u-boot image
    /lib/boot/uImage                # buildroot Linux image?
    /lib/boot/am335x-kno_txt.dtb    # buildroot device tree?
    /lib/boot/MLO                   # Intial low-level boot loader (which loads u-boot)
    /lib/boot/UpdateBootloader.sh   # Script for updating the boot loader

During a system update, the **UpdateBootloader.sh** is run.
This script flashes the files to specific flash blocks and then deleted the files.

**fw_printenv** also mentions a **bootlogo.bmp** but this seems to be flashed only during factory initialization.

The nand paritions metioned in **UpdateBootloader.bmp** are set on a regular TXT with the u-boot environment variable

    mtdparts=mtdparts=nand.0:128k(NAND.SPL1),128k(NAND.SPL2),128k(NAND.SPL3),128k(NAND.SPL4),256k(NAND.dtb),1m(NAND.U-boot),128k(NAND.U-boot-env),128k(NAND.U-boot-env-backup),5m(NAND.uImage),256k(NAND.bootlogo),-(NAND.rootfs)

But this setting is not found in autoconf.mk - not sure where it comes from - maybe it is automatic

# Toolchain fixes

Modern Linux might use linker scripts in .so files. This means .so files can be text files which refer to one or multiple other .so files.

For the TXT toolchain, in several files wrong absolute paths are given. The paths must be removed (just file name) in files:

    /opt/FT/TXT/opt/ext-toolchain/arm-linux-gnueabihf/libc/usr/lib/libpthread.so
    /opt/FT/TXT/arm-buildroot-linux-gnueabihf/sysroot/usr/lib/libpthread.so
    /opt/FT/TXT/opt/ext-toolchain/arm-linux-gnueabihf/libc/usr/lib/libc.so
    /opt/FT/TXT/arm-buildroot-linux-gnueabihf/sysroot/usr/lib/libc.so

# Finding processes which are loaded from an executable in the file system vs kernel image

```
ls -l /proc/*/exe
```

/proc/pid/exe is a link to the executable of an image.

/proc/pid/status contains the process name in case there is no executable

# TXT Initernationalization reduction

## Time zones

Time zones probabyl should be reduced to a few.
Currently only the unused /usr/share/zoneinfo/right branch has been deleted

    https://mm.icann.org/pipermail/tz/2015-February/022024.html
    http://www.gtkdb.de/index_7_905.html

This is done in FT-TXT/board/FT/TXT/post-build_4.1.sh

## Locales

System locales are just there to show Linux error messages in non English. I don't think anybody needs this, so I removed all locales.

# Some assorted TXT commands 

Run signed update:

    sudo /usr/sbin/exec_signed.sh update.sh *.sig

Power Off TXT

    sudo /usr/share/txt-utils/power-off

Restart TXT (as root)

    kill 1

Disconnect a ssh session

    ENTER ~ .

Check library dependencies (example sshd)

    export LD_DEBUG=all
    /lib/ld-linux-armhf.so.3 --list /usr/sbin/sshd

# Some assorted host commands

Delete host key, supply password and login without asking for fingeprint authentication, ...

    ssh-keygen -f "/home/michael/.ssh/known_hosts" -R 192.168.7.2 ; sshpass -p ROBOPro ssh -oStrictHostKeyChecking=no -l ROBOPro 192.168.7.2 
    ssh-keygen -f "/home/michael/.ssh/known_hosts" -R 192.168.7.2 ; sshpass -p <PW> ssh -oStrictHostKeyChecking=no -l root 192.168.7.2 

List the shared libraries used by busybox

    cd buildroot/output/target
    $ readelf -d bin/busybox | grep NEEDED
    0x00000001 (NEEDED)                     Shared library: [libc.so.6]
    $ readelf -d lib/libc.so.6 | grep NEEDED
    0x00000001 (NEEDED)                     Shared library: [ld-linux-armhf.so.3]
    $ readelf -d lib/ld-linux-armhf.so.3 | grep NEEDED

