################################################################################
#
# libfreefare-git
#
################################################################################

LIBFREEFARE_GIT_VERSION = master
LIBFREEFARE_GIT_SITE = https://github.com/nfc-tools/libfreefare.git
LIBFREEFARE_GIT_SITE_METHOD = git
LIBFREEFARE_GIT_DEPENDENCIES = libnfc-git openssl
LIBFREEFARE_GIT_LICENSE = LGPL-3.0+ with exception
LIBFREEFARE_GIT_LICENSE_FILES = COPYING
LIBFREEFARE_GIT_AUTORECONF = YES
LIBFREEFARE_GIT_INSTALL_STAGING = YES
LIBFREEFARE_GIT_INSTALL_TARGET = YES

ifeq ($(BR2_STATIC_LIBS),y)
# openssl needs zlib even if the libfreefare example itself doesn't
LIBFREEFARE_GIT_CONF_ENV += LIBS='-lz'
endif

$(eval $(autotools-package))
