<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Techspot\SendQuote\Block;

/**
 * Sendquote Product Items abstract Block
 */
abstract class AbstractBlock extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * Sendquote Product Items Collection
     *
     * @var \Techspot\SendQuote\Model\ResourceModel\Item\Collection
     */
    protected $_collection;

    /**
     * Store sendquote Helper
     *
     * @var  \Techspot\SendQuote\Helper\Data $sendquoteHelper
     */
    protected $_sendquoteHelper;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     * @param \Techspot\SendQuote\Helper\Data $sendquoteHelper
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = [],
        \Techspot\SendQuote\Helper\Data $sendquoteHelper
    ) {
        $this->_sendquoteHelper = $sendquoteHelper;
        $this->httpContext = $httpContext;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Retrieve Sendquote Data Helper
     *
     * @return \Techspot\SendQuote\Helper\Data
     */
    protected function _getHelper()
    {
        return $this->_sendquoteHelper;
    }

    /**
     * Retrieve Sendquote model
     *
     * @return \Techspot\SendQuote\Model\Sendquote
     */
    protected function _getSendquote()
    {
        return $this->_getHelper()->getSendquote();
    }

    /**
     * Prepare additional conditions to collection
     *
     * @param \Techspot\SendQuote\Model\ResourceModel\Item\Collection $collection
     * @return \Techspot\SendQuote\Block\Customer\Sendquote
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _prepareCollection($collection)
    {
        return $this;
    }

    /**
     * Create sendquote item collection
     *
     * @return \Techspot\SendQuote\Model\ResourceModel\Item\Collection
     */
    protected function _createSendquoteItemCollection()
    {
        return $this->_getSendquote()->getItemCollection();
    }

    /**
     * Retrieve Sendquote Product Items collection
     *
     * @return \Techspot\SendQuote\Model\ResourceModel\Item\Collection
     */
    public function getSendquoteItems()
    {
        if ($this->_collection === null) {
            $this->_collection = $this->_createSendquoteItemCollection();
            $this->_prepareCollection($this->_collection);
        }

        return $this->_collection;
    }

    /**
     * Retrieve sendquote instance
     *
     * @return \Techspot\SendQuote\Model\Sendquote
     */
    public function getSendquoteInstance()
    {
        return $this->_getSendquote();
    }

    /**
     * Retrieve params for Removing item from sendquote
     *
     * @param \Magento\Catalog\Model\Product|\Techspot\SendQuote\Model\Item $item
     *
     * @return string
     */
    public function getItemRemoveParams($item)
    {
        return $this->_getHelper()->getRemoveParams($item);
    }

    /**
     * Retrieve Add Item to shopping cart params for POST request
     *
     * @param string|\Magento\Catalog\Model\Product|\Techspot\SendQuote\Model\Item $item
     * @return string
     */
    public function getItemAddToCartParams($item)
    {
        return $this->_getHelper()->getAddToCartParams($item);
    }

    /**
     * Retrieve Add Item to shopping cart URL from shared sendquote
     *
     * @param string|\Magento\Catalog\Model\Product|\Techspot\SendQuote\Model\Item $item
     * @return string
     */
    public function getSharedItemAddToCartUrl($item)
    {
        return $this->_getHelper()->getSharedAddToCartUrl($item);
    }

    /**
     * Retrieve URL for adding All items to shopping cart from shared sendquote
     *
     * @return string
     */
    public function getSharedAddAllToCartUrl()
    {
        return $this->_getHelper()->getSharedAddAllToCartUrl();
    }

    /**
     * Retrieve params for adding Product to sendquote
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToSendquoteParams($product)
    {
        return $this->_getHelper()->getAddParams($product);
    }

    /**
     * Returns item configure url in sendquote
     *
     * @param \Magento\Catalog\Model\Product|\Techspot\SendQuote\Model\Item $product
     *
     * @return string
     */
    public function getItemConfigureUrl($product)
    {
        return $this->_getHelper()->getConfigureUrl($product);
    }

    /**
     * Retrieve Escaped Description for Sendquote Item
     *
     * @param \Magento\Catalog\Model\Product $item
     * @return string
     */
    public function getEscapedDescription($item)
    {
        if ($item->getDescription()) {
            return $this->escapeHtml($item->getDescription());
        }
        return '&nbsp;';
    }

    /**
     * Check Sendquote item has description
     *
     * @param \Magento\Catalog\Model\Product $item
     * @return bool
     */
    public function hasDescription($item)
    {
        return trim($item->getDescription()) != '';
    }

    /**
     * Retrieve formated Date
     *
     * @param string $date
     * @return string
     */
    public function getFormatedDate($date)
    {
        return $this->formatDate($date, \IntlDateFormatter::MEDIUM);
    }

    /**
     * Check is the sendquote has a salable product(s)
     *
     * @return bool
     */
    public function isSaleable()
    {
        foreach ($this->getSendquoteItems() as $item) {
            if ($item->getProduct()->isSaleable()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve sendquote loaded items count
     *
     * @return int
     */
    public function getSendquoteItemsCount()
    {
        return $this->_getSendquote()->getItemsCount();
    }

    /**
     * Retrieve Qty from item
     *
     * @param \Techspot\SendQuote\Model\Item|\Magento\Catalog\Model\Product $item
     * @return float
     */
    public function getQty($item)
    {
        $qty = $item->getQty() * 1;
        if (!$qty) {
            $qty = 1;
        }
        return $qty;
    }

    /**
     * Check is the sendquote has items
     *
     * @return bool
     */
    public function hasSendquoteItems()
    {
        return $this->getSendquoteItemsCount() > 0;
    }

    /**
     * Retrieve URL to item Product
     *
     * @param  \Techspot\SendQuote\Model\Item|\Magento\Catalog\Model\Product $item
     * @param  array $additional
     * @return string
     */
    public function getProductUrl($item, $additional = [])
    {
        return $this->_getHelper()->getProductUrl($item, $additional);
    }

    /**
     * Product image url getter
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getImageUrl($product)
    {
        return $this->_imageHelper->init($product, 'sendquote_small_image')->getUrl();
    }

    /**
     * Return HTML block with price
     *
     * @param \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item
     * @param string $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string|null
     */
    public function getItemPriceHtml(
        \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item,
        $priceType = \Magento\Catalog\Pricing\Price\ConfiguredPriceInterface::CONFIGURED_PRICE_CODE,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        $priceRender->setItem($item);
        $arguments += [
            'zone'         => $renderZone,
            'render_block' => $priceRender
        ];
        return $priceRender ? $priceRender->render($priceType, $item->getProduct(), $arguments) : null;
    }
}
