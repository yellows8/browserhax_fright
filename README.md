This repo contains exploits for libstagefright used in the Nintendo New3DS system Internet Browser.

The stsc exploit is based on a PoC from here: https://blog.zimperium.com/stagefright-vulnerability-details-stagefright-detector-tool-released/

This requires the following repo: https://github.com/yellows8/3ds_browserhax_common See that repo for usage info as well.  

* stsc("browserhax_fright.php"): All system-versions <=10.1.0-27 are supported. System-version 10.2.0-28 fixed the vuln used by this. This was originally implemented on August 6-7, 2015.
* tx3g("browserhax_fright_tx3g.php"): All system-versions <=10.5.0-30 are supported, vuln was fixed with 10.6.0-31. From the .php: "This tx3g version was originally implemented using system-version v10.2, on November 3, 2015. The PoC mp4 this is based on is from roughly October 24, 2015."
* skater31hax: All system-versions <=11.0.0-33 are supported. See the source if you want any more vuln details etc.

Currently the length of the URL used for accessing this hax must be less than 48 characters.

The exploits will automatically trigger when loading the mp4, no user-input needed once mp4-loading is started. There must be less than 3 open browser tabs when running the exploits, not including the tab for the mp4. If you want to access these exploits via QR-code, just use the QR-code from the site linked below(the exploits aren't usable with direct URLs for these exploits via QR-code).  

See the following for a hosted version of this: https://yls8.mtheall.com/3dsbrowserhax.php  


See here regarding Wii U: https://github.com/yellows8/wiiu_browserhax_fright

