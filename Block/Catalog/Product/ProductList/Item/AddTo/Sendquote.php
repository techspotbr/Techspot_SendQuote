<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Block\Catalog\Product\ProductList\Item\AddTo;

/**
 * Add product to sendquote
 */
class Sendquote extends \Magento\Catalog\Block\Product\ProductList\Item\Block
{
     /**
     * @var Techspot\SendQuote\Helper\Data
     */
    protected $_sendquoteHelper;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Techspot\SendQuote\Helper\Data $sendquoteHelper
    ) {
        parent::__construct(
            $context
        );
        $this->_sendquoteHelper = $sendquoteHelper;
    }

    /**
     * @return \Techspot\SendQuote\Helper\Data
     */
    public function getSendquoteHelper()
    {
        return $this->_sendquoteHelper;
    }

    /**
     * Check whether the sendquote is allowed
     *
     * @return string|bool
     */
    public function isSendQuoteAllowed()
    {
        if($this->getProduct()->_getData(\Techspot\SendQuote\Model\Catalog\Product::ONLY_QUOTATION_ATTRIBUTE)){
            return $this->_sendquoteHelper->isAllow();
        }
        return false;
    }

    /**
     * Retrieve params for adding Product to sendquote
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToSendquoteParams($product)
    {
        return $this->_sendquoteHelper->getAddParams($product);
    }

}
