<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Block\Adminhtml\Sendquote\View;

/**
 * Sendquote view form
 *
 * @api
 * @author     Techspot Core Team <babumsouza1@gmail.com>
 * @since 100.0.2
 */
class Form extends \Magento\Backend\Block\Widget
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
     * Retrieve sendquote model instance
     *
     * @return \Techspot\SendQuote\Model\Sendquote
     */
    public function getSendquote()
    {
        return $this->_coreRegistry->registry('current_quotation');
    }

    /**
     * Retrieve formated price
     *
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->getSendquote()->formatPrice($price);
    }

    
}
