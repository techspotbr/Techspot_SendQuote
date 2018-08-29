<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Sendquote block customer items
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Techspot\SendQuote\Block\Share\Email;

/**
 * @api
 * @since 100.0.2
 */
class Items extends \Techspot\SendQuote\Block\AbstractBlock
{
    /**
     * @var string
     */
    protected $_template = 'email/items.phtml';

    /**
     * Retrieve Product View URL
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $additional
     * @return string
     */
    public function getProductUrl($product, $additional = [])
    {
        $additional['_scope_to_url'] = true;
        return parent::getProductUrl($product, $additional);
    }

    /**
     * Retrieve URL for add product to shopping cart
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = [])
    {
        $additional['nocookie'] = 1;
        $additional['_scope_to_url'] = true;
        return parent::getAddToCartUrl($product, $additional);
    }

    /**
     * Check whether sendquote item has description
     *
     * @param \Techspot\SendQuote\Model\Item $item
     * @return bool
     */
    public function hasDescription($item)
    {
        $hasDescription = parent::hasDescription($item);
        if ($hasDescription) {
            return $item->getDescription() !== $this->_sendquoteHelper->defaultCommentString();
        }
        return $hasDescription;
    }
}
