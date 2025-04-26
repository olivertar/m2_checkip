<?php

namespace Orangecat\Checkip\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ActionMode implements ArrayInterface
{
    /**
     * To Option Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __("Block access")],
            ['value' => '2', 'label' => __("Pause access (delay)")],
            ['value' => '3', 'label' => __("Only log")]
        ];
    }
}
