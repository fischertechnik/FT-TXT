#!/bin/bash

# Extract playlod from update script

set -e
set -x

##### PATHS #####

# Directory in which this script is
SCRIPTDIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Update output
UPDATE="$(dirname "$SCRIPTDIR")/update"

# Update script
UPDATE2="$UPDATE/update.sh"

# Update script 2 payload
PAYLOAD2="$UPDATE/update.tar.gz"

##### Split update script #####

match=$(grep -n -a -m 1 '^PAYLOADTAR:$' $UPDATE2 | cut -d ':' -f 1)
payload_start=$((match + 1))
tail -n +$payload_start $UPDATE2 > $PAYLOAD2
