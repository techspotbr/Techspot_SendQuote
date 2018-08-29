<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Block\Adminhtml\Sendquote\Edit\Items\Renderer;


/**
 * Adminhtml sendquote item renderer
 *
 * @api
 * @since 100.0.2
 */
class DefaultRenderer extends \Techspot\SendQuote\Block\Adminhtml\Sendquote\View\Items\AbstractItems
{
    /**
     * Get order item
     *
     * @return Item
     */
    public function getItem()
    {
        return $this->_getData('item');//->getOrderItem();
    }
}
