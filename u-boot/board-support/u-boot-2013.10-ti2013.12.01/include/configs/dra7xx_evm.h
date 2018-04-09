/*
 * (C) Copyright 2013
 * Texas Instruments Incorporated.
 * Lokesh Vutla	  <lokeshvutla@ti.com>
 *
 * Configuration settings for the TI DRA7XX board.
 * See omap5_common.h for omap5 common settings.
 *
 * SPDX-License-Identifier:	GPL-2.0+
 */

#ifndef __CONFIG_DRA7XX_EVM_H
#define __CONFIG_DRA7XX_EVM_H

#define CONFIG_DRA7XX
#define CONFIG_ENV_IS_NOWHERE

/* MMC ENV related defines */
#if defined(CONFIG_EMMC_BOOT)
#undef CONFIG_ENV_IS_NOWHERE
#define CONFIG_ENV_IS_IN_MMC
#define CONFIG_SYS_MMC_ENV_DEV		1	/* SLOT2: eMMC(1) */
#define CONFIG_ENV_OFFSET		0xE0000
#define CONFIG_ENV_OFFSET_REDUND	(CONFIG_ENV_OFFSET + CONFIG_ENV_SIZE)
#define CONFIG_SYS_REDUNDAND_ENVIRONMENT
#define CONFIG_CMD_SAVEENV
#endif

/* Enhance our eMMC support / experience. */
#define CONFIG_CMD_GPT
#define CONFIG_EFI_PARTITION
#define CONFIG_PARTITION_UUIDS
#define CONFIG_CMD_PART
/* Define the default GPT table for eMMC */
#define PARTS_DEFAULT \
	"uuid_disk=${uuid_gpt_disk};" \
	"name=rootfs,start=2MiB,size=-,uuid=${uuid_gpt_rootfs}"

#define CONSOLEDEV			"ttyO0"
#define CONFIG_CONS_INDEX		1
#define CONFIG_SYS_NS16550_COM1		UART1_BASE
#define CONFIG_BAUDRATE			115200

#define CONFIG_SYS_OMAP_ABE_SYSCK

#include <configs/omap5_common.h>

/* CPSW Ethernet */
#define CONFIG_CMD_NET			/* 'bootp' and 'tftp' */
#define CONFIG_CMD_DHCP
#define CONFIG_BOOTP_DNS		/* Configurable parts of CMD_DHCP */
#define CONFIG_BOOTP_DNS2
#define CONFIG_BOOTP_SEND_HOSTNAME
#define CONFIG_BOOTP_GATEWAY
#define CONFIG_BOOTP_SUBNETMASK
#define CONFIG_NET_RETRY_COUNT		10
#define CONFIG_CMD_PING
#define CONFIG_CMD_MII
#define CONFIG_DRIVER_TI_CPSW		/* Driver for IP block */
#define CONFIG_MII			/* Required in net/eth.c */
#define CONFIG_PHY_GIGE			/* per-board part of CPSW */
#define CONFIG_PHYLIB

/* SPI */
#undef	CONFIG_OMAP3_SPI
#define CONFIG_TI_QSPI
#define CONFIG_SPI_FLASH
#define CONFIG_SPI_FLASH_SPANSION
#define CONFIG_CMD_SF
#define CONFIG_CMD_SPI
#define CONFIG_SPI_FLASH_BAR
#define CONFIG_TI_SPI_MMAP
#define CONFIG_CH_QSPI
#define CONFIG_SF_DEFAULT_SPEED                48000000
#define CONFIG_DEFAULT_SPI_MODE                SPI_MODE_3

/*
 * Default to using SPI for environment, etc.
 * 0x000000 - 0x010000 : QSPI.SPL (64KiB)
 * 0x010000 - 0x020000 : QSPI.SPL.backup1 (64KiB)
 * 0x020000 - 0x030000 : QSPI.SPL.backup2 (64KiB)
 * 0x030000 - 0x040000 : QSPI.SPL.backup3 (64KiB)
 * 0x040000 - 0x140000 : QSPI.u-boot (1MiB)
 * 0x140000 - 0x150000 : QSPI.u-boot-spl-os (64KiB)
 * 0x150000 - 0x160000 : QSPI.u-boot-env (64KiB)
 * 0x160000 - 0x170000 : QSPI.u-boot-env.backup1 (64KiB)
 * 0x170000 - 0x970000 : QSPI.kernel (8MiB)
 * 0x970000 - 0x2000000 : USERLAND
 */
