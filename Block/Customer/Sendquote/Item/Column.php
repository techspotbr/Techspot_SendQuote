<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Techspot\SendQuote\Block\Customer\Sendquote\Item;

/**
 * Sendquote block customer item column
 *
 * @api
 * @method \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface getItem()
 * @since 100.0.2
 */
class Column extends \Techspot\SendQuote\Block\AbstractBlock
{
    /**
     * Checks whether column should be shown in table
     *
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * Retrieve block html
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->isEnabled()) {
            if (!$this->getLayout()) {
                return '';
            }
            foreach ($this->getLayout()->getChildBlocks($this->getNameInLayout()) as $child) {
                if ($child) {
                    $child->setItem($this->getItem());
                }
            }
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Retrieve column related javascript code
     *
     * @return string
     */
    public function getJs()
    {
        if (!$this->getLayout()) {
            return '';
        }
        $js = '';
        foreach ($this->getLayout()->getChildBlocks($this->getNameInLayout()) as $block) {
            $js .= $block->getJs();
        }
        return $js;
    }
}
