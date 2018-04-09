/*
 * mux.c
 *
 * Copyright (C) 2011 Texas Instruments Incorporated - http://www.ti.com/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation version 2.
 *
 * This program is distributed "as is" WITHOUT ANY WARRANTY of any
 * kind, whether express or implied; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

#include <common.h>
#include <asm/arch/sys_proto.h>
#include <asm/arch/hardware.h>
#include <asm/arch/mux.h>
#include <asm/io.h>
#include <i2c.h>
#include "board.h"

#include "mux_am335x.h"
#include "pinmux_board.h"

static struct module_pin_mux uart0_pin_mux[] = {
	{OFFSET(uart0_rxd), (MODE(0) | PULLUP_EN | RXACTIVE)},	/* UART0_RXD */
	{OFFSET(uart0_txd), (MODE(0) | PULLUDEN)},		/* UART0_TXD */
	{-1},
};

/* LCD_BACKLIGHT_ON kn1 */
static struct module_pin_mux gpio0_7_pin_muxlcd[] = {
	{OFFSET(ecap0_in_pwm0_out), (MODE(7) | PULLUDEN)},      /* GPIO0_7 */
	{-1},
	};



void enable_uart0_pin_mux(void)
{
	configure_module_pin_mux(uart0_pin_mux);
}

void enable_i2c0_pin_mux(void)
{
puts(">>> I2C0 On\n");
MUX_VAL(CONTROL_PADCONF_I2C0_SDA, (IEN | OFF | MODE0 )) /* I2C0_SDA */\
MUX_VAL(CONTROL_PADCONF_I2C0_SCL, (IEN | OFF | MODE0 )) /* I2C0_SCL */\
}

void enable_board_pin_mux(struct am335x_baseboard_id *header)
{
	/* Do board-specific muxes. */
	puts(">>> Setting MUX Start\n");
	MUX_EVM();
	puts(">>> Setting MUX End\n");
	configure_module_pin_mux(gpio0_7_pin_muxlcd);
}
