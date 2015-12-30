This repo contains exploits for libstagefright used in the Nintendo New3DS system Internet Browser.

The stsc exploit is based on a PoC from here: https://blog.zimperium.com/stagefright-vulnerability-details-stagefright-detector-tool-released/

This requires the following repo: https://github.com/yellows8/3ds_browserhax_common See that repo for usage info as well.  

* stsc("browserhax_fright.php"): All system-versions <=10.1.0-27 are supported. System-version 10.2.0-28 fixed the vuln used by this. This was originally implemented on August 6-7, 2015.
* tx3g("browserhax_fright_tx3g.php"): All system-versions <=10.3.0-28 are supported, as of when this exploit was released. From the .php: "This tx3g version was originally implemented using system-version v10.2, on November 3, 2015. The PoC mp4 this is based on is from roughly October 24, 2015."

Currently the length of the URL used for accessing this hax must be less than 48 characters.

The exploits will automatically trigger when loading the mp4, no user-input needed once mp4-loading is started. There must be less than 3 open browser tabs when running the exploits, not including the tab for the mp4. Direct QR-code scanning with an URL for these exploits can't be done due to this. This is caused by an assert due to a memory allocation failure, presumably because of the heap corruption done by these exploits. This assert happens when the bottom-screen colorfill was already set to yellow by 3ds_browserhax_common.  
To bypass this issue with QR-code scanning, or to setup the browser so that it auto-loads the exploit, you can do the following:  
* 1) Disable wifi.
* 2) Scan the QR-code in Home Menu, or goto the target URL in the browser.
* 3) Exit the browser and re-enable wifi.
* 4) Launch the browser. By just launching the browser(with the current URL set to the mp4), the exploit will automatically trigger without any more user-input.

Note that using the QR-code on the site linked below works fine, it's just that if you use a QR-code containing an URL directly for the exploit page(http-redirects included), the above issues occur.

See the following for a hosted version of this: http://yls8.mtheall.com/3dsbrowserhax.php  


Wii U is also affected: all Wii U titles which use mvplayer.rpl(included with each title using it) are affected since that uses libstagefright. At the time of writing, the Internet Browser title from system-verson 5.5.0 is still affected by libstagefright vulns in general(that mvplayer.rpl build is from before August 2015).

