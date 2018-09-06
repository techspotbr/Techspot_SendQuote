<?php
/**
 *
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Controller;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

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
     * @var DateTime
     */
    protected $dateTime;

      /**
     * @var TimeZone
     */
    protected $timezone;

    /**
     * @param \Techspot\SendQuote\Model\SendquoteFactory $sendquoteFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param RequestInterface $request
     * @param DateTime $dateTime
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        \Techspot\SendQuote\Model\SendquoteFactory $sendquoteFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        RequestInterface $request,
        DateTime $dateTime,
        TimezoneInterface $timezone
    ) {
        $this->request = $request;
        $this->sendquoteFactory = $sendquoteFactory;
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->dateTime = $dateTime;
        $this->timezone = $timezone;
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

            // Create a new quotation
            /*
            if(!$this->canEdit($sendquote->getCreatedAt())){
                $sendquote = $this->sendquoteFactory->create();
                $sendquote->setCustomerId($customerId);
                $sendquote->setCreatedAt($this->timezone->date()->format('Y-m-d H:i:s'));
                $sendquote->setSharingCode($sendquote->generateSharingCode());
                $sendquote->save();
            }*/

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

    protected function canEdit($quotationDate)
    {
        $createdAt = $this->timezone->date(new \DateTime($quotationDate))->format('Y-m-d');
        $currentDate = $this->timezone->date()->format('Y-m-d');

        if($createdAt == $currentDate){
            return true;
        }
        return false;
    }
}
