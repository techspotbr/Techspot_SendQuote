<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Api;

/**
 * Sendquote repository interface.
 *
 * An sendquote is a record of the quotation of customer.
 * @api
 * @since 100.0.2
 */
interface SendquoteRepositoryInterface
{
    /**
     * Lists sendquotes that match specified search criteria.
     *
     * This call returns an array of objects, but detailed information about each object’s attributes might not be
     * included. See http://devdocs.magento.com/codelinks/attributes.html#SendquoteRepositoryInterface to
     * determine which call to use to get detailed information about all attributes for an object.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria The search criteria.
     * @return \Techspot\SendQuote\Api\Data\SendquoteSearchResultInterface Sendquote search result interface.
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Return Sendquote object
     *
     * @return \Techspot\SendQuote\Api\Data\SendquoteInterface
     */
    public function create();

    /**
     * Loads a specified sendquote.
     *
     * @param int $id The sendquote ID.
     * @return \Techspot\SendQuote\Api\Data\SendquoteInterface Sendquote interface.
     */
    public function get($id);

    /**
     * Deletes a specified sendquote.
     *
     * @param \Techspot\SendQuote\Api\Data\SendquoteInterface $entity The sendquote.
     * @return bool
     */
    public function delete(\Techspot\SendQuote\Api\Data\SendquoteInterface $entity);

    /**
     * Performs persist operations for a specified sendquote.
     *
     * @param \Techspot\SendQuote\Api\Data\SendquoteInterface $entity The sendquote.
     * @return \Techspot\SendQuote\Api\Data\SendquoteInterface Sendquote interface.
     */
    public function save(\Techspot\SendQuote\Api\Data\SendquoteInterface $entity);
}
