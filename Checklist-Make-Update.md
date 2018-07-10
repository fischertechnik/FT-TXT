# Adjust version numbers in files

Run

    /data/transfer/TX2/buildroot/MakeUpdate/edit_version_files.sh

Or manually adjust

    Adjust version number in /data/transfer/TX2/ROBOProLib/ftProVersion.h
    Adjust version number in /data/transfer/TX2/buildroot/ft-TXT/update/update.sh.in
    Adjust version number in /data/transfer/TX2/buildroot/ft-TXT/board/knobloch/TXT/rootfs/etc/sysversion
    Adjust version number in /data/transfer/TX2/buildroot/MakeUpdate/CopyToProgAndSetup.bat
    Adjust firmware update source in gedit /data/transfer/TX2/buildroot/vm_put_binaries.sh
    Adjust version number in /data/transfer/ROBOPro/ftPro/SetupWiX/Filenames
    Adjust version number in /data/transfer/ROBOPro/ftPro/Firmware/
    Adjust version number in C:\User\Michael\fischertechnik\ftPro\SetupWiX\ROBOPro.wxs

In buildroot-2018/FT-TXT (Clean / initial buil donly)

- Make-TXT-Buildroot-Clean.sh

In Eclipse

- Recompile all apps and libraries
- Copy from transfer folder to FT-TXT/board/...

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
