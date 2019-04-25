<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\CustomerData;

use Magento\Catalog\Model\Product\Image\NotLoadInfoImageException;
use Magento\Customer\CustomerData\SectionSourceInterface;

/**
 * Sendquote section
 */
class Sendquote implements SectionSourceInterface
{
    /**
     * @var string
     */
    const SIDEBAR_ITEMS_NUMBER = 3;

    /**
     * @var \Techspot\SendQuote\Helper\Data
     */
    protected $sendquoteHelper;

    /**
     * @var \Magento\Catalog\Helper\ImageFactory
     */
    protected $imageHelperFactory;

    /**
     * @var \Magento\Framework\App\ViewInterface
     */
    protected $view;

    /**
     * @var \Techspot\SendQuote\Block\Customer\Sidebar
     */
    protected $block;

    /**
     * @param \Techspot\SendQuote\Helper\Data $sendquoteHelper
     * @param \Techspot\SendQuote\Block\Customer\Sidebar $block
     * @param \Magento\Catalog\Helper\ImageFactory $imageHelperFactory
     * @param \Magento\Framework\App\ViewInterface $view
     */
    public function __construct(
        \Techspot\SendQuote\Helper\Data $sendquoteHelper,
        \Techspot\SendQuote\Block\Customer\Sidebar $block,
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory,
        \Magento\Framework\App\ViewInterface $view
    ) {
        $this->sendquoteHelper = $sendquoteHelper;
        $this->imageHelperFactory = $imageHelperFactory;
        $this->block = $block;
        $this->view = $view;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        $counter = $this->getCounter();
        return [
            'counter' => $counter,
            'items' => $counter ? $this->getItems() : [],
        ];
    }

    /**
     * @return string
     */
    protected function getCounter()
    {
        return $this->createCounter($this->sendquoteHelper->getItemCount());
    }

    /**
     * Create button label based on sendquote item quantity
     *
     * @param int $count
     * @return \Magento\Framework\Phrase|null
     */
    protected function createCounter($count)
    {
        if ($count > 1) {
            return __('%1 items', $count);
        } elseif ($count == 1) {
            return __('1 item');
        }
        return null;
    }

    /**
     * Get sendquote items
     *
     * @return array
     */
    protected function getItems()
    {
        $this->view->loadLayout();

        $collection = $this->sendquoteHelper->getSendquoteItemCollection();
        $collection->clear()->setPageSize(self::SIDEBAR_ITEMS_NUMBER)
            ->setInStockFilter(true)->setOrder('added_at');

        $items = [];
        foreach ($collection as $sendquoteItem) {
            $items[] = $this->getItemData($sendquoteItem);
        }
        return $items;
    }

    /**
     * Retrieve sendquote item data
     *
     * @param \Magento\Sendquote\Model\Item $sendquoteItem
     * @return array
     */
    protected function getItemData(\Techspot\SendQuote\Model\Item $sendquoteItem)
    {
        $product = $sendquoteItem->getProduct();
        return [
            'image' => $this->getImageData($product),
            'product_url' => $this->sendquoteHelper->getProductUrl($sendquoteItem),
            'product_name' => $product->getName(),
            'product_price' => $this->block->getProductPriceHtml(
                $product,
                'sendquote_configured_price',
                \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
                ['item' => $sendquoteItem]
            ),
            'product_is_saleable_and_visible' => $product->isSaleable() && $product->isVisibleInSiteVisibility(),
            'product_has_required_options' => $product->getTypeInstance()->hasRequiredOptions($product),
            'add_to_cart_params' => $this->sendquoteHelper->getAddToCartParams($sendquoteItem, true),
            'delete_item_params' => $this->sendquoteHelper->getRemoveParams($sendquoteItem, true),
        ];
    }

    /**
     * Retrieve product image data
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Block\Product\Image
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function getImageData($product)
    {
        /** @var \Magento\Catalog\Helper\Image $helper */
        $helper = $this->imageHelperFactory->create()
            ->init($product, 'sendquote_sidebar_block');

        $template = $helper->getFrame()
            ? 'Magento_Catalog/product/image'
            : 'Magento_Catalog/product/image_with_borders';

        try {
            $imagesize = $helper->getResizedImageInfo();
        } catch (NotLoadInfoImageException $exception) {
            $imagesize = [$helper->getWidth(), $helper->getHeight()];
        }

        $width = $helper->getFrame()
            ? $helper->getWidth()
            : $imagesize[0];

        $height = $helper->getFrame()
            ? $helper->getHeight()
            : $imagesize[1];

        return [
            'template' => $template,
            'src' => $helper->getUrl(),
            'width' => $width,
            'height' => $height,
            'alt' => 'Teste',//$helper->getLabel(),
        ];
    }
}
