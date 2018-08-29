<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Controller\Shared;

use Magento\Framework\App\Action\Context;
use Techspot\SendQuote\Model\ItemCarrier;
use Magento\Framework\Controller\ResultFactory;

class Allcart extends \Magento\Framework\App\Action\Action
{
    /**
     * @var SendquoteProvider
     */
    protected $sendquoteProvider;

    /**
     * @var \Techspot\SendQuote\Model\ItemCarrier
     */
    protected $itemCarrier;

    /**
     * @param Context $context
     * @param SendquoteProvider $sendquoteProvider
     * @param ItemCarrier $itemCarrier
     */
    public function __construct(
        Context $context,
        SendquoteProvider $sendquoteProvider,
        ItemCarrier $itemCarrier
    ) {
        $this->sendquoteProvider = $sendquoteProvider;
        $this->itemCarrier = $itemCarrier;
        parent::__construct($context);
    }

    /**
     * Add all items from sendquote to shopping cart
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $sendquote = $this->sendquoteProvider->getSendquote();
        if (!$sendquote) {
            /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $resultForward->forward('noroute');
            return $resultForward;
        }
        $redirectUrl = $this->itemCarrier->moveAllToCart($sendquote, $this->getRequest()->getParam('qty'));
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($redirectUrl);
        return $resultRedirect;
    }
}
