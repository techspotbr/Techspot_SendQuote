<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Api;

/**
 * Sendquote item repository interface.
 *
 * An sendquote is a record of the quotation of customer. An sendquote item is a quotation item in an sendquote.
 * @api
 * @since 100.0.2
 */
interface SendquoteItemRepositoryInterface
{
    /**
     * Lists the quotation items that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Sales\Api\Data\SendquoteItemSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Loads a specified quotation item.
     *
     * @param int $id The invoice item ID.
     * @return \Techspot\SendQuote\Api\Data\SendquoteItemInterface Sendquote item interface.
     */
    public function get($id);

    /**
     * Deletes a specified quotation item.
     *
     * @param \Techspot\SendQuote\Api\Data\SendquoteItemInterface $entity The invoice item.
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Magento\Sales\Api\Data\SendquoteItemInterface $entity);

    /**
     * Performs persist operations for a specified quotation item.
     *
     * @param \Techspot\SendQuote\Api\Data\SendquoteItemInterface $entity The invoice item.
     * @return \Techspot\SendQuote\Api\Data\SendquoteItemInterface Sendquote item interface.
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magento\Sales\Api\Data\SendquoteItemInterface $entity);
}
