<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Sendquote block customer items
 */
namespace Techspot\SendQuote\Block\Rss;

/**
 * @api
 * @since 100.0.2
 */
class Link extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Techspot\SendQuote\Helper\Data
     */
    protected $sendquoteHelper;

    /**
     * @var \Magento\Framework\App\Rss\UrlBuilderInterface
     */
    protected $rssUrlBuilder;

    /**
     * @var \Magento\Framework\Url\EncoderInterface
     */
    protected $urlEncoder;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Techspot\SendQuote\Helper\Data $sendquoteHelper
     * @param \Magento\Framework\App\Rss\UrlBuilderInterface $rssUrlBuilder
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Techspot\SendQuote\Helper\Data $sendquoteHelper,
        \Magento\Framework\App\Rss\UrlBuilderInterface $rssUrlBuilder,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->sendquoteHelper = $sendquoteHelper;
        $this->rssUrlBuilder = $rssUrlBuilder;
        $this->urlEncoder = $urlEncoder;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->rssUrlBuilder->getUrl($this->getLinkParams());
    }

    /**
     * Check whether status notification is allowed
     *
     * @return bool
     */
    public function isRssAllowed()
    {
        return $this->_scopeConfig->isSetFlag(
            'rss/sendquote/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    protected function getLinkParams()
    {
        $params = [];
        $sendquoteId = $this->sendquoteHelper->getSendquote()->getId();
        $customer = $this->sendquoteHelper->getCustomer();
        if ($customer) {
            $key = $customer->getId() . ',' . $customer->getEmail();
            $params = [
                'type' => 'sendquote',
                'data' => $this->urlEncoder->encode($key),
                '_secure' => false
            ];
        }
        if ($sendquoteId) {
            $params['sendquote_id'] = $sendquoteId;
        }
        return $params;
    }
}
