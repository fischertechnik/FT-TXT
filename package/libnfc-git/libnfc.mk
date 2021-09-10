################################################################################
#
# libnfc-git
#
################################################################################

LIBNFC_GIT_VERSION = 4ae4cc8
LIBNFC_GIT_SITE = https://github.com/nfc-tools/libnfc.git
LIBNFC_GIT_SITE_METHOD = git
LIBNFC_GIT_LICENSE = LGPL-3.0+
LIBNFC_GIT_LICENSE_FILES = COPYING
LIBNFC_GIT_AUTORECONF = YES
LIBNFC_GIT_INSTALL_STAGING = YES
LIBNFC_GIT_INSTALL_TARGET = YES

LIBNFC_GIT_DEPENDENCIES = host-pkgconf libusb libusb-compat

# N.B. The acr122 driver requires pcsc-lite.
LIBNFC_GIT_CONF_OPTS = --with-drivers=pn532_i2c

ifeq ($(BR2_PACKAGE_LIBNFC_GIT_EXAMPLES),y)
LIBNFC_GIT_CONF_OPTS += --enable-example
LIBNFC_GIT_DEPENDENCIES += readline
else
LIBNFC_GIT_CONF_OPTS += --disable-example
endif

$(eval $(autotools-package))
