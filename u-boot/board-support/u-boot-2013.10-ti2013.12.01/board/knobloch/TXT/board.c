/*
 * board.c
 *
 * Board functions for TI AM335X based boards
 *
 * Copyright (C) 2011, Texas Instruments, Incorporated - http://www.ti.com/
 *
 * SPDX-License-Identifier:	GPL-2.0+
 */

#include <common.h>
#include <errno.h>
#include <spl.h>
#include <asm/arch/cpu.h>
#include <asm/arch/hardware.h>
#include <asm/arch/omap.h>
#include <asm/arch/ddr_defs.h>
#include <asm/arch/clock.h>
#include <asm/arch/gpio.h>
#include <asm/arch/mmc_host_def.h>
#include <asm/arch/sys_proto.h>
#include <asm/arch/mem.h>
#include <asm/io.h>
#include <asm/emif.h>
#include <asm/gpio.h>
#include <i2c.h>
#include <miiphy.h>
#include <cpsw.h>
#include <power/tps65910.h>
#include "board.h"

DECLARE_GLOBAL_DATA_PTR;

/* GPIO 3:21 that controls power to DDR on TXT_knobloch */
#define GPIO_DDR_VTT_EN		117

#define TXTMICRON 1
//#define TXTSAMSUNG 1

#ifdef TXTMICRON
#define DDR3_FREQ 303
//#define DDR3_FREQ 400
#endif

#ifdef TXTSAMSUNG
#define DDR3_FREQ 400
#endif

/* GPIO 3:18 that controls power to USB1_DRVBUS on TXT_knobloch */
#define GPIO_USB1_DRVBUS_EN		109

static struct ctrl_dev *cdev = (struct ctrl_dev *)CTRL_DEVICE_BASE;

/*
 * Read header information from EEPROM into global structure.
 */
static int read_eeprom(struct am335x_baseboard_id *header)
{
	/* Check if baseboard eeprom is available */
	if (i2c_probe(CONFIG_SYS_I2C_EEPROM_ADDR)) {
		puts("Could not probe the EEPROM; something fundamentally "
			"wrong on the I2C bus.\n");
		return -ENODEV;
	}

	/* read the eeprom using i2c */
	if (i2c_read(CONFIG_SYS_I2C_EEPROM_ADDR, 0, CONFIG_SYS_I2C_EEPROM_ADDR_LEN, (uchar *)header,
		     sizeof(struct am335x_baseboard_id))) {
		puts("Could not read the EEPROM; something fundamentally"
			" wrong on the I2C bus.\n");
		return -EIO;
	}

	if (header->magic != 0xEE3355AA) {
		/*
		 * read the eeprom using i2c again,
		 * but use only a 1 byte address
		 */
		if (i2c_read(CONFIG_SYS_I2C_EEPROM_ADDR, 0, CONFIG_SYS_I2C_EEPROM_ADDR_LEN, (uchar *)header,
			     sizeof(struct am335x_baseboard_id))) {
			puts("Could not read the EEPROM; something "
				"fundamentally wrong on the I2C bus.>>\n");
			return -EIO;
		}

		if (header->magic != 0xEE3355AA) {
			printf("Incorrect magic number (0x%x) in EEPROM\n",
					header->magic);
			return -EINVAL;
		}
	}

	return 0;
}

#if defined(CONFIG_SPL_BUILD) || defined(CONFIG_NOR_BOOT)

#ifdef TXTMICRON

static const struct ddr_data ddr3_data = {
	.datardsratio0 = MT41J128MJT125_RD_DQS,
	.datawdsratio0 = MT41J128MJT125_WR_DQS,
	.datafwsratio0 = MT41J128MJT125_PHY_FIFO_WE,
	.datawrsratio0 = MT41J128MJT125_PHY_WR_DATA,
};

static const struct cmd_control ddr3_cmd_ctrl_data = {
	.cmd0csratio = MT41J128MJT125_RATIO,
	.cmd0iclkout = MT41J128MJT125_INVERT_CLKOUT,

	.cmd1csratio = MT41J128MJT125_RATIO,
	.cmd1iclkout = MT41J128MJT125_INVERT_CLKOUT,

	.cmd2csratio = MT41J128MJT125_RATIO,
	.cmd2iclkout = MT41J128MJT125_INVERT_CLKOUT,
};
static struct emif_regs ddr3_emif_reg_data = {
	.sdram_config = MT41J128MJT125_EMIF_SDCFG,
	.ref_ctrl = MT41J128MJT125_EMIF_SDREF,
	.sdram_tim1 = MT41J128MJT125_EMIF_TIM1,
	.sdram_tim2 = MT41J128MJT125_EMIF_TIM2,
	.sdram_tim3 = MT41J128MJT125_EMIF_TIM3,
	.zq_config = MT41J128MJT125_ZQ_CFG,
	.emif_ddr_phy_ctlr_1 = MT41J128MJT125_EMIF_READ_LATENCY |
				PHY_EN_DYN_PWRDN,
};


