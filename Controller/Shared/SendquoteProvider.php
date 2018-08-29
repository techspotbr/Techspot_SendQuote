<?php
/**
 *
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Controller\Shared;

use Techspot\SendQuote\Controller\SendquoteProviderInterface;

class SendquoteProvider implements SendquoteProviderInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Techspot\SendQuote\Model\SendquoteFactory
     */
    protected $sendquoteFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Techspot\SendQuote\Model\Sendquote
     */
    protected $sendquote;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Techspot\SendQuote\Model\SendquoteFactory $sendquoteFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Techspot\SendQuote\Model\SendquoteFactory $sendquoteFactory,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->request = $request;
        $this->sendquoteFactory = $sendquoteFactory;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Retrieve current sendquote
     * @param string $sendquoteId
     * @return \Techspot\SendQuote\Model\Sendquote
     */
    public function getSendquote($sendquoteId = null)
    {
        if ($this->sendquote) {
            return $this->sendquote;
        }
        $code = (string)$this->request->getParam('code');
        if (empty($code)) {
            return false;
        }

        $sendquote = $this->sendquoteFactory->create()->loadByCode($code);
        if (!$sendquote->getId()) {
            return false;
        }

        $this->checkoutSession->setSharedSendquote($code);
        $this->sendquote = $sendquote;
        return $sendquote;
    }
}
