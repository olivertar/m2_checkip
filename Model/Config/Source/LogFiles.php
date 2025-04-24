<?php

namespace Orangecat\Checkip\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Orangecat\Checkip\Block\Adminhtml\Log\Index as LogBlock;

class LogFiles implements ArrayInterface
{
    /** @var LogBlock */
    protected $logBlock;

    /**
     * Constructor
     *
     * @param LogBlock $logBlock
     */
    public function __construct(LogBlock $logBlock)
    {
        $this->logBlock = $logBlock;
    }

    /**
     * To Option Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->logBlock->getAvailableLogFiles();
    }
}
