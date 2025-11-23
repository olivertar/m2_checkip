<?php
/**
 * Copyright © Orangecat. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Orangecat\Checkip\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Glob;
use Orangecat\Checkip\Model\Service\LogIpService;


class LogIpFiles implements ArrayInterface
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
        $logDir = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR)->getAbsolutePath('ipblacklist/log');
        $files = Glob::glob($logDir . '/' . LogIpService::LOG_IP_FILENAME . '*.log');

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
