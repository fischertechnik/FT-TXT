# Adjust version numbers in files in Ubuntu VM

Run below command and edit the version number in all files and folders opened

    ~/FT-TXT/Edit-Version-Files-Linux.sh
    ~/FT-TXT/Edit-Version-Files-Windows.sh

In ~/FT-TXT (Clean / initial buil donly)

- ./Make-TXT-Bootloader.sh 
- ./Make-TXT-Buildroot-Clean.sh

Modern Linux might use linker scripts in .so files. This means .so files can be text files which refer to one or multiple other .so files.

For the TXT toolchain, in several files wrong absolute paths are given. The paths must be removed (just file name) in files:

```
gedit \
    /opt/FT/TXT/opt/ext-toolchain/arm-linux-gnueabihf/libc/usr/lib/libpthread.so \
    /opt/FT/TXT/arm-buildroot-linux-gnueabihf/sysroot/usr/lib/libpthread.so \
    /opt/FT/TXT/opt/ext-toolchain/arm-linux-gnueabihf/libc/usr/lib/libc.so \
    /opt/FT/TXT/arm-buildroot-linux-gnueabihf/sysroot/usr/lib/libc.so
```

In Eclipse (eclipse-cpp-2019-06-R-linux-gtk-x86_64, workspace ~/eclipse)

- Recompile all apps and libraries
  - Select all projects and right click / Build Configurations / Clean App
  - Select TxtControlMain and right click / Build Configurations / Build All
- Check build dates of files in ~/Transfer
- Copy from ~/Transfer to ~/FT-TXT/board/FT/TXT/rootfs/usr/lib
  - run ~/Transfer/copy_there_release.sh
- Check file date of libROBOProLib.so in ~/FT-TXT/board/FT/TXT/rootfs/usr/lib
- ATTENTION: From 4.4.3 on, Michael Soegtrop builds ROBOProLib.
  All other libs are provided binary by fischertechnik!

In ~/FT-TXT:

- ./Make-TXT-Buildroot-Incremental.sh
- ./Make-TXT-UpdateScripts.sh
- ./Sign-Connect-Reader.sh
- ./Sign-TXT-UpdateScripts.sh
- sudo ./Make-TXT-Image.sh
- ./Copy-TXT-Update-To-Windows.sh

In Windows
- D:\fischertechnik\Update\Copy-TXT-Update-ROBOPro.bat
- Make a ROBOPro Release build
- Do a FW Update
- Check FW
- Update Setup data from fischertechnik
- Generate new GUID for Product Id and package Id, but not for UpgradeCode
- Update ReleaseNotes.txt
- makesetup_norm.bat
