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

# Create updatescript
cat update-in.sh > "$UPDATE/update.sh"

echo "" >> "$UPDATE/update.sh"
echo "PAYLOADTOOLSBEG:" >> "$UPDATE/update.sh"
gzip -c "$UPDATE/ShowProgressOld" | base64 >> "$UPDATE/update.sh"
echo "PAYLOADTOOLSEND:" >> "$UPDATE/update.sh"

echo "" >> "$UPDATE/update.sh"
echo "PAYLOADTAR:" >> "$UPDATE/update.sh"
cat "$BUILDROOT/output/images/rootfs.tar.gz" >> "$UPDATE/update.sh"

chmod u+x "$UPDATE/update.sh"
