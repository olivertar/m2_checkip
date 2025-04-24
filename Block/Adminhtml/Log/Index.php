<?php

namespace Orangecat\Checkip\Block\Adminhtml\Log;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Glob;
use Orangecat\Checkip\Model\Service\LogService;

class Index extends Template
{
    /** @var Filesystem */
    protected $filesystem;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Filesystem $filesystem
     * @param array $data
     */
    public function __construct(
        Context    $context,
        Filesystem $filesystem,
        array      $data = []
    ) {
        $this->filesystem = $filesystem;
        parent::__construct($context, $data);
    }

    /**
     * Get Available Log Files
     *
     * @return array
     */
    public function getAvailableLogFiles()
    {
        $logDir = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR)->getAbsolutePath('ipblacklist/log');
        $files = Glob::glob($logDir . '/' . LogService::LOG_FILENAME . '*.log');

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
