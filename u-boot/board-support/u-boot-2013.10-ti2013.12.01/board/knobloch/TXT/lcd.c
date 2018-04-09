/*
 * lcd.c
 *
 * LCD functions for TI AM335X 
 *
 * Copyright (C) 2014, Knobloch GmbH http://www.knobloch-gmbh.de
 *
 */

#include <common.h>

#include <asm/arch-am33xx/hardware_am33xx.h>        // def's for PRCM Register
#include <asm/arch-am33xx/cpu_am335x.h>             // def's for PLL Register

void ClearWindowRGB(u32 u32ColStart, u32 u32RowStart, u32 u32ColEnd, u32 u32RowEnd, u32 u32ColorRGB);
void SetWindowSize(u32 u32ColStart, u32 u32RowStart, u32 u32ColEnd, u32 u32RowEnd);


    static volatile struct cm_per_pll       *psCmPerReg = (struct cm_per_pll *) CM_PER;
    static volatile struct cm_wkup_pll      *psCmWkupReg = (struct cm_wkup_pll *) CM_WKUP;
    static volatile struct cm_dpll          *psCmDpllReg = (struct cm_dpll *) CM_DPLL;
    static volatile struct LCD_REGISTERS    *psLcdReg = (struct LCD_REGISTERS *) LCD_CNTL_BASE;
    
    
#define  x_offset 0
#define  y_offset 0
#define  P_width  240
#define  P_height 320
#define  RED      0xf800
#define  GREEN    0x07e0
#define  BLUE     0x001f
#define  YELLOW   0xffe0
#define  CYAN     0x07ff
#define  PURPLE   0xf81f
#define  WHITE    0xffff
#define  BLACK    0x0000  
// Ft-Blau
// R = 01011111   95
// G = 10001001		137
// B = 10111011		187
// --> 5/6/5 - 16Bit
// -> 01011100 01010111
#define FTBLUE 0x5C57

