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

# Copy step 1 script
cp update-step-1.sh "$UPDATE/update-1.sh" 
cp update-step-1-rp.sh "$UPDATE/update-1-rp.sh" 

# Create step 2 script
cat update-step-2.sh > "$UPDATE/update-2.sh"
echo "PAYLOAD:" >> "$UPDATE/update-2.sh"
cat "$BUILDROOT/output/images/rootfs.tar.gz" >> "$UPDATE/update-2.sh"
