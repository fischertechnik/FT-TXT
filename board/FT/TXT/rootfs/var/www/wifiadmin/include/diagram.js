// JavaScript Diagram Builder 3.33
// Copyright (c) 2001-2005 Lutz Tautenhahn, all rights reserved.
//
// The Author grants you a non-exclusive, royalty free, license to use,
// modify and redistribute this software, provided that this copyright notice
// and license appear on all copies of the software.
// This software is provided "as is", without a warranty of any kind.

//Modified : deleted some functs to lighten memmory size and download overhead (Box,Bar,Arrow,Pie,Arrea,Dot)

var _N_Dia=0, _N_Bar=0, _N_Box=0, _N_Dot=0, _N_Pix=0, _N_Line=0, _N_Area=0, _N_Arrow=0, _N_Pie=0, _zIndex=0;
var _dSize = (navigator.appName == "Microsoft Internet Explorer") ? 1 : -1;
if (navigator.userAgent.search("Opera")>=0) _dSize=-1;
var _IE=0;
if (_dSize==1)
{ _IE=1;
  if (window.document.documentElement.clientHeight) _dSize=-1; //IE in standards-compliant mode
}
var _nav4 = (document.layers) ? 1 : 0;
var _DiagramTarget=window;
var _BFont="font-family:Verdana;font-weight:bold;font-size:10pt;line-height:13pt;"
var _PathToScript="";

