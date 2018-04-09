#!/bin/sh

TARGETDIR=$1

echo ">>>$TARGETDIR<<<"

BUILD=`cat ../FT-TXT/board/FT/TXT/BUILD`
BUILDDATE=`date +"%Y-%m-%d %H:%M"`
BUILD=$((BUILD+1))
echo $BUILD > ../FT-TXT/board/FT/TXT/BUILD

chmod 600  ../FT-TXT/board/FT/TXT/rootfs/root/.ssh/id*
cp -a ../FT-TXT/board/FT/TXT/rootfs/* $TARGETDIR/
cp -v output/staging/usr/bin/locale $TARGETDIR/usr/bin/
mkdir -p $TARGETDIR/usr/share/i18n
cp -a output/staging/usr/share/i18n/* $TARGETDIR/usr/share/i18n

#------------------------------------
#-- fonts kopieren, muss noch geändert werden dass qt mit fontconfig funktioniert
mkdir -p $TARGETDIR/usr/lib/fonts
cp $TARGETDIR/usr/share/fonts/dejavu/* $TARGETDIR/usr/lib/fonts
cp $TARGETDIR/usr/share/fonts/liberation/* $TARGETDIR/usr/lib/fonts
#------------------------------------
#------------------------------------
echo "fischertechnik TXT Rel 2.0 Build $BUILD ($BUILDDATE)" > $TARGETDIR/etc/BUILD
