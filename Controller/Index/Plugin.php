<?php
/**
 *
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Controller\Index;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;

class Plugin
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Techspot\SendQuote\Model\AuthenticationStateInterface
     */
    protected $authenticationState;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirector;

    /**
     * @param CustomerSession $customerSession
     * @param \Techspot\SendQuote\Model\AuthenticationStateInterface $authenticationState
     * @param ScopeConfigInterface $config
     * @param RedirectInterface $redirector
     */
    public function __construct(
        CustomerSession $customerSession,
        \Techspot\SendQuote\Model\AuthenticationStateInterface $authenticationState,
        ScopeConfigInterface $config,
        RedirectInterface $redirector
    ) {
        $this->customerSession = $customerSession;
        $this->authenticationState = $authenticationState;
        $this->config = $config;
        $this->redirector = $redirector;
    }

    /**
     * Perform customer authentication and sendquote feature state checks
     *
     * @param \Magento\Framework\App\ActionInterface $subject
     * @param RequestInterface $request
     * @return void
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function beforeDispatch(\Magento\Framework\App\ActionInterface $subject, RequestInterface $request)
    {
        if ($this->authenticationState->isEnabled() && !$this->customerSession->authenticate()) {
            $subject->getActionFlag()->set('', 'no-dispatch', true);
            if (!$this->customerSession->getBeforeSendquoteUrl()) {
                $this->customerSession->setBeforeSendquoteUrl($this->redirector->getRefererUrl());
            }
            $this->customerSession->setBeforeSendquoteRequest($request->getParams());
            $this->customerSession->setBeforeRequestParams($this->customerSession->getBeforeSendquoteRequest());
            $this->customerSession->setBeforeModuleName('sendquote');
            $this->customerSession->setBeforeControllerName('index');
            $this->customerSession->setBeforeAction('add');
        }
        if (!$this->config->isSetFlag('sendquote/general/active')) {
            throw new NotFoundException(__('Page not found.'));
        }
    }
}
