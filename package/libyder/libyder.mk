LIBYDER_VERSION = fdcf30db62f1debdecf7fa6106de4883cdb0631c
INST_VERSION = 1.1.1
LIBYDER_SITE = https://github.com/babelouest/yder.git
LIBYDER_SITE_METHOD = git
LIBYDER_INSTALL_STAGING = YES
LIBYDER_INSTALL_TARGET = YES

LIBYDER_DEPENDENCIES = liborcania
define LIBYDER_BUILD_CMDS
	$(MAKE) CC="$(TARGET_CC)" CXX="$(TARGET_CXX)" LD="$(TARGET_LD)" -C $(@D) all
endef

define LIBYDER_INSTALL_STAGING_CMDS
	$(INSTALL) -D -m 0755 $(@D)/src/libyder.so.$(INST_VERSION) $(STAGING_DIR)/usr/lib/libyder.so.$(INST_VERSION)
	cp -d $(@D)/src/libyder.so $(STAGING_DIR)/usr/lib/libyder.so
	cp -d $(@D)/src/yder.h $(STAGING_DIR)/usr/include
endef

define LIBYDER_INSTALL_TARGET_CMDS
	$(INSTALL) -D -m 0755 $(@D)/src/libyder.so.$(INST_VERSION) $(TARGET_DIR)/usr/lib/libyder.so.$(INST_VERSION)
	cp -d $(@D)/src/libyder.so $(TARGET_DIR)/usr/lib/libyder.so
endef

$(eval $(generic-package))