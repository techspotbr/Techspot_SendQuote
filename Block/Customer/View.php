<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Sendquote block customer items
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Techspot\SendQuote\Block\Customer;

/**
 * @api
 * @since 100.0.2
 */
class View extends \Techspot\SendQuote\Block\AbstractBlock
{
    /**
     * List of product options rendering configurations by product type
     *
     * @var array
     */
    protected $_optionsCfg = [];

    /**
     * @var \Magento\Catalog\Helper\Product\ConfigurationPool
     */
    protected $_helperPool;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $postDataHelper;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Catalog\Helper\Product\ConfigurationPool $helperPool
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Techspot\SendQuote\Helper\Data $sendquoteHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Helper\Product\ConfigurationPool $helperPool,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        array $data = [],
        \Techspot\SendQuote\Helper\Data $sendquoteHelper
    ) {
        parent::__construct(
            $context,
            $httpContext,
            $data,
            $sendquoteHelper
        );
        $this->_helperPool = $helperPool;
        $this->currentCustomer = $currentCustomer;
        $this->postDataHelper = $postDataHelper;
    }

    /**
     * Add sendquote conditions to collection
     *
     * @param  \Techspot\SendQuote\Model\ResourceModel\Item\Collection $collection
     * @return $this
     */
    protected function _prepareCollection($collection)
    {
        $collection->setInStockFilter(true)->setOrder('added_at', 'ASC');
        return $this;
    }

    /**
     * Preparing global layout
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('Quotation #'));
    }

    /**
     * Retrieve Back URL
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }

    /**
     * Sets all options render configurations
     *
     * @param null|array $optionCfg
     * @return $this
     */
    public function setOptionsRenderCfgs($optionCfg)
    {
        $this->_optionsCfg = $optionCfg;
        return $this;
    }

    /**
     * Returns all options render configurations
     *
     * @return array
     */
    public function getOptionsRenderCfgs()
    {
        return $this->_optionsCfg;
    }

    /**
     * Adds config for rendering product type options
     *
     * @param string $productType
     * @param string $helperName
     * @param null|string $template
     * @return $this
     */
    public function addOptionsRenderCfg($productType, $helperName, $template = null)
    {
        $this->_optionsCfg[$productType] = ['helper' => $helperName, 'template' => $template];
        return $this;
    }

    /**
     * Returns html for showing item options
     *
     * @param string $productType
     * @return array|null
     */
    public function getOptionsRenderCfg($productType)
    {
        if (isset($this->_optionsCfg[$productType])) {
            return $this->_optionsCfg[$productType];
        } elseif (isset($this->_optionsCfg['default'])) {
            return $this->_optionsCfg['default'];
        } else {
            return null;
        }
    }

    /**
     * Returns html for showing item options
     *
     * @param \Techspot\SendQuote\Model\Item $item
     * @return string
     */
    public function getDetailsHtml(\Techspot\SendQuote\Model\Item $item)
    {
        $cfg = $this->getOptionsRenderCfg($item->getProduct()->getTypeId());
        if (!$cfg) {
            return '';
        }

        $block = $this->getChildBlock('item_options');
        if (!$block) {
            return '';
        }

        if ($cfg['template']) {
            $template = $cfg['template'];
        } else {
            $cfgDefault = $this->getOptionsRenderCfg('default');
            if (!$cfgDefault) {
                return '';
            }
            $template = $cfgDefault['template'];
        }

        $block->setTemplate($template);
        $block->setOptionList($this->_helperPool->get($cfg['helper'])->getOptions($item));
        return $block->toHtml();
    }

    /**
     * Returns qty to show visually to user
     *
     * @param \Techspot\SendQuote\Model\Item $item
     * @return float
     */
    public function getAddToCartQty(\Techspot\SendQuote\Model\Item $item)
    {
        $qty = $this->getQty($item);
        return $qty ? $qty : 1;
    }

    /**
     * Get add all to cart params for POST request
     * @return string
     */
    public function getAddAllToCartParams()
    {
        return $this->postDataHelper->getPostData(
            $this->getUrl('sendquote/index/allcart'),
            ['sendquote_id' => $this->getSendquoteInstance()->getId()]
        );
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->currentCustomer->getCustomerId()) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