function Diagram()
{ this.xtext="";
  this.ytext="";
  this.title="";
  this.XScale=1;
  this.YScale=1;
  this.XScalePosition="bottom";
  this.YScalePosition="left";
  this.Font="font-family:Verdana;font-weight:normal;font-size:10pt;line-height:13pt;";
  this.ID="Dia"+_N_Dia; _N_Dia++; _zIndex++;
  this.zIndex=_zIndex;
  this.logsub=new Array(0.301, 0.477, 0.602, 0.699, 0.778, 0.845, 0.903, 0.954);
  this.SetFrame=_SetFrame;
  this.SetBorder=_SetBorder;
  this.SetText=_SetText;
  this.SetGridColor=_SetGridColor;
  this.SetXGridColor=_SetXGridColor;
  this.SetYGridColor=_SetYGridColor;
  this.ScreenX=_ScreenX;
  this.ScreenY=_ScreenY;
  this.RealX=_RealX;
  this.RealY=_RealY;
  this.XGrid=new Array(3);
  this.GetXGrid=_GetXGrid;
  this.YGrid=new Array(3);
  this.GetYGrid=_GetYGrid;
  this.XGridDelta=0;
  this.YGridDelta=0;
  this.XSubGrids=0;
  this.YSubGrids=0;
  this.SubGrids=0;
  this.XGridColor="";
  this.YGridColor="";
  this.XSubGridColor="";
  this.YSubGridColor="";
  this.MaxGrids=0;
  this.DateInterval=_DateInterval;
  this.Draw=_Draw;
  this.SetVisibility=_SetVisibility;
  this.SetTitle=_SetTitle;
  this.Delete=_Delete;
  return(this);
}
function _SetFrame(theLeft, theTop, theRight, theBottom)
{ this.left   = theLeft;
  this.right  = theRight;
  this.top    = theTop;
  this.bottom = theBottom;
}
function _SetBorder(theLeftX, theRightX, theBottomY, theTopY)
{ this.xmin = theLeftX;
  this.xmax = theRightX;
  this.ymin = theBottomY;
  this.ymax = theTopY;
}
function _SetText(theScaleX, theScaleY, theTitle)
{ this.xtext=theScaleX;
  this.ytext=theScaleY;
  this.title=theTitle;
}
function _SetGridColor(theGridColor, theSubGridColor)
{ this.XGridColor=theGridColor;
  this.YGridColor=theGridColor;
  if ((theSubGridColor)||(theSubGridColor==""))
  { this.XSubGridColor=theSubGridColor;
    this.YSubGridColor=theSubGridColor;
  }
}
function _SetXGridColor(theGridColor, theSubGridColor)
{ this.XGridColor=theGridColor;
  if ((theSubGridColor)||(theSubGridColor==""))
    this.XSubGridColor=theSubGridColor;
}
function _SetYGridColor(theGridColor, theSubGridColor)
{ this.YGridColor=theGridColor;
  if ((theSubGridColor)||(theSubGridColor==""))
    this.YSubGridColor=theSubGridColor;
}
function _ScreenX(theRealX)
{ return(Math.round((theRealX-this.xmin)/(this.xmax-this.xmin)*(this.right-this.left)+this.left));
}
function _ScreenY(theRealY)
{ return(Math.round((this.ymax-theRealY)/(this.ymax-this.ymin)*(this.bottom-this.top)+this.top));
}
function _RealX(theScreenX)
{ return(this.xmin+(this.xmax-this.xmin)*(theScreenX-this.left)/(this.right-this.left));
}
function _RealY(theScreenY)
{ return(this.ymax-(this.ymax-this.ymin)*(theScreenY-this.top)/(this.bottom-this.top));
}
function _sign(rr)
{ if (rr<0) return(-1); else return(1);
}
function _DateInterval(vv)
{ var bb=140*24*60*60*1000; //140 days
  this.SubGrids=4;
  if (vv>=bb) //140 days < 5 months
  { bb=8766*60*60*1000;//1 year
    if (vv<bb) //1 year 
      return(bb/12); //1 month
    if (vv<bb*2) //2 years 
      return(bb/6); //2 month
    if (vv<bb*5/2) //2.5 years
    { this.SubGrids=6; return(bb/4); } //3 month
    if (vv<bb*5) //5 years
    { this.SubGrids=6; return(bb/2); } //6 month
    if (vv<bb*10) //10 years
      return(bb); //1 year
    if (vv<bb*20) //20 years
      return(bb*2); //2 years
    if (vv<bb*50) //50 years
    { this.SubGrids=5; return(bb*5); } //5 years
    if (vv<bb*100) //100 years
    { this.SubGrids=5; return(bb*10); } //10 years
    if (vv<bb*200) //200 years
      return(bb*20); //20 years
    if (vv<bb*500) //500 years
    { this.SubGrids=5; return(bb*50); } //50 years
    this.SubGrids=5; return(bb*100); //100 years
  }
  bb/=2; //70 days
  if (vv>=bb) { this.SubGrids=7; return(bb/5); } //14 days
  bb/=2; //35 days
  if (vv>=bb) { this.SubGrids=7; return(bb/5); } //7 days
  bb/=7; bb*=4; //20 days
  if (vv>=bb) return(bb/5); //4 days
  bb/=2; //10 days
  if (vv>=bb) return(bb/5); //2 days
  bb/=2; //5 days
  if (vv>=bb) return(bb/5); //1 day
  bb/=2; //2.5 days
  if (vv>=bb) return(bb/5); //12 hours
  bb*=3; bb/=5; //1.5 day
  if (vv>=bb) { this.SubGrids=6; return(bb/6); } //6 hours
  bb/=2; //18 hours
  if (vv>=bb) { this.SubGrids=6; return(bb/6); } //3 hours
  bb*=2; bb/=3; //12 hours
  if (vv>=bb) return(bb/6); //2 hours
  bb/=2; //6 hours
  if (vv>=bb) return(bb/6); //1 hour
  bb/=2; //3 hours
  if (vv>=bb) { this.SubGrids=6; return(bb/6); } //30 mins
  bb/=2; //1.5 hours
  if (vv>=bb) { this.SubGrids=5; return(bb/6); } //15 mins
  bb*=2; bb/=3; //1 hour
  if (vv>=bb) { this.SubGrids=5; return(bb/6); } //10 mins
  bb/=3; //20 mins
  if (vv>=bb) { this.SubGrids=5; return(bb/4); } //5 mins
  bb/=2; //10 mins
  if (vv>=bb) return(bb/5); //2 mins
  bb/=2; //5 mins
  if (vv>=bb) return(bb/5); //1 min
  bb*=3; bb/=2; //3 mins
  if (vv>=bb) { this.SubGrids=6; return(bb/6); } //30 secs
  bb/=2; //1.5 mins
  if (vv>=bb) { this.SubGrids=5; return(bb/6); } //15 secs
  bb*=2; bb/=3; //1 min
  if (vv>=bb) { this.SubGrids=5; return(bb/6); } //10 secs
  bb/=3; //20 secs
  if (vv>=bb) { this.SubGrids=5; return(bb/4); } //5 secs
  bb/=2; //10 secs
  if (vv>=bb) return(bb/5); //2 secs
  return(bb/10); //1 sec
}
function _DayOfYear(dd,mm,yy) //Unused, you can use this for your own date format
{ DOM=new Array(31,28,31,30,31,30,31,31,30,31,30,31);
  var ii, nn=dd;
  for (ii=0; ii<mm-1; ii++) nn+=DOM[ii];
  if ((mm>2)&&(yy%4==0)) nn++;
  return(nn);
}
function _GetKWT(dd,mm,yy) //Unused, you can use this for your own date format 
{ //this is the implementation of DIN 1355, not of the american standard!   
  var ss=new Date(yy,0,1);
  var ww=ss.getDay(); //0=Sun,1=Mon,2=Tue,3=Wed,4=Thu,5=Fri,6=Sat
  ww=(ww+2)%7-3; //0=Mon,1=Tue,2=Wed,3=Thu,-3=Fri,-2=Sat,-1=Sun
  ww+=(_DayOfYear(dd,mm,yy)-1);
  if (ww<0) return(_GetKWT(31+dd,12,yy-1));
  if ((mm==12)&&(dd>28))
  { if (ww%7+29<=dd) return("01/"+eval(ww%7+1)); //31: Mon-Wed, 30: Mon-Tue, 29: Mon
  }
  ss=Math.floor(ww/7+1);
  if (ss<10) ss="0"+ss;
  return(ss+"/"+eval(ww%7+1));
}
function _DateFormat(vv, ii, ttype)
{ var yy, mm, dd, hh, nn, ss, vv_date=new Date(vv);
  Month=new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
  Weekday=new Array("Sun","Mon","Tue","Wed","Thu","Fri","Sat");
  if (ii>15*24*60*60*1000)
  { if (ii<365*24*60*60*1000)
    { vv_date.setTime(vv+15*24*60*60*1000);
      yy=vv_date.getUTCFullYear()%100;
      if (yy<10) yy="0"+yy;
      mm=vv_date.getUTCMonth()+1;
      if (ttype==5) ;//You can add your own date format here
      if (ttype==4) return(Month[mm-1]);
      if (ttype==3) return(Month[mm-1]+" "+yy);
      return(mm+"/"+yy);
    }
    vv_date.setTime(vv+183*24*60*60*1000);
    yy=vv_date.getUTCFullYear();
    return(yy);
  }
  vv_date.setTime(vv);
  yy=vv_date.getUTCFullYear();
  mm=vv_date.getUTCMonth()+1;
  dd=vv_date.getUTCDate();
  ww=vv_date.getUTCDay();
  hh=vv_date.getUTCHours();
  nn=vv_date.getUTCMinutes();
  ss=vv_date.getUTCSeconds();
  if (ii>=86400000)//1 day
  { if (ttype==5) ;//You can add your own date format here
    if (ttype==4) return(Weekday[ww]);
    if (ttype==3) return(mm+"/"+dd);
    return(dd+"."+mm+".");
  }
  if (ii>=21600000)//6 hours 
  { if (hh==0) 
    { if (ttype==5) ;//You can add your own date format here
      if (ttype==4) return(Weekday[ww]);
      if (ttype==3) return(mm+"/"+dd);
      return(dd+"."+mm+".");
    }
    else
    { if (ttype==5) ;//You can add your own date format here
      if (ttype==4) return((hh<=12) ? hh+"am" : hh%12+"pm");
      if (ttype==3) return((hh<=12) ? hh+"am" : hh%12+"pm");
      return(hh+":00");
    }
  }
  if (ii>=60000)//1 min
  { if (nn<10) nn="0"+nn;
    if (ttype==5) ;//You can add your own date format here
    if (ttype==4) return((hh<=12) ? hh+"."+nn+"am" : hh%12+"."+nn+"pm");
    if (nn=="00") nn="";
    else nn=":"+nn;
    if (ttype==3) return((hh<=12) ? hh+nn+"am" : hh%12+nn+"pm");
    if (nn=="") nn=":00";
    return(hh+nn);
  }
  if (ss<10) ss="0"+ss;
  return(nn+":"+ss);
}
function _GetXGrid()
{ var x0,i,j,l,x,r,dx,xr,invdifx,deltax;
  dx=(this.xmax-this.xmin);
  if (Math.abs(dx)>0)
  { invdifx=(this.right-this.left)/(this.xmax-this.xmin);
    if ((this.XScale==1)||(isNaN(this.XScale)))
    { r=1;
      while (Math.abs(dx)>=100) { dx/=10; r*=10; }
      while (Math.abs(dx)<10) { dx*=10; r/=10; }
      if (Math.abs(dx)>=50) { this.SubGrids=5; deltax=10*r*_sign(dx); }
      else
      { if (Math.abs(dx)>=20) { this.SubGrids=5; deltax=5*r*_sign(dx); }
        else { this.SubGrids=4; deltax=2*r*_sign(dx); }
      }
    }
    else deltax=this.DateInterval(Math.abs(dx))*_sign(dx);
    if (this.XGridDelta!=0) deltax=this.XGridDelta;
    if (this.XSubGrids!=0) this.SubGrids=this.XSubGrids;
    x=Math.floor(this.xmin/deltax)*deltax;
    i=0;
    this.XGrid[1]=deltax;
    if (deltax!=0) this.MaxGrids=Math.floor(Math.abs((this.xmax-this.xmin)/deltax))+2;
    else this.MaxGrids=0;
    for (j=this.MaxGrids; j>=-1; j--)
    { xr=x+j*deltax;
      x0=Math.round(this.left+(-this.xmin+xr)*invdifx);
      if ((x0>=this.left)&&(x0<=this.right))
      { if (i==0) this.XGrid[2]=xr;
        this.XGrid[0]=xr;
        i++;
      }
    }
  }
  return(this.XGrid);
}
function _GetYGrid()
{ var y0,i,j,l,y,r,dy,yr,invdify,deltay;
  dy=this.ymax-this.ymin;
  if (Math.abs(dy)>0)
  { invdify=(this.bottom-this.top)/(this.ymax-this.ymin);
    if ((this.YScale==1)||(isNaN(this.YScale)))
    { r=1;
      while (Math.abs(dy)>=100) { dy/=10; r*=10; }
      while (Math.abs(dy)<10) { dy*=10; r/=10; }
      if (Math.abs(dy)>=50) { this.SubGrids=5; deltay=10*r*_sign(dy); }
      else
      { if (Math.abs(dy)>=20) { this.SubGrids=5; deltay=5*r*_sign(dy); }
        else { this.SubGrids=4; deltay=2*r*_sign(dy); }
      }
    }
    else deltay=this.DateInterval(Math.abs(dy))*_sign(dy);
    if (this.YGridDelta!=0) deltay=this.YGridDelta;
    if (this.YSubGrids!=0) this.SubGrids=this.YSubGrids;
    y=Math.floor(this.ymax/deltay)*deltay;
    this.YGrid[1]=deltay;
    i=0;
    if (deltay!=0) this.MaxGrids=Math.floor(Math.abs((this.ymax-this.ymin)/deltay))+2;
    else this.MaxGrids=0;
    for (j=-1; j<=this.MaxGrids; j++)
    { yr=y-j*deltay;
      y0=Math.round(this.top+(this.ymax-yr)*invdify);
      if ((y0>=this.top)&&(y0<=this.bottom))
      { if (i==0) this.YGrid[2]=yr;
        this.YGrid[0]=yr;
        i++;
      }
    }
  }
  return(this.YGrid);
}
function _nvl(vv, rr)
{ if (vv==null) return(rr);
  var ss=String(vv);
  while (ss.search("'")>=0) ss=ss.replace("'","&#39;");
  return(ss);
}
function _cursor(aa)
{ if (aa)
  { if (_dSize==1) return("cursor:hand;");
    else  return("cursor:pointer;");
  }  
  return("");
}
function _GetArrayMin(aa)
{ var ii, mm=aa[0];
  for (ii=1; ii<aa.length; ii++)
  { if (mm>aa[ii]) mm=aa[ii];
  }
  return(mm);
}
function _GetArrayMax(aa)
{ var ii, mm=aa[0];
  for (ii=1; ii<aa.length; ii++)
  { if (mm<aa[ii]) mm=aa[ii];
  }
  return(mm);
}
function _IsImage(ss)
{ if (!ss) return(false);
  var tt=String(ss).toLowerCase().split(".");
  if (tt.length!=2) return(false);
  switch (tt[1])
  { case "gif": return(true);
    case "png": return(true);
    case "jpg": return(true);
    case "jpg": return(true);
    return(false);
  }  
}

