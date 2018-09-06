<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Block\Adminhtml\Sendquote\View;

use \Magento\Framework\App\ObjectManager;

/**
 * Sendquote view form
 *
 * @api
 * @author     Techspot Core Team <babumsouza1@gmail.com>
 */
class Form extends \Magento\Backend\Block\Widget
{
    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    protected $_customerGroupCollection;

    /**
     * Admin session
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_session;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Backend session
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendSession;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Backend\Model\Auth\Session $backendSession
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Backend\Model\Auth\Session $backendSession,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Group $customerGroupCollection,
        array $data = []
    ) {
        $this->_backendSession = $backendSession;
        $this->_coreRegistry = $registry;
        $this->_customerFactory = $customerFactory;
        $this->_customerGroupCollection = $customerGroupCollection;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve source
     *
     * @return \Techspot\SendQuote\Model\Sendquote
     */
    public function getSource()
    {
        return $this->getSendquote();
    }

    /**
     * Retrieve sendquote model instance
     *
     * @return \Techspot\SendQuote\Model\Sendquote
     */
    public function getSendquote()
    {
        return $this->_coreRegistry->registry('current_quotation');
    }

    /**
     * Retrieve formated price
     *
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->getSendquote()->formatPrice($price);
    }

    protected function getCustomer(){
        $model = $this->_customerFactory->create();
        $customer = $model->load($this->getSendquote()->getCustomerId());

        if($customer->getId()){
            return $customer;
        }
        return false;
    }

    public function getCustomerName()
    {
        $customer = $this->getCustomer();

        if($customer->getId()){
            $customerName = $customer->getFirstname() .' '. $customer->getLastname();
            return $customerName;
        }
        return '';
    }

    public function getCustomerEmail()
    {
        return $this->getCustomer()->getEmail();
    }

    /**
     * Return name of the customer group.
     *
     * @return string
     */
    public function getCustomerGroupName()
    {
        $customerGroup = $this->_customerGroupCollection->load($this->getCustomer()->getGroupId()); 
        return $customerGroup->getCustomerGroupCode();
    }
}
