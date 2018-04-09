#platform
PLATFORM=am335x-knobloch_txt

#Architecture
ARCH=armv7-a

#u-boot machine
UBOOT_MACHINE=TXT_knobloch_config

#Default CC value to be used when cross compiling.  This is so that the
#GNU Make default of "cc" is not used to point to the host compiler
export CC=$(CROSS_COMPILE)gcc

CFLAGS= -march=armv7-a -marm -mthumb-interwork -mfloat-abi=hard -mfpu=neon -mtune=cortex-a8

MAKE_JOBS=2
