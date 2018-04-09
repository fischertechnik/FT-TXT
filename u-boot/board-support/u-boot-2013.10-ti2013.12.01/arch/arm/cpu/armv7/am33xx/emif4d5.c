/*
 * emif4d5.c
 *
 * AM43XX emif4d5 configuration file
 *
 * Copyright (C) 2013, Texas Instruments, Incorporated - http://www.ti.com/
 *
 * SPDX-License-Identifier:	GPL-2.0+
 */
#include <common.h>
#include <asm/arch/cpu.h>
#include <asm/arch/ddr_defs.h>
#include <asm/arch/hardware.h>
#include <asm/arch/clock.h>
#include <asm/arch/sys_proto.h>
#include <asm/io.h>
#include <asm/emif.h>

static struct ddr_ctrl *ddrctrl = (struct ddr_ctrl *)DDR_CTRL_ADDR;
static struct cm_device_inst *cm_device =
				(struct cm_device_inst *)CM_DEVICE_INST;
static struct ddr_cmdtctrl *ioctrl_reg =
			(struct ddr_cmdtctrl *)DDR_CONTROL_BASE_ADDR;
static struct vtp_reg *vtpreg = (struct vtp_reg *)VTP0_CTRL_ADDR;

DECLARE_GLOBAL_DATA_PTR;

int dram_init(void)
{
	/* dram_init must store complete ramsize in gd->ram_size */
	gd->ram_size = get_ram_size(
			(void *)CONFIG_SYS_SDRAM_BASE,
			CONFIG_MAX_RAM_BANK_SIZE);
	return 0;
}

static void config_vtp(void)
{
	writel(readl(&vtpreg->vtp0ctrlreg) | VTP_CTRL_ENABLE,
	       &vtpreg->vtp0ctrlreg);
	writel(readl(&vtpreg->vtp0ctrlreg) & (~VTP_CTRL_START_EN),
	       &vtpreg->vtp0ctrlreg);
	writel(readl(&vtpreg->vtp0ctrlreg) | VTP_CTRL_START_EN,
	       &vtpreg->vtp0ctrlreg);

	while ((readl(&vtpreg->vtp0ctrlreg) & VTP_CTRL_READY) !=
	       VTP_CTRL_READY)
		;
}

static void ext_phy_settings(const struct emif_regs *regs,
			     const u32 *ext_phy_ctrl_const_regs)
{
	u32 *ext_phy_ctrl_base = 0;
	u32 *emif_ext_phy_ctrl_base = 0;
	u32 i = 0;

	struct emif_reg_struct *emif = (struct emif_reg_struct *)EMIF1_BASE;

	ext_phy_ctrl_base = (u32 *)&(regs->emif_ddr_ext_phy_ctrl_1);
	emif_ext_phy_ctrl_base = (u32 *)&(emif->emif_ddr_ext_phy_ctrl_1);

	/* Configure external phy control timing registers */
	for (i = 0; i < EMIF_EXT_PHY_CTRL_TIMING_REG; i++) {
		writel(*ext_phy_ctrl_base, emif_ext_phy_ctrl_base++);
		/* Update shadow registers */
		writel(*ext_phy_ctrl_base++, emif_ext_phy_ctrl_base++);
	}

	/* TODO: Reconcile with OMAP5/DRA7xx changes */
#define EMIF_EXT_PHY_CTRL_CONST_REG	0x14
	for (i = 0; i < EMIF_EXT_PHY_CTRL_CONST_REG; i++) {
		writel(ext_phy_ctrl_const_regs[i],
		       emif_ext_phy_ctrl_base++);
		/* Update shadow registers */
		writel(ext_phy_ctrl_const_regs[i],
		       emif_ext_phy_ctrl_base++);
	}
}

static inline u32 get_mr(u32 base, u32 cs, u32 mr_addr)
{
	u32 mr;
	struct emif_reg_struct *emif = (struct emif_reg_struct *)base;

	mr_addr |= cs << EMIF_REG_CS_SHIFT;
	writel(mr_addr, &emif->emif_lpddr2_mode_reg_cfg);

	mr = readl(&emif->emif_lpddr2_mode_reg_data);
	debug("get_mr: EMIF1 cs %d mr %08x val 0x%x\n", cs, mr_addr, mr);
	if (((mr & 0x0000ff00) >>  8) == (mr & 0xff) &&
	    ((mr & 0x00ff0000) >> 16) == (mr & 0xff) &&
	    ((mr & 0xff000000) >> 24) == (mr & 0xff))
		return mr & 0xff;
	else
		return mr;
}

static inline void set_mr(u32 base, u32 cs, u32 mr_addr, u32 mr_val)
{
	struct emif_reg_struct *emif = (struct emif_reg_struct *)base;

	mr_addr |= cs << EMIF_REG_CS_SHIFT;
	writel(mr_addr, &emif->emif_lpddr2_mode_reg_cfg);
	writel(mr_val, &emif->emif_lpddr2_mode_reg_data);
}

