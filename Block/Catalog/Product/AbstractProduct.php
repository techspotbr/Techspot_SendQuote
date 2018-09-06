<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Block\Catalog\Product;

/**
 * Class AbstractProduct
 */
class AbstractProduct extends \Magento\Catalog\Block\Product\AbstractProduct
{ 
    /**
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_sendquoteHelper;

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(\Magento\Catalog\Block\Product\Context $context, array $data = [])
    {
        $this->_sendquoteHelper = $context->getSendquoteHelper();
        parent::__construct($context, $data);
    }
}
