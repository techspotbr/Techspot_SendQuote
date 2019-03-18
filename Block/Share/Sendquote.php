<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Sendquote block shared items
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Techspot\SendQuote\Block\Share;

/**
 * @api
 * @since 100.0.2
 */
class Sendquote extends \Techspot\SendQuote\Block\AbstractBlock
{
    /**
     * Customer instance
     *
     * @var \Magento\Customer\Api\Data\CustomerInterface
     */
    protected $_customer = null;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;
    
    /**
     * Store sendquote Helper
     *
     * @var  \Techspot\SendQuote\Helper\Data $sendquoteHelper
     */
    protected $_sendquoteHelper;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param array $data,
     * @param \Techspot\SendQuote\Helper\Data $sendquoteHelper
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        array $data = [],
        \Techspot\SendQuote\Helper\Data $sendquoteHelper
    ) {
        $this->customerRepository = $customerRepository;
        $this->_sendquoteHelper = $sendquoteHelper;
        parent::__construct(
            $context,
            $httpContext,
            $data,
            $sendquoteHelper
        );
    }

    /**
     * Prepare global layout
     *
     * @return $this
     *
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set($this->getHeader());
        return $this;
    }

    /**
     * Retrieve Shared Sendquote Customer instance
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getSendquoteCustomer()
    {
        if ($this->_customer === null) {
            $this->_customer = $this->customerRepository->getById($this->_getSendquote()->getCustomerId());
        }

        return $this->_customer;
    }

    /**
     * Retrieve Page Header
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeader()
    {
        return __("%1's Quotations", $this->escapeHtml($this->getSendquoteCustomer()->getFirstname()));
    }
}
