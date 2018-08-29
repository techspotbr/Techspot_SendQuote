<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Api\Data;

/**
 * Sendquote interface.
 *
 * An sendquote is a record of the quotations of customer.
 * @api
 * @since 100.0.2
 */
interface SendquoteInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    /*
     * Entity ID.
     */
    const ENTITY_ID = 'sendquote_id';

     /*
     * Customer ID.
     */
    const CUSTOMER_ID = 'customer_id';

     /*
     * Shared.
     */
    const SHARED = 'shared';

     /*
     * Sharing Code.
     */
    const SHARING_CODE = 'sharing_code';
    
    /*
     * Updated-at timestamp.
     */
    const UPDATED_AT = 'updated_at';
    
    /*
     * Items.
     */
    const ITEMS = 'items';

    /**
     * Gets the ID for the sendquote.
     *
     * @return int|null Sendquote ID.
     */
    public function getEntityId();

    /**
     * Sets entity ID.
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * Gets the ID for the Customer.
     *
     * @return int|null Customer ID.
     */
    public function getCustomerId();

    /**
     * Sets Customer ID.
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Gets Shered.
     *
     * @return int|null shered.
     */
    public function getShered();

    /**
     * Sets Shered.
     *
     * @param int $shered
     * @return $this
     */
    public function setShered($shered);

    /**
     * Gets the sharingCode.
     *
     * @return string|null sharingCode.
     */
    public function getSharingCode();

    /**
     * Sets sharingCode.
     *
     * @param int $sharingCode
     * @return $this
     */
    public function setSharingCode($sharingCode);

    /**
     * Gets the updated-at timestamp for the sendquote.
     *
     * @return string|null Updated-at timestamp.
     */
    public function getUpdatedAt();

    /**
     * Sets the updated-at timestamp for the sendquote.
     *
     * @param string $timestamp
     * @return $this
     */
    public function setUpdatedAt($timestamp);

    /**
     * Gets the items in the sendquote.
     *
     * @return \Magento\Sales\Api\Data\SendquoteItemInterface[] Array of sendquote items.
     */
    public function getItems();

    /**
     * Sets the items in the sendquote.
     *
     * @param \Magento\Sales\Api\Data\SendquoteItemInterface[] $items
     * @return $this
     */
    public function setItems($items);

}
