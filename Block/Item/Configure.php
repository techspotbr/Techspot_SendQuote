<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Sendquote Item Configure block
 * Serves for configuring item on product view page
 *
 * @module     Sendquote
 */
namespace Techspot\SendQuote\Block\Item;

/**
 * @api
 * @since 100.0.2
 */
class Configure extends \Magento\Framework\View\Element\Template
{
    /**
     * Sendquote data
     *
     * @var \Techspot\SendQuote\Helper\Data
     */
    protected $_sendquoteData = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Techspot\SendQuote\Helper\Data $sendquoteData
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Techspot\SendQuote\Helper\Data $sendquoteData,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_sendquoteData = $sendquoteData;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Return sendquote widget options
     *
     * @return array
     */
    public function getSendquoteOptions()
    {
        return ['productType' => $this->escapeHtml($this->getProduct()->getTypeId())];
    }

    /**
     * Returns product being edited
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('product');
    }

    /**
     * Get update params for http post
     *
     * @return bool|string
     */
    public function getUpdateParams()
    {
        return $this->_sendquoteData->getUpdateParams($this->getSendquoteItem());
    }

    /**
     * Returns sendquote item being configured
     *
     * @return \Magento\Catalog\Model\Product|\Techspot\SendQuote\Model\Item
     */
    protected function getSendquoteItem()
    {
        return $this->_coreRegistry->registry('sendquote_item');
    }

    /**
     * Configure product view blocks
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        // Set custom add to cart url
        $block = $this->getLayout()->getBlock('product.info');
        if ($block && $this->getSendquoteItem()) {
            $url = $this->_sendquoteData->getAddToCartUrl($this->getSendquoteItem());
            $block->setCustomAddToCartUrl($url);
        }

        return parent::_prepareLayout();
    }
}
