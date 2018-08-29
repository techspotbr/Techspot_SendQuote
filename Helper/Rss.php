<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Techspot\SendQuote\Helper;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @api
 * @since 100.0.2
 */
class Rss extends \Techspot\SendQuote\Helper\Data
{
    /**
     * @var \Magento\Customer\Api\Data\CustomerInterface
     */
    protected $_customer;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Techspot\SendQuote\Model\SendquoteFactory $sendquoteFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Customer\Helper\View $customerViewHelper
     * @param \Techspot\SendQuote\Controller\SendquoteProviderInterface $sendquoteProvider
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession,
        \Techspot\SendQuote\Model\SendquoteFactory $sendquoteFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Customer\Helper\View $customerViewHelper,
        \Techspot\SendQuote\Controller\SendquoteProviderInterface $sendquoteProvider,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_customerRepository = $customerRepository;

        parent::__construct(
            $context,
            $coreRegistry,
            $customerSession,
            $sendquoteFactory,
            $storeManager,
            $postDataHelper,
            $customerViewHelper,
            $sendquoteProvider,
            $productRepository
        );
    }

    /**
     * Retrieve Sendquote model
     *
     * @return \Techspot\SendQuote\Model\Sendquote
     */
    public function getSendquote()
    {
        if ($this->_sendquote === null) {
            $this->_sendquote = $this->_sendquoteFactory->create();

            $sendquoteId = $this->_getRequest()->getParam('sendquote_id');
            if ($sendquoteId) {
                $this->_sendquote->load($sendquoteId);
            } else {
                if ($this->getCustomer()->getId()) {
                    $this->_sendquote->loadByCustomerId($this->getCustomer()->getId());
                }
            }
        }
        return $this->_sendquote;
    }

    /**
     * Retrieve Customer instance
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomer()
    {
        if ($this->_customer === null) {
            $params = $this->urlDecoder->decode($this->_getRequest()->getParam('data'));
            $data   = explode(',', $params);
            $customerId    = abs(intval($data[0]));
            if ($customerId && ($customerId == $this->_customerSession->getCustomerId())) {
                $this->_customer = $this->_customerRepository->getById($customerId);
            } else {
                $this->_customer = $this->_customerFactory->create();
            }
        }

        return $this->_customer;
    }

    /**
     * Is allow RSS
     *
     * @return bool
     */
    public function isRssAllow()
    {
        return $this->_moduleManager->isEnabled('Magento_Rss')
            && $this->scopeConfig->isSetFlag(
                'rss/sendquote/active',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
    }
}
