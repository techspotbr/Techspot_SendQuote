<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Helper;

use Magento\Framework\App\ActionInterface;
use Techspot\SendQuote\Controller\SendquoteProviderInterface;

/**
 * SendQuote Data Helper
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @api
 * @since 100.0.2
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Config key 'Display Sendquote Summary'
     */
    const XML_PATH_SENDQUOTE_LINK_USE_QTY = 'sendquote/sendquote_link/use_qty';

    /**
     * Config key 'Display Out of Stock Products'
     */
    const XML_PATH_CATALOGINVENTORY_SHOW_OUT_OF_STOCK = 'cataloginventory/options/show_out_of_stock';

    /**
     * Currently logged in customer
     *
     * @var \Magento\Customer\Api\Data\CustomerInterface
     */
    protected $_currentCustomer;

    /**
     * Customer Sendquote instance
     *
     * @var \Techspot\SendQuote\Model\Sendquote
     */
    protected $_sendquote;

    /**
     * Sendquote Product Items Collection
     *
     * @var \Techspot\SendQuote\Model\ResourceModel\Item\Collection
     */
    protected $_productCollection;

    /**
     * Sendquote Items Collection
     *
     * @var \Techspot\SendQuote\Model\ResourceModel\Item\Collection
     */
    protected $_sendquoteItemCollection;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Techspot\SendQuote\Model\SendquoteFactory
     */
    protected $_sendquoteFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $_postDataHelper;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerViewHelper;

    /**
     * @var \Techspot\SendQuote\Controller\SendquoteProviderInterface
     */
    protected $sendquoteProvider;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Techspot\SendQuote\Model\SendquoteFactory $sendquoteFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Customer\Helper\View $customerViewHelper
     * @param SendquoteProviderInterface $sendquoteProvider
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession,
        \Techspot\SendQuote\Model\SendquoteFactory $sendquoteFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Customer\Helper\View $customerViewHelper,
        SendquoteProviderInterface $sendquoteProvider,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_customerSession = $customerSession;
        $this->_sendquoteFactory = $sendquoteFactory;
        $this->_storeManager = $storeManager;
        $this->_postDataHelper = $postDataHelper;
        $this->_customerViewHelper = $customerViewHelper;
        $this->sendquoteProvider = $sendquoteProvider;
        $this->productRepository = $productRepository;
        parent::__construct($context);
    }

    /**
     * Retrieve customer login status
     *
     * @return bool
     */
    protected function _isCustomerLogIn()
    {
        return $this->_customerSession->isLoggedIn();
    }

    /**
     * Retrieve logged in customer
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    protected function _getCurrentCustomer()
    {
        return $this->getCustomer();
    }

    /**
     * Set current customer
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return void
     */
    public function setCustomer(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        $this->_currentCustomer = $customer;
    }

    /**
     * Retrieve current customer
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer()
    {
        if (!$this->_currentCustomer && $this->_customerSession->isLoggedIn()) {
            $this->_currentCustomer = $this->_customerSession->getCustomerDataObject();
        }
        return $this->_currentCustomer;
    }

    /**
     * Retrieve sendquote by logged in customer
     *
     * @return \Techspot\SendQuote\Model\Sendquote
     */
    public function getSendquote()
    {
        if ($this->_sendquote === null) {
            if ($this->_coreRegistry->registry('shared_sendquote')) {
                $this->_sendquote = $this->_coreRegistry->registry('shared_sendquote');
            } else {
                $this->_sendquote = $this->sendquoteProvider->getSendquote();
            }
        }
        return $this->_sendquote;
    }

    /**
     * Retrieve sendquote item count (include config settings)
     * Used in top link menu only
     *
     * @return int
     */
    public function getItemCount()
    {
        $storedDisplayType = $this->_customerSession->getSendquoteDisplayType();
        $currentDisplayType = $this->scopeConfig->getValue(
            self::XML_PATH_SENDQUOTE_LINK_USE_QTY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $storedDisplayOutOfStockProducts = $this->_customerSession->getDisplayOutOfStockProducts();
        $currentDisplayOutOfStockProducts = $this->scopeConfig->getValue(
            self::XML_PATH_CATALOGINVENTORY_SHOW_OUT_OF_STOCK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!$this->_customerSession->hasSendquoteItemCount() ||
            $currentDisplayType != $storedDisplayType ||
            $this->_customerSession->hasDisplayOutOfStockProducts() ||
            $currentDisplayOutOfStockProducts != $storedDisplayOutOfStockProducts
        ) {
            $this->calculate();
        }

        return $this->_customerSession->getSendquoteItemCount();
    }

    /**
     * Create sendquote item collection
     *
     * @return \Techspot\SendQuote\Model\ResourceModel\Item\Collection
     */
    protected function _createSendquoteItemCollection()
    {
        return $this->getSendquote()->getItemCollection();
    }

    /**
     * Retrieve sendquote items collection
     *
     * @return \Techspot\SendQuote\Model\ResourceModel\Item\Collection
     */
    public function getSendquoteItemCollection()
    {
        if ($this->_sendquoteItemCollection === null) {
            $this->_sendquoteItemCollection = $this->_createSendquoteItemCollection();
        }
        return $this->_sendquoteItemCollection;
    }

    /**
     * Retrieve Item Store for URL
     *
     * @param \Magento\Catalog\Model\Product|\Techspot\SendQuote\Model\Item $item
     * @return \Magento\Store\Model\Store
     */
    protected function _getUrlStore($item)
    {
        $storeId = null;
        $product = null;
        if ($item instanceof \Techspot\SendQuote\Model\Item) {
            $product = $item->getProduct();
        } elseif ($item instanceof \Magento\Catalog\Model\Product) {
            $product = $item;
        }
        if ($product) {
            if ($product->isVisibleInSiteVisibility()) {
                $storeId = $product->getStoreId();
            } else {
                if ($product->hasUrlDataObject()) {
                    $storeId = $product->getUrlDataObject()->getStoreId();
                }
            }
        }
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * Retrieve params for removing item from sendquote
     *
     * @param \Magento\Catalog\Model\Product|\Techspot\SendQuote\Model\Item $item
     * @param bool $addReferer
     * @return string
     */
    public function getRemoveParams($item, $addReferer = false)
    {
        $url = $this->_getUrl('sendquote/index/remove');
        $params = ['item' => $item->getSendquoteItemId()];
        if ($addReferer) {
            $params = $this->addRefererToParams($params);
        }
        return $this->_postDataHelper->getPostData($url, $params);
    }

    /**
     * Retrieve URL for configuring item from sendquote
     *
     * @param \Magento\Catalog\Model\Product|\Techspot\SendQuote\Model\Item $item
     * @return string
     */
    public function getConfigureUrl($item)
    {
        return $this->_getUrl(
            'sendquote/index/configure',
            [
                'id' => $item->getSendquoteItemId(),
                'product_id' => $item->getProductId()
            ]
        );
    }

    /**
     * Retrieve params for adding product to sendquote
     *
     * @param \Magento\Catalog\Model\Product|\Techspot\SendQuote\Model\Item $item
     * @param array $params
     * @return string
     */
    public function getAddParams($item, array $params = [])
    {
        $productId = null;
        if ($item instanceof \Magento\Catalog\Model\Product) {
            $productId = $item->getEntityId();
        }
        if ($item instanceof \Techspot\SendQuote\Model\Item) {
            $productId = $item->getProductId();
        }

        $url = $this->_getUrlStore($item)->getUrl('sendquote/index/add');
        if ($productId) {
            $params['product'] = $productId;
        }

        return $this->_postDataHelper->getPostData($url, $params);
    }

    /**
     * Retrieve params for adding product to sendquote
     *
     * @param int $itemId
     *
     * @return string
     */
    public function getMoveFromCartParams($itemId)
    {
        $url = $this->_getUrl('sendquote/index/fromcart');
        $params = ['item' => $itemId];
        return $this->_postDataHelper->getPostData($url, $params);
    }

    /**
     * Retrieve params for updating product in sendquote
     *
     * @param \Magento\Catalog\Model\Product|\Techspot\SendQuote\Model\Item $item
     *
     * @return  string|false
     */
    public function getUpdateParams($item)
    {
        $itemId = null;
        if ($item instanceof \Magento\Catalog\Model\Product) {
            $itemId = $item->getSendquoteItemId();
            $productId = $item->getId();
        }
        if ($item instanceof \Techspot\SendQuote\Model\Item) {
            $itemId = $item->getId();
            $productId = $item->getProduct()->getId();
        }

        $url = $this->_getUrl('sendquote/index/updateItemOptions');
        if ($itemId) {
            $params = ['id' => $itemId, 'product' => $productId, 'qty' => $item->getQty()];
            return $this->_postDataHelper->getPostData($url, $params);
        }

        return false;
    }

    /**
     * Retrieve params for adding item to shopping cart
     *
     * @param string|\Magento\Catalog\Model\Product|\Techspot\SendQuote\Model\Item $item
     * @return  string
     */
    public function getAddToCartUrl($item)
    {
        return $this->_getUrlStore($item)->getUrl('sendquote/index/cart', $this->_getCartUrlParameters($item));
    }

    /**
     * Retrieve URL for adding item to shopping cart
     *
     * @param string|\Magento\Catalog\Model\Product|\Techspot\SendQuote\Model\Item $item
     * @param bool $addReferer
     * @return string
     */
    public function getAddToCartParams($item, $addReferer = false)
    {
        $params = $this->_getCartUrlParameters($item);
        if ($addReferer) {
            $params = $this->addRefererToParams($params);
        }
        return $this->_postDataHelper->getPostData(
            $this->_getUrlStore($item)->getUrl('sendquote/index/cart'),
            $params
        );
    }

    /**
     * Add UENC referer to params
     *
     * @param array $params
     * @return array
     */
    public function addRefererToParams(array $params)
    {
        $params[ActionInterface::PARAM_NAME_URL_ENCODED] =
            $this->urlEncoder->encode($this->_getRequest()->getServer('HTTP_REFERER'));
        return $params;
    }

    /**
     * Retrieve URL for adding item to shopping cart from shared sendquote
     *
     * @param string|\Magento\Catalog\Model\Product|\Techspot\SendQuote\Model\Item $item
     * @return  string
     */
    public function getSharedAddToCartUrl($item)
    {
        return $this->_postDataHelper->getPostData(
            $this->_getUrlStore($item)->getUrl('sendquote/shared/cart'),
            $this->_getCartUrlParameters($item)
        );
    }

    /**
     * Retrieve URL for adding All items to shopping cart from shared sendquote
     *
     * @return string
     */
    public function getSharedAddAllToCartUrl()
    {
        return $this->_postDataHelper->getPostData(
            $this->_storeManager->getStore()->getUrl('*/*/allcart', ['_current' => true])
        );
    }

    /**
     * @param string|\Magento\Catalog\Model\Product|\Techspot\SendQuote\Model\Item $item
     * @return array
     */
    protected function _getCartUrlParameters($item)
    {
        $params = [
            'item' => is_string($item) ? $item : $item->getSendquoteItemId(),
        ];
        if ($item instanceof \Techspot\SendQuote\Model\Item) {
            $params['qty'] = $item->getQty();
        }
        return $params;
    }

    /**
     * Retrieve customer sendquote url
     *
     * @param int $sendquoteId
     * @return string
     */
    public function getListUrl($sendquoteId = null)
    {
        $params = [];
        if ($sendquoteId) {
            $params['sendquote_id'] = $sendquoteId;
        }
        return $this->_getUrl('sendquote', $params);
    }

    /**
     * Check is allow sendquote module
     *
     * @return bool
     */
    public function isAllow()
    {
        if ($this->_moduleManager->isOutputEnabled($this->_getModuleName()) && $this->scopeConfig->getValue(
            'sendquote/general/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )
        ) {
            return true;
        }
        return false;
    }

    /**
     * Check is allow sendquote action in shopping cart
     *
     * @return bool
     */
    public function isAllowInCart()
    {
        return $this->isAllow() && $this->getCustomer();
    }

    /**
     * Retrieve customer name
     *
     * @return string|void
     */
    public function getCustomerName()
    {
        return $this->getCustomer()
            ? $this->_customerViewHelper->getCustomerName($this->getCustomer())
            : null;
    }

    /**
     * Retrieve RSS URL
     *
     * @param int|string|null $sendquoteId
     * @return string
     */
    public function getRssUrl($sendquoteId = null)
    {
        $customer = $this->_getCurrentCustomer();
        if ($customer) {
            $key = $customer->getId() . ',' . $customer->getEmail();
            $params = ['data' => $this->urlEncoder->encode($key), '_secure' => false];
        }
        if ($sendquoteId) {
            $params['sendquote_id'] = $sendquoteId;
        }
        return $this->_getUrl('sendquote/index/rss', $params);
    }

    /**
     * Retrieve default empty comment message
     *
     * @return \Magento\Framework\Phrase
     */
    public function defaultCommentString()
    {
        return __('Comment');
    }

    /**
     * Retrieve default empty comment message
     *
     * @return \Magento\Framework\Phrase
     */
    public function getDefaultSendquoteName()
    {
        return __('Quotations');
    }

    /**
     * Calculate count of sendquote items and put value to customer session.
     * Method called after sendquote modifications and trigger 'sendquote_items_renewed' event.
     * Depends from configuration.
     *
     * @return $this
     */
    public function calculate()
    {
        $count = 0;
        if ($this->getCustomer()) {
            $collection = $this->getSendquoteItemCollection()->setInStockFilter(true);
            if ($this->scopeConfig->getValue(
                self::XML_PATH_SENDQUOTE_LINK_USE_QTY,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
            ) {
                $count = $collection->getItemsQty();
            } else {
                $count = $collection->getSize();
            }
            $this->_customerSession->setSendquoteDisplayType(
                $this->scopeConfig->getValue(
                    self::XML_PATH_SENDQUOTE_LINK_USE_QTY,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            );
            $this->_customerSession->setDisplayOutOfStockProducts(
                $this->scopeConfig->getValue(
                    self::XML_PATH_CATALOGINVENTORY_SHOW_OUT_OF_STOCK,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            );
        }
        $this->_customerSession->setSendquoteItemCount($count);
        $this->_eventManager->dispatch('sendquote_items_renewed');
        return $this;
    }

    /**
     * Should display item quantities in my sendquote link
     *
     * @return bool
     */
    public function isDisplayQty()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SENDQUOTE_LINK_USE_QTY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve URL to item Product
     *
     * @param  \Techspot\SendQuote\Model\Item|\Magento\Catalog\Model\Product $item
     * @param  array $additional
     * @return string
     */
    public function getProductUrl($item, $additional = [])
    {
        if ($item instanceof \Magento\Catalog\Model\Product) {
            $product = $item;
        } else {
            $product = $item->getProduct();
        }
        $buyRequest = $item->getBuyRequest();
        if (is_object($buyRequest)) {
            $config = $buyRequest->getSuperProductConfig();
            if ($config && !empty($config['product_id'])) {
                $product = $this->productRepository->getById(
                    $config['product_id'],
                    false,
                    $this->_storeManager->getStore()->getStoreId()
                );
            }
        }
        return $product->getUrlModel()->getUrl($product, $additional);
    }
}
