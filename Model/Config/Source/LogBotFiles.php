<?php

namespace Orangecat\Checkip\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Glob;
use Orangecat\Checkip\Model\Service\LogBotService;

class LogBotFiles implements ArrayInterface
{
    /** @var Filesystem */
    protected $filesystem;

    /**
     * Constructor
     *
     * @param Filesystem $filesystem
     */
    public function __construct(
        Filesystem $filesystem
    ) {
        $this->filesystem = $filesystem;
    }

    /**
     * To Option Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $logDir = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR)->getAbsolutePath('botblacklist/log');
        $files = Glob::glob($logDir . '/' . LogBotService::LOG_BOT_FILENAME . '*.log');

        $logFiles = [];
        foreach ($files as $file) {
            $filename = basename($file);
            $logFiles[] = [
                'value' => $filename,
                'label' => $filename
            ];
        }

        return $logFiles;
    }
}
