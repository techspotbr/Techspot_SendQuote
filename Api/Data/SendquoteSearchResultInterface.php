<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Api\Data;

/**
 * Sendquote search result interface.
 *
 * An sendquote is a record of the quotation of customer.
 * @api
 * @since 100.0.2
 */
interface SendquoteSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Gets collection items.
     *
     * @return \Techspot\SendQuote\Api\Data\SendquoteInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Sets collection items.
     *
     * @param \Techspot\SendQuote\Api\Data\SendquoteInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