/*
static const struct ddr_data ddr3_data = {
	.datardsratio0 = 0x40,  //RD_DQS
	.datawdsratio0 = 0x7c,  //WR_DQS
	.datafwsratio0 = 0x100, //PHY_FIFO_WE
	.datawrsratio0 = 0x7c,  //PHY_WR_DATA
};

static const struct cmd_control ddr3_cmd_ctrl_data = {
	.cmd0csratio = 0x40, // PHY_RD DQS_SLAVE_RATIO
	.cmd0iclkout = 0x1,  // INVERT_CLKOUT

	.cmd1csratio = 0x40, // PHY_RD DQS_SLAVE_RATIO
	.cmd1iclkout = 0x1,  //INVERT_CLKOUT

	.cmd2csratio = 0x40, // PHY_RD DQS_SLAVE_RATIO
	.cmd2iclkout = 0x1,  //INVERT_CLKOUT,
};

static struct emif_regs ddr3_emif_reg_data = {
	.sdram_config = MT41J128MJT125_EMIF_SDCFG,
	.ref_ctrl = MT41J128MJT125_EMIF_SDREF,
	.sdram_tim1 = 0x0AAAE4DB,
	.sdram_tim2 = 0x26437FDA,
	.sdram_tim3 = 0x501F83FF,
	.zq_config = MT41J128MJT125_ZQ_CFG,
	.emif_ddr_phy_ctlr_1 = MT41J128MJT125_EMIF_READ_LATENCY |
				PHY_EN_DYN_PWRDN,
};
*/
#endif

#ifdef TXTSAMSUNG
static const struct ddr_data ddr3_data = {
        .datardsratio0 = K4B2G1646EBIH9_RD_DQS,
        .datawdsratio0 = K4B2G1646EBIH9_WR_DQS,
        .datafwsratio0 = K4B2G1646EBIH9_PHY_FIFO_WE,
        .datawrsratio0 = K4B2G1646EBIH9_PHY_WR_DATA,
};

static const struct cmd_control ddr3_cmd_ctrl_data = {
        .cmd0csratio = K4B2G1646EBIH9_RATIO,
        .cmd0iclkout = K4B2G1646EBIH9_INVERT_CLKOUT,

        .cmd1csratio = K4B2G1646EBIH9_RATIO,
        .cmd1iclkout = K4B2G1646EBIH9_INVERT_CLKOUT,

        .cmd2csratio = K4B2G1646EBIH9_RATIO,
        .cmd2iclkout = K4B2G1646EBIH9_INVERT_CLKOUT,
};

static struct emif_regs ddr3_emif_reg_data = {
        .sdram_config = K4B2G1646EBIH9_EMIF_SDCFG,
        .ref_ctrl = K4B2G1646EBIH9_EMIF_SDREF,
        .sdram_tim1 = K4B2G1646EBIH9_EMIF_TIM1,
        .sdram_tim2 = K4B2G1646EBIH9_EMIF_TIM2,
        .sdram_tim3 = K4B2G1646EBIH9_EMIF_TIM3,
        .zq_config = K4B2G1646EBIH9_ZQ_CFG,
        .emif_ddr_phy_ctlr_1 = K4B2G1646EBIH9_EMIF_READ_LATENCY,
};

#endif



#ifdef CONFIG_SPL_OS_BOOT
int spl_start_uboot(void)
{
	/* break into full u-boot on 'c' */
	return (serial_tstc() && serial_getc() == 'c');
}
#endif

#define OSC	(V_OSCK/1000000)
const struct dpll_params dpll_ddr_txt = {
	DDR3_FREQ, OSC-1, 1, -1, -1, -1, -1};

