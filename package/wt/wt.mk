################################################################################
#
# wt
#
################################################################################

WT_VERSION = 3.3.6
WT_SOURCE = $(WT_VERSION).tar.gz 
WT_SITE = https://github.com/kdeforche/wt/archive
WT_INSTALL_STAGING = YES
WT_INSTALL_TARGET = YES
##WT_CONF_OPTS = 
##WT_DEPENDENCIES = 

WT_LICENSE = LGPLv2
WT_LICENSE_FILES = LICENSE

$(eval $(cmake-package))
