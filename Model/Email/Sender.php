<?php
namespace Orangecat\Checkip\Model\Email;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Orangecat\Checkip\Model\Config;

class Sender
{
    /** @var TransportBuilder */
    protected $transportBuilder;

    /** @var StoreManagerInterface */
    protected $storeManager;

    /** @var Config */
    private $config;

    /**
     * Constructor
     *
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        Config        $config
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    /**
     * Send Status Change Notification
     *
     * @param bool $enabled
     */
    public function sendStatusChangeNotification($enabled)
    {
        $emails = $this->config->getApiEmails();

        if (!$emails) {
            return;
        }

        $enabledText = $enabled ? 'Enabled' : 'Disabled';

        $store = $this->storeManager->getStore();
        $transport = $this->transportBuilder
            ->setTemplateIdentifier('checkip_status_change_email')
            ->setTemplateOptions([
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $store->getId(),
            ])
            ->setTemplateVars([
                'store' => $store,
                'status' => $enabledText
            ])
            ->setFromByScope('general')
            ->addTo($emails)
            ->getTransport();

        $transport->sendMessage();
    }
}
