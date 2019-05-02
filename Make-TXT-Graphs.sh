#!/bin/bash

set -e
set -x

WRKDIR=`pwd`

cd ../buildroot
make graph-depends
make graph-build
make graph-size

cd $WRKDIR