void am33xx_spl_board_init(void)
{
	struct am335x_baseboard_id header;
	int mpu_vdd;
	int sil_rev;
	extern void LcdInit();

	puts(">>>TEST LCD-1\n");
	LcdInit();

	if (read_eeprom(&header) < 0)
		puts("Could not get board ID.\n");

	/* Get the frequency */
	dpll_mpu_opp100.m = am335x_get_efuse_mpu_max_freq(cdev);

	/*
	 * The GP EVM, IDK and EVM SK use a TPS65910 PMIC.  For all
	 * MPU frequencies we support we use a CORE voltage of
	 * 1.1375V.  For MPU voltage we need to switch based on
	 * the frequency we are running at.
	 */
	if (i2c_probe(TPS65910_CTRL_I2C_ADDR))
		return;

	/*
	 * Depending on MPU clock and PG we will need a different
	 * VDD to drive at that speed.
	 */
	sil_rev = readl(&cdev->deviceid) >> 28;
	mpu_vdd = am335x_get_tps65910_mpu_vdd(sil_rev,
					      dpll_mpu_opp100.m);

	/* Tell the TPS65910 to use i2c */
	tps65910_set_i2c_control();

	/* First update MPU voltage. */		 
	if (tps65910_voltage_update(MPU, mpu_vdd))
		return;

	/* Second, update the CORE voltage. */
	if (tps65910_voltage_update(CORE, TPS65910_OP_REG_SEL_1_1_3))
		return;

	/* Set CORE Frequencies to OPP100 */
	do_setup_dpll(&dpll_core_regs, &dpll_core_opp100);
	printf("---> MPU %d VDD %d \n", dpll_mpu_opp100.m, mpu_vdd);
	
		 
       /* Set MPU Frequency to what we detected now that voltages are set */
       do_setup_dpll(&dpll_mpu_regs, &dpll_mpu_opp100);		 
}

const struct dpll_params *get_dpll_ddr_params(void)
{
	struct am335x_baseboard_id header;

	enable_i2c0_pin_mux();
	i2c_init(CONFIG_SYS_I2C_SPEED, CONFIG_SYS_I2C_SLAVE);
	if (read_eeprom(&header) < 0)
		puts("Could not get board ID.\n");

	return &dpll_ddr_txt;
}

void set_uart_mux_conf(void)
{
#ifdef CONFIG_SERIAL1
	enable_uart0_pin_mux();
#endif /* CONFIG_SERIAL1 */
#ifdef CONFIG_SERIAL2
	enable_uart1_pin_mux();
#endif /* CONFIG_SERIAL2 */
#ifdef CONFIG_SERIAL3
	enable_uart2_pin_mux();
#endif /* CONFIG_SERIAL3 */
#ifdef CONFIG_SERIAL4
	enable_uart3_pin_mux();
#endif /* CONFIG_SERIAL4 */
#ifdef CONFIG_SERIAL5
	enable_uart4_pin_mux();
#endif /* CONFIG_SERIAL5 */
#ifdef CONFIG_SERIAL6
	enable_uart5_pin_mux();
#endif /* CONFIG_SERIAL6 */
}

void set_mux_conf_regs(void)
{
	__maybe_unused struct am335x_baseboard_id header;

	if (read_eeprom(&header) < 0)
		puts("Could not get board ID.\n");

	enable_board_pin_mux(&header);
}

void sdram_init(void)
{
	__maybe_unused struct am335x_baseboard_id header;

	if (read_eeprom(&header) < 0)
		puts("Could not get board ID.\n");
			
	/*
	 * Turn on LCD Backlight for Test purposes
	 */
	gpio_request(7, "lcd_BACKL");
	gpio_direction_output(7, 1);

	/*
	 * Board TXT_knobloch use gpio3_21 to enable DDR3.
	 * This is safe enough to do on older revs.
	 */
	gpio_request(GPIO_DDR_VTT_EN, "ddr_vtt_en");
	gpio_direction_output(GPIO_DDR_VTT_EN, 1);

	gpio_request(GPIO_USB1_DRVBUS_EN, "usb1_drvbus_en");
	gpio_direction_output(GPIO_USB1_DRVBUS_EN, 1);

#ifdef TXTMICRON
	config_ddr(DDR3_FREQ, MT41J128MJT125_IOCTRL_VALUE, &ddr3_data,
		   &ddr3_cmd_ctrl_data, &ddr3_emif_reg_data, 0);
#endif

#ifdef TXTSAMSUNG
	config_ddr(DDR3_FREQ, K4B2G1646EBIH9_IOCTRL_VALUE, &ddr3_data,
		   &ddr3_cmd_ctrl_data, &ddr3_emif_reg_data, 0);
#endif

}
#endif

/*
 * Basic board specific setup.  Pinmux has been handled already.
 */
