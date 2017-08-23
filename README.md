# Nexus #

## Description ##
- A simple and easy-to-use Nexus, written in PHP by [André Lichtenthäler](https://bikossor.de).
- Requires PHP >= 4.0.3 (October 2000).

## Changelog ##
### Version: 0.3.0 (August 23rd, 2017)
- [Change] Replaced ```file_puts_content()``` to ```fopen()``` for compatibility reasons
- [Change] Cleaned up some parts
- [Added] Now the cache directory will be created if it doesn't exists
- [Added] ```LICENSE``` to the default blacklist
- [Added] Some CSS prefixes

### Version: 0.2.0 (February 25th, 2017)
- [Added] A yet simple lightbox

### Version: 0.1.0 (February 25th, 2017)
- Introduction
- [Added] Ability to search in a directory
- [Added] Files are [natural](http://php.net/manual/de/function.natsort.php) sorted *(will be adjustable later)*
- [Added] Memory management (free, total and used disk-space)
- [Added] Sortable tables with [sorttable.js](http://www.kryogenix.org/code/browser/sorttable/)
- [Added] Adjustable blacklist for files and directories
