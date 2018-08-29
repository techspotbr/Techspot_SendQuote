<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Block\Adminhtml\Sendquote\Edit;

/**
 * Adminhtml sales item renderer
 *
 * @api
 * @since 100.0.2
 */
class Items extends \Techspot\SendQuote\Block\Adminhtml\Sendquote\View\Items\AbstractItems
{
    /**
     * Retrieve source
     *
     * @return \Techspot\SendQuote\Model\Sendquote
     */
    public function getSource()
    {
        return $this->getSendquote();
    }

    /**
     * Retrieve invoice model instance
     *
     * @return \Techspot\SendQuote\Model\Sendquote
     */
    public function getSendquote()
    {
        return $this->_coreRegistry->registry('current_quotation');
    }

    /**
     * Retrieve formatted price
     *
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        //return $this->getSendquote()->getOrder()->formatPrice($price);
    }

    public function getAllItems()
    {
        return $this->getSendquote()->getItemCollection();
    }
}