int board_init(void)
{
	gd->bd->bi_boot_params = CONFIG_SYS_SDRAM_BASE + 0x100;
#if defined(CONFIG_NOR) || defined(CONFIG_NAND)
	gpmc_init();
#endif
	return 0;
}

#ifdef CONFIG_BOARD_LATE_INIT
int board_late_init(void)
{
#ifdef CONFIG_ENV_VARS_UBOOT_RUNTIME_CONFIG
	/* Get the frequency */
	int dpll_mpu_opp100_m = am335x_get_efuse_mpu_max_freq(cdev);


	char safe_string[HDR_NAME_LEN + 1];
	struct am335x_baseboard_id header;

	if (read_eeprom(&header) < 0)
		puts("Could not get board ID.\n");

	/* Now set variables based on the header. */
	strncpy(safe_string, (char *)header.name, sizeof(header.name));
	safe_string[sizeof(header.name)] = 0;
	setenv("board_name", safe_string);

//	setenv("bootdelay", "3");


	strncpy(safe_string, (char *)header.version, sizeof(header.version));
	safe_string[sizeof(header.version)] = 0;
	setenv("board_rev", safe_string);
	
	switch(dpll_mpu_opp100_m) {
	
	case 1000:
		 setenv("opp", "fdt set /cpus/cpu@0 operating-points <0x000f4240 0x00149d58 0x000c3500 0x00139b88 0x000afc80 0x00139b88 0x000927c0 0x0012b128 0x0007a120 0x00112a88 0x000493e0 0x00112a88 0x00043238 0x00112a88>");
		 break;
	
	case 800:
		 setenv("opp", "fdt set /cpus/cpu@0 operating-points <0x000c3500 0x00139b88 0x000afc80 0x00139b88 0x000927c0 0x0012b128 0x0007a120 0x00112a88 0x000493e0 0x00112a88 0x00043238 0x00112a88>");
		 break;
	
	case 720:
		 setenv("opp", "fdt set /cpus/cpu@0 operating-points <0x000afc80 0x00139b88 0x000927c0 0x0012b128 0x0007a120 0x00112a88 0x000493e0 0x00112a88 0x00043238 0x00112a88>");
		 break;
	
	case 600:
		setenv("opp", "fdt set /cpus/cpu@0 operating-points <0x000927c0 0x0012b128 0x0007a120 0x00112a88 0x000493e0 0x00112a88 0x00043238 0x00112a88>");
		break;

	case 500:
		setenv("opp", "fdt set /cpus/cpu@0 operating-points <0x0007a120 0x00112a88 0x000493e0 0x00112a88 0x00043238 0x00112a88>");
		break;

	case 300:
		setenv("opp", "fdt set /cpus/cpu@0 operating-points <0x000493e0 0x00112a88 0x00043238 0x00112a88>");
		break;

	case 275:
		setenv("opp", "fdt set /cpus/cpu@0 operating-points <0x00043238 0x00112a88>");
		break;

	}
	
	
	
	
	
#endif

	return 0;
}
#endif

#if (defined(CONFIG_DRIVER_TI_CPSW) && !defined(CONFIG_SPL_BUILD)) || \
	(defined(CONFIG_SPL_ETH_SUPPORT) && defined(CONFIG_SPL_BUILD))
static void cpsw_control(int enabled)
{
	/* VTP can be added here */

	return;
}

static struct cpsw_slave_data cpsw_slaves[] = {
	{
		.slave_reg_ofs	= 0x208,
		.sliver_reg_ofs	= 0xd80,
		.phy_addr	= 0,
	},
	{
		.slave_reg_ofs	= 0x308,
		.sliver_reg_ofs	= 0xdc0,
		.phy_addr	= 1,
	},
};

static struct cpsw_platform_data cpsw_data = {
	.mdio_base		= CPSW_MDIO_BASE,
	.cpsw_base		= CPSW_BASE,
	.mdio_div		= 0xff,
	.channels		= 8,
	.cpdma_reg_ofs		= 0x800,
	.slaves			= 1,
	.slave_data		= cpsw_slaves,
	.ale_reg_ofs		= 0xd00,
	.ale_entries		= 1024,
	.host_port_reg_ofs	= 0x108,
	.hw_stats_reg_ofs	= 0x900,
	.bd_ram_ofs		= 0x2000,
	.mac_control		= (1 << 5),
	.control		= cpsw_control,
	.host_port_num		= 0,
	.version		= CPSW_CTRL_VERSION_2,
};
#endif

#if defined(CONFIG_DRIVER_TI_CPSW) || \
	(defined(CONFIG_USB_ETHER) && defined(CONFIG_MUSB_GADGET))
