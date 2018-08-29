<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Block\Catalog\Product\ProductList\Item\AddTo;

/**
 * Add product to sendquote
 *
 * @api
 * @since 100.1.1
 */
class Sendquote extends \Magento\Catalog\Block\Product\ProductList\Item\Block
{
    /**
     * @var Techspot\SendQuote\Helper\Data
     */
    protected $_sendquoteHelper;
    
    /**
     * @param \Techspot\SendQuote\Helper\Data $sendquoteHelper
     * @codingStandardsIgnoreStart
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Techspot\SendQuote\Helper\Data $sendquoteHelper
    ) {
        $this->_sendquoteHelper = $sendquoteHelper;
    }

    /**
     * @return \Techspot\SendQuote\Helper\Data
     * @since 100.1.1
     */
    public function getSendquoteHelper()
    {
        return $this->_sendquoteHelper;
    }
}