static void configure_mr(u32 base, u32 cs)
{
	u32 mr_addr;

	while (get_mr(base, cs, LPDDR2_MR0) & LPDDR2_MR0_DAI_MASK)
		;
	set_mr(base, cs, LPDDR2_MR10, 0x56);

	set_mr(base, cs, LPDDR2_MR1, 0x43);
	set_mr(base, cs, LPDDR2_MR2, 0x2);

	mr_addr = LPDDR2_MR2 | EMIF_REG_REFRESH_EN_MASK;
	set_mr(base, cs, mr_addr, 0x2);
}

void do_sdram_init(const struct ctrl_ioregs *ioregs,
		   const struct emif_regs *regs,
		   const u32 *ext_phy_ctrl_const_regs, u32 sdram_type)
{
	struct emif_reg_struct *emif = (struct emif_reg_struct *)EMIF1_BASE;

	config_vtp();

	writel(readl(&cm_device->cm_dll_ctrl) & ~0x1, &cm_device->cm_dll_ctrl);
	while ((readl(&cm_device->cm_dll_ctrl) && CM_DLL_READYST) == 0)
		;

	/* io settings */
	writel(ioregs->cm0ioctl, &ioctrl_reg->cm0ioctl);
	writel(ioregs->cm1ioctl, &ioctrl_reg->cm1ioctl);
	writel(ioregs->cm2ioctl, &ioctrl_reg->cm2ioctl);
	writel(ioregs->dt0ioctl, &ioctrl_reg->dt0ioctl);
	writel(ioregs->dt1ioctl, &ioctrl_reg->dt1ioctl);
	writel(ioregs->dt2ioctrl, &ioctrl_reg->dt2ioctrl);
	writel(ioregs->dt3ioctrl, &ioctrl_reg->dt3ioctrl);
	writel(ioregs->emif_sdram_config_ext,
	       &ioctrl_reg->emif_sdram_config_ext);
	writel(0x80000000, &ddrctrl->ddrioctrl);

	/* Set CKE to be controlled by EMIF/DDR PHY */
	writel(readl(&ddrctrl->ddrckectrl) | 0x3, &ddrctrl->ddrckectrl);

	/*
	 * disable initialization and refreshes for now until we
	 * finish programming EMIF regs.
	 */
	setbits_le32(&emif->emif_sdram_ref_ctrl, EMIF_REG_INITREF_DIS_MASK);

	writel(regs->sdram_tim1, &emif->emif_sdram_tim_1);
	writel(regs->sdram_tim1, &emif->emif_sdram_tim_1_shdw);
	writel(regs->sdram_tim2, &emif->emif_sdram_tim_2);
	writel(regs->sdram_tim2, &emif->emif_sdram_tim_2_shdw);
	writel(regs->sdram_tim3, &emif->emif_sdram_tim_3);
	writel(regs->sdram_tim3, &emif->emif_sdram_tim_3_shdw);

	writel(0xA0, &emif->emif_pwr_mgmt_ctrl);
	writel(0xA0, &emif->emif_pwr_mgmt_ctrl_shdw);
	writel(0x1, &emif->emif_iodft_tlgc);
	writel(regs->zq_config, &emif->emif_zq_config);

	writel(regs->temp_alert_config, &emif->emif_temp_alert_config);
	writel(regs->emif_rd_wr_lvl_rmp_win, &emif->emif_rd_wr_lvl_rmp_win);
	writel(regs->emif_rd_wr_lvl_rmp_ctl, &emif->emif_rd_wr_lvl_rmp_ctl);
	writel(regs->emif_rd_wr_lvl_ctl, &emif->emif_rd_wr_lvl_ctl);
	writel(regs->emif_ddr_phy_ctlr_1, &emif->emif_ddr_phy_ctrl_1);
	writel(regs->emif_ddr_phy_ctlr_1, &emif->emif_ddr_phy_ctrl_1_shdw);
	writel(regs->emif_rd_wr_exec_thresh, &emif->emif_rd_wr_exec_thresh);

	ext_phy_settings(regs, ext_phy_ctrl_const_regs);

	clrbits_le32(&emif->emif_sdram_ref_ctrl, EMIF_REG_INITREF_DIS_MASK);

	writel(regs->sdram_config, &emif->emif_sdram_config);
	writel(regs->sdram_config, &cstat->secure_emif_sdram_config);
	writel(regs->ref_ctrl, &emif->emif_sdram_ref_ctrl);

	if (sdram_type == EMIF_SDRAM_TYPE_LPDDR2) {
		configure_mr(EMIF1_BASE, 0);
		configure_mr(EMIF1_BASE, 1);
	}
}
