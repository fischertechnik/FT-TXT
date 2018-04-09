/*
 * cpu.h
 *
 * AM33xx specific header file
 *
 * Copyright (C) 2011, Texas Instruments, Incorporated - http://www.ti.com/
 *
 * SPDX-License-Identifier:	GPL-2.0+
 */

#ifndef _AM335X_CPU_H
#define _AM335X_CPU_H

#if !(defined(__KERNEL_STRICT_NAMES) || defined(__ASSEMBLY__))
#include <asm/types.h>
#endif /* !(__KERNEL_STRICT_NAMES || __ASSEMBLY__) */

#include <asm/arch/hardware.h>




//
// Encapsulating peripheral functional clocks
// pll registers
//
struct cm_per_pll               // Adress 0x44E0_0000
{ 
	u32 l4ls_clkstctrl;         // offset 0x00
	u32 l3s_clkstctrl;          // offset 0x04
	u32 res_offset_0x08;        // offset 0x08
	u32 l3_clkstctrl;	        // offset 0x0c
	u32 res_offset_0x10;        // offset 0x10
	u32 cpgmac0_clkctrl;        // offset 0x14 
	u32 lcdc_clkctrl;           // offset 0x18 
	u32 usb0_clkctrl;           // offset 0x1C 
	u32 res_offset_0x20;        // offset 0x20
	u32 tptc0_clkctrl;          // offset 0x24 
	u32 emif_clkctrl;           // offset 0x28 
	u32 ocmcram_clkctrl;        // offset 0x2c 
	u32 gpmc_clkctrl;           // offset 0x30 
	u32 mcasp0_clkctrl;         // offset 0x34 
	u32 uart5_clkctrl;          // offset 0x38 
	u32 mmc0_clkctrl;           // offset 0x3C 
	u32 elm_clkctrl;            // offset 0x40 
	u32 i2c2_clkctrl;           // offset 0x44 
	u32 i2c1_clkctrl;           // offset 0x48 
	u32 spi0_clkctrl;           // offset 0x4C 
	u32 spi1_clkctrl;           // offset 0x50 
	u32 res_offset_0x54;        // offset 0x54
	u32 res_offset_0x58;        // offset 0x58
	u32 res_offset_0x5c;        // offset 0x5c
	u32 l4ls_clkctrl;           // offset 0x60 
	u32 res_offset_0x64;        // offset 0x64
	u32 mcasp1_clkctrl;         // offset 0x68 
	u32 uart1_clkctrl;          // offset 0x6C 
	u32 uart2_clkctrl;          // offset 0x70 
	u32 uart3_clkctrl;          // offset 0x74 
	u32 uart4_clkctrl;          // offset 0x78 
	u32 timer7_clkctrl;         // offset 0x7C 
	u32 timer2_clkctrl;         // offset 0x80 
	u32 timer3_clkctrl;         // offset 0x84 
	u32 timer4_clkctrl;         // offset 0x88 
	u32 res_offset_0x8c;        // offset 0x8c
	u32 res_offset_0x90;        // offset 0x90
	u32 res_offset_0x94;        // offset 0x94
	u32 res_offset_0x98;        // offset 0x98
	u32 res_offset_0x9c;        // offset 0x9c
	u32 res_offset_0xA0;        // offset 0xA0
	u32 res_offset_0xA4;        // offset 0xA4
	u32 res_offset_0xA8;        // offset 0xA8
	u32 gpio1_clkctrl;          // offset 0xAC 
	u32 gpio2_clkctrl;          // offset 0xB0 
	u32 gpio3_clkctrl;          // offset 0xB4 
	u32 res_offset_0xb8;        // offset 0xB8
	u32 tpcc_clkctrl;           // offset 0xBC 
	u32 dcan0_clkctrl;          // offset 0xC0 
	u32 dcan1_clkctrl;          // offset 0xC4 
	u32 res_offset_0xc8;        // offset 0xC8
	u32 epwmss1_clkctrl;        // offset 0xCC 
	u32 res_offset_0xd0;        // offset 0xD0
	u32 epwmss0_clkctrl;        // offset 0xD4 
	u32 epwmss2_clkctrl;        // offset 0xD8 
	u32 l3_instr_clkctrl;       // offset 0xDC 
	u32 l3_clkctrl;             // Offset 0xE0 
	u32 ieee5000_clkctrl;       // Offset 0xE4
	u32 pru_icss_clkctrl;       // Offset 0xE8
	u32 timer5_clkctrl;         // offset 0xEC
	u32 timer6_clkctrl;         // offset 0xF0
	u32 mmc1_clkctrl;           // offset 0xF4 
	u32 mmc2_clkctrl;           // offset 0xF8 
	u32 tptc1_clkctrl;          // offset 0xFC
	u32 tptc2_clkctrl;          // offset 0x100
	u32 res_offset_0x104;       // offset 0x104
	u32 res_offset_0x108;       // offset 0x108
	u32 spinlock_clkctrl;       // offset 0x10c
	u32 mailbox0_clkctrl;       // offset 0x110
	u32 res_offset_0x114;       // offset 0x114
	u32 res_offset_0x118;       // offset 0x118
	u32 l4hs_clkstctrl;         // offset 0x11C 
	u32 l4hs_clkctrl;           // offset 0x120 
	u32 res_offset_0x124;       // offset 0x124
	u32 res_offset_0x128;       // offset 0x128
	u32 ocpwp_l3_clkstctrl;     // offset 0x12C
	u32 ocpwp_l3_clkctrl;       // offset 0x130
	u32 res_offset_0x134;       // offset 0x134
	u32 res_offset_0x138;       // offset 0x138
	u32 res_offset_0x13c;       // offset 0x13C
	u32 pru_icss_clkstctrl;     // offset 0x140
	u32 cpsw_clkstctrl;         // offset 0x144
	u32 lcdc_clkstctrl;         // offset 0x148
	u32 clk32div32k_clkctrl;    // offset 0x14c
	u32 clk_24mhz_clkstct;      // offset 0x150
};




