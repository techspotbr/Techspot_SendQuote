<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Controller\Index;

use Magento\Framework\App\Action;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Techspot\SendQuote\Controller\AbstractIndex
{
    /**
     * @var \Techspot\SendQuote\Controller\SendquoteProviderInterface
     */
    protected $sendquoteProvider;

    /**
     * @param Action\Context $context
     * @param \Techspot\SendQuote\Controller\SendquoteProviderInterface $sendquoteProvider
     */
    public function __construct(
        Action\Context $context,
        \Techspot\SendQuote\Controller\SendquoteProviderInterface $sendquoteProvider
    ) {
        $this->sendquoteProvider = $sendquoteProvider;
        parent::__construct($context);
    }

    /**
     * Display customer sendquote
     *
     * @return \Magento\Framework\View\Result\Page
     * @throws NotFoundException
     */
    public function execute()
    {
        if (!$this->sendquoteProvider->getSendquote()) {
            throw new NotFoundException(__('Page not found.'));
        }
        /** @var \Magento\Framework\View\Result\Page resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        return $resultPage;
    }
}
