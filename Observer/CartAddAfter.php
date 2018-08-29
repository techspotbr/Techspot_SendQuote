<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Techspot\SendQuote\Observer;
 
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\SerializerInterface;

class CartAddAfter implements ObserverInterface
{

    protected $_objectManager;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        SerializerInterface $serializer,
        \Magento\Framework\ObjectManagerInterface $objectManager) 
    {
        $this->serializer = $serializer;
        $this->_objectManager = $objectManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {

        $item = $observer->getEvent()->getData('quote_item');         
        $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
        
        $buyRequest = $item->getOptionByCode('info_buyRequest');
        $buyRequestData = $this->serializer->unserialize($buyRequest->getValue());

        if(null != $buyRequestData['sendquote_item_id']){

            $model = $this->_objectManager->create(\Techspot\SendQuote\Model\Item::class);
            $quotationItem = $model->load($buyRequestData['sendquote_item_id']);
            
            if(null != $quotationItem->getCustomPrice()){
                $item->setCustomPrice($quotationItem->getCustomPrice());
                $item->setOriginalCustomPrice($quotationItem->getCustomPrice());
                $item->getProduct()->setIsSuperMode(true);
            } 
        }
    }
}
