LIBORCANIA_VERSION = 4b908eea87f59130ee672881e88ec4b9d5b49ccc
INST_VERSION = 1.1.1
LIBORCANIA_SITE = https://github.com/babelouest/orcania.git
LIBORCANIA_SITE_METHOD = git
LIBORCANIA_INSTALL_STAGING = YES
LIBORCANIA_INSTALL_TARGET = YES

define LIBORCANIA_BUILD_CMDS
	$(MAKE) CC="$(TARGET_CC)" CXX="$(TARGET_CXX)" LD="$(TARGET_LD)" -C $(@D) all
endef

define LIBORCANIA_INSTALL_STAGING_CMDS
	$(INSTALL) -D -m 0755 $(@D)/src/liborcania.so.$(INST_VERSION) $(STAGING_DIR)/usr/lib/liborcania.so.$(INST_VERSION)
	cp -d $(@D)/src/liborcania.so $(STAGING_DIR)/usr/lib/liborcania.so
	cp -d $(@D)/src/orcania.h $(STAGING_DIR)/usr/include
endef

define LIBORCANIA_INSTALL_TARGET_CMDS
	$(INSTALL) -D -m 0755 $(@D)/src/liborcania.so.$(INST_VERSION) $(TARGET_DIR)/usr/lib/liborcania.so.$(INST_VERSION)
	cp -d $(@D)/src/liborcania.so $(TARGET_DIR)/usr/lib/liborcania.so
endef

$(eval $(generic-package))