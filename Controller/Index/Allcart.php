<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Controller\Index;

use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\App\Action;
use Magento\Framework\App\Action\Context;
use Techspot\SendQuote\Controller\SendquoteProviderInterface;
use Techspot\SendQuote\Model\ItemCarrier;
use Magento\Framework\Controller\ResultFactory;

class Allcart extends \Techspot\SendQuote\Controller\AbstractIndex
{
    /**
     * @var SendquoteProviderInterface
     */
    protected $sendquoteProvider;

    /**
     * @var \Techspot\SendQuote\Model\ItemCarrier
     */
    protected $itemCarrier;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * @param Context $context
     * @param SendquoteProviderInterface $sendquoteProvider
     * @param Validator $formKeyValidator
     * @param ItemCarrier $itemCarrier
     */
    public function __construct(
        Context $context,
        SendquoteProviderInterface $sendquoteProvider,
        Validator $formKeyValidator,
        ItemCarrier $itemCarrier
    ) {
        $this->sendquoteProvider = $sendquoteProvider;
        $this->formKeyValidator = $formKeyValidator;
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
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $resultForward->forward('noroute');
            return $resultForward;
        }

        $sendquote = $this->sendquoteProvider->getSendquote();
        if (!$sendquote) {
            $resultForward->forward('noroute');
            return $resultForward;
        }
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $redirectUrl = $this->itemCarrier->moveAllToCart($sendquote, $this->getRequest()->getParam('qty'));
        $resultRedirect->setUrl($redirectUrl);
        return $resultRedirect;
    }
}
