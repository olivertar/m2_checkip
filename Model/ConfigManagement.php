<?php
namespace Orangecat\Checkip\Model;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Orangecat\Checkip\Api\ConfigManagementInterface;
use Orangecat\Checkip\Model\Config;
use Orangecat\Checkip\Model\Email\Sender as EmailSender;

class ConfigManagement implements ConfigManagementInterface
{
    /** @var Config */
    private $config;

    /** @var WriterInterface */
    protected $configWriter;

    /** @var TypeListInterface */
    protected $cacheTypeList;

    /** @var RemoteAddress */
    private $remoteAddress;

    /** @var EmailSender */
    protected $emailSender;

    /**
     * Constructor
     *
     * @param Config $config
     * @param WriterInterface $configWriter
     * @param TypeListInterface $cacheTypeList
     * @param RemoteAddress $remoteAddress
     * @param EmailSender $emailSender
     */
    public function __construct(
        Config        $config,
        WriterInterface $configWriter,
        TypeListInterface $cacheTypeList,
        RemoteAddress $remoteAddress,
        EmailSender $emailSender
    ) {
        $this->config = $config;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        $this->remoteAddress = $remoteAddress;
        $this->emailSender = $emailSender;
    }

    /**
     * Set Ip Enabled
     *
     * @param bool $enabled
     * @return bool
     */
    public function setIpEnabled($enabled)
    {
        if($this->isIpValidToAccess()) {
            if (!$this->config->isApiIpEnabled()) {
                return false;
            }

            $this->emailSender->sendStatusChangeNotification($enabled);

            if ($this->config->onlyNotification()) {
                return true;
            }

            $this->configWriter->save(Config::XML_PATH_IP_ENABLED, $enabled ? '1' : '0');
            $this->cleanCache();

            return true;
        }

        return false;
    }

    /**
     * Set Bot Enabled
     *
     * @param bool $enabled
     * @return bool
     */
    public function setBotEnabled($enabled)
    {
        if($this->isIpValidToAccess()) {
            if (!$this->config->isApiBotEnabled()) {
                return false;
            }

            $this->emailSender->sendStatusChangeNotification($enabled);

            if ($this->config->onlyNotification()) {
                return true;
            }

            $this->configWriter->save(Config::XML_PATH_BOT_ENABLED, $enabled ? '1' : '0');
            $this->cleanCache();

            return true;
        }

        return false;
    }

    /**
     * Set All Enabled
     *
     * @param bool $enabled
     * @return bool
     */
    public function setAllEnabled($enabled)
    {
        if($this->isIpValidToAccess()) {

            $this->emailSender->sendStatusChangeNotification($enabled);

            if ($this->config->onlyNotification()) {
                return true;
            }

            $ctr = false;
            if ($this->config->isApiIpEnabled()) {
                $this->configWriter->save(Config::XML_PATH_IP_ENABLED, $enabled ? '1' : '0');
                $ctr = true;
            }

            if ($this->config->isApiBotEnabled()) {
                $this->configWriter->save(Config::XML_PATH_BOT_ENABLED, $enabled ? '1' : '0');
                $ctr = true;
            }

            if ($ctr) {
                $this->cleanCache();
                $this->emailSender->sendStatusChangeNotification($enabled);

                return true;
            }
        }

        return false;
    }

    private function cleanCache()
    {
        $this->cacheTypeList->cleanType('config');

        if ($this->config->clearFpcCache()) {
            $this->cacheTypeList->cleanType('full_page');
        }
    }

    private function isIpValidToAccess()
    {
        $ip = $this->remoteAddress->getRemoteAddress();
        if (in_array($ip, $this->config->getApiWhitelist())) {
            return true;
        }

        return false;
    }
}