#if defined(CONFIG_QSPI_BOOT)
#define CONFIG_ENV_IS_IN_SPI_FLASH
#define CONFIG_SYS_REDUNDAND_ENVIRONMENT
#define CONFIG_ENV_SPI_MAX_HZ           CONFIG_SF_DEFAULT_SPEED
#undef	CONFIG_SPL_MAX_SIZE
#define CONFIG_SPL_MAX_SIZE             (64 << 10) /* 64 KiB */
#undef CONFIG_ENV_IS_NOWHERE
#define CONFIG_ENV_SECT_SIZE		(64 << 10) /* 64 KB sectors */
#define CONFIG_ENV_OFFSET		0x1d0000
#define CONFIG_ENV_OFFSET_REDUND	0x1e0000

#ifdef MTDIDS_DEFAULT
#undef MTDIDS_DEFAULT
#endif
#define MTDIDS_DEFAULT			"nor0=m25p80-flash.0"

#ifdef MTDPARTS_DEFAULT
#undef MTDPARTS_DEFAULT
#endif
#define MTDPARTS_DEFAULT			"mtdparts=qspi.0:64k(SPL)," \
						"64k(QSPI.SPL.backup1)," \
						"64k(QSPI.SPL.backup2)," \
						"64k(QSPI.SPL.backup3)," \
						"1m(QSPI.u-boot)," \
						"64k(QSPI.u-boot-spl-os)," \
						"64k(QSPI.u-boot-env)," \
						"64k(QSPI.u-boot-env.backup1)," \
						"8m(QSPI.kernel)," \
						"-(QSPI.rootfs)"
#endif

/* SPI SPL */
#define CONFIG_SPL_SPI_SUPPORT
#define CONFIG_SPL_SPI_LOAD
#define CONFIG_SPL_SPI_FLASH_SUPPORT
#define CONFIG_SPL_SPI_BUS             0
#define CONFIG_SPL_SPI_CS              0
#define CONFIG_SYS_SPI_U_BOOT_OFFS     0x40000

/* USB xHCI HOST */
#define CONFIG_CMD_USB
#define CONFIG_USB_HOST
#define CONFIG_USB_XHCI
#define CONFIG_USB_XHCI_OMAP
#define CONFIG_USB_STORAGE
#define CONFIG_SYS_USB_XHCI_MAX_ROOT_PORTS 2

#define CONFIG_OMAP_USB_PHY
#define CONFIG_OMAP_USB2PHY2_HOST

/* SATA */
#define CONFIG_BOARD_LATE_INIT
#define CONFIG_CMD_SCSI
#define CONFIG_LIBATA
#define CONFIG_SCSI_AHCI
#define CONFIG_SCSI_AHCI_PLAT
#define CONFIG_SYS_SCSI_MAX_SCSI_ID	1
#define CONFIG_SYS_SCSI_MAX_LUN		1
#define CONFIG_SYS_SCSI_MAX_DEVICE	(CONFIG_SYS_SCSI_MAX_SCSI_ID * \
						CONFIG_SYS_SCSI_MAX_LUN)

/* NAND support */
#ifdef CONFIG_NAND
/* NAND: device related configs */
#define CONFIG_SYS_NAND_5_ADDR_CYCLE
#define CONFIG_SYS_NAND_PAGE_COUNT		(CONFIG_SYS_NAND_BLOCK_SIZE / \
						 CONFIG_SYS_NAND_PAGE_SIZE)