//  Encapsulating core pll registers
struct cm_wkup_pll                  // Adress 0x44E0_0400
{ 
	u32 wkup_clkstctrl;             // offset 0x00 
	u32 wkup_ctrl_clkctrl;          // offset 0x04 
	u32 wkup_gpio0_clkctrl;         // offset 0x08 
	u32 wkup_l4wkup_clkctrl;        // offset 0x0C 
	u32 wkup_timer0_clkctrl;        // offset 0x10 
	u32 wkup_debugss_clkctrl;       // offset 0x14
	u32 l3_aon_clkstctrl;           // offset 0x18
	u32 autoidle_dpll_mpu;          // offset 0x1C
	u32 idlest_dpll_mpu;            // offset 0x20 
	u32 ssc_deltamstep_dpll_mpu;    // offset 0x24
	u32 ssc_modfreqdiv_dpll_mpu;    // offset 0x28
	u32 clksel_dpll_mpu;            // offset 0x2C
	u32 autoidle_dpll_ddr;          // offset 0x30
	u32 idlest_dpll_ddr;            // offset 0x34
	u32 ssc_deltamstep_dpll_ddr;    // offset 0x38
	u32 ssc_modfreqdiv_dpll_ddr;    // offset 0x3C
	u32 clksel_dpll_ddr;            // offset 0x40
	u32 autoidle_dpll_disp;         // offset 0x44
	u32 idlest_dpll_disp;           // offset 0x48
	u32 ssc_deltamstep_dpll_disp;   // offset 0x4C
	u32 ssc_modfreqdiv_dpll_disp;   // offset 0x50
	u32 clksel_dpll_disp;           // offset 0x54
	u32 autoidle_dpll_core;         // offset 0x58
	u32 idlest_dpll_core;           // offset 0x5C
	u32 ssc_deltamstep_dpll_core;   // offset 0x60
	u32 ssc_modfreqdiv_dpll_core;   // offset 0x64
	u32 clksel_dpll_core;           // offset 0x68
	u32 autoidle_dpll_per;          // offset 0x6C
	u32 idlest_dpll_per;            // offset 0x70
	u32 ssc_deltamstep_dpll_per;    // offset 0x74
	u32 ssc_modfreqdiv_dpll_per;    // offset 0x78
	u32 clkdcoldo_dpll_per;         // offset 0x7C
	u32 div_m4_dpll_core;           // offset 0x80 
	u32 div_m5_dpll_core;           // offset 0x84 
	u32 clkmod_dpll_mpu;            // offset 0x88 
	u32 clkmod_dpll_per;            // offset 0x8c 
	u32 clkmod_dpll_core;           // offset 0x90 
	u32 clkmod_dpll_ddr;            // offset 0x94 
	u32 clkmod_dpll_disp;           // offset 0x98 
	u32 clksel_dpll_per;            // offset 0x9c 
	u32 div_m2_dpll_ddr;            // offset 0xA0 
	u32 div_m2_dpll_disp;           // offset 0xA4 
	u32 div_m2_dpll_mpu;            // offset 0xA8 
	u32 div_m2_dpll_per;            // offset 0xAC 
    u32 wkup_wkup_m3_clkctrl;       // offset 0xB0
    u32 wkup_uart0_clkctrl;         // offset 0xB4
    u32 wkup_i2c0_clkctrl;          // offset 0xB8
	u32 wkup_adc_tsc_clkctrl;       // offset 0xBC
    u32 wkup_smartreflex0_clkctrl;  // offset 0xC0
    u32 wkup_timer1_clkctrl;        // offset 0xC4
    u32 wkup_smartreflex1_clkctrl;  // offset 0xC8
    u32 l4_wkup_aon_clkstctrl;      // offset 0xCC
	u32 res_offset_0xd0;            // offset 0xD0
    u32 wkup_wdt1_clkctrl;          // offset 0xD0
	u32 div_m6_dpll_core;           // offset 0xD8 
};



