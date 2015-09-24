This is an exploit for libstagefright used in the Nintendo New3DS system Internet Browser. This was originally implemented on August 6-7, 2015.

This exploits the "stsc" vuln with MP4 parsing. https://blog.zimperium.com/stagefright-vulnerability-details-stagefright-detector-tool-released/

This requires the following repo: https://github.com/yellows8/3ds_browserhax_common See that repo for usage info as well.

Currently the length of the URL used for accessing this hax must be less than 48 characters.

The exploit will automatically trigger when loading the mp4, no user-input needed once mp4-loading is started. There must be less than 3 open browser tabs when running this exploit, not including the tab for the mp4. Direct QR-code scanning with an URL for this exploit can't be done due to this. This is caused by an assert due to a memory allocation failure, presumably because of the heap corruption done by this exploit.  
To bypass this issue with QR-code scanning, or to setup the browser so that it auto-loads the exploit, you can do the following:  
* 1) Disable wifi.
* 2) Scan the QR-code in Home Menu, or goto the target URL in the browser.
* 3) Exit the browser and re-enable wifi.
* 4) Launch the browser. By just launching the browser(with the current URL set to the mp4), the exploit will automatically trigger without any more user-input.

See the following for a hosted version of this: http://yls8.mtheall.com/3dsbrowserhax.php