function _Draw(theDrawColor, theTextColor, isScaleText, theTooltipText, theOnClickAction, theOnMouseoverAction, theOnMouseoutAction)
{ var x0,y0,i,j,itext,l,x,y,r,u,fn,dx,dy,xr,yr,invdifx,invdify,deltax,deltay,id=this.ID,lay=0,selObj="",divtext="",ii=0,oo,k,sub,sshift;
  var c151="&#151;", nbsp=(_IE)? "&nbsp;" : "";
  var EventActions="";
  if (_nvl(theOnClickAction,"")!="") EventActions+="onClick='"+_nvl(theOnClickAction,"")+"' ";
  if (_nvl(theOnMouseoverAction,"")!="") EventActions+="onMouseover='"+_nvl(theOnMouseoverAction,"")+"' ";
  if (_nvl(theOnMouseoutAction,"")!="") EventActions+="onMouseout='"+_nvl(theOnMouseoutAction,"")+"' ";
  lay--; 
  if (document.all) selObj=eval("_DiagramTarget.document.all."+id);
  else selObj=_DiagramTarget.document.getElementById(id);
  if (selObj) lay--;
  if (lay<-1)
    selObj.title=_nvl(theTooltipText,"");
  else
    _DiagramTarget.document.writeln("<div id='"+this.ID+"' title='"+_nvl(theTooltipText,"")+"'>"); 
  if (_IsImage(theDrawColor))
    divtext="<div id='"+this.ID+"i"+eval(ii++)+"' "+EventActions+"style='"+_cursor(theOnClickAction)+"position:absolute; left:"+eval(this.left)+"px; width:"+eval(this.right-this.left+_dSize)+"px; top:"+eval(this.top)+"px; height:"+eval(this.bottom-this.top+_dSize)+"px; color:"+theTextColor+"; border-style:solid; border-width:1px; z-index:"+this.zIndex+"'><img src='"+theDrawColor+"' width="+eval(this.right-this.left-1)+" height="+eval(this.bottom-this.top-1)+"></div>";
  else
    divtext="<div id='"+this.ID+"i"+eval(ii++)+"' "+EventActions+"style='"+_cursor(theOnClickAction)+"position:absolute; left:"+eval(this.left)+"px; width:"+eval(this.right-this.left+_dSize)+"px; top:"+eval(this.top)+"px; height:"+eval(this.bottom-this.top+_dSize)+"px; background-color:"+theDrawColor+"; color:"+theTextColor+"; border-style:solid; border-width:1px; z-index:"+this.zIndex+"'>&nbsp;</div>";  
  if ((this.XScale==1)||(isNaN(this.XScale)))
  { u="";
    fn="";
    if (isNaN(this.XScale))
    { if (this.XScale.substr(0,9)=="function ") fn=eval("window."+this.XScale.substr(9));
      else u=this.XScale;
    }
    dx=(this.xmax-this.xmin);
    if (Math.abs(dx)>0)
    { invdifx=(this.right-this.left)/(this.xmax-this.xmin);
      r=1;
      while (Math.abs(dx)>=100) { dx/=10; r*=10; }
      while (Math.abs(dx)<10) { dx*=10; r/=10; }
      if (Math.abs(dx)>=50) { this.SubGrids=5; deltax=10*r*_sign(dx); }
      else
      { if (Math.abs(dx)>=20) { this.SubGrids=5; deltax=5*r*_sign(dx); }
        else { this.SubGrids=4; deltax=2*r*_sign(dx); }
      }
      if (this.XGridDelta!=0) deltax=this.XGridDelta;
      if (this.XSubGrids!=0) this.SubGrids=this.XSubGrids;
      sub=deltax*invdifx/this.SubGrids;
      sshift=0;
      if ((this.XScalePosition=="top-left")||(this.XScalePosition=="bottom-left")) sshift=-Math.abs(deltax*invdifx/2);
      if ((this.XScalePosition=="top-right")||(this.XScalePosition=="bottom-right")) sshift=Math.abs(deltax*invdifx/2);
      x=Math.floor(this.xmin/deltax)*deltax;
      itext=0;
      if (deltax!=0) this.MaxGrids=Math.floor(Math.abs((this.xmax-this.xmin)/deltax))+2;
      else this.MaxGrids=0;
      for (j=this.MaxGrids; j>=-1; j--)
      { xr=x+j*deltax;
        x0=Math.round(this.left+(-this.xmin+xr)*invdifx);
        if (this.XSubGridColor)
        { for (k=1; k<this.SubGrids; k++)
          { if ((x0-k*sub>this.left+1)&&(x0-k*sub<this.right-1))
              divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' style='position:absolute; left:"+Math.round(x0-k*sub)+"px; top:"+eval(this.top+1)+"px; z-index:"+this.zIndex+"; width:1px; height:"+eval(this.bottom-this.top-1)+"px; background-color:"+this.XSubGridColor+"; font-size:1pt'></div>";
          }
          if (this.SubGrids==-1)
          for (k=0; k<8; k++)
          { if ((x0-this.logsub[k]*sub*_sign(deltax)>this.left+1)&&(x0-this.logsub[k]*sub*_sign(deltax)<this.right-1))
              divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' style='position:absolute; left:"+Math.round(x0-this.logsub[k]*sub*_sign(deltax))+"px; top:"+eval(this.top+1)+"px; z-index:"+this.zIndex+"; width:1px; height:"+eval(this.bottom-this.top-1)+"px; background-color:"+this.XSubGridColor+"; font-size:1pt'></div>";
          }
        }
        if ((x0>=this.left)&&(x0<=this.right))
        { itext++;
          if ((itext!=2)||(!isScaleText))
          { if (r>1) 
            { if (fn) l=fn(xr)+"";
              else l=xr+""+u; 
            }
            else 
            { if (fn) l=fn(Math.round(10*xr/r)/Math.round(10/r))+"";
              else l=Math.round(10*xr/r)/Math.round(10/r)+""+u; 
            }
            if (l.charAt(0)==".") l="0"+l;
            if (l.substr(0,2)=="-.") l="-0"+l.substr(1,100);
          }
          else l=this.xtext;
          if (this.XScalePosition.substr(0,3)!="top")
          { if ((x0+sshift>=this.left)&&(x0+sshift<=this.right))
              divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' align=center style='position:absolute; left:"+eval(x0-50+sshift)+"px; width:102px; top:"+eval(this.bottom+8)+"px; color:"+theTextColor+";"+this.Font+" z-index:"+this.zIndex+"'>"+l+"</div>";
            divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' style='position:absolute; left:"+x0+"px; top:"+eval(this.bottom-5)+"px; z-index:"+this.zIndex+"; width:1px; height:12px; background-color:"+theTextColor+"; font-size:1pt'></div>";
          }
          else
          { if ((x0+sshift>=this.left)&&(x0+sshift<=this.right))
              divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' align=center style='position:absolute; left:"+eval(x0-50+sshift)+"px; width:102px; top:"+eval(this.top-24)+"px; color:"+theTextColor+";"+this.Font+" z-index:"+this.zIndex+"'>"+l+"</div>";
            divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' style='position:absolute; left:"+x0+"px; top:"+eval(this.top-5)+"px; z-index:"+this.zIndex+"; width:1px; height:12px; background-color:"+theTextColor+"; font-size:1pt'></div>";
          }
          if ((this.XGridColor)&&(x0>this.left)&&(x0<this.right)) divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' style='position:absolute; left:"+x0+"px; top:"+eval(this.top+1)+"px; z-index:"+this.zIndex+"; width:1px; height:"+eval(this.bottom-this.top-1)+"px; background-color:"+this.XGridColor+"; font-size:1pt'></div>";
        }
      }
    }
  }
  if ((!isNaN(this.XScale))&&(this.XScale>1))
  { dx=(this.xmax-this.xmin);
    if (Math.abs(dx)>0)
    { invdifx=(this.right-this.left)/(this.xmax-this.xmin);
      deltax=this.DateInterval(Math.abs(dx))*_sign(dx);
      if (this.XGridDelta!=0) deltax=this.XGridDelta;
      if (this.XSubGrids!=0) this.SubGrids=this.XSubGrids;
      sub=deltax*invdifx/this.SubGrids;
      sshift=0;
      if ((this.XScalePosition=="top-left")||(this.XScalePosition=="bottom-left")) sshift=-Math.abs(deltax*invdifx/2);
      if ((this.XScalePosition=="top-right")||(this.XScalePosition=="bottom-right")) sshift=Math.abs(deltax*invdifx/2);            
      x=Math.floor(this.xmin/deltax)*deltax;
      itext=0;
      if (deltax!=0) this.MaxGrids=Math.floor(Math.abs((this.xmax-this.xmin)/deltax))+2;
      else this.MaxGrids=0;
      for (j=this.MaxGrids; j>=-2; j--)
      { xr=x+j*deltax;
        x0=Math.round(this.left+(-this.xmin+x+j*deltax)*invdifx);
        if (this.XSubGridColor)
        { for (k=1; k<this.SubGrids; k++)
          { if ((x0-k*sub>this.left+1)&&(x0-k*sub<this.right-1))
              divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' style='position:absolute; left:"+Math.round(x0-k*sub)+"px; top:"+eval(this.top+1)+"px; z-index:"+this.zIndex+"; width:1px; height:"+eval(this.bottom-this.top-1)+"px; background-color:"+this.XSubGridColor+"; font-size:1pt'></div>";
          }
        }  
        if ((x0>=this.left)&&(x0<=this.right))
        { itext++;
          if ((itext!=2)||(!isScaleText)) l=_DateFormat(xr, Math.abs(deltax), this.XScale);
          else l=this.xtext;
          if (this.XScalePosition.substr(0,3)!="top")
          { if ((x0+sshift>=this.left)&&(x0+sshift<=this.right))
              divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' align=center style='position:absolute; left:"+eval(x0-50+sshift)+"px; width:102px; top:"+eval(this.bottom+8)+"px; color:"+theTextColor+";"+this.Font+" z-index:"+this.zIndex+"'>"+l+"</div>";
            divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' style='position:absolute; left:"+x0+"px; top:"+eval(this.bottom-5)+"px; z-index:"+this.zIndex+"; width:1px; height:12px; background-color:"+theTextColor+"; font-size:1pt'></div>";
          }
          else
          { if ((x0+sshift>=this.left)&&(x0+sshift<=this.right))
              divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' align=center style='position:absolute; left:"+eval(x0-50+sshift)+"px; width:102px; top:"+eval(this.top-24)+"px; color:"+theTextColor+";"+this.Font+" z-index:"+this.zIndex+"'>"+l+"</div>";
            divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' style='position:absolute; left:"+x0+"px; top:"+eval(this.top-5)+"px; z-index:"+this.zIndex+"; width:1px; height:12px; background-color:"+theTextColor+"; font-size:1pt'></div>";
          }
          if ((this.XGridColor)&&(x0>this.left)&&(x0<this.right)) divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' style='position:absolute; left:"+x0+"px; top:"+eval(this.top+1)+"px; z-index:"+this.zIndex+"; width:1px; height:"+eval(this.bottom-this.top-1)+"px; background-color:"+this.XGridColor+"; font-size:1pt'></div>";
        }
      }
    }
  }
  if ((this.YScale==1)||(isNaN(this.YScale)))
  { u="";
    fn="";
    if (isNaN(this.YScale))
    { if (this.YScale.substr(0,9)=="function ") fn=eval("window."+this.YScale.substr(9));
      else u=this.YScale;
    }
    dy=this.ymax-this.ymin;
    if (Math.abs(dy)>0)
    { invdify=(this.bottom-this.top)/(this.ymax-this.ymin);
      r=1;
      while (Math.abs(dy)>=100) { dy/=10; r*=10; }
      while (Math.abs(dy)<10) { dy*=10; r/=10; }
      if (Math.abs(dy)>=50) { this.SubGrids=5; deltay=10*r*_sign(dy); }
      else
      { if (Math.abs(dy)>=20) { this.SubGrids=5; deltay=5*r*_sign(dy); }
        else { this.SubGrids=4; deltay=2*r*_sign(dy); }
      }      
      if (this.YGridDelta!=0) deltay=this.YGridDelta;
      if (this.YSubGrids!=0) this.SubGrids=this.YSubGrids;
      sub=deltay*invdify/this.SubGrids;
      sshift=0;
      if ((this.YScalePosition=="left-top")||(this.YScalePosition=="right-top")) sshift=-Math.abs(deltay*invdify/2);
      if ((this.YScalePosition=="left-bottom")||(this.YScalePosition=="right-bottom")) sshift=Math.abs(deltay*invdify/2);  
      y=Math.floor(this.ymax/deltay)*deltay;
      itext=0;
      if (deltay!=0) this.MaxGrids=Math.floor(Math.abs((this.ymax-this.ymin)/deltay))+2;
      else this.MaxGrids=0;
      for (j=-1; j<=this.MaxGrids; j++)
      { yr=y-j*deltay;
        y0=Math.round(this.top+(this.ymax-yr)*invdify);
        if (this.YSubGridColor)
        { for (k=1; k<this.SubGrids; k++)
          { if ((y0+k*sub>this.top+1)&&(y0+k*sub<this.bottom-1))
              divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' style='position:absolute; left:"+eval(this.left+1)+"px; top:"+Math.round(y0+k*sub)+"px; z-index:"+this.zIndex+"; height:1px; width:"+eval(this.right-this.left-1)+"px; background-color:"+this.YSubGridColor+"; font-size:1pt;line-height:1pt'>"+nbsp+"</div>";
          }
          if (this.SubGrids==-1)
          { for (k=0; k<8; k++)
            { if ((y0+this.logsub[k]*sub*_sign(deltay)>this.top+1)&&(y0+this.logsub[k]*sub*_sign(deltay)<this.bottom-1))
                divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' style='position:absolute; left:"+eval(this.left+1)+"px; top:"+Math.round(y0+this.logsub[k]*sub*_sign(deltay))+"px; z-index:"+this.zIndex+"; height:1px; width:"+eval(this.right-this.left-1)+"px; background-color:"+this.YSubGridColor+"; font-size:1pt;line-height:1pt'>"+nbsp+"</div>";
            }
          }
        }
        if ((y0>=this.top)&&(y0<=this.bottom))
        { itext++;
          if ((itext!=2)||(!isScaleText))
          { if (r>1)
            { if (fn) l=fn(yr)+"";
              else l=yr+""+u;
            }   
            else
            { if (fn) l=fn(Math.round(10*yr/r)/Math.round(10/r))+"";
              else l=Math.round(10*yr/r)/Math.round(10/r)+""+u;
            }  
            if (l.charAt(0)==".") l="0"+l;
            if (l.substr(0,2)=="-.") l="-0"+l.substr(1,100);
          }
          else l=this.ytext;
          if (this.YScalePosition.substr(0,5)!="right")
          { if ((y0+sshift>=this.top)&&(y0+sshift<=this.bottom))
              divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' align=right style='position:absolute; left:"+eval(this.left-100)+"px; width:89px; top:"+eval(y0-9+sshift)+"px; color:"+theTextColor+";"+this.Font+" z-index:"+this.zIndex+"'>"+l+"</div>";
            divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' style='position:absolute; left:"+eval(this.left-5)+"px; top:"+eval(y0)+"px; z-index:"+this.zIndex+"; height:1px; width:11px; background-color:"+theTextColor+"; font-size:1pt;line-height:1pt'>"+nbsp+"</div>";
          }
          else
          { if ((y0+sshift>=this.top)&&(y0+sshift<=this.bottom))
              divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' align=left style='position:absolute; left:"+eval(this.right+10)+"px; width:89px; top:"+eval(y0-9+sshift)+"px; color:"+theTextColor+";"+this.Font+" z-index:"+this.zIndex+"'>"+l+"</div>";
            divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' style='position:absolute; left:"+eval(this.right-5)+"px; top:"+eval(y0)+"px; z-index:"+this.zIndex+"; height:1px; width:11px; background-color:"+theTextColor+"; font-size:1pt;line-height:1pt'>"+nbsp+"</div>";
          }
          if ((this.YGridColor)&&(y0>this.top)&&(y0<this.bottom)) divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' style='position:absolute; left:"+eval(this.left+1)+"px; top:"+eval(y0)+"px; z-index:"+this.zIndex+"; height:1px; width:"+eval(this.right-this.left-1)+"px; background-color:"+this.YGridColor+"; font-size:1pt;line-height:1pt'>"+nbsp+"</div>";
        }
      }
    }
  }
  if ((!isNaN(this.YScale))&&(this.YScale>1))
  { dy=this.ymax-this.ymin;
    if (Math.abs(dy)>0)
    { invdify=(this.bottom-this.top)/(this.ymax-this.ymin);
      deltay=this.DateInterval(Math.abs(dy))*_sign(dy);
      if (this.YGridDelta!=0) deltay=this.YGridDelta;
      if (this.YSubGrids!=0) this.SubGrids=this.YSubGrids;
      sub=deltay*invdify/this.SubGrids;
      sshift=0;
      if ((this.YScalePosition=="left-top")||(this.YScalePosition=="right-top")) sshift=-Math.abs(deltay*invdify/2);
      if ((this.YScalePosition=="left-bottom")||(this.YScalePosition=="right-bottom")) sshift=Math.abs(deltay*invdify/2);  
      y=Math.floor(this.ymax/deltay)*deltay;
      itext=0;
      if (deltay!=0) this.MaxGrids=Math.floor(Math.abs((this.ymax-this.ymin)/deltay))+2;
      else this.MaxGrids=0;
      for (j=-2; j<=this.MaxGrids; j++)
      { yr=y-j*deltay;
        y0=Math.round(this.top+(this.ymax-y+j*deltay)*invdify);
        if (this.YSubGridColor)
        { for (k=1; k<this.SubGrids; k++)
          { if ((y0+k*sub>this.top+1)&&(y0+k*sub<this.bottom-1))
              divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' style='position:absolute; left:"+eval(this.left+1)+"px; top:"+Math.round(y0+k*sub)+"px; z-index:"+this.zIndex+"; height:1px; width:"+eval(this.right-this.left-1)+"px; background-color:"+this.YSubGridColor+"; font-size:1pt;line-height:1pt'>"+nbsp+"</div>";
          }
        }
        if ((y0>=this.top)&&(y0<=this.bottom))
        { itext++;
          if ((itext!=2)||(!isScaleText)) l=_DateFormat(yr, Math.abs(deltay), this.YScale);
          else l=this.ytext;
          if (this.YScalePosition.substr(0,5)!="right")
          { if ((y0+sshift>=this.top)&&(y0+sshift<=this.bottom))
              divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' align=right style='position:absolute; left:"+eval(this.left-100)+"px; width:89px; top:"+eval(y0-9+sshift)+"px; color:"+theTextColor+";"+this.Font+" z-index:"+this.zIndex+"'>"+l+"</div>";
            divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' style='position:absolute; left:"+eval(this.left-5)+"px; top:"+eval(y0)+"px; z-index:"+this.zIndex+"; height:1px; width:11px; background-color:"+theTextColor+"; font-size:1pt;line-height:1pt'>"+nbsp+"</div>";
          }
          else
          { if ((y0+sshift>=this.top)&&(y0+sshift<=this.bottom))
              divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' align=left style='position:absolute; left:"+eval(this.right+10)+"px; width:89px; top:"+eval(y0-9+sshift)+"px; color:"+theTextColor+";"+this.Font+" z-index:"+this.zIndex+"'>"+l+"</div>";
            divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' style='position:absolute; left:"+eval(this.right-5)+"px; top:"+eval(y0)+"px; z-index:"+this.zIndex+"; height:1px; width:11px; background-color:"+theTextColor+"; font-size:1pt;line-height:1pt'>"+nbsp+"</div>";
          }
          if ((this.YGridColor)&&(y0>this.top)&&(y0<this.bottom)) divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' style='position:absolute; left:"+eval(this.left+1)+"px; top:"+eval(y0)+"px; z-index:"+this.zIndex+"; height:1px; width:"+eval(this.right-this.left-1)+"px; background-color:"+this.YGridColor+"; font-size:1pt;line-height:1pt'>"+nbsp+"</div>";
        }
      }
    }
  }
  if (this.XScalePosition.substr(0,3)!="top") 
    divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' align=center style='position:absolute; left:"+this.left+"px; width:"+eval(this.right-this.left)+"px; top:"+eval(this.top-20)+"px; color:"+theTextColor+";"+this.Font+" z-index:"+this.zIndex+"'>"+this.title+"</div>";
  else
    divtext+="<div id='"+this.ID+"i"+eval(ii++)+"' align=center style='position:absolute; left:"+this.left+"px; width:"+eval(this.right-this.left)+"px; top:"+eval(this.bottom+4)+"px; color:"+theTextColor+";"+this.Font+" z-index:"+this.zIndex+"'>"+this.title+"</div>";  
  if (lay<-1)
    selObj.innerHTML=divtext;
  else
    _DiagramTarget.document.writeln(divtext+"</div>");
}

