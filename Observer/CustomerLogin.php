<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Techspot\SendQuote\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Techspot\SendQuote\Helper\Data;

/**
 * Class CustomerLogin
 * @package Techspot\SendQuote\Observer
 */
class CustomerLogin implements ObserverInterface
{
    /**
     * @var \Techspot\SendQuote\Helper\Data
     */
    protected $sendquoteData;

    /**
     * @param Data $sendquoteData
     */
    public function __construct(Data $sendquoteData)
    {
        $this->sendquoteData = $sendquoteData;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $this->sendquoteData->calculate();
    }
}
