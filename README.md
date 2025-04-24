# M2 Check IP Module

Block sessions from IPs that have been reported

This module has been developed for Magento => 2.4.6

This module allows you to download form "iplists.firehol.org" a list of reported IP addresses and compare the client IP addresses with this list.
If there is a match, we can pause the request and/or reject it, preventing the creation of a PHP session.

The list will be downloaded every certain time established by a cron job.

From the Magento OS administration panel, you can view the list of IPs with which there has been a match.

The list of matches is saved in TXT format in a daily log that is deleted every few days.

## Installation

The extension must be installed via `composer`. To proceed, run these commands in your terminal:

```
composer require orangecat/checkip
bin/magento setup:upgrade
```

## Configuration
Stores > Configuration > Security > Check IP
- Enable module
- Update list every (hours)
- IPs blacklist file download URL
- Mode (Block / Pause)
- Log retention days

## Check logs
System > IP Blacklist Log

## Log folder
var/ipblacklist


