#!/bin/sh

# Execute a signed shell script as root
# Parameters
# $1: Script file name
# $2: Signature file name (this also selects the key)

# Exit on all errors
set -e

# Determine Key file
case "$2" in
  *000500005808.sig) keyfile="/etc/rootauth/000500005808.pub" ;;
  *000500005809.sig) keyfile="/etc/rootauth/000500005809.pub" ;;
  *00050000580a.sig) keyfile="/etc/rootauth/00050000580a.pub" ;;
  *00050000582a.sig) keyfile="/etc/rootauth/00050000582a.pub" ;;
  *00050000582b.sig) keyfile="/etc/rootauth/00050000582b.pub" ;;
  *00050000582c.sig) keyfile="/etc/rootauth/00050000582c.pub" ;;
  *00050000582d.sig) keyfile="/etc/rootauth/00050000582d.pub" ;;
  *00050000582e.sig) keyfile="/etc/rootauth/00050000582e.pub" ;;
  *) echo "Illegal argument #2 - exit"
     exit 1
esac

# Set umask to exclusive root access
umask 077

# Create a new folder with unique name in /tmp
folder=$( mktemp -d -p /tmp )

# Copy file to new folder
# File must be copied to ensure exlusive root access and to avoid modifications between verification and execution
cp "$1" $folder/executable

# Dump access rights (just for checking)
ls -la $folder

# Verify the signature and run script
if [ "$( /usr/bin/openssl dgst -sha512 -verify $keyfile -signature "$2" $folder/executable )" == "Verified OK" ]
then
  chmod u+x $folder/executable
  $folder/executable
else
  echo "Signature check failed - script not executed!"
fi

# Remove file
rm -f $folder/executable
rmdir $folder
