-include Rules.make

MAKE_JOBS ?= 1

all: u-boot
clean: u-boot_clean

u-boot-spl: u-boot
u-boot-spl_clean: u-boot_clean

u-boot:
	@echo ===================================
	@echo    Building U-boot
	@echo ===================================
	$(MAKE) -j $(MAKE_JOBS) -C ./board-support/u-boot-* CROSS_COMPILE=$(CROSS_COMPILE) $(UBOOT_MACHINE)
	$(MAKE) -j $(MAKE_JOBS) -C ./board-support/u-boot-* CROSS_COMPILE=$(CROSS_COMPILE)
	$(MAKE) -j $(MAKE_JOBS) -C ./board-support/u-boot-* HOSTCC=$(CROSS_COMPILE)gcc HOSTSTRIP=$(CROSS_COMPILE)strip env

u-boot_clean:
	@echo ===================================
	@echo    Cleaining U-boot
	@echo ===================================
	$(MAKE) -C ./board-support/u-boot-* CROSS_COMPILE=$(CROSS_COMPILE) distclean

