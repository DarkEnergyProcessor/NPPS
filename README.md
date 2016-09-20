Null-Pointer Private Server
===========================

This is currently most flexible private server (I think) ever created.

The private server code is written entirely from stratch, without using any framework.

###System Requirements

* PHP 7. MANDATORY!!!

* MBString PHP extension.

* SQLite3 (or later) PHP module. SQLite v3.7.0 or later is required if you're using SQLite3 as DB backend.

* MySQL v5.5 (or later) and MySQLi PHP module (if you're using MySQL as DB backend)

* For Windows: Windows 7 SP1 or Windows Server 2008 R2 SP1 with latest updates (because you can't run PHP 7 in the earlier Windows version). Windows 8.1 or Windows Server 2012 is recommended.

* For Ubuntu: Ubuntu 16.04 (with simple `apt-get`), or 14.04 with [Ondřej Surý PPA](https://launchpad.net/~ondrej/+archive/ubuntu/php) to install PHP 7 and it's modules.

* For Mac OS X 10.6 and above: [use this method](http://php-osx.liip.ch/). **64-bit only**. This one is untested.

###Some Notes

* You don't need to do special preparation. Just clone, install necessary requirements above, create web server with document root set to this directory, and visit.
