################################################################################
#
# python-ufw
#
################################################################################

PYTHON_UFW_VERSION = 0.35
PYTHON_UFW_SOURCE = ufw-$(PYTHON_UFW_VERSION).tar.gz
PYTHON_UFW_SITE = https://launchpad.net/ufw/0.35/0.35/+download
#PYTHON_UFW_LICENSE = MIT
#PYTHON_UFW_LICENSE_FILES = LICENSE
PYTHON_UFW_SETUP_TYPE = distutils

define PYTHON_UFW_BUILD_CMDS
	cd $(PYTHON_UFW_BUILDDIR); \
	$(PYTHON_UFW_PYTHON_INTERPRETER) setup.py build -f

	cd $(PYTHON_UFW_BUILDDIR); \
	rm -rf ./SETUP;\
	mkdir ./SETUP; \
	$(PYTHON_UFW_PYTHON_INTERPRETER) setup.py install -f --home=$(PYTHON_UFW_BUILDDIR)/SETUP

	sed -i -e 1c"#! /usr/bin/env /usr/bin/python " $(PYTHON_UFW_BUILDDIR)/SETUP/usr/sbin/ufw
endef

define PYTHON_UFW_INSTALL_TARGET_CMDS
	echo "================>"; \
	cd $(PYTHON_UFW_BUILDDIR)/SETUP; \
	cp -av * $(TARGET_DIR)
endef

$(eval $(python-package))
