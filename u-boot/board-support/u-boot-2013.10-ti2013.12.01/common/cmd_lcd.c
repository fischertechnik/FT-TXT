/*
 * Control GPIO pins on the fly
 *
 * Copyright (c) 2008-2011 Analog Devices Inc.
 *
 * Licensed under the GPL-2 or later.
 */

#include <common.h>
#include <command.h>
#include <malloc.h>
/*
 * lcd.c
 *
 * LCD functions for TI AM335X 
 *
 * Copyright (C) 2014, Knobloch GmbH http://www.knobloch-gmbh.de
 *
 */

#include <watchdog.h> 
#include <asm/arch-am33xx/hardware_am33xx.h>        // def's for PRCM Register
#include <asm/arch-am33xx/cpu_am335x.h>             // def's for PLL Register

static void SetWindowRGB(u32 u32ColStart, u32 u32RowStart, u32 u32ColEnd, u32 u32RowEnd, u32 u32ColorRGB);
static void SetWindowSize(u32 u32ColStart, u32 u32RowStart, u32 u32ColEnd, u32 u32RowEnd);


    static volatile struct cm_per_pll       *psCmPerReg = (struct cm_per_pll *) CM_PER;
    static volatile struct cm_wkup_pll      *psCmWkupReg = (struct cm_wkup_pll *) CM_WKUP;
    static volatile struct cm_dpll          *psCmDpllReg = (struct cm_dpll *) CM_DPLL;
    static volatile struct LCD_REGISTERS    *psLcdReg = (struct LCD_REGISTERS *) LCD_CNTL_BASE;
    
#define  RED      0xf800
#define  GREEN    0x07e0  
#define  BLUE     0x001f
#define  YELLOW   0xffe0
#define  CYAN     0x07ff   
#define  PURPLE   0xf81f   
#define  WHITE    0xffff
#define  BLACK    0x0000
#define FTBLUE 0x5C57 /* Fischertechnik Blau */

// Clear Window 
static void SetWindowRGB(u32 u32ColStart, u32 u32RowStart, u32 u32ColEnd, u32 u32RowEnd, u32 u32ColorRGB)
{  
    unsigned short volatile  *pLcdCmd;
    unsigned short volatile  *pLcdData;

    u32 u32Col, u32Row;

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


static void CopyToLcd(void *lcd_base, u32 u32ColStart, u32 u32RowStart, u32 u32ColEnd, u32 u32RowEnd) {

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
		WATCHDOG_RESET();

        for (u32Col=0; u32Col<u32ColEnd; u32Col++)
        {
            *pLcdData = *((unsigned short *)lcd_base);           // Daten schreiben
			lcd_base += 2;
        }
    }
}


static void FillFb(void *lcd_base, u32 Color, u32 u32ColStart, u32 u32RowStart, u32 u32ColEnd, u32 u32RowEnd) {
    u32 u32Col,
        u32Row;

    for (u32Row=0; u32Row<u32RowEnd; u32Row++)
    {
		WATCHDOG_RESET();

        for (u32Col=0; u32Col<u32ColEnd; u32Col++)
        {
            *((unsigned short *)lcd_base) = Color;           // Daten schreiben
			lcd_base += 2;
        }
    }
}


// Sets a window from top-left (0,0) corner to bottom-right corner (240,320  ) 
static void SetWindowSize(u32 u32ColStart, u32 u32RowStart, u32 u32ColEnd, u32 u32RowEnd)
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

/*-----------------------------------------------------------*/
/*-----------------------------------------------------------*/
/* Calculate nr. of bits per pixel  and nr. of colors */
#define NBITS_TXT(bit_code)		(1 << (bit_code))
#define NCOLORS(bit_code)	(1 << NBITS_TXT(bit_code))
#define FB_PUT_BYTE_TXT(fb, from) *(fb)++ = *(from)++

typedef struct vidinfo_txt {
	ushort	vl_col;		/* Number of columns (i.e. 160) */
	ushort	vl_row;		/* Number of rows (i.e. 100) */

	u_char	vl_bpix;	/* Bits per pixel, 0 = 1 */

	ushort	*cmap;		/* Pointer to the colormap */

	void	*priv;		/* Pointer to driver-specific data */
} vidinfo_txt_t;

