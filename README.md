# simple-php-video-playlist-generator
Generate playlist from your PC video collection to .m3u file for Android VLC playback.
Single-file php script that will generate a playlist from your video collection.

** Requirements **

* apache2 server with mod_rewrite
* php

** Installation **

* Clone this repository to your videos folder
* Create apache2 virtual host with desired port for your collection
* Add Listen XX port in apache2 config
* Allow remote access for port
* Install VLC on your Android device
* Open your desktop IP:PORT from Android device browser
* Click on any directory, all video files are loaded into VLC
