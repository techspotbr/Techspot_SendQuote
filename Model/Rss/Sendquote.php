<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Techspot\SendQuote\Model\Rss;

use Magento\Framework\App\Rss\DataProviderInterface;

/**
 * Sendquote RSS model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Sendquote implements DataProviderInterface
{
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * System event manager
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * Parent layout of the block
     *
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var \Techspot\SendQuote\Helper\Data
     */
    protected $sendquoteHelper;

    /**
     * @var \Magento\Catalog\Helper\Output
     */
    protected $outputHelper;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var \Techspot\SendQuote\Block\Customer\Sendquote
     */
    protected $sendquoteBlock;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @param \Techspot\SendQuote\Helper\Rss $sendquoteHelper
     * @param \Techspot\SendQuote\Block\Customer\Sendquote $sendquoteBlock
     * @param \Magento\Catalog\Helper\Output $outputHelper
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Techspot\SendQuote\Helper\Rss $sendquoteHelper,
        \Techspot\SendQuote\Block\Customer\Sendquote $sendquoteBlock,
        \Magento\Catalog\Helper\Output $outputHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->sendquoteHelper = $sendquoteHelper;
        $this->sendquoteBlock = $sendquoteBlock;
        $this->outputHelper = $outputHelper;
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->eventManager = $eventManager;
        $this->customerFactory = $customerFactory;
        $this->layout = $layout;
        $this->request = $request;
    }

    /**
     * Check if RSS feed allowed
     *
     * @return mixed
     */
    public function isAllowed()
    {
        return (bool)$this->scopeConfig->getValue(
            'rss/sendquote/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get RSS feed items
     *
     * @return array
     */
    public function getRssData()
    {
        $sendquote = $this->getSendquote();
        if ($sendquote->getId()) {
            $data = $this->getHeader();

            /** @var $sendquoteItem \Techspot\SendQuote\Model\Item */
            foreach ($sendquote->getItemCollection() as $sendquoteItem) {
                /* @var $product \Magento\Catalog\Model\Product */
                $product = $sendquoteItem->getProduct();
                $productUrl = $this->sendquoteBlock->getProductUrl($product, ['_rss' => true]);
                $product->setAllowedInRss(true);
                $product->setAllowedPriceInRss(true);
                $product->setProductUrl($productUrl);
                $args = ['product' => $product];

                $this->eventManager->dispatch('rss_sendquote_xml_callback', $args);

                if (!$product->getAllowedInRss()) {
                    continue;
                }

                $description = '<table><tr><td><a href="' . $productUrl . '"><img src="'
                    . $this->imageHelper->init($product, 'rss_thumbnail')->getUrl()
                    . '" border="0" align="left" height="75" width="75"></a></td>'
                    . '<td style="text-decoration:none;">'
                    . $this->outputHelper->productAttribute(
                        $product,
                        $product->getShortDescription(),
                        'short_description'
                    ) . '<p>';

                if ($product->getAllowedPriceInRss()) {
                    $description .= $this->getProductPriceHtml($product);
                }
                $description .= '</p>';

                if (trim($product->getDescription()) != '') {
                    $description .= '<p>' . __('Comment:') . ' '
                        . $this->outputHelper->productAttribute(
                            $product,
                            $product->getDescription(),
                            'description'
                        ) . '<p>';
                }
                $description .= '</td></tr></table>';

                $data['entries'][] = ([
                    'title' => $product->getName(),
                    'link' => $productUrl,
                    'description' => $description,
                ]);
            }
        } else {
            $data = [
                'title' => __('We cannot retrieve the Quotations.'),
                'description' => __('We cannot retrieve the Quotations.'),
                'link' => $this->urlBuilder->getUrl(),
                'charset' => 'UTF-8',
            ];
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getCacheKey()
    {
        return 'rss_sendquote_data';
    }

    /**
     * @return int
     */
    public function getCacheLifetime()
    {
        return 60;
    }

    /**
     * Get data for Header section of RSS feed
     *
     * @return array
     */
    public function getHeader()
    {
        $customerId = $this->getSendquote()->getCustomerId();
        $customer = $this->customerFactory->create()->load($customerId);
        $title = __('%1\'s Sendquote', $customer->getName());
        $newUrl = $this->urlBuilder->getUrl(
            'sendquote/shared/index',
            ['code' => $this->getSendquote()->getSharingCode()]
        );

        return ['title' => $title, 'description' => $title, 'link' => $newUrl, 'charset' => 'UTF-8'];
    }

    /**
     * Retrieve Sendquote model
     *
     * @return \Techspot\SendQuote\Model\Sendquote
     */
    protected function getSendquote()
    {
        $sendquote = $this->sendquoteHelper->getSendquote();
        return $sendquote;
    }

    /**
     * Return HTML block with product price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getProductPriceHtml(\Magento\Catalog\Model\Product $product)
    {
        $price = '';
        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->layout->getBlock('product.price.render.default');
        if (!$priceRender) {
            $priceRender = $this->layout->createBlock(
                \Magento\Framework\Pricing\Render::class,
                'product.price.render.default',
                ['data' => ['price_render_handle' => 'catalog_product_prices']]
            );
        }
        if ($priceRender) {
            $price = $priceRender->render(
                'sendquote_configured_price',
                $product,
                ['zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST]
            );
        }
        return $price;
    }

    /**
     * @return array
     */
    public function getFeeds()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthRequired()
    {
        if ($this->request->getParam('sharing_code') == $this->getSendquote()->getSharingCode()) {
            return false;
        }
        return true;
    }
}