/*
*/
function _SetDotColor(theColor)
{ this.Color=theColor;
  var tt="", selObj;
  if (document.all) selObj=eval("_DiagramTarget.document.all."+this.ID);
  else selObj=_DiagramTarget.document.getElementById(this.ID);
  if (isNaN(this.Type))
  { tt+="<div style='position:absolute;left:0px;top:0px;width:"+this.Size+"px;height:"+this.Size+"px;background-color:"+theColor+";font-size:1pt;line-height:1pt;'>";
    tt+="<img src='"+theType+"' width="+this.Size+"px height="+this.Size+"px style='vertical-align:bottom'></div>";
  } 
  else
  { if (this.Type%6==0)
    { tt+="<div style='position:absolute;left:1px;top:"+Math.round(this.Size/4+0.3)+"px;width:"+eval(this.Size-1)+"px;height:"+eval(this.Size+1-2*Math.round(this.Size/4+0.3))+"px;background-color:"+theColor+";font-size:1pt'></div>";
      tt+="<div style='position:absolute;left:"+Math.round(this.Size/4+0.3)+"px;top:1px;width:"+eval(this.Size+1-2*Math.round(this.Size/4+0.3))+"px;height:"+eval(this.Size-1)+"px;background-color:"+theColor+";font-size:1pt'></div>";
    }
    if (this.Type%6==1)
    { tt+="<div style='position:absolute;left:"+Math.round(this.Size/2-this.Size/8)+"px;top:0px;width:"+Math.round(this.Size/4)+"px;height:"+this.Size+"px;background-color:"+theColor+";font-size:1pt'></div>";
      tt+="<div style='position:absolute;left:0px;top:"+Math.round(this.Size/2-this.Size/8)+"px;width:"+this.Size+"px;height:"+Math.round(this.Size/4)+"px;background-color:"+theColor+";font-size:1pt'></div>";
    }
    if (this.Type%6==2)
      tt+="<div style='position:absolute;left:0px;top:0px;width:"+this.Size+"px;height:"+this.Size+"px;background-color:"+theColor+";font-size:1pt'></div>";
    if (this.Type%6==3)
    { tt+="<div style='position:absolute;left:0px;top:"+Math.round(this.Size/4)+"px;width:"+this.Size+"px;height:"+Math.round(this.Size/2)+"px;background-color:"+theColor+";font-size:1pt'></div>";
      tt+="<div style='position:absolute;left:"+Math.round(this.Size/2-this.Size/8)+"px;top:0px;width:"+Math.round(this.Size/4)+"px;height:"+this.Size+"px;background-color:"+theColor+";font-size:1pt'></div>";
    }
    if (this.Type%6==4)
    { tt+="<div style='position:absolute;left:"+Math.round(this.Size/4)+"px;top:0px;width:"+Math.round(this.Size/2)+"px;height:"+this.Size+"px;background-color:"+theColor+";font-size:1pt'></div>";
      tt+="<div style='position:absolute;left:0px;top:"+Math.round(this.Size/2-this.Size/8)+"px;width:"+this.Size+"px;height:"+Math.round(this.Size/4)+"px;background-color:"+theColor+";font-size:1pt'></div>";
    }
    if (this.Type%6==5)
    { if (_dSize==1)
        tt+="<div style='position:absolute;left:0px;top:0px;width:"+this.Size+"px;height:"+this.Size+"px;border-width:"+Math.round(this.Size/6)+"px; border-style:solid;border-color:"+theColor+";font-size:1pt;line-height:1pt'></div>";
      else
        tt+="<div style='position:absolute;left:0px;top:0px;width:"+Math.round(this.Size-this.Size/4)+"px;height:"+Math.round(this.Size-this.Size/4)+"px;border-width:"+Math.round(this.Size/6)+"px; border-style:solid;border-color:"+theColor+";font-size:1pt;line-height:1pt'></div>";
    }
  }  
  selObj.innerHTML=tt;
}
function _DotMoveTo(theX, theY)
{ var id=this.ID, selObj;
  if (!isNaN(parseInt(theX))) this.X=theX;
  if (!isNaN(parseInt(theY))) this.Y=theY;
  if (document.all) selObj=eval("_DiagramTarget.document.all."+id);
  else selObj=_DiagramTarget.document.getElementById(id);
  with (selObj.style)
  { if (!isNaN(parseInt(theX))) left=eval(theX-this.dX)+"px";
    if (!isNaN(parseInt(theY))) top=eval(theY-this.dY)+"px";
    visibility="visible";
  }
}

