#!/bin/bash

set -e
set -x

##### PATHS AND FILES #####

# External files
EXTERNAL="$(cd ../.. ; pwd)"

# Root fs
ROOTFS="$(cd board/FT/TXT/rootfs ; pwd)"

# Firmware file to use
FIRMWARE=$EXTERNAL/DeliveryVonKnobloch/2017-11-30a_WLAN_SSID_Fixes/FW_for_TXT/FwUpdTxt

##### COPY FILES TO LOCAL TREE #####

cp $EXTERNAL/ROBOProLib/Release/libROBOProLib.so       $ROOTFS/usr/lib
cp $EXTERNAL/TxtControlLib/Release/libTxtControlLib.so $ROOTFS/usr/lib
cp $EXTERNAL/MotorIOLib/Release/libMotorIOLib.so       $ROOTFS/usr/lib
cp $EXTERNAL/SDLWidgetsLib/Release/libSDLWidgetsLib.so $ROOTFS/usr/lib
cp $EXTERNAL/Libs_for_TXT/libKeLibTxt.so               $ROOTFS/usr/lib
cp $FIRMWARE                                           $ROOTFS/sbin/
cp $EXTERNAL/TxtControlMain/Release/TxtControlMain     $ROOTFS/opt/knobloch/
cp $EXTERNAL/LibBME680/Release/libBME680.so            $ROOTFS/opt/knobloch/libs/
cp $EXTERNAL/LibExampleSLI/Release/libExampleSLI.so    $ROOTFS/opt/knobloch/libs/
cp $EXTERNAL/LibThirdParty/libalgobsec.so              $ROOTFS/opt/knobloch/libs/

##### SHOW LIB FILE TIMES #####
set +x

ls -l $EXTERNAL/ROBOProLib/Release/libROBOProLib.so
ls -l $EXTERNAL/TxtControlLib/Release/libTxtControlLib.so
ls -l $EXTERNAL/MotorIOLib/Release/libMotorIOLib.so
ls -l $EXTERNAL/SDLWidgetsLib/Release/libSDLWidgetsLib.so
ls -l $EXTERNAL/Libs_for_TXT/libKeLibTxt.so
ls -l $FIRMWARE
ls -l $EXTERNAL/TxtControlMain/Release/TxtControlMain
ls -l $EXTERNAL/LibBME680/Release/libBME680.so
ls -l $EXTERNAL/LibExampleSLI/Release/libExampleSLI.so
ls -l $EXTERNAL/LibThirdParty/libalgobsec.so
