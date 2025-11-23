<?php
/**
 * Copyright © Orangecat. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Orangecat\Checkip\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class LogBotDetection implements ArrayInterface
{
    /**
     * To Option Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $out[] = [
            'value' => 'crawlerlist',
            'label' => 'Crawler list'
        ];
        $out[] = [
            'value' => 'whitelist',
            'label' => 'Whitelist'
        ];
        $out[] = [
            'value' => 'blacklist',
            'label' => 'Blacklist'
        ];

        return $out;
    }
}