function Pixel(theX, theY, theColor)
{ this.ID="Pix"+_N_Pix; _N_Pix++; _zIndex++;
  this.left=theX;
  this.top=theY;
  this.dX=0;
  this.dY=0;
  this.Color=theColor;
  this.SetVisibility=_SetVisibility;
  this.SetColor=_SetPixelColor;  
  this.MoveTo=_DotMoveTo;
  this.Delete=_Delete;
  _DiagramTarget.document.writeln("<div id='"+this.ID+"' style='position:absolute;left:"+eval(theX-this.dX)+"px;top:"+eval(theY-this.dY)+"px;width:1px;height:2px;background-color:"+theColor+";font-size:1pt;z-index:"+_zIndex+"'></div>");
  return(this);
}
function _SetPixelColor(theColor)
{ var id=this.ID, selObj;
  this.Color=theColor;
  if (document.all) selObj=eval("_DiagramTarget.document.all."+id);
  else selObj=_DiagramTarget.document.getElementById(id);
  selObj.style.backgroundColor=theColor;
}
function _SetVisibility(isVisible)
{ var ll, id=this.ID, selObj;
  if (document.all)
  { selObj=eval("_DiagramTarget.document.all."+id);
    if (isVisible) selObj.style.display="inline";
    else selObj.style.display="none";
  }
  else
  { selObj=_DiagramTarget.document.getElementById(id);
    if (isVisible) selObj.style.display="inline";
    else selObj.style.display="none";
    if (id.substr(0,3)=='Dia')
    { var ii=0;
      selObj=_DiagramTarget.document.getElementById(id+'i'+eval(ii++));
      while (selObj!=null)
      { if (isVisible) selObj.style.display="inline";
        else selObj.style.display="none";
        selObj=_DiagramTarget.document.getElementById(id+'i'+eval(ii++));
      }
    }
  }
}
function _SetTitle(theTitle)
{ var id=this.ID, selObj;
  if (document.all) selObj=eval("_DiagramTarget.document.all."+id);
  else selObj=_DiagramTarget.document.getElementById(id);
  selObj.title=theTitle;
}
function _MoveTo(theLeft, theTop)
{ var id=this.ID, selObj, ww=0;
  if (this.BorderWidth) ww=this.BorderWidth;
  if (!isNaN(parseInt(theLeft))) this.left=theLeft;
  if (!isNaN(parseInt(theTop))) this.top=theTop;
  if (document.all) selObj=eval("_DiagramTarget.document.all."+id);
  else selObj=_DiagramTarget.document.getElementById(id);
  with (selObj.style)
  { if (!isNaN(parseInt(theLeft))) left=theLeft+"px";
    if (!isNaN(parseInt(theTop))) top=theTop+"px";
    if (this.height<=2*ww) visibility="hidden";
    else visibility="visible";
  }
}
function _ResizeTo(theLeft, theTop, theWidth, theHeight)
{ var id=this.ID, selObj, ww=0;
  if (this.BorderWidth) ww=this.BorderWidth;
  if (!isNaN(parseInt(theLeft))) this.left=theLeft;
  if (!isNaN(parseInt(theTop))) this.top=theTop;
  if (!isNaN(parseInt(theWidth))) this.width=theWidth;
  if (!isNaN(parseInt(theHeight))) this.height=theHeight;
  if (_IsImage(this.Text)) this.SetText(this.Text);
  if (document.all) selObj=eval("_DiagramTarget.document.all."+id);
  else selObj=_DiagramTarget.document.getElementById(id);
  with (selObj.style)
  { if (!isNaN(parseInt(theLeft))) left=theLeft+"px";
    if (!isNaN(parseInt(theTop))) top=theTop+"px";
    if (!isNaN(parseInt(theWidth))) width=eval(theWidth-ww+ww*_dSize)+"px";
    if (!isNaN(parseInt(theHeight))) height=eval(theHeight-ww+ww*_dSize)+"px";
    if (this.height<=2*ww) visibility="hidden";
    else visibility="visible";
  }
}
function _Delete()
{ var id=this.ID, selObj;
  if (document.all)
  { selObj=eval("_DiagramTarget.document.all."+id);
    selObj.outerHTML="";
  }
  else
  { selObj=_DiagramTarget.document.getElementById(id); 
    selObj.parentNode.removeChild(selObj);
  }
}
function _SetColor(theColor)
{ this.Color=theColor;
  if ((theColor!="")&&(theColor.length<this.Color.length)) this.Color="#"+theColor;
  else this.Color=theColor;
  this.ResizeTo("", "", "", "");
}
//You can delete the following 3 functions, if you do not use Line objects
function Line(theX0, theY0, theX1, theY1, theColor, theSize, theTooltipText, theOnClickAction, theOnMouseoverAction, theOnMouseoutAction)
{ this.ID="Line"+_N_Line; _N_Line++; _zIndex++;
  this.X0=theX0;
  this.Y0=theY0;
  this.X1=theX1;
  this.Y1=theY1;
  this.Color=theColor;
  if ((theColor!="")&&(theColor.length==6)) this.Color="#"+theColor;
  this.Size=Number(_nvl(theSize,1));
  this.Cursor=_cursor(theOnClickAction);
  this.SetVisibility=_SetVisibility;
  this.SetColor=_SetColor;
  this.SetTitle=_SetTitle;
  this.MoveTo=_LineMoveTo;
  this.ResizeTo=_LineResizeTo;
  this.Delete=_Delete;
  this.EventActions="";
  if (_nvl(theOnClickAction,"")!="") this.EventActions+="onClick='"+_nvl(theOnClickAction,"")+"' ";
  if (_nvl(theOnMouseoverAction,"")!="") this.EventActions+="onMouseover='"+_nvl(theOnMouseoverAction,"")+"' ";
  if (_nvl(theOnMouseoutAction,"")!="") this.EventActions+="onMouseout='"+_nvl(theOnMouseoutAction,"")+"' ";
  var xx0, yy0, xx1, yy1, ll, rr, tt, bb, ww, hh, ccl, ccr, cct, ccb;
  var ss2=Math.floor(this.Size/2), nbsp=(_IE)? "&nbsp;" : "";
  var ddir=(((this.Y1>this.Y0)&&(this.X1>this.X0))||((this.Y1<this.Y0)&&(this.X1<this.X0))) ? true : false;
  if (theX0<=theX1) { ll=theX0; rr=theX1; }
  else { ll=theX1; rr=theX0; }
  if (theY0<=theY1) { tt=theY0; bb=theY1; }
  else { tt=theY1; bb=theY0; }
  ww=rr-ll; hh=bb-tt;
  _DiagramTarget.document.write("<div id='"+this.ID+"' style='visibility:hidden;position:absolute;left:"+eval(ll-ss2)+"px;top:"+eval(tt-ss2)+"px; width:"+eval(ww+this.Size)+"px; height:"+eval(hh+this.Size)+"px; z-index:"+_zIndex+";' title='"+_nvl(theTooltipText,"")+"'>");
  if ((ww==0)||(hh==0))
    _DiagramTarget.document.write("<div "+this.EventActions+"style='visibility:visible;"+_cursor(theOnClickAction)+"position:absolute;left:0px;top:0px;width:"+eval(ww+this.Size)+"px;height:"+eval(hh+this.Size)+"px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>");
  else
  { if (ww>hh)
    { ccr=0;
      cct=0;
      while (ccr<ww)
      { ccl=ccr;
        while ((2*ccr*hh<=(2*cct+1)*ww)&&(ccr<=ww)) ccr++;
        if (ddir)
          _DiagramTarget.document.write("<div "+this.EventActions+"style='visibility:visible;"+_cursor(theOnClickAction)+"position:absolute;left:"+ccl+"px;top:"+cct+"px;width:"+eval(ccr-ccl+this.Size)+"px;height:"+this.Size+"px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>");
        else
          _DiagramTarget.document.write("<div "+this.EventActions+"style='visibility:visible;"+_cursor(theOnClickAction)+"position:absolute;left:"+eval(ww-ccr)+"px;top:"+cct+"px;width:"+eval(ccr-ccl+this.Size)+"px;height:"+this.Size+"px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>");
        cct++;
      }
    }
    else
    { ccb=0;
      ccl=0;
      while (ccb<hh)
      { cct=ccb;
        while ((2*ccb*ww<=(2*ccl+1)*hh)&&(ccb<hh)) ccb++;
        if (ddir)
          _DiagramTarget.document.write("<div "+this.EventActions+"style='visibility:visible;"+_cursor(theOnClickAction)+"position:absolute;left:"+ccl+"px;top:"+cct+"px;width:"+this.Size+"px;height:"+eval(ccb-cct+this.Size)+"px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>");
        else
          _DiagramTarget.document.write("<div "+this.EventActions+"style='visibility:visible;"+_cursor(theOnClickAction)+"position:absolute;left:"+eval(ww-ccl)+"px;top:"+cct+"px;width:"+this.Size+"px;height:"+eval(ccb-cct+this.Size)+"px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>");
        ccl++;
      }
    }
  }           
  _DiagramTarget.document.writeln("</div>");
  return(this);
}
function _LineResizeTo(theX0, theY0, theX1, theY1)
{ var xx0, yy0, xx1, yy1, ll, rr, tt, bb, ww, hh, ccl, ccr, cct, ccb, id=this.ID,selObj="",divtext="";
  var ss2=Math.floor(this.Size/2), nbsp=(_IE)? "&nbsp;" : "";
  if (!isNaN(parseInt(theX0))) this.X0=theX0;
  if (!isNaN(parseInt(theY0))) this.Y0=theY0;
  if (!isNaN(parseInt(theX1))) this.X1=theX1;
  if (!isNaN(parseInt(theY1))) this.Y1=theY1;
  var ddir=(((this.Y1>this.Y0)&&(this.X1>this.X0))||((this.Y1<this.Y0)&&(this.X1<this.X0))) ? true : false;
  if (this.X0<=this.X1) { ll=this.X0; rr=this.X1; }
  else { ll=this.X1; rr=this.X0; }
  if (this.Y0<=this.Y1) { tt=this.Y0; bb=this.Y1; }
  else { tt=this.Y1; bb=this.Y0; }
  ww=rr-ll; hh=bb-tt;
  if (document.all) selObj=eval("_DiagramTarget.document.all."+id);
  else selObj=_DiagramTarget.document.getElementById(id);
  with (selObj.style)
  { left=eval(ll-ss2)+"px";
    top=eval(tt-ss2)+"px";
    width=eval(ww+this.Size)+"px";
    height=eval(hh+this.Size)+"px";
  }
  if ((ww==0)||(hh==0))
    divtext+="<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:0px;top:0px;width:"+eval(ww+this.Size)+"px;height:"+eval(hh+this.Size)+"px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>";
  else
  { if (ww>hh)
    { ccr=0;
      cct=0;
      while (ccr<ww)
      { ccl=ccr;
        while ((2*ccr*hh<=(2*cct+1)*ww)&&(ccr<=ww)) ccr++;
        if (ddir)
          divtext+="<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:"+ccl+"px;top:"+cct+"px;width:"+eval(ccr-ccl+this.Size)+"px;height:"+this.Size+"px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>";
        else
          divtext+="<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:"+eval(ww-ccr)+"px;top:"+cct+"px;width:"+eval(ccr-ccl+this.Size)+"px;height:"+this.Size+"px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>";
        cct++;
      }
    }
    else
    { ccb=0;
      ccl=0;
      while (ccb<hh)
      { cct=ccb;
        while ((2*ccb*ww<=(2*ccl+1)*hh)&&(ccb<hh)) ccb++;
        if (ddir)
          divtext+="<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:"+ccl+"px;top:"+cct+"px;width:"+this.Size+"px;height:"+eval(ccb-cct+this.Size)+"px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>";
        else
          divtext+="<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:"+eval(ww-ccl)+"px;top:"+cct+"px;width:"+this.Size+"px;height:"+eval(ccb-cct+this.Size)+"px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>";
        ccl++;
      }
    }
  } 
  selObj.innerHTML=divtext;
}
function _LineMoveTo(theLeft, theTop)
{ var id=this.ID, selObj;
  var ss2=Math.floor(this.Size/2);
  if (document.all) selObj=eval("_DiagramTarget.document.all."+id);
  else selObj=_DiagramTarget.document.getElementById(id);
  with (selObj.style)
  { if (!isNaN(parseInt(theLeft))) left=eval(theLeft-ss2)+"px";
    if (!isNaN(parseInt(theTop))) top=eval(theTop-ss2)+"px";
    visibility="visible";
  }
}
//You can delete the following 2 functions, if you do not use Area objects
/*function Area(theX0, theY0, theX1, theY1, theColor, theBase, theTooltipText, theOnClickAction, theOnMouseoverAction, theOnMouseoutAction)
{ this.ID="Area"+_N_Area; _N_Area++; _zIndex++;
  this.X0=theX0;
  this.Y0=theY0;
  this.X1=theX1;
  this.Y1=theY1;
  this.Color=theColor;
  if ((theColor!="")&&(theColor.length==6)) this.Color="#"+theColor;
  this.Base=theBase;
  this.Cursor=_cursor(theOnClickAction);
  this.SetVisibility=_SetVisibility;
  this.SetColor=_SetColor;  
  this.SetTitle=_SetTitle;
  this.MoveTo=_MoveTo;
  this.ResizeTo=_AreaResizeTo;
  this.Delete=_Delete;
  this.EventActions="";
  if (_nvl(theOnClickAction,"")!="") this.EventActions+="onClick='"+_nvl(theOnClickAction,"")+"' ";
  if (_nvl(theOnMouseoverAction,"")!="") this.EventActions+="onMouseover='"+_nvl(theOnMouseoverAction,"")+"' ";
  if (_nvl(theOnMouseoutAction,"")!="") this.EventActions+="onMouseout='"+_nvl(theOnMouseoutAction,"")+"' ";
  var ii, dd, ll, rr, tt, bb, ww, hh, nbsp=(_IE)? "&nbsp;" : "";
  if (theX0<=theX1) { ll=Math.round(theX0); rr=Math.round(theX1); }
  else { ll=Math.round(theX1); rr=Math.round(theX0); }
  if (theY0<=theY1) { tt=Math.round(theY0); bb=Math.round(theY1); }
  else { tt=Math.round(theY1); bb=Math.round(theY0); }
  ww=rr-ll; hh=bb-tt;
  if (theBase<=tt)
    _DiagramTarget.document.write("<div id='"+this.ID+"' style='position:absolute;left:"+ll+"px;top:"+theBase+"px; width:"+ww+"px; height:"+hh+"px; z-index:"+_zIndex+"; font-size:1pt; line-height:1pt;' title='"+_nvl(theTooltipText,"")+"'>");
  else
    _DiagramTarget.document.write("<div id='"+this.ID+"' style='position:absolute;left:"+ll+"px;top:"+tt+"px; width:"+ww+"px; height:"+Math.max(hh, theBase-tt)+"px; z-index:"+_zIndex+"; font-size:1pt; line-height:1pt;' title='"+_nvl(theTooltipText,"")+"'>");
  if (theBase<=tt)
  { if ((theBase<tt)&&(ww>0))
    { _DiagramTarget.document.write("<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:0px;top:0px;width:"+ww+"px;height:"+eval(tt-theBase)+"px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>");
    }
    if (((theY0<theY1)&&(theX0<theX1))||((theY0>theY1)&&(theX0>theX1)))
    { for (ii=0; ii<hh; ii++)
        _DiagramTarget.document.write("<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:"+Math.round(ww*(ii+0.5)/hh)+"px;top:"+eval(tt-theBase+ii)+"px;width:"+(ww-Math.round(ww*(ii+0.5)/hh))+"px;height:1px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>");
    }
    if (((theY0>theY1)&&(theX0<theX1))||((theY0<theY1)&&(theX0>theX1)))
    { for (ii=0; ii<hh; ii++)
        _DiagramTarget.document.write("<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:0px;top:"+eval(tt-theBase+ii)+"px;width:"+(ww-Math.round(ww*(ii+0.5)/hh))+"px;height:1px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>");
    }
  }
  if ((theBase>tt)&&(theBase<bb))
  { dd=Math.round((theBase-tt)/hh*ww);
    if (((theY0<theY1)&&(theX0<theX1))||((theY0>theY1)&&(theX0>theX1)))
    { for (ii=0; ii<theBase-tt; ii++)
        _DiagramTarget.document.write("<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:0px;top:"+ii+"px;width:"+Math.round(ww*(ii+0.5)/hh)+"px;height:1px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>");
      for (ii=0; ii<bb-theBase; ii++)
        _DiagramTarget.document.write("<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:"+(dd+Math.round(ww*(ii+0.5)/hh))+"px;top:"+eval(theBase-tt+ii)+"px;width:"+(ww-Math.round(ww*(ii+0.5)/hh))+"px;height:1px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>");
    }
    if (((theY0>theY1)&&(theX0<theX1))||((theY0<theY1)&&(theX0>theX1)))
    { for (ii=0; ii<theBase-tt; ii++)
        _DiagramTarget.document.write("<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:"+(ww-Math.round(ww*(ii+0.5)/hh))+"px;top:"+ii+"px;width:"+Math.round(ww*(ii+0.5)/hh)+"px;height:1px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>");
      for (ii=0; ii<bb-theBase; ii++)
        _DiagramTarget.document.write("<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:0px;top:"+eval(theBase-tt+ii)+"px;width:"+(ww-dd-Math.round(ww*(ii+0.5)/hh))+"px;height:1px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>");
    }
  }
  if (theBase>=bb)
  { if ((theBase>bb)&&(ww>0))
    { _DiagramTarget.document.write("<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:0px;top:"+(hh)+"px;width:"+ww+"px;height:"+eval(theBase-bb)+"px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>");
    }
    if (((theY0<theY1)&&(theX0<theX1))||((theY0>theY1)&&(theX0>theX1)))
    { for (ii=0; ii<hh; ii++)
        _DiagramTarget.document.write("<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:0px;top:"+ii+"px;width:"+Math.round(ww*(ii+0.5)/hh)+"px;height:1px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>");
    }
    if (((theY0>theY1)&&(theX0<theX1))||((theY0<theY1)&&(theX0>theX1)))
    { for (ii=0; ii<hh; ii++)
        _DiagramTarget.document.write("<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:"+(ww-Math.round(ww*(ii+0.5)/hh))+"px;top:"+ii+"px;width:"+Math.round(ww*(ii+0.5)/hh)+"px;height:1px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>");
    }
  }
  _DiagramTarget.document.writeln("</div>");
}
function _AreaResizeTo(theX0, theY0, theX1, theY1)
{ var dd, ll, rr, tt, bb, ww, hh, id=this.ID,selObj="",divtext="", nbsp=(_IE)? "&nbsp;" : "";
  if (!isNaN(parseInt(theX0))) this.X0=theX0;
  if (!isNaN(parseInt(theY0))) this.Y0=theY0;
  if (!isNaN(parseInt(theX1))) this.X1=theX1;
  if (!isNaN(parseInt(theY1))) this.Y1=theY1;
  if (this.X0<=this.X1) { ll=Math.round(this.X0); rr=Math.round(this.X1); }
  else { ll=Math.round(this.X1); rr=Math.round(this.X0); }
  if (this.Y0<=this.Y1) { tt=Math.round(this.Y0); bb=Math.round(this.Y1); }
  else { tt=Math.round(this.Y1); bb=Math.round(this.Y0); }
  ww=rr-ll; hh=bb-tt;
  if (document.all) selObj=eval("_DiagramTarget.document.all."+id);
  else selObj=_DiagramTarget.document.getElementById(id);
  with (selObj.style)
  { if (this.Base<=tt) { left=ll+"px"; top=this.Base+"px"; width=ww+"px"; height=hh+"px"; }
    else { left=ll+"px"; top=tt+"px"; width=ww+"px"; height=Math.max(hh, this.Base-tt)+"px";}
  }
  if (this.Base<=tt)
  { if ((this.Base<tt)&&(ww>0))
    { divtext+="<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:0px;top:0px;width:"+ww+"px;height:"+eval(tt-this.Base)+"px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>";
    }
    if (((this.Y0<this.Y1)&&(this.X0<this.X1))||((this.Y0>this.Y1)&&(this.X0>this.X1)))
    { for (ii=0; ii<hh; ii++)
        divtext+="<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:"+Math.round(ww*(ii+0.5)/hh)+"px;top:"+eval(tt-this.Base+ii)+"px;width:"+(ww-Math.round(ww*(ii+0.5)/hh))+"px;height:1px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>";
    }
    if (((this.Y0>this.Y1)&&(this.X0<this.X1))||((this.Y0<this.Y1)&&(this.X0>this.X1)))
    { for (ii=0; ii<hh; ii++)
        divtext+="<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:0px;top:"+eval(tt-this.Base+ii)+"px;width:"+(ww-Math.round(ww*(ii+0.5)/hh))+"px;height:1px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>";
    }
  }
  if ((this.Base>tt)&&(this.Base<bb))
  { dd=Math.round((this.Base-tt)/hh*ww);
    if (((this.Y0<this.Y1)&&(this.X0<this.X1))||((this.Y0>this.Y1)&&(this.X0>this.X1)))
    { for (ii=0; ii<this.Base-tt; ii++)
        divtext+="<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:0px;top:"+ii+"px;width:"+Math.round(ww*(ii+0.5)/hh)+"px;height:1px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>";
      for (ii=0; ii<bb-this.Base; ii++)
        divtext+="<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:"+(dd+Math.round(ww*(ii+0.5)/hh))+"px;top:"+eval(this.Base-tt+ii)+"px;width:"+(ww-Math.round(ww*(ii+0.5)/hh))+"px;height:1px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>";
    }
    if (((this.Y0>this.Y1)&&(this.X0<this.X1))||((this.Y0<this.Y1)&&(this.X0>this.X1)))
    { for (ii=0; ii<this.Base-tt; ii++)
        divtext+="<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:"+(ww-Math.round(ww*(ii+0.5)/hh))+"px;top:"+ii+"px;width:"+Math.round(ww*(ii+0.5)/hh)+"px;height:1px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>";
      for (ii=0; ii<bb-this.Base; ii++)
        divtext+="<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:0px;top:"+eval(this.Base-tt+ii)+"px;width:"+(ww-dd-Math.round(ww*(ii+0.5)/hh))+"px;height:1px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>";
    }
  }
  if (this.Base>=bb)
  { if ((this.Base>bb)&&(ww>0))
    { divtext+="<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:0px;top:"+hh+"px;width:"+ww+"px;height:"+eval(this.Base-bb)+"px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>";
    }
    if (((this.Y0<this.Y1)&&(this.X0<this.X1))||((this.Y0>this.Y1)&&(this.X0>this.X1)))
    { for (ii=0; ii<hh; ii++)
        divtext+="<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:0px;top:"+ii+"px;width:"+Math.round(ww*(ii+0.5)/hh)+"px;height:1px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>";
    }
    if (((this.Y0>this.Y1)&&(this.X0<this.X1))||((this.Y0<this.Y1)&&(this.X0>this.X1)))
    { for (ii=0; ii<hh; ii++)
        divtext+="<div "+this.EventActions+"style='visibility:visible;"+this.Cursor+"position:absolute;left:"+(ww-Math.round(ww*(ii+0.5)/hh))+"px;top:"+ii+"px;width:"+Math.round(ww*(ii+0.5)/hh)+"px;height:1px;background-color:"+this.Color+";font-size:1pt;line-height:1pt;'>"+nbsp+"</div>";
    }
  }
  selObj.innerHTML=divtext;
}
*/
/*
*/
