<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Sendquote item interface.
 *
 * An sendquote is a record of the quotation of customer. An sendquote item is a quotation item in an quote.
 * @api
 * @since 100.0.2
 */
interface SendquoteItemInterface extends ExtensibleDataInterface, LineItemInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    /*
     * Entity ID.
     */
    const ENTITY_ID = 'sendquote_item_id';
    /*
     * Parent ID.
     */
    const PARENT_ID = 'sendquote_id';

    /*
    * Product ID.
    */
    const PRODUCT_ID = 'product_id';

    /*
    * Store ID.
    */
    const STORE_ID = 'store_id';

    /*
    * Added at.
    */
    const ADDED_AT = 'added_at';

    /*
    * Description.
    */
    const DESCRIPTION = 'description';

    /*
     * Quantity.
     */
    const QTY = 'qty';
    
    /*
     * Price.
     */
    const PRICE = 'price';

    /*
     * Custom Price.
     */
    const CUSTOM_PRICE = 'custom_price';

    /*
     * User ID.
     */
    const USER_ID = 'user_id';

    /**
     * Gets the ID for the quotation item.
     *
     * @return int|null Sendquote item ID.
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
     * Gets the product ID for the quotation item.
     *
     * @return int|null Product ID.
     */
    public function getProductId();

    /**
     * Sets the product ID for the quotation item.
     *
     * @param int $id
     * @return $this
     */
    public function setProductId($id);

    /**
     * Gets the store ID for the quotation item.
     *
     * @return int|null Sendquote item ID.
     */
    public function getStoreId();

    /**
     * Sets store ID.
     *
     * @param int $entityId
     * @return $this
     */
    public function setStoreId($entityId);

    /**
     * Gets the addet-at timestamp for the sendquote.
     *
     * @return string|null addet-at timestamp.
     */
    public function getAddetdAt();

    /**
     * Sets the addet-at timestamp for the sendquote.
     *
     * @param string $timestamp
     * @return $this
     */
    public function setAddetdAt($timestamp);

     /**
     * Gets the description for the quotation item.
     *
     * @return string|null Price.
     */
    public function getDescription();

    /**
     * Sets the description for the quotation item.
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Gets the price for the quotation item.
     *
     * @return float|null Price.
     */
    public function getQty();

    /**
     * Sets the qty for the quotation item.
     *
     * @param float $qty
     * @return $this
     */
    public function setQty($qty);

    /**
     * Gets the price for the quotation item.
     *
     * @return float|null Price.
     */
    public function getPrice();

    /**
     * Sets the price for the quotation item.
     *
     * @param float $price
     * @return $this
     */
    public function setPrice($price);

    /**
     * Gets the custom price for the quotation item.
     *
     * @return float|null Price.
     */
    public function getCustomPrice();

    /**
     * Sets the custom price for the quotation item.
     *
     * @param float $customPrice
     * @return $this
     */
    public function setCustomPrice($customPrice);

     /**
     * Gets the user ID for the quotation item.
     *
     * @return int|null Product ID.
     */
    public function getUserId();

    /**
     * Sets the user ID for the quotation item.
     *
     * @param int $id
     * @return $this
     */
    public function setUserId($id);
}
