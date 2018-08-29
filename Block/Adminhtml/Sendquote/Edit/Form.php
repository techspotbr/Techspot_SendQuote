<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Block\Adminhtml\Sendquote\Edit;

/**
 * Sendquote view form
 *
 * @api
 * @author     Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
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

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', ['sendquote_id' => $this->getSendquote()->getSendquoteId()]);
    }
}
