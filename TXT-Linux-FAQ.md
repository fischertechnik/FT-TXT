# U-BOOT

## U-BOOT Documentation

* [U-Boot Manual](https://www.denx.de/wiki/DULG/Manual) (a newer version that what is on the TXT)
* [Nand commands](https://www.denx.de/wiki/DULG/UBootCmdGroupNand)
* See also on disk "buildroot-2018/u-boot/board-support/u-boot-2013.10-ti2013.12.01/doc"

## How are the initial u-boot parameters set?

Initial u-boot parameters and are set in :

    buildroot-2018/u-boot/board-support/u-boot-2013.10-ti2013.12.01/include/autoconf.mk

Initial u-boot environment variables are set in this file in **CONFIG_EXTRA_ENV_SETTINGS**

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

# ROOT access

## Root password

The TXT initially has a random unknown root password.
The root password is generated on first boot (and during major updates) and immediately discarded.

The ROBOPro user can at any time create a new random root password using the command:

    sudo /usr/sbin/new_root_password.sh 90

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