Null-Pointer Private Server
==========================================

If you want to know more about this SIF private server, please see Design.txt

It doesn't use any framework so it's lightweight. While it's lightweight, it will be designed to be flexible.

###System Requirements

* PHP 7.x. MADATORY!!!

* SQLite3 (or later) PHP module. SQLite v3.7.0 or later is required if you use SQLite3 as DB backend.

* MySQL v5.5 (or later) and MySQLi PHP module (if you use MySQL as DB backend)

* For Windows: Windows 7 SP1 or Windows Server 2008 R2 SP1 (because you can't run PHP 7 in the earlier Windows version)

* For Ubuntu: Ubuntu 16.04, but using 14.04 is possible with [Ondřej Surý PPA](https://launchpad.net/~ondrej/+archive/ubuntu/php) to install PHP 7 and it's modules.

###Some Notes

* You don't need to do special preparation. Just clone, create web server with document root set to this directory, and visit.

* Hosting it under Linux is recommended and will gain 2x more speed than hosting it under Windows.