#define CONFIG_SYS_NAND_PAGE_SIZE		2048
#define CONFIG_SYS_NAND_OOBSIZE			64
#define CONFIG_SYS_NAND_BLOCK_SIZE		(128*1024)
#define CONFIG_SPL_NAND_DEVICE_WIDTH		16
/* NAND: driver related configs */
#define CONFIG_NAND_OMAP_GPMC
#define CONFIG_NAND_OMAP_ELM
#define CONFIG_CMD_NAND
#define CONFIG_SYS_NAND_BASE			0x8000000
#define CONFIG_SYS_MAX_NAND_DEVICE		1
#define CONFIG_SYS_NAND_ONFI_DETECTION
#define CONFIG_SYS_NAND_BAD_BLOCK_POS		NAND_LARGE_BADBLOCK_POS
#define CONFIG_SYS_NAND_ECCPOS		      { 2, 3, 4, 5, 6, 7, 8, 9, \
					       10, 11, 12, 13, 14, 15, 16, 17, \
					       18, 19, 20, 21, 22, 23, 24, 25, \
					       26, 27, 28, 29, 30, 31, 32, 33, \
					       34, 35, 36, 37, 38, 39, 40, 41, \
					       42, 43, 44, 45, 46, 47, 48, 49, \
					       50, 51, 52, 53, 54, 55, 56, 57, }
#define CONFIG_SYS_NAND_ECCSIZE			512
#define CONFIG_SYS_NAND_ECCBYTES		14
#define CONFIG_NAND_OMAP_ECCSCHEME		OMAP_ECC_BCH8_CODE_HW
#if !defined(CONFIG_SPI_BOOT) && !defined(CONFIG_NOR_BOOT) && \
	!defined(CONFIG_EMMC_BOOT)
   #define MTDIDS_DEFAULT			"nand0=nand.0"
  #define MTDPARTS_DEFAULT		      "mtdparts=nand.0:" \
					      "128k(NAND.SPL)," \
					      "128k(NAND.SPL.backup1)," \
					      "128k(NAND.SPL.backup2)," \
					      "128k(NAND.SPL.backup3)," \
					      "256k(NAND.u-boot-spl-os)," \
					      "1m(NAND.u-boot)," \
					      "128k(NAND.u-boot-env)," \
					      "128k(NAND.u-boot-env.backup1)," \
					      "8m(NAND.kernel)," \
					      "-(NAND.rootfs)"
  #undef CONFIG_ENV_IS_NOWHERE
  #define CONFIG_ENV_IS_IN_NAND
  #define CONFIG_ENV_OFFSET			0x001C0000
  #define CONFIG_ENV_OFFSET_REDUND		0x001E0000
  #define CONFIG_SYS_ENV_SECT_SIZE		CONFIG_SYS_NAND_BLOCK_SIZE
#endif
/* NAND: SPL related configs */
#if !defined(CONFIG_SPI_BOOT) && !defined(CONFIG_NOR_BOOT) && \
	!defined(CONFIG_EMMC_BOOT)
  #define CONFIG_SPL_NAND_AM33XX_BCH
  #define CONFIG_SPL_NAND_SUPPORT
  #define CONFIG_SPL_NAND_BASE
  #define CONFIG_SPL_NAND_DRIVERS
  #define CONFIG_SPL_NAND_ECC
  #define CONFIG_SYS_NAND_U_BOOT_START		CONFIG_SYS_TEXT_BASE
  #define CONFIG_SYS_NAND_U_BOOT_OFFS		0x000C0000
 /* NAND: SPL falcon mode related configs */
  #ifdef CONFIG_SPL_OS_BOOT
    #define CONFIG_CMD_SPL_NAND_OFS		0x00080000 /* os parameters */
    #define CONFIG_SYS_NAND_SPL_KERNEL_OFFS	0x00A00000 /* kernel offset */
    #define CONFIG_CMD_SPL_WRITE_SIZE		0x2000
  #endif
#endif
#else
#define NANDARGS ""
#endif /* !CONFIG_NAND */

#define FIND_FDT_FILE \
	"findfdt="\
		"if test $board_name = dra7xx; then " \
			"setenv fdtfile dra7-evm.dtb; fi;" \
		"if test $fdtfile = undefined; then " \
			"echo WARNING: Could not determine device tree to use; fi; \0" \

#ifdef CONFIG_MMC
#define BOOT_TARGETS_MMC "mmc0"
#else
#define BOOT_TARGETS_MMC ""
#endif

#ifdef CONFIG_USB_HOST
#define BOOT_TARGETS_USB "usb"
#else
#define BOOT_TARGETS_USB ""
#endif

#ifdef CONFIG_NAND
#define BOOT_TARGETS_NAND "nand"
#else
#define BOOT_TARGETS_NAND ""
#endif

#endif /* __CONFIG_DRA7XX_EVM_H */
