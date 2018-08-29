<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Techspot\SendQuote\Block\Customer\Sendquote;

/**
 * Sendquote block customer items
 *
 * @api
 * @since 100.0.2
 */
class Items extends \Magento\Framework\View\Element\Template
{
    /**
     * Retrieve table column object list
     *
     * @return \Techspot\SendQuote\Block\Customer\Sendquote\Item\Column[]
     */
    public function getColumns()
    {
        $columns = [];
        foreach ($this->getLayout()->getChildBlocks($this->getNameInLayout()) as $child) {
            if ($child instanceof \Techspot\SendQuote\Block\Customer\Sendquote\Item\Column && $child->isEnabled()) {
                $columns[] = $child;
            }
        }
        return $columns;
    }
}
