<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Sendquote item option resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Techspot\SendQuote\Model\ResourceModel\Item;

/**
 * @api
 * @since 100.0.2
 */
class Option extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sendquote_item_option', 'option_id');
    }
}
