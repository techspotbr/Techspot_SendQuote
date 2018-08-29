<?php
/**
 *
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Controller;

use Magento\Framework\App\RequestInterface;

class SendquoteProvider implements SendquoteProviderInterface
{
    /**
     * @var \Techspot\SendQuote\Model\Sendquote
     */
    protected $sendquote;

    /**
     * @var \Techspot\SendQuote\Model\SendquoteFactory
     */
    protected $sendquoteFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @param \Techspot\SendQuote\Model\SendquoteFactory $sendquoteFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param RequestInterface $request
     */
    public function __construct(
        \Techspot\SendQuote\Model\SendquoteFactory $sendquoteFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        RequestInterface $request
    ) {
        $this->request = $request;
        $this->sendquoteFactory = $sendquoteFactory;
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getSendquote($sendquoteId = null)
    {
        if ($this->sendquote) {
            return $this->sendquote;
        }
        try {
            if (!$sendquoteId) {
                $sendquoteId = $this->request->getParam('sendquote_id');
            }
            $customerId = $this->customerSession->getCustomerId();
            $sendquote = $this->sendquoteFactory->create();

            if (!$sendquoteId && !$customerId) {
                return $sendquote;
            }

            if ($sendquoteId) {
                $sendquote->load($sendquoteId);
            } elseif ($customerId) {
                $sendquote->loadByCustomerId($customerId, true);
            }

            if (!$sendquote->getId() || $sendquote->getCustomerId() != $customerId) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(
                    __('The requested Quotations doesn\'t exist.')
                );
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->messageManager->addError($e->getMessage());
            return false;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t create the Quotations right now.'));
            return false;
        }
        $this->sendquote = $sendquote;
        return $sendquote;
    }
}
