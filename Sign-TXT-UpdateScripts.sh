#!/bin/bash

# Sign an update script

set -e
set +x

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
UPDATEDIR="$(dirname "$SCRIPTDIR")/update"
UPDATEBASE="$UPDATEDIR/update-$1"
UPDATE="$UPDATEBASE.sh"

# Signature history folder
SIGNATUREROOTDIR="$(dirname "$SCRIPTDIR")/signature-history"

##### Get data from card #####

SIGCOUNTPRE=$( gpg --card-status | grep 'Signature counter' | egrep -o '[0-9]+' )
gpgconf --kill gpg-agent
sleep 1

CARDSERIAL=$( pkcs15-tool --dump | grep "Serial number" | egrep -o '([0-9a-f]{12})' )
mkdir -p $SIGNATUREROOTDIR/$CARDSERIAL
pkcs15-tool --read-public-key 1 > $SIGNATUREROOTDIR/$CARDSERIAL/$CARDSERIAL.pub

##### Copy file to be signed #####

SIGCOUNT=$(( SIGCOUNTPRE +1 ))
SIGNATURDIR=$SIGNATUREROOTDIR/$CARDSERIAL/$SIGCOUNT
mkdir -p $SIGNATURDIR
cp $UPDATE $SIGNATURDIR/
SIGNATURBASE=$SIGNATURDIR/update-$1

##### hash file #####

openssl dgst -sha512 -binary $UPDATE  > $SIGNATURBASE.dgst

##### check if file with this hash already exists

HASHDIR=$SIGNATUREROOTDIR/$CARDSERIAL/hashes
mkdir -p $HASHDIR
HASHCODE=$(hexdump -n 16 -e '16/1 "%02x" "\n"' $SIGNATURBASE.dgst)

# Try creating a link from the hash directory to the current signature folder
if [ ! -L $HASHDIR/$HASHCODE ]
then
    # Link did not exist => new signature
    # sign file
    pkcs15-crypt --sign --input $SIGNATURBASE.dgst --output $SIGNATURBASE-$CARDSERIAL.sigbad --signature-format openssl --sha-512 --pkcs1 --key 1
    # fix sgnature
    SIZE=$( stat --printf="%s" $SIGNATURBASE-$CARDSERIAL.sigbad )
    PAD=$(( 512 - SIZE ))
    head -c $PAD /dev/zero | cat - $SIGNATURBASE-$CARDSERIAL.sigbad > $SIGNATURBASE-$CARDSERIAL.sig
    # copy signature
    cp $SIGNATURBASE-$CARDSERIAL.sig $UPDATEBASE-$CARDSERIAL.sig
    # Create hash link
    ln -s $SIGNATURDIR $HASHDIR/$HASHCODE
else
    # Link did exist => existing signature
    echo "===================================================================================================="
    echo "Signature wit code $HASHCODE already exists in"
    echo "$(readlink -f $HASHDIR/$HASHCODE)"
    echo "Just copying signature"
    echo "===================================================================================================="
    # copy cached signature
    cp $HASHDIR/$HASHCODE/*.sig $UPDATEBASE-$CARDSERIAL.sig
    # remove signature folder
    rm -rf $SIGNATURDIR
fi

##### check signature #####

openssl dgst -sha512 -binary -verify $SIGNATUREROOTDIR/$CARDSERIAL/$CARDSERIAL.pub -signature $UPDATEBASE-$CARDSERIAL.sig $UPDATE

##### card status #####

gpg --card-status
