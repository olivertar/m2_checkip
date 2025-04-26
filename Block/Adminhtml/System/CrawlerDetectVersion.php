<?php

namespace Orangecat\Checkip\Block\Adminhtml\System;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class CrawlerDetectVersion extends Field
{
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = '<div style="padding:5px 0;">';
        $html .= '<strong style="color:red;">' . __('To ensure your store has the highest level of protection, it is important that the Crawler Detect dependency is always up to date.') . '</strong>';

        try {
            if (!class_exists(\Composer\InstalledVersions::class)) {
                $html .= '<br/><strong style="color:orange;">' . __('Composer not installed.') . '</strong>';
            } elseif (!\Composer\InstalledVersions::isInstalled('jaybizzle/crawler-detect')) {
                $html .= '<br/><strong style="color:red;">' . __('Not installed') . '</strong>';
            } else {
                $version = \Composer\InstalledVersions::getPrettyVersion('jaybizzle/crawler-detect');
                $html .= '<br/><strong>' . __('Installed version') . ':</strong> ' . $version;
            }

            $release = $this->getLatestRelease();

            if ($release) {
                $html .= '<br/><strong>' . __('Github last release') . ':</strong> ' . $release['tag_name'];
                $html .= '<br/><strong>' . __('Date last release') . ':</strong> ' . $release['published_at'];
                $html .= '<br/><a href="' . $release['html_url'] . '" target="_blank">' . __('Check in GitHub') . '</a>';
            } else {
                $html .= '<br/><strong style="color:orange;">' . __('Cant get last release version from Github.') . '</strong>';
            }
        } catch (\Throwable $e) {
            $html .= '<br/><strong style="color:red;">' . __('Error checking version.') . ':</strong> ' . $e->getMessage();
        }

        $html .= '</div>';
        return $html;
    }

    private function getLatestRelease()
    {
        $url = 'https://api.github.com/repos/JayBizzle/Crawler-Detect/releases/latest';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/vnd.github.v3+json',
            'User-Agent: Orangecat_Checkip'
        ]);
        $response = curl_exec($ch);

        if ($response === false) {
            return null;
        }

        $releaseData = json_decode($response, true);

        if (isset($releaseData['tag_name'])) {
            return $releaseData;
        }

        return null;
    }
}
