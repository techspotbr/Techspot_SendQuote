<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Sendquote resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Techspot\SendQuote\Model\ResourceModel;

/**
 * @api
 * @since 100.0.2
 */
class Sendquote extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store sendquote items count
     *
     * @var null|int
     */
    protected $_itemsCount = null;

    /**
     * Store customer ID field name
     *
     * @var string
     */
    protected $_customerIdFieldName = 'customer_id';

    /**
     * Set main entity table name and primary key field name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sendquote', 'sendquote_id');
    }

    /**
     * Prepare sendquote load select query
     *
     * @param string $field
     * @param mixed $value
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($field == $this->_customerIdFieldName) {
            $select->order('sendquote_id ' . \Magento\Framework\DB\Select::SQL_ASC)->limit(1);
        }
        return $select;
    }

    /**
     * Getter for customer ID field name
     *
     * @return string
     */
    public function getCustomerIdFieldName()
    {
        return $this->_customerIdFieldName;
    }

    /**
     * Setter for customer ID field name
     *
     * @param string $fieldName
     * @return $this
     */
    public function setCustomerIdFieldName($fieldName)
    {
        $this->_customerIdFieldName = $fieldName;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setHasDataChanges(true);
        return parent::save($object);
    }
}
