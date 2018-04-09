#!/bin/bash
FILENAME=$1

SHA_VAL=`sha256sum -b $FILENAME  | awk '{ print $1 }'`
echo -e "sha256\t$SHA_VAL\t`basename $FILENAME`"

#echo -e "sha256\t`sha256sum -b ./dl/zip30.tgz`"