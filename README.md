# M2 Check IP Module

Block sessions for Bots and IPs that have been reported

This module has been developed for Magento => 2.4.6

This module allows you to download form "iplists.firehol.org" a list of reported IP addresses and compare the client IP addresses with this list.
If there is a match, we can pause the request and/or reject it, preventing the creation of a PHP session.

The list will be downloaded every certain time established by a cron job.

From the Magento OS administration panel, you can view the list of IPs with which there has been a match.

The list of matches is saved in TXT format in a daily log that is deleted every few days.

Additionally, you can also check for crawler visits. For this, we use the "Crawler-Detect" package (https://github.com/JayBizzle/Crawler-Detect/) as a dependency.

You also have the option to enable/disable IP or bot protection via API and receive email notifications when the API is accessed.
This is especially usefu:
- GET /V1/checkip/enableip?enabled=true:false
- GET /V1/checkip/enablebot?enabled=true:false
- GET /V1/checkip/enableall?enabled=true:false

## Installation

The extension must be installed via `composer`. To proceed, run these commands in your terminal:

```
composer require orangecat/checkip
bin/magento setup:upgrade
```

## Configuration IP
Stores > Configuration > Security > Check IP
- Enable IP Check
- Cron settings
- IPs blacklist file download URL
- Mode (Block / Pause)
- Log retention days
- Aditional Blacklist
- Whitelist

## Configuration Bot
Stores > Configuration > Security > Check IP
- Enable Bot Check
- Mode (Block / Pause)
- Log retention days
- Aditional Blacklist
- Whitelist

## Check IP logs
System > IP Blacklist Log

## Check Bot logs
System > Bot Blacklist Log

## Log IP folder
var/ipblacklist

## Log Bot folder
var/botblacklist

## Configuration API
Stores > Configuration > Security > Check IP
- Only Email Notification?
- Emails Notification
- Enable API IP
- Enable API BOT
- Clear Full Page Cache after status change
- Whitelist


