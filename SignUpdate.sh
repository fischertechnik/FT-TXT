#!/bin/bash

# Sign an update script

set -e
set -x

##### ATTENTION #####

# In case the reader does not respond:
# - unplug the reader from USB
# - insert smart card into reader (chip side UP)
# - pcsc_scan
# - plugin reader
# - as soon as card is detected, press CTRL+C

##### ARGUMENTS #####

if [ "$1" = "" ] ; then
    echo "expected step number as argument $1"
    exit 1
fi

##### PATHS #####

# Directory in which this script is
SCRIPTDIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Update script
UPDATEDIR="$(dirname "$SCRIPTDIR")/update/step$1"
UPDATE="$UPDATEDIR/update.sh"

# Signature history folder
SIGNATUREROOTDIR="$(dirname "$SCRIPTDIR")/signature-history"

##### Get data from card #####

SIGCOUNTPRE=$( gpg --card-status | grep 'Signature counter' | egrep -o '[0-9]+' )
gpgconf --kill gpg-agent
sleep 1

CARDSERIAL=$( pkcs15-tool --dump | grep "Serial number" | egrep -o '([0-9a-f]{12})' )
mkdir -p $SIGNATUREROOTDIR/$CARDSERIAL
pkcs15-tool --read-public-key 1 > $SIGNATUREROOTDIR/$CARDSERIAL/$CARDSERIAL.pub

##### BACKUP FILES #####

SIGCOUNT=$(( SIGCOUNTPRE +1 ))
SIGNATURDIR=$SIGNATUREROOTDIR/$CARDSERIAL/$SIGCOUNT
mkdir -p $SIGNATURDIR
cp $UPDATE $SIGNATURDIR/

##### hash and sign file #####

openssl dgst -sha512 -binary $UPDATE  > $SIGNATURDIR/update.dgst
pkcs15-crypt --sign --input $SIGNATURDIR/update.dgst --output $SIGNATURDIR/update.$CARDSERIAL.sigbad --signature-format openssl --sha-512 --pkcs1 --key 1

# fix sgnature

SIZE=$( stat --printf="%s" $SIGNATURDIR/update.$CARDSERIAL.sigbad )
PAD=$(( 512 - SIZE ))
head -c $PAD /dev/zero | cat - $SIGNATURDIR/update.$CARDSERIAL.sigbad > $SIGNATURDIR/update.$CARDSERIAL.sig

# copy signature

cp $SIGNATURDIR/update.$CARDSERIAL.sig $UPDATEDIR/$CARDSERIAL.sig

##### check signature #####

openssl dgst -sha512 -binary -verify $SIGNATUREROOTDIR/$CARDSERIAL/$CARDSERIAL.pub -signature $SIGNATURDIR/update.$CARDSERIAL.sig $UPDATE

##### card status #####

gpg --card-status
