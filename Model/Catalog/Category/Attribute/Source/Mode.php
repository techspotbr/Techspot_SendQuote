<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Model\Catalog\Category\Attribute\Source;

/**
 * Catalog category landing page attribute source
 *
 * @author  Tech Spot Core Team <techspot@techspot.com.br>
 */
class Mode extends \Magento\Catalog\Model\Category\Attribute\Source\Mode
{
    /**#@+
     * Category display modes
     */
    const DM_PRODUCT_WITHOUT_PRICE = 'PRODUCTS_WITHOUT_PRICE';

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['value' => \Magento\Catalog\Model\Category::DM_PRODUCT, 'label' => __('Products only')],
                ['value' => self::DM_PRODUCT_WITHOUT_PRICE, 'label' => __('Products Without Price')],
                ['value' => \Magento\Catalog\Model\Category::DM_PAGE, 'label' => __('Static block only')],
                ['value' => \Magento\Catalog\Model\Category::DM_MIXED, 'label' => __('Static block and products')],
            ];
        }
        return $this->_options;
    }
}
