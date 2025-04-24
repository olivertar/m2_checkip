<?php

namespace Orangecat\Checkip\Cron;

use Exception;
use Magento\Cron\Model\Cron\Task\Scheduler;
use Magento\Cron\Model\ScheduleFactory;
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

    /** @var ScheduleFactory */
    protected $scheduleFactory;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param File $file
     * @param Config $config
     * @param ScheduleFactory $scheduleFactory
     */
    public function __construct(
        LoggerInterface $logger,
        File            $file,
        Config          $config,
        ScheduleFactory $scheduleFactory
    ) {
        $this->logger = $logger;
        $this->file = $file;
        $this->config = $config;
        $this->scheduleFactory = $scheduleFactory;
    }

    /**
     * Execute
     */
    public function execute()
    {
        $this->updateCronJob();

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

    /**
     * Update Cron Job
     */
    protected function updateCronJob()
    {
        $updateInterval = $this->config->getUpdateInterval();

        if ($updateInterval > 0) {
            $schedule = $this->scheduleFactory->create();
            $schedule->setJobCode('checkip_update_blacklist');
            $schedule->setScheduledAt($this->getNextScheduledTime($updateInterval));
            $schedule->save();
            $this->logger->info(__("Orangecat_Checkip: Cron job updated to run every %s hours.", $updateInterval));
        }
    }

    /**
     * Get Next Scheduled Time
     *
     * @param int $updateInterval
     * @return string
     */
    protected function getNextScheduledTime($updateInterval)
    {
        $currentTime = time();
        $nextExecutionTime = strtotime("+$updateInterval hours", $currentTime);
        return date('Y-m-d H:i:s', $nextExecutionTime);
    }
}
