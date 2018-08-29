<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Techspot\SendQuote\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Techspot\SendQuote\Helper\Data;
use Techspot\SendQuote\Model\Sendquote;
use Techspot\SendQuote\Model\SendquoteFactory;

/**
 * Class CartUpdateBefore
 * @package Techspot\SendQuote\Observer
 */
class CartUpdateBefore implements ObserverInterface
{
    /**
     * Sendquote data
     *
     * @var Data
     */
    protected $sendquoteData;

    /**
     * @var SendquoteFactory
     */
    protected $sendquoteFactory;

    /**
     * @param Data $sendquoteData
     * @param SendquoteFactory $sendquoteFactory
     */
    public function __construct(
        Data $sendquoteData,
        SendquoteFactory $sendquoteFactory
    ) {
        $this->sendquoteData = $sendquoteData;
        $this->sendquoteFactory = $sendquoteFactory;
    }

    /**
     * Get customer sendquote model instance
     *
     * @param   int $customerId
     * @return  Sendquote|false
     */
    protected function getSendquote($customerId)
    {
        if (!$customerId) {
            return false;
        }
        return $this->sendquoteFactory->create()->loadByCustomerId($customerId, true);
    }

    /**
     * Check move quote item to sendquote request
     *
     * @param   Observer $observer
     * @return  $this
     */
    public function execute(Observer $observer)
    {
        $cart = $observer->getEvent()->getCart();
        $data = $observer->getEvent()->getInfo()->toArray();
        $productIds = [];

        $sendquote = $this->getSendquote($cart->getQuote()->getCustomerId());
        if (!$sendquote) {
            return $this;
        }

        /**
         * Collect product ids marked for move to sendquote
         */
        /*
        foreach ($data as $itemId => $itemInfo) {
            if (!empty($itemInfo['sendquote']) && ($item = $cart->getQuote()->getItemById($itemId))) {
                $productId = $item->getProductId();
                $buyRequest = $item->getBuyRequest();

                if (array_key_exists('qty', $itemInfo) && is_numeric($itemInfo['qty'])) {
                    $buyRequest->setQty($itemInfo['qty']);
                }
                $sendquote->addNewItem($productId, $buyRequest);

                $productIds[] = $productId;
                $cart->getQuote()->removeItem($itemId);
            }
        }

        if (count($productIds)) {
            $sendquote->save();
            $this->sendquoteData->calculate();
        }
        */
        return $this;
    }
}
