################################################################################
#
# python-speedtest-cli
#
################################################################################

PYTHON_SPEEDTEST_CLI_VERSION = v2.1.3
PYTHON_SPEEDTEST_CLI_SITE_METHOD = git
PYTHON_SPEEDTEST_CLI_SITE = https://github.com/sivel/speedtest-cli.git
PYTHON_SPEEDTEST_CLI_LICENSE = APACHE2
PYTHON_SPEEDTEST_CLI_LICENSE_FILES = LICENSE
PYTHON_SPEEDTEST_CLI_SETUP_TYPE = setuptools

$(eval $(python-package))