static ushort ColTable_txt[256];

static	vidinfo_txt_t panel_info = {
		240,
		320,
		4,
		ColTable_txt,
		NULL,
	};


static int lcd_get_size_txt(int *line_length)
{
	*line_length = (panel_info.vl_col * NBITS_TXT(panel_info.vl_bpix)) / 8;
	return *line_length * panel_info.vl_row;
}


typedef struct bmp_color_table_entry_txt {
	__u8	blue;
	__u8	green;
	__u8	red;
	__u8	reserved;
} __attribute__ ((packed)) bmp_color_table_entry_txt_t;

/* When accessing these fields, remember that they are stored in little
   endian format, so use linux macros, e.g. le32_to_cpu(width)          */

typedef struct bmp_header_txt {
	/* Header */
	char signature[2];
	__u32	file_size;
	__u32	reserved;
	__u32	data_offset;
	/* InfoHeader */
	__u32	size;
	__u32	width;
	__u32	height;
	__u16	planes;
	__u16	bit_count;
	__u32	compression;
	__u32	image_size;
	__u32	x_pixels_per_m;
	__u32	y_pixels_per_m;
	__u32	colors_used;
	__u32	colors_important;
	/* ColorTable */

} __attribute__ ((packed)) bmp_header_txt_t;

typedef struct bmp_image_txt {
	bmp_header_txt_t header;
	/* We use a zero sized array just as a placeholder for variable
	   sized array */
	bmp_color_table_entry_txt_t color_table[0];
} bmp_image_txt_t;




