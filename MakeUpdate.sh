#!/bin/bash

set -e
set -x

##### PATHS #####

# Directory in which this script is
SCRIPTDIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Build root
BUILDROOT="$(dirname "$SCRIPTDIR")/buildroot"

# Update output
UPDATE="$(dirname "$SCRIPTDIR")/update"

##### CREATE UPDATE #####

mkdir -p "$UPDATE/step1"
mkdir -p "$UPDATE/step2"

# Copy step 1 script
cp update-step-1.sh "$UPDATE/step1/update.sh" 

# Create step 2 script
cat update-step-2.sh > "$UPDATE/step2/update.sh"
echo "PAYLOAD:" >> "$UPDATE/step2/update.sh"
cat "$BUILDROOT/output/images/rootfs.tar.gz" >> "$UPDATE/step2/update.sh"
