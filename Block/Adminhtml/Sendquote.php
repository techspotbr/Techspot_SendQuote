<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Block\Adminhtml;

class Sendquote extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_quote';
        $this->_blockGroup = 'Techspot_SendQuote';
        $this->_headerText = __('Quotes');
        parent::_construct();
        $this->buttonList->remove('add');
    }
}
