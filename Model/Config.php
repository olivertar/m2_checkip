<?php

namespace Orangecat\Checkip\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    public const XML_PATH_ENABLED = 'orangecat_checkip/general/enabled';

    public const XML_PATH_UPDATE_INTERVAL = 'orangecat_checkip/general/update_interval';

    public const XML_PATH_ACTION_MODE = 'orangecat_checkip/general/action_mode';

    public const XML_PATH_DELAY_SECONDS = 'orangecat_checkip/general/delay_seconds';

    public const XML_PATH_URL_DOWNLOAD_IP_BLACKLIST = 'orangecat_checkip/general/url_ipblacklist';

    public const XML_PATH_LOG_RETENTION_DAYS = 'orangecat_checkip/general/log_retention_days';

    public const FILE_IPS_BLACKLIST = '/var/ipblacklist/ipblacklist.txt';

    public const DEFAULT_URL_DOWNLOAD_IP_BLACKLIST = 'https://iplists.firehol.org/files/firehol_level1.netset';

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
     * Is Enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Update Interval
     *
     * @return integer
     */
    public function getUpdateInterval(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_UPDATE_INTERVAL, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Action Mode
     *
     * @return integer
     */
    public function getActionMode(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_ACTION_MODE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Delay Seconds
     *
     * @return integer
     */
    public function getDelaySeconds(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_DELAY_SECONDS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Url Download Ip Blacklist
     *
     * @return string
     */
    public function getUrlDownloadIpBlacklist(): string
    {
        $url = (string)$this->scopeConfig->getValue(
            self::XML_PATH_URL_DOWNLOAD_IP_BLACKLIST,
            ScopeInterface::SCOPE_STORE
        );
        if (empty($url)) {
            $url = Config::DEFAULT_URL_DOWNLOAD_IP_BLACKLIST;
        }
        return trim($url);
    }

    /**
     * Get Log Retention Days
     *
     * @return integer
     */
    public function getLogRetentionDays()
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_LOG_RETENTION_DAYS, ScopeInterface::SCOPE_STORE);
    }
}
