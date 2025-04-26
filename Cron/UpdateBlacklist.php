<?php

namespace Orangecat\Checkip\Cron;

use Exception;
use Magento\Framework\Filesystem\Driver\File;
use Orangecat\Checkip\Model\Config;
use Psr\Log\LoggerInterface;

class UpdateBlacklist
{
    /** @var LoggerInterface */
    protected $logger;

    /** @var File */
    protected $file;

    /** @var Config */
    protected $config;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param File $file
     * @param Config $config
     */
    public function __construct(
        LoggerInterface $logger,
        File            $file,
        Config          $config
    ) {
        $this->logger = $logger;
        $this->file = $file;
        $this->config = $config;
    }

    /**
     * Execute
     */
    public function execute()
    {
        try {
            $url = $this->config->getUrlDownloadIpBlacklist();
            $content = file_get_contents($url);
            if ($content !== false) {
                $blacklistPath = BP . Config::FILE_IPS_BLACKLIST;
                $directory = dirname($blacklistPath);
                if (!$this->file->isExists($directory)) {
                    $this->file->createDirectory($directory);
                }

                $this->file->filePutContents($blacklistPath, $content);
                $this->logger->info(__("Orangecat_Checkip: IP list updated successfully."));
            } else {
                $this->logger->error(__("Orangecat_Checkip: Error downloading IP list."));
            }
        } catch (Exception $e) {
            $this->logger->error(__("Orangecat_Checkip: Exception when updating the blacklist - ") . $e->getMessage());
        }
    }
}
