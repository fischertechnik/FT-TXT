LIBULFIUS_VERSION = fca4261026a9bfc1963a16eee2607883aa08bd27
ULFIUS_INST_VERSION = 2.2.4
LIBULFIUS_SITE = https://github.com/babelouest/ulfius.git
LIBULFIUS_SITE_METHOD = git
LIBULFIUS_INSTALL_STAGING = YES
LIBULFIUS_INSTALL_TARGET = YES

LIBULFIUS_DEPENDENCIES = libyder

define LIBULFIUS_BUILD_CMDS
	$(MAKE) CC="$(TARGET_CC)" CXX="$(TARGET_CXX)" LD="$(TARGET_LD)" -C $(@D) all
endef

define LIBULFIUS_INSTALL_STAGING_CMDS
	$(INSTALL) -D -m 0755 $(@D)/src/libulfius.so.$(ULFIUS_INST_VERSION) $(STAGING_DIR)/usr/lib/libulfius.so.$(ULFIUS_INST_VERSION)
	cp -d $(@D)/src/libulfius.so $(STAGING_DIR)/usr/lib/libulfius.so
	cp -d $(@D)/src/ulfius.h $(STAGING_DIR)/usr/include
endef

define LIBULFIUS_INSTALL_TARGET_CMDS
	$(INSTALL) -D -m 0755 $(@D)/src/libulfius.so.$(ULFIUS_INST_VERSION) $(TARGET_DIR)/usr/lib/libulfius.so.$(ULFIUS_INST_VERSION)
	cp -d $(@D)/src/libulfius.so $(TARGET_DIR)/usr/lib/libulfius.so
endef

$(eval $(generic-package))