//  Encapsulating cm dpll registers
struct cm_dpll                      // Adress 0x44E0_0500
{ 
	u32 res_offset_0x00;            // offset 0x00
	u32 clksel_timer7_clk;          // offset 0x04
	u32 clksel_timer2_clk;          // offset 0x08
	u32 clksel_timer3_clk;          // offset 0x0C
	u32 clksel_timer4_clk;          // offset 0x10
	u32 cm_mac_clksel;              // offset 0x14
	u32 clksel_timer5_clk;          // offset 0x18
	u32 clksel_timer6_clk;          // offset 0x1C
	u32 cm_cpts_rft_clksel;         // offset 0x20
	u32 res_offset_0x24;            // offset 0x24
	u32 clksel_timer1ms_clk;        // offset 0x28
	u32 clksel_gfx_fclk;            // offset 0x2C
	u32 clksel_pru_icss_ocp_clk;    // offset 0x30
	u32 clksel_lcdc_pixel_clk;      // offset 0x34
	u32 clksel_wdt1_clk;            // offset 0x38
	u32 clksel_gpio0_dbclk;         // offset 0x3C
};    
    
    


struct LCD_REGISTERS                // Adress 0x4830_E0000
{ 
	u32 pid;                        // offset 0x00 
	u32 ctrl;                       // offset 0x04 
	u32 res_offset_0x08;            // offset 0x08
	u32 lidd_ctrl;                  // offset 0x0C
	u32 lidd_cs0_conf;              // offset 0x10
    u32 lidd_cs0_addr;              // offset 0x14
    u32 lidd_cs0_data;              // offset 0x18
	u32 lidd_cs1_conf;              // offset 0x1C
    u32 lidd_cs1_addr;              // offset 0x20
    u32 lidd_cs1_data;              // offset 0x24
    u32 raster_ctrl;                // offset 0x28
    u32 raster_timing_0;            // offset 0x2C
    u32 raster_timing_1;            // offset 0x30
    u32 raster_timing_2;            // offset 0x34
    u32 raster_subpanel;            // offset 0x38
    u32 raster_subpanel2;           // offset 0x3C
    u32 lcddma_ctrl;                // offset 0x40
    u32 lcddma_fb0_base;            // offset 0x44
    u32 lcddma_fb0_ceiling;         // offset 0x48
    u32 lcddma_fb1_base;            // offset 0x4C
    u32 lcddma_fb1_ceiling;         // offset 0x50
    u32 sysconfig;                  // offset 0x54
    u32 irqstatus_raw;              // offset 0x58
    u32 irqstatus;                  // offset 0x5C
    u32 irqenable_set;              // offset 0x60
    u32 irqenable_clear;            // offset 0x64
	u32 res_offset_0x68;            // offset 0x68
    u32 clkc_enable;                // offset 0x6C
    u32 clkc_reset;                 // offset 0x70
};
    
    
    
    
    
    
    
    
    
    
    




#endif /* _AM33XX_CPU_H */