int board_eth_init(bd_t *bis)
{
	int rv, n = 0;
	uint8_t mac_addr[6];
	uint32_t mac_hi, mac_lo;
	__maybe_unused struct am335x_baseboard_id header;

	/* try reading mac address from efuse */
	mac_lo = readl(&cdev->macid0l);
	mac_hi = readl(&cdev->macid0h);
	mac_addr[0] = mac_hi & 0xFF;
	mac_addr[1] = (mac_hi & 0xFF00) >> 8;	mac_addr[2] = (mac_hi & 0xFF0000) >> 16;
	mac_addr[3] = (mac_hi & 0xFF000000) >> 24;
	mac_addr[4] = mac_lo & 0xFF;
	mac_addr[5] = (mac_lo & 0xFF00) >> 8;

#if (defined(CONFIG_DRIVER_TI_CPSW) && !defined(CONFIG_SPL_BUILD)) || \
	(defined(CONFIG_SPL_ETH_SUPPORT) && defined(CONFIG_SPL_BUILD))
	if (!getenv("ethaddr")) {
		printf("<ethaddr> not set. Validating first E-fuse MAC\n");

		if (is_valid_ether_addr(mac_addr))
			eth_setenv_enetaddr("ethaddr", mac_addr);
	}

#ifdef CONFIG_DRIVER_TI_CPSW

	mac_lo = readl(&cdev->macid1l);
	mac_hi = readl(&cdev->macid1h);
	mac_addr[0] = mac_hi & 0xFF;
	mac_addr[1] = (mac_hi & 0xFF00) >> 8;
	mac_addr[2] = (mac_hi & 0xFF0000) >> 16;
	mac_addr[3] = (mac_hi & 0xFF000000) >> 24;
	mac_addr[4] = mac_lo & 0xFF;
	mac_addr[5] = (mac_lo & 0xFF00) >> 8;

	if (!getenv("eth1addr")) {
		if (is_valid_ether_addr(mac_addr))
			eth_setenv_enetaddr("eth1addr", mac_addr);
	}

	if (read_eeprom(&header) < 0)
		puts("Could not get board ID.\n");

	if (board_is_bone(&header) || board_is_bone_lt(&header) ||
	    board_is_idk(&header)) {
		writel(MII_MODE_ENABLE, &cdev->miisel);
		cpsw_slaves[0].phy_if = cpsw_slaves[1].phy_if =
				PHY_INTERFACE_MODE_MII;
	} else {
		writel((RGMII_MODE_ENABLE | RGMII_INT_DELAY), &cdev->miisel);
		cpsw_slaves[0].phy_if = cpsw_slaves[1].phy_if =
				PHY_INTERFACE_MODE_RGMII;
	}

	rv = cpsw_register(&cpsw_data);
	if (rv < 0)
		printf("Error %d registering CPSW switch\n", rv);
	else
		n += rv;
#endif

	/*
	 *
	 * CPSW RGMII Internal Delay Mode is not supported in all PVT
	 * operating points.  So we must set the TX clock delay feature
	 * in the AR8051 PHY.  Since we only support a single ethernet
	 * device in U-Boot, we only do this for the first instance.
	 */
#define AR8051_PHY_DEBUG_ADDR_REG	0x1d
#define AR8051_PHY_DEBUG_DATA_REG	0x1e
#define AR8051_DEBUG_RGMII_CLK_DLY_REG	0x5
#define AR8051_RGMII_TX_CLK_DLY		0x100

	if (board_is_evm_sk(&header) || board_is_gp_evm(&header)) {
		const char *devname;
		devname = miiphy_get_current_dev();

		miiphy_write(devname, 0x0, AR8051_PHY_DEBUG_ADDR_REG,
				AR8051_DEBUG_RGMII_CLK_DLY_REG);
		miiphy_write(devname, 0x0, AR8051_PHY_DEBUG_DATA_REG,
				AR8051_RGMII_TX_CLK_DLY);
	}
#endif
#if defined(CONFIG_USB_ETHER) && \
	(!defined(CONFIG_SPL_BUILD) || defined(CONFIG_SPL_USBETH_SUPPORT))
	if (is_valid_ether_addr(mac_addr))
		eth_setenv_enetaddr("usbnet_devaddr", mac_addr);

	rv = usb_eth_initialize(bis);
	if (rv < 0)
		printf("Error %d registering USB_ETHER\n", rv);
	else
		n += rv;
#endif
	return n;
}
#endif
