<?php

namespace Orangecat\Checkip\Model\Service;

use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Orangecat\Checkip\Model\Config;
use Orangecat\Checkip\Model\LoggerIp;

class LogIpService
{
    public const LOG_IP_FILENAME = 'ips_detected_in_blacklist';

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var File
     */
    private $file;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var bool $alreadyLogged
     */
    private bool $alreadyLogged = false;

    /**
     * Constructor
     *
     * @param LoggerIp $logger
     * @param File $file
     * @param DateTime $dateTime
     * @param Config $config
     */
    public function __construct(
        LoggerIp   $logger,
        File     $file,
        DateTime $dateTime,
        Config   $config
    ) {
        $this->logger = $logger;
        $this->file = $file;
        $this->dateTime = $dateTime;
        $this->config = $config;
    }

    /**
     * Log
     *
     * @param string $message
     */
    public function log($message)
    {
        if (!$this->alreadyLogged) {

            $directory = BP . '/var/ipblacklist/log/';
            if (!$this->file->isExists($directory)) {
                $this->file->createDirectory($directory);
            }

            $logPath = $directory . self::LOG_IP_FILENAME . '.log';

            $this->rotateIfNeeded($logPath);

            $this->logger->debug($message);
            $this->alreadyLogged = true;
        }
    }

    /**
     * Rotate If Needed
     *
     * @param string $logPath
     */
    private function rotateIfNeeded($logPath)
    {
        if (!$this->file->isExists($logPath)) {
            return;
        }

        $lastModified = $this->file->stat($logPath)['mtime'];
        $lastModifiedDate = date('Y-m-d', $lastModified);
        $today = $this->dateTime->gmtDate('Y-m-d');

        if ($lastModifiedDate !== $today) {
            $archivedPath = BP . '/var/ipblacklist/log/' . self::LOG_IP_FILENAME . '_' . $lastModifiedDate . '.log';

            if (!$this->file->isExists($archivedPath)) {
                $this->file->rename($logPath, $archivedPath);
            }

            $this->cleanupOldLogs();
        }
    }

    /**
     * Cleanup Old Logs
     */
    private function cleanupOldLogs()
    {
        $logDir = BP . '/var/ipblacklist/log';
        $retentionDays = $this->config->getLogRetentionDays();
        $files = $this->file->readDirectory($logDir);

        $now = time();

        foreach ($files as $file) {
            if (preg_match('/' . self::LOG_IP_FILENAME . '_(\d{4}-\d{2}-\d{2})\.log$/', $file, $matches)) {
                $fileDate = strtotime($matches[1]);
                $age = ($now - $fileDate) / (60 * 60 * 24);

                if ($age > $retentionDays) {
                    $this->file->deleteFile($file);
                }
            }
        }
    }
}
