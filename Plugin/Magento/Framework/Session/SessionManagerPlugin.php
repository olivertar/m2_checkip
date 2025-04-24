<?php

namespace Orangecat\Checkip\Plugin\Magento\Framework\Session;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Session\SessionManager;
use Orangecat\Checkip\Model\Config;
use Orangecat\Checkip\Model\Service\LogService;

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

    /** @var LogService */
    private $logService;

    /**
     * Constructor
     *
     * @param Config $config
     * @param HttpRequest $httpRequest
     * @param RemoteAddress $remoteAddress
     * @param File $file
     * @param LogService $logService
     */
    public function __construct(
        Config        $config,
        HttpRequest   $httpRequest,
        RemoteAddress $remoteAddress,
        File          $file,
        LogService    $logService
    ) {
        $this->config = $config;
        $this->httpRequest = $httpRequest;
        $this->remoteAddress = $remoteAddress;
        $this->file = $file;
        $this->logService = $logService;
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
        if (!$this->config->isEnabled()) {
            return $proceed();
        }

        $ip = $this->remoteAddress->getRemoteAddress();
        $blacklistPath = BP . Config::FILE_IPS_BLACKLIST;

        if (!$this->file->isExists($blacklistPath)) {
            return $proceed();
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
                $userAgent = $this->httpRequest->getServer('HTTP_USER_AGENT', '');
                $this->logService->log($ip . '|' . $cidr . '|' . $userAgent);

                $mode = $this->config->getActionMode();

                if ($mode === 1) {
                    return $subject;
                } elseif ($mode === 2) {
                    sleep($this->config->getDelaySeconds());
                }

                break;
            }
        }

        return $proceed();
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
        list($subnet, $mask) = explode('/', $cidr);
        return (ip2long($ip) & ~((1 << (32 - $mask)) - 1)) == ip2long($subnet);
    }
}
