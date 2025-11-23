<?php
/**
 * Copyright © Orangecat. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Orangecat\Checkip\Block\Adminhtml\System;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ApiInstructions extends Field
{
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = '<div style="padding:5px 0;">';
        $html .= '<p>' . __('The module allows you to enable or disable IP or Bot checking via API.') . '</p>';
        $html .= '<p>' . __('If your infrastructure is capable of setting alerts, you can detect increased consumption, for example, of your database and send a notification to activate anti-bot protection and deactivate it when the level returns to normal.') . '</p>';
        $html .= '<p>' . __('The available endpoints are:') . '</p>';
        $html .= '<p>- GET /V1/checkip/enableip?enabled=true:false</p>';
        $html .= '<p>- GET /V1/checkip/enablebot?enabled=true:false</p>';
        $html .= '<p>- GET /V1/checkip/enableall?enabled=true:false</p>';
        $html .= '<p>' . __('You can also receive an email notification every time the API is reached.') . '</p>';
        $html .= '</div>';

        return $html;
    }
}

