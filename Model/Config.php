<?php
/**
 * Copyright © Orangecat. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Orangecat\Checkip\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    public const XML_PATH_IP_ENABLED = 'orangecat_checkip/ip/enabled';

    public const XML_PATH_IP_UPDATE_INTERVAL = 'orangecat_checkip/ip/update_interval';

    public const XML_PATH_IP_ACTION_MODE = 'orangecat_checkip/ip/action_mode';

    public const XML_PATH_IP_DELAY_SECONDS = 'orangecat_checkip/ip/delay_seconds';

    public const XML_PATH_IP_URL_DOWNLOAD_IP_BLACKLIST = 'orangecat_checkip/ip/url_ipblacklist';

    public const XML_PATH_IP_LOG_RETENTION_DAYS = 'orangecat_checkip/ip/log_retention_days';

    public const XML_PATH_IP_BLACKLIST = 'orangecat_checkip/ip/blacklist';

    public const XML_PATH_IP_WHITELIST = 'orangecat_checkip/ip/whitelist';

    public const FILE_IPS_BLACKLIST = '/var/ipblacklist/ipblacklist.txt';

    public const DEFAULT_URL_DOWNLOAD_IP_BLACKLIST = 'https://iplists.firehol.org/files/firehol_level1.netset';

    public const XML_PATH_BOT_ENABLED = 'orangecat_checkip/bot/enabled';

    public const XML_PATH_BOT_UPDATE_INTERVAL = 'orangecat_checkip/bot/update_interval';

    public const XML_PATH_BOT_ACTION_MODE = 'orangecat_checkip/bot/action_mode';

    public const XML_PATH_BOT_DELAY_SECONDS = 'orangecat_checkip/bot/delay_seconds';

    public const XML_PATH_BOT_LOG_RETENTION_DAYS = 'orangecat_checkip/bot/log_retention_days';

    public const XML_PATH_BOT_BLACKLIST = 'orangecat_checkip/bot/blacklist';

    public const XML_PATH_BOT_WHITELIST = 'orangecat_checkip/bot/whitelist';

    public const XML_PATH_API_ENABLED_IP = 'orangecat_checkip/api/enabledip';

    public const XML_PATH_API_ENABLED_BOT = 'orangecat_checkip/api/enabledbot';

    public const XML_PATH_API_CLEAR_FPC = 'orangecat_checkip/api/cache_fpc';

    public const XML_PATH_API_WHITELIST = 'orangecat_checkip/api/whitelist';

    public const XML_PATH_API_ONLY_NOTIFICATION = 'orangecat_checkip/api/notification';

    public const XML_PATH_API_EMAIL = 'orangecat_checkip/api/emails_notification';

    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Is IP Enabled
     *
     * @return bool
     */
    public function isIpEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_IP_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get IP Update Interval
     *
     * @return integer
     */
    public function getIpUpdateInterval(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_IP_UPDATE_INTERVAL, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get IP Action Mode
     *
     * @return integer
     */
    public function getIpActionMode(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_IP_ACTION_MODE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get IP Delay Seconds
     *
     * @return integer
     */
    public function getIpDelaySeconds(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_IP_DELAY_SECONDS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Url Download Ip Blacklist
     *
     * @return string
     */
    public function getUrlDownloadIpBlacklist(): string
    {
        $url = (string)$this->scopeConfig->getValue(
            self::XML_PATH_IP_URL_DOWNLOAD_IP_BLACKLIST,
            ScopeInterface::SCOPE_STORE
        );
        if (empty($url)) {
            $url = Config::DEFAULT_URL_DOWNLOAD_IP_BLACKLIST;
        }
        return trim($url);
    }

    /**
     * Get IP Log Retention Days
     *
     * @return integer
     */
    public function getIpLogRetentionDays()
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_IP_LOG_RETENTION_DAYS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get IP Blacklist
     *
     * @return array
     */
    public function getIpBlacklist(): array
    {
        return $this->getList(self::XML_PATH_IP_BLACKLIST);
    }

    /**
     * Get IP Whitelist
     *
     * @return array
     */
    public function getIpWhitelist(): array
    {
        return $this->getList(self::XML_PATH_IP_WHITELIST);
    }

    /**
     * Is BOT Enabled
     *
     * @return bool
     */
    public function isBotEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_BOT_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get BOT Update Interval
     *
     * @return integer
     */
    public function getBotUpdateInterval(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_BOT_UPDATE_INTERVAL, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get BOT Action Mode
     *
     * @return integer
     */
    public function getBotActionMode(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_BOT_ACTION_MODE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get BOT Delay Seconds
     *
     * @return integer
     */
    public function getBotDelaySeconds(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_BOT_DELAY_SECONDS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get BOT Log Retention Days
     *
     * @return integer
     */
    public function getBotLogRetentionDays()
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_BOT_LOG_RETENTION_DAYS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get BOT Blacklist
     *
     * @return array
     */
    public function getBotBlacklist(): array
    {
        return $this->getList(self::XML_PATH_BOT_BLACKLIST);
    }

    /**
     * Get BOT Whitelist
     *
     * @return array
     */
    public function getBotWhitelist(): array
    {
        return $this->getList(self::XML_PATH_BOT_WHITELIST);
    }

    /**
     * Get List
     *
     * @return array
     */
    private function getList(string $path): array
    {
        $value = (string)$this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);

        $list = [];
        foreach (explode("\n", $value) as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $list[] = $line;
        }

        return $list;
    }

    /**
     * Is API IP Enabled
     *
     * @return bool
     */
    public function isApiIpEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_API_ENABLED_IP, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Is API Bot Enabled
     *
     * @return bool
     */
    public function isApiBotEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_API_ENABLED_BOT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Only Notification
     *
     * @return bool
     */
    public function onlyNotification(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_API_ONLY_NOTIFICATION, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Clear Fpc Cache
     *
     * @return bool
     */
    public function clearFpcCache(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_API_CLEAR_FPC, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Api Whitelist
     *
     * @return array
     */
    public function getApiWhitelist(): array
    {
        return $this->getList(self::XML_PATH_API_WHITELIST);
    }

    /**
     * Get Api Emails
     *
     * @return array
     */
    public function getApiEmails(): array
    {
        $emails = (string)$this->scopeConfig->getValue(self::XML_PATH_API_EMAIL, ScopeInterface::SCOPE_STORE);
        if ($emails) {
            return array_map('trim', explode(',', $emails));
        }
        return [];
    }
}
