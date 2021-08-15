#!/bin/sh

# NOTE: This file is specific to upting the ROBOPro-Version

cp ~/eclipse/ROBOProLib/ftProVersion.h /fischertechnik/TX2/ROBOProLib/ftProVersion.h

code \
   /fischertechnik/ROBOPro/ftPro/SetupWiX/Filenames/Filenames_*.txt \
   /fischertechnik/ROBOPro/ftPro/SetupWiX/ROBOPro.wxs

nautilus /fischertechnik/ROBOPro/ftPro/Firmware
