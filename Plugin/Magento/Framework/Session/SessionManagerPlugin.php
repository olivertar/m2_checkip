<?php
/**
 * Copyright © Orangecat. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Orangecat\Checkip\Plugin\Magento\Framework\Session;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Session\SessionManager;
use Orangecat\Checkip\Model\Config;
use Orangecat\Checkip\Model\Service\LogIpService;
use Orangecat\Checkip\Model\Service\LogBotService;
use Jaybizzle\CrawlerDetect\CrawlerDetect;

class SessionManagerPlugin
{
    /** @var Config */
    private $config;

    /** @var HttpRequest */
    private $httpRequest;

    /** @var RemoteAddress */
    private $remoteAddress;

    /** @var File */
    private $file;

    /** @var LogIpService */
    private $logIpService;

    /** @var LogBotService */
    private $logBotService;

    /** @var CrawlerDetect */
    private $crawlerDetect;

    /**
     * Constructor
     *
     * @param Config $config
     * @param HttpRequest $httpRequest
     * @param RemoteAddress $remoteAddress
     * @param File $file
     * @param LogIpService $logIpService
     * @param LogBotService $logBotService
     * @param CrawlerDetect $crawlerDetect
     */
    public function __construct(
        Config        $config,
        HttpRequest   $httpRequest,
        RemoteAddress $remoteAddress,
        File          $file,
        LogIpService  $logIpService,
        LogBotService $logBotService,
        CrawlerDetect $crawlerDetect
    ) {
        $this->config = $config;
        $this->httpRequest = $httpRequest;
        $this->remoteAddress = $remoteAddress;
        $this->file = $file;
        $this->logIpService = $logIpService;
        $this->logBotService = $logBotService;
        $this->crawlerDetect = $crawlerDetect;
    }

    /**
     * Around Start
     *
     * @param SessionManager $subject
     * @param callable $proceed
     * @return callable
     */
    public function aroundStart(SessionManager $subject, callable $proceed)
    {
        $ip = $this->remoteAddress->getRemoteAddress();
        $user_agent = $this->httpRequest->getServer('HTTP_USER_AGENT', '');

        if($this->checkBot($ip, $user_agent)){
            return $subject;
        }else{
            if($this->checkIp($ip, $user_agent)){
                return $subject;
            }
        }
        return $proceed();

    }

    private function checkIp($ip, $user_agent)
    {
        if (!$this->config->isIpEnabled()) {
            return false;
        }

        if (in_array($ip, $this->config->getIpWhitelist())) {
            $this->logIpService->log($ip . '|-|' . $user_agent . '|whitelist');
            return false;
        }

        $mode = $this->config->getIpActionMode();

        if (in_array($ip, $this->config->getIpBlacklist())) {
            $this->logIpService->log($ip . '|-|' . $user_agent . '|blacklist');
            if ($mode === 1) {
                return true;
            } elseif ($mode === 2) {
                sleep($this->config->getIpDelaySeconds());
            }

            return false;
        }

        $blacklistPath = BP . Config::FILE_IPS_BLACKLIST;

        if (!$this->file->isExists($blacklistPath)) {
            return false;
        }

        $content = $this->file->fileGetContents($blacklistPath);
        $blacklist = explode("\n", $content);

        foreach ($blacklist as $k => $cidr) {
            if (empty($cidr)) {
                continue;
            }

            if (substr($cidr, 0, 1) === '#') {
                continue;
            }
            if ($this->ipInRange($ip, $cidr)) {
                $this->logIpService->log($ip . '|' . $cidr . '|' . $user_agent . '|default');
                if ($mode === 1) {
                    return true;
                } elseif ($mode === 2) {
                    sleep($this->config->getIpDelaySeconds());
                }

                return false;
            }
        }

        return false;
    }

    private function checkBot($ip, $user_agent)
    {
        if (!$this->config->isBotEnabled()) {
            return false;
        }

        if (in_array($user_agent, $this->config->getBotWhitelist())) {
            $this->logBotService->log($ip . '|' . $user_agent. '|whitelist');
            return false;
        }

        $mode = $this->config->getBotActionMode();

        if (in_array($user_agent, $this->config->getBotBlacklist())) {
            $this->logBotService->log($ip . '|' . $user_agent. '|blacklist');
            if ($mode === 1) {
                return true;
            } elseif ($mode === 2) {
                sleep($this->config->getBotDelaySeconds());
            }

            return false;
        }

        if($this->crawlerDetect->isCrawler($user_agent)){
            $this->logBotService->log($ip . '|' . $user_agent. '|crawlerlist');
            if ($mode === 1) {
                return true;
            } elseif ($mode === 2) {
                sleep($this->config->getBotDelaySeconds());
            }
        }

        return false;
    }

    /**
     * Ip In Range
     *
     * @param string $ip
     * @param string $cidr
     * @return string
     */
    private function ipInRange($ip, $cidr)
    {
        // Keep working with single ip without range
        if (strpos($cidr, '/') === false) {
            $cidr .= '/32';
        }

        list($subnet, $mask) = explode('/', $cidr);
        return (ip2long($ip) & ~((1 << (32 - $mask)) - 1)) == ip2long($subnet);
    }
}
