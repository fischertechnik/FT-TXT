#!/bin/bash

# Prepare card reader
# The Gemalto card reader goes to sleep if it is not used within short time after plugin
# So unplug it, run pcsc_scan and plug it in again

echo "Please unplug the Gemalto card reader now!"
echo "Then press Enter and plug it in again"
echo "ATTENTION: If Linux runs in VM, connect to USB2 port"
echo "(Or enable USB3 in the VM)"
echo "As soon as the card reader is detected, press CTRL+C"
read -p "Press enter as soon as you unplugged the reader!"

pcsc_scan
