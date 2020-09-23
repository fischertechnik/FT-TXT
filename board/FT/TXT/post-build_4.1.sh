#!/bin/sh

set -x

TARGETDIR=$1
echo ">>>$TARGETDIR<<<"

# Update build number
BUILD=`cat ../FT-TXT/board/FT/TXT/BUILD`
BUILDDATE=`date +"%Y-%m-%d %H:%M"`
BUILD=$((BUILD+1))
echo $BUILD > ../FT-TXT/board/FT/TXT/BUILD

# Fix access rights in rootfs source
chmod 600  ../FT-TXT/board/FT/TXT/rootfs/root/.ssh/id*

# Update rootfs in $TARGETDIR from ../FT-TXT/board/FT/TXT/rootfs
rm -rf $TARGETDIR/etc/sudoers
cp -a ../FT-TXT/board/FT/TXT/rootfs/* $TARGETDIR/

# Local support seems to be no longer required by TxtControl
# All references to locale.h, libintl.h, /usr/lib/local are commented out => remove this completely
# cp -v output/staging/usr/bin/locale $TARGETDIR/usr/bin/
# mkdir -p $TARGETDIR/usr/share/i18n
# cp -a output/staging/usr/share/i18n/* $TARGETDIR/usr/share/i18n

# right time zones are not used (all links are to posix) => remove unused time zones
rm -rf $TARGETDIR/usr/share/zoneinfo/right

# Copy fonts
# TODO: fix this such that this works with fontconfig
# Instead of copying the fonts to /usr/lib, create a symlink
ln -s $TARGETDIR/usr/share/fonts/ $TARGETDIR/usr/lib/fonts

# Copy boot loader files
cp $BINARIES_DIR/uImage $TARGETDIR/lib/boot
cp $BINARIES_DIR/am335x-kno_txt.dtb $TARGETDIR/lib/boot

# Create ROBOPro
mkdir $TARGETDIR/opt/knobloch/ROBOPro
chmod 775 $TARGETDIR/opt/knobloch/ROBOPro

# Create C-Program
mkdir $TARGETDIR/opt/knobloch/C-Program
chmod 775 $TARGETDIR/opt/knobloch/C-Program

# Cloud
chmod 744 $TARGETDIR/opt/knobloch/Cloud/TxtSmartHome.cloud
#touch $TARGETDIR/opt/knobloch/Cloud/.protected
#chmod 444 $TARGETDIR/opt/knobloch/Cloud/.protected

# SoundFiles
chmod 744 $TARGETDIR/opt/knobloch/SoundFiles/
#touch $TARGETDIR/opt/knobloch/Sounds/.protected
#chmod 444 $TARGETDIR/opt/knobloch/Sounds/.protected

# libs
chmod 775 $TARGETDIR/opt/knobloch/libs/
chmod 744 $TARGETDIR/opt/knobloch/libs/libBME680.so
chmod 744 $TARGETDIR/opt/knobloch/libs/libExampleSLI.so
#touch $TARGETDIR/opt/knobloch/libs/.protected
#chmod 444 $TARGETDIR/opt/knobloch/libs/.protected

# Create Python
#mkdir $TARGETDIR/opt/knobloch/Python
#chmod 775 $TARGETDIR/opt/knobloch/Python
#touch $TARGETDIR/opt/knobloch/Python/.protected
#chmod 444 $TARGETDIR/opt/knobloch/Python/.protected

# Create Scratch
mkdir $TARGETDIR/opt/knobloch/Scratch
chmod 775 $TARGETDIR/opt/knobloch/Scratch
#touch $TARGETDIR/opt/knobloch/Scratch/.protected
#chmod 444 $TARGETDIR/opt/knobloch/Scratch/.protected

# Create Data
mkdir $TARGETDIR/opt/knobloch/Data
chmod 775 $TARGETDIR/opt/knobloch/Data

# Rename S50sshd (start sshd manual)
SERVICE=S50sshd
NSERVICE=mS50sshd
if [ -f $TARGETDIR/etc/init.d/$SERVICE ]
then
        rm $TARGETDIR/etc/init.d/$NSERVICE
        mv $TARGETDIR/etc/init.d/$SERVICE $TARGETDIR/etc/init.d/$NSERVICE
fi

# Rename S41dhcpcd (start dhcpcd manual, will be started from GUI if requested)
SERVICE=S41dhcpcd
NSERVICE=mS41dhcpcd
if [ -f $TARGETDIR/etc/init.d/$SERVICE ]
then
        rm $TARGETDIR/etc/init.d/$NSERVICE
        mv $TARGETDIR/etc/init.d/$SERVICE $TARGETDIR/etc/init.d/$NSERVICE
fi

# WEB server
rm $TARGETDIR/var/www/civetweb_64x64.png
chmod -R 775 $TARGETDIR/var/www

# Set build info
echo "fischertechnik TXT Rel 2.0 Build $BUILD ($BUILDDATE)" > $TARGETDIR/etc/BUILD
