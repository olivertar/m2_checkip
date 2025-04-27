<?php
namespace Orangecat\Checkip\Api;

interface ConfigManagementInterface
{
    /**
     * Set Ip Enabled
     *
     * @param bool $enabled
     * @return bool
     */
    public function setIpEnabled($enabled);

    /**
     * Set Bot Enabled
     *
     * @param bool $enabled
     * @return bool
     */
    public function setBotEnabled($enabled);
    /**
     * Set All Enabled
     *
     * @param bool $enabled
     * @return bool
     */
    public function setAllEnabled($enabled);
}