static int lcd_display_bitmap_txt(ulong bmp_image, int x, int y)
{
	ushort *cmap = NULL;
	ushort *cmap_base = NULL;
	ushort i, j;
	uchar *fb;
	bmp_image_txt_t *bmp=(bmp_image_txt_t *)bmp_image;
	uchar *bmap;
	ushort padded_width;
	int lcd_line_length;
	unsigned long width, height, byte_width;
	unsigned long pwidth = panel_info.vl_col;
	unsigned colors, bpix, bmp_bpix;
	void *lcd_base;

	lcd_get_size_txt(&lcd_line_length);

	if (!bmp || !(bmp->header.signature[0] == 'B' &&
		bmp->header.signature[1] == 'M')) {
		printf("Error: no valid bmp image at %lx\n", bmp_image);

		return 1;
	}

	width = le32_to_cpu(bmp->header.width);
	height = le32_to_cpu(bmp->header.height);
	bmp_bpix = le16_to_cpu(bmp->header.bit_count);
	colors = 1 << bmp_bpix;
	
	
	if(width < panel_info.vl_col)
            x = (panel_info.vl_col - width)/2;

	if(height < panel_info.vl_row)
            y = (panel_info.vl_row - height)/2;

	bpix = NBITS_TXT(panel_info.vl_bpix);
    
	if (bpix != 1 && bpix != 8 && bpix != 16 && bpix != 32) {
		printf ("Error<>: %d bit/pixel mode, but BMP has %d bit/pixel\n",
			bpix, bmp_bpix);

		return 1;
	}
	/* We support displaying 8bpp BMPs on 16bpp LCDs */
	if (bpix != bmp_bpix && !(bmp_bpix == 8 && bpix == 16)) {
		printf ("Error><: %d bit/pixel mode, but BMP has %d bit/pixel\n",
			bpix,
			le16_to_cpu(bmp->header.bit_count));

		return 1;
	}
	debug("Display-bmp: %d x %d  with %d colors\n",
		(int)width, (int)height, (int)colors);

	lcd_base = calloc(panel_info.vl_col * panel_info.vl_row * 2, 1);

	if (lcd_base == NULL) {
		printf ("Error: not enough memory for fb copy\n");
		return 1;
	}
	
	FillFb(lcd_base, FTBLUE, 0, 0, panel_info.vl_col, panel_info.vl_row);

	if (bmp_bpix == 8) {
		cmap = panel_info.cmap;
		cmap_base = cmap;

		/* Set color map */
		for (i = 0; i < colors; ++i) {
			bmp_color_table_entry_txt_t cte = bmp->color_table[i];
			ushort colreg =
				( ((cte.red)   << 8) & 0xf800) |
				( ((cte.green) << 3) & 0x07e0) |
				( ((cte.blue)  >> 3) & 0x001f) ;
			*cmap = colreg;

			cmap++;
		}
	}

	padded_width = (width & 0x3 ? (width & ~0x3) + 4 : width);

	if ((x + width) > pwidth)
		width = pwidth - x;
	if ((y + height) > panel_info.vl_row)
		height = panel_info.vl_row - y;

	bmap = (uchar *) bmp + le32_to_cpu(bmp->header.data_offset);
	fb   = (uchar *) (lcd_base +
		(y + height - 1) * lcd_line_length + x * bpix / 8);

	switch (bmp_bpix) {
	case 1: /* pass through */
	case 8:
		if (bpix != 16)
			byte_width = width;
		else
			byte_width = width * 2;

		for (i = 0; i < height; ++i) {
			WATCHDOG_RESET();
			for (j = 0; j < width; j++) {
				if (bpix != 16) {
					FB_PUT_BYTE_TXT(fb, bmap);
				} else {
					*(uint16_t *)fb = cmap_base[*(bmap++)];
					fb += sizeof(uint16_t) / sizeof(*fb);
				}
			}
			bmap += (padded_width - width);
			fb -= byte_width + lcd_line_length;
		}
		break;

	case 16:
		for (i = 0; i < height; ++i) {
			WATCHDOG_RESET();
			for (j = 0; j < width; j++)
				*((unsigned short *)(fb)) = *((unsigned short *)(bmap));
				/*fb_put_word(&fb, &bmap);*/

			bmap += (padded_width - width) * 2;
			fb -= width * 2 + lcd_line_length;
		}
		break;

#ifdef XXX
	case 32:
		for (i = 0; i < height; ++i) {
			for (j = 0; j < width; j++) {
				*(fb++) = *(bmap++);
				*(fb++) = *(bmap++);
				*(fb++) = *(bmap++);
				*(fb++) = *(bmap++);
			}
			fb -= lcd_line_length + width * (bpix / 8);
		}
		break;
#endif		

	default:
		break;
	};

	/* jetzt anzeigen */
       printf("CopyToLcd\n");
       CopyToLcd(lcd_base, 0, 0, panel_info.vl_col, panel_info.vl_row);

	cfree(lcd_base);
	return 0;
}

enum lcd_cmd {
	LCD_GREEN,
	LCD_RED,
	LCD_BLUE,
	LCD_BMP,
};

static int do_lcd(cmd_tbl_t *cmdtp, int flag, int argc, char * const argv[])
{
	enum lcd_cmd sub_cmd;
	const char *str_cmd;


	if (argc != 2)
 show_usage:
		return CMD_RET_USAGE;
		
	str_cmd = argv[1];
	/* parse the behavior */
	switch (*str_cmd) {
		case 'g': sub_cmd = LCD_GREEN;  SetWindowRGB( 0, 0, 240, 320, 0x07F0); break;
		case 'r': sub_cmd = LCD_RED; SetWindowRGB( 0, 0, 240, 320, 0xF800); break;
		case 'b': sub_cmd = LCD_BLUE; SetWindowRGB( 0, 0, 240, 320, 0x001F); break;
		case 'f': sub_cmd = LCD_BLUE; SetWindowRGB( 0, 0, 240, 320, FTBLUE); break;
		case 'l': sub_cmd = LCD_BMP; lcd_display_bitmap_txt(0x80200000, 0, 0); break;
		default:  goto show_usage;
	}
	printf("LCD Command called\n");
	return 0;
}

U_BOOT_CMD(lcd, 3, 0, do_lcd,
	"lcd green|red|blue",
	"<green|red|blue|ftblue|logo\n"
	"   logo: BMP at 0x80200000\n"
	"    - fill lcd with color or show BMP");
