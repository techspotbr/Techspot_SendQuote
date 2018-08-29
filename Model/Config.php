<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Model;

/**
 * @api
 * @since 100.0.2
 */
class Config
{
    const XML_PATH_SHARING_EMAIL_LIMIT = 'sendquote/email/number_limit';

    const XML_PATH_SHARING_TEXT_LIMIT = 'sendquote/email/text_limit';

    const SHARING_EMAIL_LIMIT = 10;

    const SHARING_TEXT_LIMIT = 255;

    /**
     * @var \Magento\Catalog\Model\Config
     */
    private $_catalogConfig;

    /**
     * @var \Magento\Catalog\Model\Attribute\Config
     */
    private $_attributeConfig;

    /**
     * Number of emails allowed for sharing sendquote
     *
     * @var int
     */
    private $_sharingEmailLimit;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Catalog\Model\Attribute\Config $attributeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Catalog\Model\Attribute\Config $attributeConfig
    ) {
        $emailLimitInConfig = (int)$scopeConfig->getValue(
            self::XML_PATH_SHARING_EMAIL_LIMIT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $textLimitInConfig = (int)$scopeConfig->getValue(
            self::XML_PATH_SHARING_TEXT_LIMIT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $this->_sharingEmailLimit = $emailLimitInConfig ?: self::SHARING_EMAIL_LIMIT;
        $this->_sharignTextLimit = $textLimitInConfig ?: self::SHARING_TEXT_LIMIT;
        $this->_catalogConfig = $catalogConfig;
        $this->_attributeConfig = $attributeConfig;
    }

    /**
     * Get product attributes that need in sendquote
     *
     * @return array
     */
    public function getProductAttributes()
    {
        $catalogAttributes = $this->_catalogConfig->getProductAttributes();
        $sendquoteAttributes = $this->_attributeConfig->getAttributeNames('sendquote_item');
        return array_merge($catalogAttributes, $sendquoteAttributes);
    }

    /**
     * Retrieve number of emails allowed for sharing sendquote
     *
     * @return int
     */
    public function getSharingEmailLimit()
    {
        return $this->_sharingEmailLimit;
    }

    /**
     * Retrieve maximum length of sharing email text
     *
     * @return int
     */
    public function getSharingTextLimit()
    {
        return $this->_sharignTextLimit;
    }
}
