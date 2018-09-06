<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Techspot\SendQuote\Block\Catalog\Product\View\AddTo;

use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Product view sendquote block
 *
 * @api
 * @since 100.1.1
 */
class Sendquote extends \Magento\Catalog\Block\Product\View
{

    /**
     * @var Techspot\SendQuote\Helper\Data
     */
    protected $_sendquoteHelper;

    /**
     * @param Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Customer\Model\Session $customerSession
     * @param ProductRepositoryInterface|\Magento\Framework\Pricing\PriceCurrencyInterface $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param array $data
     * @param \Techspot\SendQuote\Helper\Data $sendquoteHelper
     * @codingStandardsIgnoreStart
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data = [],
        \Techspot\SendQuote\Helper\Data $sendquoteHelper
    ) {
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
        $this->_sendquoteHelper = $sendquoteHelper;
    }

    /**
     * Return sendquote widget options json
     *
     * @return string
     * @since 100.1.1
     */
    public function getSendquoteOptionsJson()
    {
        return $this->_jsonEncoder->encode($this->getSendquoteOptions());
    }

    /**
     * Return sendquote widget options
     *
     * @return array
     * @since 100.1.1
     */
    public function getSendquoteOptions()
    {
        return ['productType' => $this->escapeHtml($this->getProduct()->getTypeId())];
    }

    /**
     * Return sendquote params
     *
     * @return string
     * @since 100.1.1
     */
    public function getSendquoteParams()
    {
        $product = $this->getProduct();
        return $this->_sendquoteHelper->getAddParams($product);
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
}
