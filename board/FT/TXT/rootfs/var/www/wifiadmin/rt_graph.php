<?php
/*+-------------------------------------------------------------------------+
  | Copyright (C) 2008 basOS (basos@users.sf.net)     |
  |                                                                         |
  | This program is free software; you can redistribute it and/or           |
  | modify it under the terms of the GNU General Public License             |
  | as published by the Free Software Foundation; either version 2          |
  | of the License, or any later version.                  |
  |                                                                         |
  | This program is distributed in the hope that it will be useful,         |
  | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
  | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
  | GNU General Public License for more details.                            |
  +-------------------------------------------------------------------------+
  | WifiAdmin: The Free WiFi Web Interface				    |
  +-------------------------------------------------------------------------+
  | Based on previous work of 						    |
  | - panousis@ceid.upatras.gr	- Panousis 8anos			    |
  | - dimopule@ceid.upatras.gr	- Dimopoulos 8umios   			    |
  +-------------------------------------------------------------------------+*/

if (!($_GET['device'])) {
	echo "<body><br><br><p class='error'>No device Specified</p></body>";
	die();
}
require "include/auth.php"; //init session
require "include/lang_init.php"; //init lang

//check priviledges
if (@$_SESSION["view_status_ext"]!="true"){
	echo "<body><br><br><p class = \"error\">".$lang['general']['enoperm']."</p></body>";
	exit();
}

$curdevname=$_GET['device'];
if (!$_GET['mode'])
	$curmode="t";
else
	$curmode=$_GET['mode'];

