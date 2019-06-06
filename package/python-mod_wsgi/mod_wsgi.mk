################################################################################
#
# python-mod_swgi
#
################################################################################

PYTHON_MOD_WSGI_VERSION = 4.6.5
PYTHON_MOD_WSGI_SOURCE = mod_wsgi-$(PYTHON_MOD_WSGI_VERSION).tar.gz
PYTHON_MOD_WSGI_SITE = $(call github,GrahamDumpleton,mod_wsgi,$(PYTHON_MOD_WSGI_VERSION))
PYTHON_MOD_WSGI_DEPENDENCIES = apache python3
PYTHON_MOD_WSGI_ENV = APXS=$(STAGING_DIR)/usr/bin/apxs
PYTHON_MOD_WSGI_SETUP_TYPE = setuptools

$(eval $(python-package))

