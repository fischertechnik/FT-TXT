#!/bin/bash
wget -q --spider https://www.fischertechnik.de

if [ $? -eq 0 ]; then
    echo "Online" > /opt/knobloch/.www
else
    echo "Offline" > /opt/knobloch/.www
fi