#!/bin/sh
mount|grep ' / '|cut -d' ' -f 1 >/opt/knobloch/.bootdev