void LcdInit(void)
{
    u32 u32Dummy;
    unsigned short volatile  *pLcdCmd;
    unsigned short volatile  *pLcdData;

    // First Set PLL 
    psCmWkupReg->clkmod_dpll_disp = (psCmWkupReg->clkmod_dpll_disp & 0x0007) | 0x00000004;    // set Bypass Mode
    
    u32Dummy = 0;
    do 
    {
        u32Dummy++;
    } while (!(psCmWkupReg->idlest_dpll_disp & 0x00000100));           // wait for BYPASS Mode

    psCmWkupReg->clksel_dpll_disp = 0x00006417;                     // set PLL Values
    psCmWkupReg->div_m2_dpll_disp = 0x00000301;                     // set PLL Values
    
    psCmWkupReg->clkmod_dpll_disp = (psCmWkupReg->clkmod_dpll_disp & 0x0007) | 0x00000007;    // set Lock Mode

    u32Dummy = 0;
    do 
    {
        u32Dummy++; 
    } 
    while ((((psCmWkupReg->idlest_dpll_disp) & 0x00000001) == 0x00000000) );           // wait for Lock Mode 

    // now PRCM Switching
    psCmDpllReg->clksel_lcdc_pixel_clk = 0x00000000;                // set to use Display PLL
    psCmPerReg->lcdc_clkctrl = 0x00000002;                          // Enable LCD Module
    
    // now Init LCD-Module
    psLcdReg->ctrl = 0x00000000;                // set LIDD Mode
    psLcdReg->lidd_ctrl = 0x00000003;           // MPU80 Mode
    psLcdReg->lidd_cs0_conf = 0x1064394F;       // 0001 0000 0110 0100 0011 1001 0100 1111 = 0x1064 394F
    psLcdReg->clkc_enable = 0x00000002;         // LIDD Clock enable

    // Zeiger setzen
    pLcdCmd = (unsigned short *)  &psLcdReg->lidd_cs0_addr;
    pLcdData = (unsigned short *) &psLcdReg->lidd_cs0_data;
    
    // Display initialisieren
    *pLcdCmd = 0x0001;                          // 1.   Software Reset schreiben
    udelay(120000);                             //      Wait 120msek

    *pLcdCmd = 0x0028;                          // 2.   Display off

    *pLcdCmd = 0x00CF;                          // 3.	Power Control B
    *pLcdData = 0x0000;
    *pLcdData = 0x0081;
    *pLcdData = 0x0030;
    
    *pLcdCmd = 0x00ED;                          // 4.	Power On Sequence Control
    *pLcdData = 0x0003;
    *pLcdData = 0x0012;
    *pLcdData = 0x0081;

    *pLcdCmd = 0x00E8;                          // 5.	Driver Timing
    *pLcdData = 0x0085;
    *pLcdData = 0x0001;
    *pLcdData = 0x0079;
    
    *pLcdCmd = 0x00CB;                          // 6.   Driver Timing
    *pLcdData = 0x0039;
    *pLcdData = 0x002C;
    *pLcdData = 0x0000;
    *pLcdData = 0x0034;
    *pLcdData = 0x0002;
    
    *pLcdCmd = 0x00F7;                          // 7.	Pump ratio control
    *pLcdData = 0x0020;

    *pLcdCmd = 0x00EA;                          // 8.   Driver Timing Control B
    *pLcdData = 0x0000;
    *pLcdData = 0x0000;

    *pLcdCmd = 0x00C0;                          // 9.	Power Control
    *pLcdData = 0x0026;

    *pLcdCmd = 0x00C1;                          // 10.	Power Control 2
    *pLcdData = 0x0011;

    *pLcdCmd = 0x00C5;                          // 11.	VCOM Control
    *pLcdData = 0x0035;
    *pLcdData = 0x003E;

    *pLcdCmd = 0x00C7;                          // 12.	VCOM Control 2
    *pLcdData = 0x00BE;

    *pLcdCmd = 0x0036;                          // 13.	Memory Access Control
    *pLcdData = 0x0048;                         //      0100 0000 --> an X spiegeln, ferner statt RGB, BGR verwenden

    *pLcdCmd = 0x003A;                          // 14.	Pixel Format
    *pLcdData = 0x0055;

    *pLcdCmd = 0x00B1;                          // 15.	Frame Rate
    *pLcdData = 0x0000;
    *pLcdData = 0x001B;

    *pLcdCmd = 0x00B4;                          // 16.	Display Inversion Control
    *pLcdData = 0x0000;

    *pLcdCmd = 0x00F2;                          // 17.	Enable 3G
    *pLcdData = 0x0002;

    *pLcdCmd = 0x0026;                          // 18.	Gamma Set
    *pLcdData = 0x0001;

    *pLcdCmd = 0x00E0;                          // 19.	Positive Gamma Correction
    *pLcdData = 0x001F;
    *pLcdData = 0x001A;
    *pLcdData = 0x0018;
    *pLcdData = 0x000A;
    *pLcdData = 0x000F;
    *pLcdData = 0x0006;
    *pLcdData = 0x0045;
    *pLcdData = 0x0087;
    *pLcdData = 0x0032;
    *pLcdData = 0x000A;
    *pLcdData = 0x0007;
    *pLcdData = 0x0002;
    *pLcdData = 0x0007;
    *pLcdData = 0x0005;
    *pLcdData = 0x0000;

    *pLcdCmd = 0x00E1;                          // 20.	Negative Gamma Correction
    *pLcdData = 0x0000;
    *pLcdData = 0x0025;
    *pLcdData = 0x0027;
    *pLcdData = 0x0005;
    *pLcdData = 0x0010;
    *pLcdData = 0x0009;
    *pLcdData = 0x003A;
    *pLcdData = 0x0078;
    *pLcdData = 0x004D;
    *pLcdData = 0x0005;
    *pLcdData = 0x0018;
    *pLcdData = 0x000D;
    *pLcdData = 0x0038;
    *pLcdData = 0x003A;
    *pLcdData = 0x001F;

    *pLcdCmd = 0x002A;                          // 21.	Column Address Set
    *pLcdData = 0x0000;
    *pLcdData = 0x0000;
    *pLcdData = 0x0000;
    *pLcdData = 0x00EF;

    *pLcdCmd = 0x002B;                          // 22.	Page Address Set
    *pLcdData = 0x0000;
    *pLcdData = 0x0000;
    *pLcdData = 0x0001;
    *pLcdData = 0x003F;

    *pLcdCmd = 0x00B7;                          // 23.	Entry Mode Set
    *pLcdData = 0x0007;

    *pLcdCmd = 0x00B6;                          // 24.	Display Function Control
    *pLcdData = 0x000A;
    *pLcdData = 0x0082;
    *pLcdData = 0x0027;
    *pLcdData = 0x0000;

    *pLcdCmd = 0x0011;                          // 25.	Sleep Out
    udelay(5000);                               //      Wait 5msek

    *pLcdCmd = 0x0029;                          // 26.	Display On
 
//    ClearWindowRGB( 0, 0, 240, 320, 0x001F);
//    ClearWindowRGB( 60, 80, 120, 160, 0xE000);
//    ClearWindowRGB( 100, 100, 160, 120, 0x0F00);
//    ClearWindowRGB( 0, 0, 240, 320, FTBLUE);

printf("LCD-Init_4: \n");
   
}
// Clear Window 
void ClearWindowRGB(u32 u32ColStart, u32 u32RowStart, u32 u32ColEnd, u32 u32RowEnd, u32 u32ColorRGB)
{  
    unsigned short volatile  *pLcdCmd;
    unsigned short volatile  *pLcdData;

    u32 u32Col,
        u32Row;

    SetWindowSize(u32ColStart, u32RowStart, u32ColEnd-1, u32RowEnd-1);
    
    pLcdCmd = (unsigned short *)  &psLcdReg->lidd_cs0_addr;
    pLcdData = (unsigned short *) &psLcdReg->lidd_cs0_data;

    *pLcdCmd = 0x002C;                                          // Memory Write, Neustart Adress Zeiger
    
    for (u32Row=0; u32Row<u32RowEnd; u32Row++)
    {
        for (u32Col=0; u32Col<u32ColEnd; u32Col++)
        {
            if (u32Col==0 && u32Row==0)
            {
                *pLcdData = (unsigned short) 0xF800;                // 1. Pixel andere Farbe
            }
            else
            {
                if (u32Col==1)
                {
                    *pLcdData = (unsigned short) 0x001F;            // 1. Pixel einer Spalte andere Farbe
                }
                else
                {
                    *pLcdData = (unsigned short) u32ColorRGB;           // Daten schreiben
                }
            }
        }
    }
}
// Sets a window from top-left (0,0) corner to bottom-right corner (240,320  )
void SetWindowSize(u32 u32ColStart, u32 u32RowStart, u32 u32ColEnd, u32 u32RowEnd)
{
    unsigned short volatile  *pLcdCmd;
    unsigned short volatile  *pLcdData;

    pLcdCmd = (unsigned short *)  &psLcdReg->lidd_cs0_addr;
    pLcdData = (unsigned short *) &psLcdReg->lidd_cs0_data;
    
    *pLcdCmd = 0x002A;                                          // Column Adress Set
    *pLcdData = (unsigned short) (u32ColStart >> 8) & 0x00FF;   // obere 8 Start-Adressbits setzen
    *pLcdData = (unsigned short) (u32ColStart & 0x00FF);        // untere 8 Start-Adressbits setzen
    *pLcdData = (unsigned short) (u32ColEnd >> 8) & 0x00FF;     // obere 8 End-Adressbits setzen
    *pLcdData = (unsigned short) (u32ColEnd & 0x00FF);          // untere 8 End-Adressbits setzen
    
    *pLcdCmd = 0x002B;                                          // Row (Page) Adress Set
    *pLcdData = (unsigned short) (u32RowStart >> 8) & 0x00FF;   // obere 8 Start-Adressbits setzen
    *pLcdData = (unsigned short) (u32RowStart & 0x00FF);        // untere 8 Start-Adressbits setzen
    *pLcdData = (unsigned short) (u32RowEnd >> 8) & 0x00FF;     // obere 8 End-Adressbits setzen
    *pLcdData = (unsigned short) (u32RowEnd & 0x00FF);          // untere 8 End-Adressbits setzen
}


