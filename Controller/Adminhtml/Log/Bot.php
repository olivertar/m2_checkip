<?php
/**
 * Copyright © Orangecat. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Orangecat\Checkip\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Bot extends Action
{
    /** @var PageFactory */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Action\Context $context,
        PageFactory    $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute
     *
     * @return object
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__("Detected Bot Log"));
        return $resultPage;
    }

    /**
     * Is Allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return true;
    }
}
