<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Sendquote customer sharing block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Techspot\SendQuote\Block\Customer;

/**
 * @api
 * @since 100.0.2
 */
class Sharing extends \Magento\Framework\View\Element\Template
{
    /**
     * Entered Data cache
     *
     * @var array|null
     */
    protected $_enteredData = null;

    /**
     * Sendquote configuration
     *
     * @var \Techspot\SendQuote\Model\Config
     */
    protected $_sendquoteConfig;

    /**
     * @var \Magento\Framework\Session\Generic
     */
    protected $_sendquoteSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Techspot\SendQuote\Model\Config $sendquoteConfig
     * @param \Magento\Framework\Session\Generic $sendquoteSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Techspot\SendQuote\Model\Config $sendquoteConfig,
        \Magento\Framework\Session\Generic $sendquoteSession,
        array $data = []
    ) {
        $this->_sendquoteConfig = $sendquoteConfig;
        $this->_sendquoteSession = $sendquoteSession;
        parent::__construct($context, $data);
    }

    /**
     * Prepare Global Layout
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Quotations Sharing'));
    }

    /**
     * Retrieve Send Form Action URL
     *
     * @return string
     */
    public function getSendUrl()
    {
        return $this->getUrl('sendquote/index/send');
    }

    /**
     * Retrieve Entered Data by key
     *
     * @param string $key
     * @return string|null
     */
    public function getEnteredData($key)
    {
        if ($this->_enteredData === null) {
            $this->_enteredData = $this->_sendquoteSession->getData('sharing_form', true);
        }

        if (!$this->_enteredData || !isset($this->_enteredData[$key])) {
            return null;
        } else {
            return $this->escapeHtml($this->_enteredData[$key]);
        }
    }

    /**
     * Retrieve back button url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('sendquote');
    }

    /**
     * Retrieve number of emails allowed for sharing
     *
     * @return int
     */
    public function getEmailSharingLimit()
    {
        return $this->_sendquoteConfig->getSharingEmailLimit();
    }

    /**
     * Retrieve maximum email length allowed for sharing
     *
     * @return int
     */
    public function getTextSharingLimit()
    {
        return $this->_sendquoteConfig->getSharingTextLimit();
    }
}
