<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Techspot\SendQuote\Block\Customer\Sendquote\Item\Column;

/**
 * Sendquote block customer item cart column
 *
 * @api
 * @since 100.0.2
 */
class Price extends \Techspot\SendQuote\Block\Customer\Sendquote\Item\Column
{
    /**
     * Returns custom_price to show visually to user
     *
     * @param \Techspot\SendQuote\Model\Item $item
     * @return float
     */
    public function getCustomPrice(\Techspot\SendQuote\Model\Item $item)
    {
        $customPrice = $item->getCustomPrice();
        return $customPrice ? $customPrice : 0;
    }

    /**
     * Return product for current item
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProductItem()
    {
        return $this->getItem()->getProduct();
    }
}
