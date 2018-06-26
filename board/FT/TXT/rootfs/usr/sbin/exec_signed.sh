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
# Cause of umask setting, it will have access rights 700
folder=$( mktemp -d -p /tmp )

# Move executable and key to new temporary folder
/bin/mv "$1" "$folder/executable"
/bin/mv "$2" "$folder/signature"

# Set file permissions of executable to root only
# The owner / permissions are part of the inode, so this applies to any hard links
# Do chown first - otherwise the old owner could change permissions back
/bin/chown root:root "$folder/executable"
/bin/chown root:root "$folder/signature"
/bin/chmod 500 "$folder/executable"
/bin/chmod 500 "$folder/signature"

# Exit in case any process is accessing the executable file
# Such processes could still access / modify the file after signatue check if they have it open before the chmod
# Note: This is not entirely safe - if a handle is transfered from one process to a child, it is not sure
# fuser will capture it, so we repeat this 100 times
for i in `seq 1 100`
do
  /usr/bin/fuser -k "$folder/executable" || true
done

# Do the same for the signature - changing teh signature shouldn't harm,
# but who knows what openssl does if the file is modified whilt it runs
# Still be less paranoid
for i in `seq 1 10`
do
  /usr/bin/fuser -k "$folder/signature" || true
done

# In case there are still processes accessing the executable file, exit
# Even this is not entirely safe, e.g. also because it is hard to check teh return value of fuser.
# 0 means "process accessing the file found", non zero means error or no process found, and it is not clear which.
# So rpeeat this as well
for i in `seq 1 100`
do
  if /usr/bin/fuser "$folder/executable"
  then
    /bin/echo "File access check failed!"
    /bin/rm -f $folder/executable
    /bin/rm -f $folder/signature
    /bin/rmdir $folder
    exit 1
  fi
done

# And again the same for the signature
for i in `seq 1 10`
do
  if /usr/bin/fuser "$folder/signature"
  then
    /bin/echo "File access check failed!"
    /bin/rm -f $folder/executable
    /bin/rm -f $folder/signature
    /bin/rmdir $folder
    exit 2
  fi
done

# Dump access rights (just for checking)
/bin/ls -la "$folder"

# OK, the executable and signature file should now be properly secured against modification after the signature check

# Verify the signature and run the executable
if [ "$( /usr/bin/openssl dgst -sha512 -verify $keyfile -signature "$folder/signature" "$folder/executable" )" == "Verified OK" ]
then
  "$folder/executable"
else
  /bin/echo "Signature check failed - script not executed!"
fi

# Remove executable, signature and temporary folder
/bin/rm -f $folder/executable
/bin/rm -f $folder/signature
/bin/rmdir $folder

# End of script
