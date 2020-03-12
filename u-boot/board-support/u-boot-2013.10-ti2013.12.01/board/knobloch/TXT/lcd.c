/*
 * lcd.c
 *
 * LCD functions for TI AM335X 
 *
 * Copyright (C) 2014,2020  Knobloch GmbH http://www.knobloch-gmbh.de
 *                          V 1.1d
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
    
    unsigned short  u16Data0,
                    u16Data1,
                    u16Data2,
                    u16Data3,
                    u16Data4;
    

    // First Set PLL 
    psCmWkupReg->clkmod_dpll_disp = (psCmWkupReg->clkmod_dpll_disp & 0x0007) | 0x00000004;    // set Bypass Mode
    
    u32Dummy = 0;
    do 
    {
        u32Dummy++;
    } while (!(psCmWkupReg->idlest_dpll_disp & 0x00000100));        // wait for BYPASS Mode

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
    
    psLcdReg->ctrl = 0x00000000;                
    psLcdReg->lidd_ctrl = 0x00000003;           
    psLcdReg->lidd_cs0_conf = 0x1064394F;       

    printf("LCD-V1.1d \n");
    
    psLcdReg->clkc_enable = 0x00000002;      

    pLcdCmd = (unsigned short *)  &psLcdReg->lidd_cs0_addr;     
    pLcdData = (unsigned short *) &psLcdReg->lidd_cs0_data;     

    *pLcdCmd = 0x0000;                          // 001
    *pLcdCmd = 0x0001;                          // 
    udelay(180000);                             // 
    *pLcdCmd = 0x0000;                          // 
    *pLcdCmd = 0x0028;                          // 005
    *pLcdCmd = 0x00CF;                          // 
    *pLcdData = 0x0000;                         // 
    *pLcdData = 0x0081;                         //
    *pLcdData = 0x0030;                         //
    *pLcdCmd = 0x00ED;                          // 010
    *pLcdData = 0x0064;                         //
    *pLcdData = 0x0003;                         //
    *pLcdData = 0x0012;                         //
    *pLcdData = 0x0081;                         //
    *pLcdCmd = 0x00E8;                          // 015 
    *pLcdData = 0x0085;                         //
    *pLcdData = 0x0001;                         //
    *pLcdData = 0x0079;                         //
    *pLcdCmd = 0x00CB;                          // 
    *pLcdData = 0x0039;                         // 020
    *pLcdData = 0x002C;                         //
    *pLcdData = 0x0000;                         //
    *pLcdData = 0x0034;                         //
    *pLcdData = 0x0002;                         //
    *pLcdCmd = 0x00F7;                          // 025 
    *pLcdData = 0x0020;                         //
    *pLcdCmd = 0x00EA;                          // 
    *pLcdData = 0x0000;                         //
    *pLcdData = 0x0000;                         //
    *pLcdCmd = 0x00C0;                          // 030
    *pLcdData = 0x0026;                         //
    *pLcdCmd = 0x00C1;                          // 
    *pLcdData = 0x0011;                         //
    *pLcdCmd = 0x00C5;                          // 
    *pLcdData = 0x0035;                         // 035
    *pLcdData = 0x003E;                         //
    *pLcdCmd = 0x00C7;                          // 
    *pLcdData = 0x00BE;                         //
    *pLcdCmd = 0x0036;                          // 
    *pLcdData = 0x0048;                         // 040
    *pLcdCmd = 0x003A;                          // 
    *pLcdData = 0x0055;                         //
    *pLcdCmd = 0x00B1;                          // 
    *pLcdData = 0x0000;                         //
    *pLcdData = 0x001B;                         // 045
    *pLcdCmd = 0x0000;                          // 
    *pLcdCmd = 0x00B4;                          //
    *pLcdData = 0x0000;                         //
    *pLcdCmd = 0x00F2;                          // 
    *pLcdData = 0x0002;                         // 050
    *pLcdCmd = 0x0026;                          // 
    *pLcdData = 0x0001;                         //
    *pLcdCmd = 0x00E0;                          // 
    *pLcdData = 0x001F;                         //
    *pLcdData = 0x001A;                         // 055
    *pLcdData = 0x0018;                         //
    *pLcdData = 0x000A;                         //
    *pLcdData = 0x000F;                         //
    *pLcdData = 0x0006;                         //
    *pLcdData = 0x0045;                         // 060
    *pLcdData = 0x0087;                         //
    *pLcdData = 0x0032;                         //
    *pLcdData = 0x000A;                         //
    *pLcdData = 0x0007;                         //
    *pLcdData = 0x0002;                         // 065
    *pLcdData = 0x0007;                         //
    *pLcdData = 0x0005;                         //
    *pLcdData = 0x0000;                         //
    *pLcdCmd = 0x00E1;                          //
    *pLcdData = 0x0000;                         // 070
    *pLcdData = 0x0025;                         //
    *pLcdData = 0x0027;                         //
    *pLcdData = 0x0005;                         //
    *pLcdData = 0x0010;                         //
    *pLcdData = 0x0009;                         // 075
    *pLcdData = 0x003A;                         //
    *pLcdData = 0x0078;                         //
    *pLcdData = 0x004D;                         //
    *pLcdData = 0x0005;                         //
    *pLcdData = 0x0018;                         // 080
    *pLcdData = 0x000D;                         //
    *pLcdData = 0x0038;                         //
    *pLcdData = 0x003A;                         //
    *pLcdData = 0x001F;                         //
    *pLcdCmd = 0x002A;                          // 085
    *pLcdData = 0x0000;                         //
    *pLcdData = 0x0000;                         //
    *pLcdData = 0x0000;                         //
    *pLcdData = 0x00EF;                         //
    *pLcdCmd = 0x002B;                          // 090
    *pLcdData = 0x0000;                         //
    *pLcdData = 0x0000;                         //
    *pLcdData = 0x0001;                         //
    *pLcdData = 0x003F;                         //
    *pLcdCmd = 0x00B7;                          // 095
    *pLcdData = 0x0007;                         //
    *pLcdCmd = 0x00B6;                          //
    *pLcdData = 0x000A;                         //
    *pLcdData = 0x0082;                         //
    *pLcdData = 0x0027;                         // 100
    *pLcdData = 0x0000;                         //
    *pLcdCmd = 0x0011;                          //
    udelay(40000);                              //
    *pLcdCmd = 0x0029;                          // 104
//    ClearWindowRGB(       0,        0,    240, 320, TEST_COLOR);
    *pLcdCmd = 0x0000;                          //
    *pLcdCmd = 0x0009;                          //
    u16Data0 = *pLcdData;   
    u16Data1 = *pLcdData;   
    u16Data2 = *pLcdData;   
    u16Data3 = *pLcdData;   
    u16Data4 = *pLcdData;   
    if ((u16Data1 != 0x00A4) || (u16Data2 != 0x0053) || (u16Data3 != 0x0004) || (u16Data4 != 0x0000))
    {
        printf("Info: LCD-Read-09  (0 A4 53 4 0):  %2X %2X %2X %2X %2X \n", u16Data0, u16Data1, u16Data2, u16Data3, u16Data4);
    }

    *pLcdCmd = 0x00D3;      
    u16Data0 = *pLcdData;   
    u16Data1 = *pLcdData;   
    u16Data2 = *pLcdData;   
    u16Data3 = *pLcdData;   
    if ((u16Data1 != 0x0000) || (u16Data2 != 0x0093) || (u16Data3 != 0x0041))
    {
        printf("Info LCD-Read-D3 (0 00 93 41):  %2X %2X %2X %2X  \n", u16Data0, u16Data1, u16Data2, u16Data3);
    }
}



// Clear Window 
void ClearWindowRGB(u32 u32ColStart, u32 u32RowStart, u32 u32ColEnd, u32 u32RowEnd, u32 u32ColorRGB)
{  
    unsigned short volatile  *pLcdCmd;
    unsigned short volatile  *pLcdData;

    u32 u32Col,
        u32Row;
        
    SetWindowSize(u32ColStart, u32RowStart, u32ColEnd-1, u32RowEnd-1);
    printf("LCD-Clear \n");
    
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
    *pLcdCmd = 0x0000;           
}


// Sets a window from top-left (0,0) corner to bottom-right corner (240,320  )
void SetWindowSize(u32 u32ColStart, u32 u32RowStart, u32 u32ColEnd, u32 u32RowEnd)
{
    unsigned short volatile  *pLcdCmd;
    unsigned short volatile  *pLcdData;

    printf("LCD-SetSize \n");

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

