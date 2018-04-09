/*
 * (C) Copyright 2013
 * Texas Instruments, <www.ti.com>
 *
 * Dan Murphy <dmurphy@ti.com>
 *
 * SPDX-License-Identifier:	GPL-2.0+
 *
 * Derived work from spl_mmc.c
 */

#include <common.h>
#include <spl.h>
#include <asm/u-boot.h>
#include <usb.h>
#include <fat.h>
#include <version.h>
#include <image.h>

DECLARE_GLOBAL_DATA_PTR;

#ifdef CONFIG_USB_STORAGE
static int usb_stor_curr_dev = -1; /* current device */
#endif

#ifdef CONFIG_SPL_FAT_SUPPORT
static int usb_load_image_fat(const char *filename)
{
	int err;
	struct image_header *header;

	header = (struct image_header *)(CONFIG_SYS_TEXT_BASE -
						sizeof(struct image_header));

	err = file_fat_read(filename, header, sizeof(struct image_header));
	if (err <= 0)
		goto end;

	spl_parse_image_header(header);

	err = file_fat_read(filename, (u8 *)spl_image.load_addr, 0);

end:
#ifdef CONFIG_SPL_LIBCOMMON_SUPPORT
	if (err <= 0)
		printf("spl: error reading image %s, err - %d\n",
		       filename, err);
#endif

	return (err <= 0);
}

#ifdef CONFIG_SPL_OS_BOOT
static int usb_load_image_fat_os(struct usb_device *usb_dev)
{
	int err;

	err = file_fat_read(CONFIG_SPL_FAT_LOAD_ARGS_NAME,
			    (void *)CONFIG_SYS_SPL_ARGS_ADDR, 0);
	if (err <= 0) {
#ifdef CONFIG_SPL_LIBCOMMON_SUPPORT
		printf("spl: error reading image %s, err - %d\n",
		       CONFIG_SPL_FAT_LOAD_ARGS_NAME, err);
#endif
		return -1;
	}

	return usb_load_image_fat(CONFIG_SPL_FAT_LOAD_KERNEL_NAME);
}
#endif
#endif
void spl_usb_load_image(void)
{
	struct usb_device *usb_dev;
	int err;
	block_dev_desc_t *stor_dev;

	usb_stop();
	err = usb_init();
	if (err) {
#ifdef CONFIG_SPL_LIBCOMMON_SUPPORT
		printf("spl: usb init failed: err - %d\n", err);
#endif
		hang();
	} else {
#ifdef CONFIG_USB_STORAGE
		/* try to recognize storage devices immediately */
		usb_stor_curr_dev = usb_stor_scan(1);
		stor_dev = usb_stor_get_dev(usb_stor_curr_dev);
#endif
	}

	debug("boot mode - FAT\n");

	err = fat_register_device(stor_dev,
			CONFIG_SYS_USB_FAT_BOOT_PARTITION);
	if (err) {
#ifdef CONFIG_SPL_LIBCOMMON_SUPPORT
		printf("spl: fat register err - %d\n", err);
#endif
		hang();
	}

#ifdef CONFIG_SPL_OS_BOOT
	if (spl_start_uboot() || usb_load_image_fat_os(usb_dev))
#endif
	err = usb_load_image_fat(CONFIG_SPL_FAT_LOAD_PAYLOAD_NAME);
	if (err) {
		puts("Error loading USB device\n");
		hang();
	}
}
