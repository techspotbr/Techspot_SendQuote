<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Techspot\SendQuote\Ui\DataProvider\Product\Collector;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductRender\ButtonInterfaceFactory;
use Magento\Catalog\Api\Data\ProductRenderInfoDtoInterface;
use Magento\Catalog\Api\Data\ProductRenderInterface;
use Magento\Catalog\Model\ProductRenderInfoDto;
use Magento\Catalog\Ui\DataProvider\Product\ProductRenderCollectorInterface;
use Magento\Catalog\Ui\DataProvider\Product\ProductRenderInfoProviderInterface;
use Techspot\SendQuote\Helper\Data;

/**
 * Collect information needed to render sendquote button on front
 */
class Button implements ProductRenderCollectorInterface
{
    /** Url Key */
    const KEY_SENDQUOTE_URL_PARAMS = "sendquote_url_params";

    /**
     * @var Data
     */
    private $sendquoteHelper;

    /**
     * @var \Magento\Catalog\Api\Data\ProductRender\ProductRenderExtensionInterfaceFactory
     */
    private $productRenderExtensionFactory;

    /**
     * @var ButtonInterfaceFactory
     */
    private $buttonInterfaceFactory;

    /**
     * @param Data $sendquoteHelper
     * @param \Magento\Catalog\Api\Data\ProductRenderExtensionFactory $productRenderExtensionFactory
     * @param ButtonInterfaceFactory $buttonInterfaceFactory
     */
    public function __construct(
        Data $sendquoteHelper,
        \Magento\Catalog\Api\Data\ProductRenderExtensionFactory $productRenderExtensionFactory,
        ButtonInterfaceFactory $buttonInterfaceFactory
    ) {
        $this->sendquoteHelper = $sendquoteHelper;
        $this->productRenderExtensionFactory = $productRenderExtensionFactory;
        $this->buttonInterfaceFactory = $buttonInterfaceFactory;
    }

    /**
     * @inheritdoc
     */
    public function collect(ProductInterface $product, ProductRenderInterface $productRender)
    {
        /** @var \Magento\Catalog\Api\Data\ProductRenderExtensionInterface $extensionAttributes */
        $extensionAttributes = $productRender->getExtensionAttributes();

        if (!$extensionAttributes) {
            $extensionAttributes = $this->productRenderExtensionFactory->create();
        }

        $button = $this->buttonInterfaceFactory->create();
        $button->setUrl($this->sendquoteHelper->getAddParams($product));
        $extensionAttributes->setSendquoteButton($button);
        $productRender->setExtensionAttributes($extensionAttributes);
    }
}
