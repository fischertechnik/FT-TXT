# Adjust version numbers in files

Run below command and edit the version number in all files and folders opened

    /data/transfer/TX2/buildroot/MakeUpdate/edit_version_files.sh

In buildroot-2018/FT-TXT (Clean / initial buil donly)

- Make-TXT-Buildroot-Clean.sh

Modern Linux might use linker scripts in .so files. This means .so files can be text files which refer to one or multiple other .so files.

For the TXT toolchain, in several files wrong absolute paths are given. The paths must be removed (just file name) in files:

```
gedit \
    /opt/FT/TXT/opt/ext-toolchain/arm-linux-gnueabihf/libc/usr/lib/libpthread.so \
    /opt/FT/TXT/arm-buildroot-linux-gnueabihf/sysroot/usr/lib/libpthread.so \
    /opt/FT/TXT/opt/ext-toolchain/arm-linux-gnueabihf/libc/usr/lib/libc.so \
    /opt/FT/TXT/arm-buildroot-linux-gnueabihf/sysroot/usr/lib/libc.so
```

In Eclipse

- Recompile all apps and libraries
- Copy from transfer folder to FT-TXT/board/...
- ATTENTION: From 4.4.3 on, Michael Soegtrop builds ROBOProLib and MotorIOLib.
  All other libs are provided binary by fischertechnik!

In buildroot-2018/FT-TXT:

- ./Make-TXT-Buildroot-Incremental.sh
- ./Make-TXT-UpdateScripts.sh
- ./Sign-Connect-Reader.sh
- ./Sign-TXT-UpdateScripts.sh 1
- ./Sign-TXT-UpdateScripts.sh 2
- sudo ./Make-TXT-Image.sh

In Windows VM
- F:\TX2\buildroot-2018\FT-TXT\Copy-TXT-Update-ROBOPro.bat
- Make a ROBOPro Release build
- Do a FW Update
- Check FW
- Update Setup data from fischertechnik
- Generate new GUID for Product Id and package Id, but not for UpgradeCode
- Update ReleaseNotes.txt
- makesetup_norm.bat
