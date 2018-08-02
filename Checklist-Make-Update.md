# Adjust version numbers in files

Run below command and edit the version number in all files and folders opened

    /data/transfer/TX2/buildroot/MakeUpdate/edit_version_files.sh

In buildroot-2018/FT-TXT (Clean / initial buil donly)

- Make-TXT-Buildroot-Clean.sh

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
