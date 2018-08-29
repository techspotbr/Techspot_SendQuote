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
class Cart extends \Techspot\SendQuote\Block\Customer\Sendquote\Item\Column
{
    /**
     * Returns qty to show visually to user
     *
     * @param \Techspot\SendQuote\Model\Item $item
     * @return float
     */
    public function getAddToCartQty(\Techspot\SendQuote\Model\Item $item)
    {
        $qty = $item->getQty();
        return $qty ? $qty : 1;
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
