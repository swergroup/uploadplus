=== Upload+ : Filenames Cleaner ===
Contributors: swergroup,pixline
Donate link: http://bit.ly/Vpy3MP
Tags: media, filename, filenames, clean, rename, uploads, upload, images, files, security, sanitization, transliteration, utf8, ascii, prefix, custom
Requires at least: 3.4.2
Tested up to: 3.4.2
Stable tag: trunk

Clean filenames , add security and sanity while uploading.

== Description ==

Filenames on the web are different than those on the desktop. Empty spaces and strange characters doesn't belong to web space.

Enter Upload+: you can set your rules and clean your files' name while they upload. Three basic rules:

* convert spaces and strange characters into dashes (-)
* only alphanumeric [A-Za-z] and digits, spaces and strange characters stripped out;
* convert spaces and strange characters in underscores (_)

You can apply website-based or date-based prefix, even a custom prefix. Lowercase or Uppercase? Your choice! 
Some default prefix:

* day (dd_)
* month/day	(mmdd_)
* year/month/day (yyyymmdd_)
* year/month/day/hour/minutes (yyyymmddhhmm_)
* year/month/day/hour/minutes/seconds (yyyymmddhhmmss_)
* random (mt-rand)
* unix timestamp
* ...

Upload+ can transliterate filenames to utf8 characters, thanks to [phputf8](http://phputf8.sourceforge.net/) opensource PHP classes: arabic or russian readable filenames!

Last but not least, you can preview settings in the media settings page without uploading files. 

= Credits = 

GPL&copy; 2008+ [SWERgroup siti internet Torino](http://swergroup.com/)

== Screenshots ==

1. Option panel (under Options &raquo; Media)

== Changelog ==

* 3.0.1 (04/11/12) custom prefix
* 3.0   (04/11/12) upgrade for WordPress 3.4.2, partial rewrite and refactoring.
* 2.7	(28/02/09) version 2.7 for WordPress 2.7 working
* 2.7b1	(26/02/09) beta version for WordPress 2.7 only 
* 2.5.1 (10/03/08) little bugfix
* 2.5	(02/03/08) tagged 2.5 release. better german support, props denis.
* 2.5b1 (26/02/08) preliminary WordPress 2.5 support, version bump to match WP version, utf8-based transliteration.
* 0.3.3 (14/09/07) works with WordPress 2.3 and WordPress MU 1.2.*
* 0.3.2 (12/07/07) silly typos fixed :-)
* 0.3.1 (11/07/07) dd_ prefix added by Ovidiu request
* 0.3   (21/06/07) optional prefix, preview of changes. first tagged stable!
* 0.2   (06/06/07) more options
* 0.1d  (20/03/07) better register hook and readme
* 0.1c  (06/03/07) fix on plugin activation, options
* 0.1a  (20/02/07) initial release


== Installation ==

1. Download the plugin Zip archive.
1. Upload `uploadplus` folder to your `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Tweak the 'Upload+ Plugin' section under 'Options'->'Media' to match your need.
1. Enjoy :-)