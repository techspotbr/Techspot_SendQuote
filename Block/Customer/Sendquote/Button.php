<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Sendquote block customer item cart column
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Techspot\SendQuote\Block\Customer\Sendquote;

/**
 * @api
 * @since 100.0.2
 */
class Button extends \Magento\Framework\View\Element\Template
{
    /**
     * Sendquote config
     *
     * @var \Techspot\SendQuote\Model\Config
     */
    protected $_sendquoteConfig;

    /**
     * Sendquote data
     *
     * @var \Techspot\SendQuote\Helper\Data
     */
    protected $_sendquoteData = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Techspot\SendQuote\Helper\Data $sendquoteData
     * @param \Techspot\SendQuote\Model\Config $sendquoteConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Techspot\SendQuote\Helper\Data $sendquoteData,
        \Techspot\SendQuote\Model\Config $sendquoteConfig,
        array $data = []
    ) {
        $this->_sendquoteData = $sendquoteData;
        $this->_sendquoteConfig = $sendquoteConfig;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve current sendquote
     *
     * @return \Techspot\SendQuote\Model\Sendquote
     */
    public function getSendquote()
    {
        return $this->_sendquoteData->getSendquote();
    }

    /**
     * Retrieve sendquote config
     *
     * @return \Techspot\SendQuote\Model\Config
     */
    public function getConfig()
    {
        return $this->_sendquoteConfig;
    }

    /**
     * Return true if can edit
     * Only quotations with status = 0 can be updated
     *
     * @return bool
     * */
    public function canUpdate($sendquote)
    {
        if($sendquote->getStatus() == 0){
            return true;
        }
        return false;
    }

    /**
     * Return true if can add to cart
     * Only quotations with status = 1 can be add to cart
     *
     * @return bool
     * */
    public function canAddToCart($sendquote)
    {
        if($sendquote->getStatus() == 1){
            return true;
        }
        return false;
    }
}
