<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Controller\Shared;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     * @var SendquoteProvider
     */
    protected $sendquoteProvider;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param Context $context
     * @param SendquoteProvider $sendquoteProvider
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        Context $context,
        SendquoteProvider $sendquoteProvider,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->sendquoteProvider = $sendquoteProvider;
        $this->registry = $registry;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Shared sendquote view page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $sendquote = $this->sendquoteProvider->getSendquote();
        $customerId = $this->customerSession->getCustomerId();

        if ($sendquote && $sendquote->getCustomerId() && $sendquote->getCustomerId() == $customerId) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl(
                $this->_objectManager->get(\Techspot\SendQuote\Helper\Data::class)->getListUrl($sendquote->getId())
            );
            return $resultRedirect;
        }

        $this->registry->register('shared_sendquote', $sendquote);

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        return $resultPage;
    }
}
