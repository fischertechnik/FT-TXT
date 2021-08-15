# Get latest version

cd ~/FT-TXT
git status
git pull

# Possibly adjust Builroot settings in

FT-TXT/configs/FT-TXT_4.1_defconfig

# Adjust version numbers in files in Ubuntu VM

In Windows: checkout the Filenames_XX.txt files.
In Linux: make sure that VSCode is setup with a default encoding of ISO-8859-1 (preferences, search encoding)

Run below command and edit the version number in all files and folders opened

    ~/FT-TXT/Edit-Version-Files-Linux.sh
    ~/FT-TXT/Edit-Version-Files-Windows.sh

# Build

## Build Firmware

In ~/FT-TXT (Clean / initial buil donly)

- ./Make-TXT-Bootloader.sh 
- ./Make-TXT-Buildroot-Clean.sh

NOTE: Buildroot does parallel builds inside each package. It might make sense to
- Set BR2_JLEVEL to 1.5 times CPU count
- Increase CPU count to 8 or 12 (memory consumption is small)
- Move the image to flash for faster disk access
- See http://nightly.buildroot.org/manual.pdf section 8.12

## Build libraries

Modern Linux might use linker scripts in .so files. This means .so files can be text files which refer to one or multiple other .so files.

For the TXT toolchain, in several files wrong absolute paths are given. The paths must be removed (just file name) in files:

```
gedit \
    /opt/FT/TXT/opt/ext-toolchain/arm-linux-gnueabihf/libc/usr/lib/libpthread.so \
    /opt/FT/TXT/arm-buildroot-linux-gnueabihf/sysroot/usr/lib/libpthread.so \
    /opt/FT/TXT/opt/ext-toolchain/arm-linux-gnueabihf/libc/usr/lib/libc.so \
    /opt/FT/TXT/arm-buildroot-linux-gnueabihf/sysroot/usr/lib/libc.so
```

In Eclipse:

- SW         ~/Install/Eclipse/eclipse-cpp-2019-06-R-linux-gtk-x86_64
- workspace  ~/eclipse

- Recompile all apps and libraries
  - Select all projects and right click / Build Configurations / Clean App
  - Select TxtControlMain and right click / Build Configurations / Build All
    (Note: this does not rebuild TxtControlLib and an Example lib, so better select all and build)
- Check build dates of files in ~/Transfer
- Copy from ~/Transfer to ~/FT-TXT/board/FT/TXT/rootfs/usr/lib
  - run ~/Transfer/copy_there_release.sh
- Check file date of libROBOProLib.so in ~/FT-TXT/board/FT/TXT/rootfs/usr/lib
- ATTENTION: From 4.4.3 on, Michael Soegtrop builds ROBOProLib.
  All other libs are provided binary by fischertechnik!

## Rebuild Firmware with new libraries

In ~/FT-TXT:

- ./Make-TXT-Buildroot-Incremental.sh
- sudo ./Make-TXT-Image.sh
- Possibly test above image via SD card boot
  - Note: On Windows multi parition images can be flashed with
    - Etcher: https://www.balena.io/etcher/
    - Win32 Disk Imager: https://sourceforge.net/projects/win32diskimager/files/
    - WinImage does *not* work for this!
- ./Make-TXT-UpdateScripts.sh
- ./Sign-Connect-Reader.sh
  - Note: in case the card reader does not work in VM, it might be due to Wireshark / USBpcap
- ./Sign-TXT-UpdateScripts.sh
- ./Copy-TXT-Update-To-Windows.sh

## Create setup

In Windows
- D:\fischertechnik\Update\Copy-TXT-Update-ROBOPro.bat
- Make a ROBOPro Release build
- Do a FW Update
- Check FW
- Update Setup data from fischertechnik
- Generate new GUID for Product Id and package Id, but not for UpgradeCode
- Update ReleaseNotes.txt
- makesetup_norm.bat
