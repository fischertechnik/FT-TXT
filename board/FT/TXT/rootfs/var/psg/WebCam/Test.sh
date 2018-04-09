#!/bin/sh
cd /var/psg/WebCam
./mjpg_streamer -i "./input_uvc.so -n -y -r 320x240 -f 25 -q 85" -o "./output_http.so -w ./www"
