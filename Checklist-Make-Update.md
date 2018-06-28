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

In Windows VM
- F:\TX2\buildroot\MakeUpdate\CopyToProgAndSetup.bat
- Make a ROBOPro Release build
- Do a FW Update
- Check FW
- Update Setup data from fischertechnik
- Generate new GUID for Product Id and package Id, but not for UpgradeCode
- Update ReleaseNotes.txt
- makesetup_norm.bat
