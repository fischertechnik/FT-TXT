#!/bin/sh

# Create a new random root password and display it on the screen
# $1: Number of seconds to display root password

# Exit on all errors
set -e

# Set file permissions of screen buffer
# The owner / permissions are part of the inode, so this applies to any hard links
# Do chown first - otherwise the old owner could change permissions back
/bin/chown root:root "/dev/fb0"
/bin/chmod 600 "/dev/fb0"

# Kill all processes accessing the frame buffer
# Such processes could still access / modify the frame buffer if they have it open before the chmod
# Note: This is not entirely safe - if a handle is transfered from one process to a child, it is not sure
# fuser will capture it, so we repeat this 20 times

echo "Making sure the frame buffer is only accessed by root ..."

for i in `seq 1 100`
do
  /usr/bin/fuser -k "/dev/fb0" || true
done

# In case there are still processes accessing the executable file, exit
# Even this is not entirely safe, e.g. also because it is hard to check teh return value of fuser.
# 0 means "process accessing the file found", non zero means error or no process found, and it is not clear which.
# So rpeeat this as well
for i in `seq 1 100`
do
  if /usr/bin/fuser "/dev/fb0"
  then
    /bin/echo "Frame buffer access check failed!"
    /bin/chown root:video "/dev/fb0"
    /bin/chmod 660 "/dev/fb0"
    exit 1
  fi
done

# Dump access rights (just for checking)
/bin/ls -la "/dev/fb0"

echo "Changing root password ..."

# OK, the frame buffer should now be properly secured against read out during password display

# Create new random root password
# The root password has 16 characters digits and upper case letters exlcuding 0,O,1 and I
NEWPASSWORD=`cat /dev/random | tr -dc 'A-HJ-NP-Z2-9' | fold -w 16 | head -n 1`
# Set the new password
{ echo $NEWPASSWORD; echo $NEWPASSWORD; } | passwd
# Display the new password
echo $NEWPASSWORD | /usr/sbin/ShowPassword -t="$1"

# Restore access rights for frame buffer
/bin/chown root:video "/dev/fb0"
/bin/chmod 660 "/dev/fb0"

# Dump access rights (just for checking)
/bin/ls -la "/dev/fb0"

echo "Done!"

# End of script