if (isset($_GET['fetch'])) {
	require "include/router_init.php";
	require "include/functions.php";
	switch ($curmode) {
	case "t": //Traffic Monitor 
		//echo rxrate LF txrate 
		$ifs=get_ethernet_devstatus($curdevname);
		echo $ifs['rx']."\n".$ifs['tx'];
	break;
	}
}
else {
	require_once "config/config.php";
?>
<html>
<head>
<?php
	switch ($curmode) {
	case 't':
		echo "<title> Realtime Traffic Graph </title>";
	break;
	}
?>
	<link rel = "stylesheet" type = "text/css" href = "./include/global.css">
	<style>
		div#msg { position:absolute; bottom:5px ;color:red; text-align:center;}
	</style>
	<script type="text/javascript">

/*function DrawGraph() {
	var x,y,i,j;
	for (i=0;i<WIDTH;i++) {
		x = Math.round(D.RealX(PHOFF+i));
		y = Q[x+XOFF];
		if (y>YHIGH) y=YHIGH;
		if (y<YLOW) y=YLOW;
		j = D.ScreenY(y);
		//document.write("("+x+","+y+"/"+i+","+j+")");
		P[i].MoveTo (PHOFF+i,j);
	}
}*/
var tmr;
function DrawGraph(D,L,Q) {
	var x,x0,x1,y0,y1,Qlen=Q[0].length;
	var YHIGH=D.ymax,YLOW=D.ymin;
	for (x=0;x<Qlen-1;x++) { //for all lines
		x0=Q[0][x];
		x1=Q[0][x+1];
		y0 = Q[1][x];
		y1 = Q[1][x+1];
		//if (y0>YHIGH) y0=YHIGH; if (y0<YLOW) y0=YLOW;
		//if (y1>YHIGH) y1=YHIGH;	if (y1<YLOW) y1=YLOW;
		//document.write("("+x+","+y+"/"+i+","+j+")");
		L[x].ResizeTo (D.ScreenX(x0),D.ScreenY(y0),D.ScreenX(x1),D.ScreenY(y1));
	}
}

function Shift2xQueueLeft(Q) {
	var i,l;
	l=Q[0].length;
	for (i=0;i<l-1;i++) {
		Q[0][i] = Q[0][i+1];
		Q[1][i] = Q[1][i+1];
	}
}

function AppendValueTo2xQueue (vx,vy,Q) {
	Shift2xQueueLeft(Q);
	var l=Q[0].length;
	Q[0][l-1] = vx;
	Q[1][l-1] = vy;
}

function ArrayOffset (offs,Q) {
	var i,l=Q.length;
	for (i=0;i<l;i++) {
		Q[i] += offs;
	}
}

function ArMax(AR) {
	var m=-2^16;
	var i,l;
	l=AR.length;
	for (i=0;i<l;i++)
		if (AR[i]>m) m=AR[i];
	return m;
}

function ArMin(AR) {
	var m=-2^16;
	var i,l;
	l=AR.length;
	for (i=0;i<l;i++)
		if (AR[i]<m) m=AR[i];
	return m;
}

function SetLimits(D,Q1,Q2) {
	var YHIGH=D.ymax,YLOW=D.ymin,XLOW=D.xmin;
	var My1,mx1,My2,mx2;
	My1 = ArMax(Q1[1]);
	My2 = ArMax(Q2[1]);
	mx1 = ArMin(Q1[0]);
	mx2 = ArMin(Q2[1]);
	if (My2>My1) My1=My2;
	if (mx2<mx1) mx1=mx2;

	if (My1<5) My1=5;
	if (My1!=YHIGH || mx1!=XLOW) {
		YHIGH = My1;
		XLOW = mx1;
		D.SetBorder(XLOW, 0, YLOW, YHIGH);
		D.Draw(COLOR_BACKG,COLOR_TEXT,true,"Realtime Traffic Graph");
	}
}

function AJAXStateChanged() {
if (xmlHttp.readyState==4) {  // 4 = "loaded"
  if (xmlHttp.status==200) {  // 200 = "OK"
	var v=xmlHttp.responseText;
	var vals=v.split("\n");
	var raterx,ratetx;
	var dt=new Date();
	var curtim=dt.getTime()/1000; //seconds since 1/1/1970
	var drift=prevvals[2] - curtim;
	if (drift<-60) drift=-TIMER_INTERVAL_SEC; //first time
	raterx = (vals[0]-prevvals[0])/(1024*(-drift)); // kbps
	ratetx = (vals[1]-prevvals[1])/(1024*(-drift));
	if (prevvals[0] == 0) raterx = 0; //first time
	if (prevvals[1] == 0) ratetx = 0; //first time
	prevvals[0] = vals[0];
	prevvals[1] = vals[1];
	prevvals[2] = curtim;
	// mode t: rx\ntx
	//TODO: RX TX support
	ArrayOffset(drift,QR[0]);
	AppendValueTo2xQueue(0,raterx,QR);
	ArrayOffset(drift,QT[0]);
	AppendValueTo2xQueue(0,ratetx,QT);
	document.getElementById("msg").innerHTML = "<table class=\"rates\" align=\"center\"><th></th><th><font color="+COLOR_RXLINE+">RX</font></th><th><font color="+COLOR_TXLINE+">TX</font></th><tr><td>Last:</td><td><b><font color="+COLOR_RXLINE+">"+raterx.toFixed(2)+"</font></b></td><td><b><font color="+COLOR_TXLINE+">"+ratetx.toFixed(2)+"</font></b></td></tr><tr><td>Max:</td><td><font color="+COLOR_RXLINE+">"+ArMax(QR[1]).toFixed(2)+"</font></td><td><font color="+COLOR_TXLINE+">"+ArMax(QT[1]).toFixed(2)+"</font></td></tr></table>";
	//alert(drift);
	waiting_resp = 0;
  }
  else {
	clearTimeout(tmr);
    	document.getElementById("msg").innerHTML="Problem retrieving data:" + xmlhttp.statusText;
  }
}
}

/* We send every TIMER_INTERVAL_SEC requests for new data IF we are clear to send
   We Draw every DRAW_PULSES the graph
*/

function timerEvent()
{
	n_pulses++;
	if (waiting_resp == 0) { //Clear to Send
		  waiting_resp = 1;
		  //** TODO We create once, inialize once, use forever --CHECK if this is ok
		  xmlHttp.onreadystatechange=AJAXStateChanged;
		  xmlHttp.open("GET","<?php echo $_SERVER['PHP_SELF']?>?device=<?php echo $curdevname?>&mode=<?php echo $curmode?>&fetch=1",true);
		  xmlHttp.send(null);
	}
	if (n_pulses >=DRAW_PULSES) {
		n_pulses = 0;
		SetLimits(D,QR,QT);
		DrawGraph(D,LR,QR);
		DrawGraph(D,LT,QT);
	}
	tmr = setTimeout("timerEvent()",TIMER_INTERVAL_SEC*1000);
	//alert("OUT"+tmr);
}

function InitializeLines(D,QLEN,COLOR) {
	var XLOW=D.xmin;
	var p=Math.round((-XLOW+1)/QLEN);
	//Generate Lines
	var L=new Array(QLEN-1);
	var j=D.ScreenY(0);
	var i,x=XLOW;
	for (i=0; i<QLEN-1;i++) {
		L[i]=new Line(D.ScreenX(x),j,D.ScreenX(x+1),j,COLOR);
		x+=p;
	}
	return L;
}

function Initialize2xQueue(D,QLEN) {
	//Initialize queue
	var XLOW=D.xmin;
	var p=Math.round((-XLOW+1)/QLEN);
	var Q = new Array(2);
	var i,x=XLOW;
	Q[0] = new Array(QLEN);
	Q[1] = new Array(QLEN);
	for (i=0;i<QLEN;i++) {
		Q[0][i] = x;
		x+=p;
		Q[1][i] = 0;
	}
	//document.writeln("DBG "+(Q[0].toString()));
	return Q;
}
</script>
<script type="text/javascript" src="include/diagram.js"></script>
<script type="text/javascript" src="include/ajax.js"></script>
</head>
<body onunload="clearTimeout(tmr)">
<script type="text/javascript">


/* Also exists clearTimeout() */

/*    
	* var D = new Diagram() //Constructor
    * D.SetFrame(theLeft, theTop, theRight, theBottom) //Screen
    * D.SetBorder(theLeftX, theRightX, theBottomY, theTopY) //World
    * D.SetText(theScaleX, theScaleY, theTitle) //Labels (optional)
    * D.ScreenX(theRealX) //Coordinate transformation world->screen
    * D.ScreenY(theRealY) //Coordinate transformation world->screen
    * D.RealX(theScreenX) //Coordinate transformation screen->world
    * D.RealY(theScreenY) //Coordinate transformation screen->world
    * D.GetXGrid() //returns array, which contains min, delta and max of the X grid
    * D.GetYGrid() //returns array, which contains min, delta and max of the Y grid
    * D.SetGridColor(theGridColor[, theSubGridColor]) //Colors of X and Y grid lines
    * D.SetXGridColor(theGridColor[, theSubGridColor]) //Colors of X grid lines
    * D.SetYGridColor(theGridColor[, theSubGridColor]) //Colors of Y grid lines
    * D.Draw(theDrawColor, theTextColor, isScaleText[, theTooltipText[, theOnClickAction [, theOnMouseoverAction[, theOnMouseoutAction]]]]) //Display, theDrawColor can be an image
    * D.SetVisibility(isVisible) //Show or Hide
    * D.SetTitle(theTitle) //TooltipText
    * D.Delete() //Delete DIV object of D from the document
    * delete D //Destructor

    * var L = new Line(theX0, theY0, theX1, theY1, theColor[, theSize[, theTooltipText[,
      theOnClickAction [, theOnMouseoverAction[, theOnMouseoutAction]]]]]) //Constructor and Display
    * L.SetColor(theColor) //Color
    * L.SetVisibility(isVisible) //Show or Hide
    * L.SetTitle(theTitle) //TooltipText
    * L.MoveTo(theLeft, theTop) //Move
    * L.ResizeTo(theX0, theY0, theX1, theY1) //Resize
    * L.Delete() //Delete DIV object of L from the document
    * delete L //Destructor

*/
//global variables
var xmlHttp;
var n_pulses=0;
var waiting_resp = 0; //0 no, 1 waiting, 2 data arived
var prevvals = new Array(0,0,0); //RX,TX,SAMPLETIME
var D,LR,QR,LT,QT;

//Configuration Parameters
var QUE_LEN = 50; //# samples
var HEIGHT = <?php echo $C_rtgraph_height?>;
var WIDTH = <?php echo $C_rtgraph_width?>;
var PHOFF = 60; //Pixel Horizantial Offset
var COLOR_BACKG = "#DDDDFF";
var COLOR_GRID = "#336633";
var COLOR_TEXT = "#666699";
var COLOR_RXLINE = "#33CC33";
var COLOR_TXLINE = "#3300CC";

/* TODO: Make program configuration variables */
var TIMER_INTERVAL_SEC = 2; //PULSE = Check Every x secs
var DRAW_PULSES = 2; //Draw Every x Pulses

//graph initial vars
var YLOWi = 0;
var YHIGHi = 10;
var XLOWi = -(TIMER_INTERVAL_SEC*(QUE_LEN+1));

// Initialize graph
document.open();
document.writeln("<div id='graph'>");

D=new Diagram();
D.SetFrame(PHOFF, 40, PHOFF+WIDTH-1, 40+HEIGHT-1);
D.SetBorder(XLOWi, 0, YLOWi, YHIGHi);
D.SetText("secs","KiBps","Traffic <?php echo $curdevname?> KiBps" );
D.SetGridColor(COLOR_GRID);
D.Draw(COLOR_BACKG,COLOR_TEXT,true,"Realtime Traffic Graph");

document.writeln("</div>");
document.writeln("<style> table.rates { background-color:"+COLOR_BACKG+"; border:1px solid; border-color:"+COLOR_GRID+";}</style>");
document.writeln("<div id='msg'>Initializing...</div>");
document.close();

// Generate Pixels
/*P=new Array(WIDTH); // Green Line of inbound traffic
j=D.ScreenY(0);
for (i=0; i<WIDTH; i++)
	P[i]=new Pixel(PHOFF+i, j, "#665533");
*/

// Green Line of inbound traffic
LR=InitializeLines(D,QUE_LEN,COLOR_RXLINE);
QR=Initialize2xQueue(D,QUE_LEN);

LT=InitializeLines(D,QUE_LEN,COLOR_TXLINE);
QT=Initialize2xQueue(D,QUE_LEN);

xmlHttp=AJAXCreateXMLHTTP();
if (xmlHttp==null) {
	  document.getElementById("msg").innerHTML = "FATAL: Could not Create AJAX Object.";
}
else {
	xmlHttp.onreadystatechange=AJAXStateChanged;
	timerEvent();
}

</script>

</body>
</html>
<?php
}
?>
