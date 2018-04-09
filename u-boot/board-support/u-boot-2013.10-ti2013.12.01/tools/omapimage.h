/*
 * (C) Copyright 2010
 * Linaro LTD, www.linaro.org
 * Author John Rigby <john.rigby@linaro.org>
 * Based on TI's signGP.c
 *
 * SPDX-License-Identifier:	GPL-2.0+
 */

#ifndef _OMAPIMAGE_H_
#define _OMAPIMAGE_H_

/* Maximum number of configuration elements for a CH */
#define MAX_CH_DATA	204

struct ch_toc {
	uint32_t section_offset;
	uint32_t section_size;
	uint8_t unused[12];
	uint8_t section_name[12];
};

struct ch_settings {
	uint8_t flags;
	uint8_t reserved[5];
};

struct ch_qspi {
	uint8_t clock;
	uint8_t read_cmd;
	uint8_t read_type;
	uint8_t num_addr_bytes;
	uint8_t num_dummy_bytes;
	uint8_t qe_num_reg;
	uint8_t qe_read_reg_cmd[4];
	uint8_t qe_position;
	uint8_t qe_enable;
	uint8_t qe_write_enable_cmd;
	uint8_t qe_write_reg_cmd;
};

struct ch_hdr {
	uint32_t section_key;
	uint8_t valid;
	uint8_t version;
	uint16_t reserved;
};

struct ch_entry {
	struct ch_hdr hdr;
	uint8_t ch_data[MAX_CH_DATA];
};

struct gp_header {
	uint32_t size;
	uint32_t load_addr;
};

#define KEY_CHSETTINGS	0xC0C0C0C1
#define KEY_CHQSPI	0xC0C0C0C6
#endif /* _OMAPIMAGE_H_ */
