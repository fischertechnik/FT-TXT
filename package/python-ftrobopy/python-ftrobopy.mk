################################################################################
#
# python-ftrobopy
#
################################################################################

PYTHON_FTROBOPY_VERSION = edc44bf00f
PYTHON_FTROBOPY_SITE_METHOD = git
PYTHON_FTROBOPY_SITE = https://github.com/ftrobopy/ftrobopy.git
PYTHON_FTROBOPY_LICENSE = MIT
PYTHON_FTROBOPY_LICENSE_FILES = LICENSE
PYTHON_FTROBOPY_INSTALL_STAGING = NO
PYTHON_FTROBOPY_DEPENDENCIES = python-serial

ifeq ($(BR2_PACKAGE_PYTHON),y)
PYTHON_FTROBOPY_DEPENDENCIES += python
else ifeq ($(BR2_PACKAGE_PYTHON3),y)
PYTHON_FTROBOPY_DEPENDENCIES += python3
endif

define PYTHON_FTROBOPY_INSTALL_PY_STUFF
	$(INSTALL) -D -m 0644 $(@D)/ftrobopy.py $(TARGET_DIR)/usr/lib/python$(PYTHON3_VERSION_MAJOR)/site-packages/ftrobopy/ftrobopy.py
	$(INSTALL) -D -m 0644 $(@D)/LICENSE.txt $(TARGET_DIR)/usr/lib/python$(PYTHON3_VERSION_MAJOR)/site-packages/ftrobopy/LICENSE.txt
	$(INSTALL) -D -m 0644 $(@D)/__init__.py $(TARGET_DIR)/usr/lib/python$(PYTHON3_VERSION_MAJOR)/site-packages/ftrobopy/__init__.py
endef
PYTHON_FTROBOPY_POST_INSTALL_TARGET_HOOKS += PYTHON_FTROBOPY_INSTALL_PY_STUFF

$(eval $(generic-package))

