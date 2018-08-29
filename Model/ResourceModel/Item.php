<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Sendquote item model resource
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Techspot\SendQuote\Model\ResourceModel;

/**
 * @api
 * @since 100.0.2
 */
class Item extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize connection and define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sendquote_item', 'sendquote_item_id');
    }

    /**
     * Load item by sendquote, product and shared stores
     *
     * @param \Techspot\SendQuote\Model\Item $object
     * @param int $sendquoteId
     * @param int $productId
     * @param array $sharedStores
     * @return $this
     */
    public function loadByProductSendquote($object, $sendquoteId, $productId, $sharedStores)
    {
        $connection = $this->getConnection();
        $storeWhere = $connection->quoteInto('store_id IN (?)', $sharedStores);
        $select = $connection->select()->from(
            $this->getMainTable()
        )->where(
            'sendquote_id=:sendquote_id AND ' . 'product_id=:product_id AND ' . $storeWhere
        );
        $bind = ['sendquote_id' => $sendquoteId, 'product_id' => $productId];
        $data = $connection->fetchRow($select, $bind);
        if ($data) {
            $object->setData($data);
        }
        $this->_afterLoad($object);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Framework\Model\AbstractModel $object)
    {
        $hasDataChanges = $object->hasDataChanges();
        $object->setIsOptionsSaved(false);

        $result = parent::save($object);

        if ($hasDataChanges && !$object->isOptionsSaved()) {
            $object->saveItemOptions();
        }
        return $result;
    }
}
