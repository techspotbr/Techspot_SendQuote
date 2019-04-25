<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Controller\Index;

use Magento\Framework\App\Action;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;

class Update extends \Techspot\SendQuote\Controller\AbstractIndex
{
    /**
     * @var \Techspot\SendQuote\Controller\SendquoteProviderInterface
     */
    protected $sendquoteProvider;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var \Techspot\SendQuote\Model\LocaleQuantityProcessor
     */
    protected $quantityProcessor;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Techspot\SendQuote\Controller\SendquoteProviderInterface $sendquoteProvider
     * @param \Techspot\SendQuote\Model\LocaleQuantityProcessor $quantityProcessor
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Techspot\SendQuote\Controller\SendquoteProviderInterface $sendquoteProvider,
        \Techspot\SendQuote\Model\LocaleQuantityProcessor $quantityProcessor
    ) {
        $this->_formKeyValidator = $formKeyValidator;
        $this->sendquoteProvider = $sendquoteProvider;
        $this->quantityProcessor = $quantityProcessor;
        parent::__construct($context);
    }

    /**
     * Update sendquote item comments
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws NotFoundException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }
        $sendquote = $this->sendquoteProvider->getSendquote();
        if (!$sendquote) {
            throw new NotFoundException(__('Page not found.'));
        }

        $post = $this->getRequest()->getPostValue();
        if ($post && isset($post['description']) && is_array($post['description'])) {
            $updatedItems = 0;

            foreach ($post['description'] as $itemId => $description) {
                $item = $this->_objectManager->create(\Techspot\SendQuote\Model\Item::class)->load($itemId);
                if ($item->getSendquoteId() != $sendquote->getId()) {
                    continue;
                }

                // Extract new values
                $description = (string)$description;

                if ($description == $this->_objectManager->get(
                    \Techspot\SendQuote\Helper\Data::class
                )->defaultCommentString()
                ) {
                    $description = '';
                }

                $qty = null;
                if (isset($post['qty'][$itemId])) {
                    $qty = $this->quantityProcessor->process($post['qty'][$itemId]);
                }
                if ($qty === null) {
                    $qty = $item->getQty();
                    if (!$qty) {
                        $qty = 1;
                    }
                } elseif (0 == $qty) {
                    try {
                        $item->delete();
                    } catch (\Exception $e) {
                        $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                        $this->messageManager->addError(__('We can\'t delete item from Quotations right now.'));
                    }
                }

                // Check that we need to save
                if ($item->getDescription() == $description && $item->getQty() == $qty) {
                    continue;
                }
                try {
                    $item->setDescription($description)->setQty($qty)->save();
                    $updatedItems++;
                    $message = __('Your quotation has been updated!');
                    $this->messageManager->addSuccess($message);

                } catch (\Exception $e) {
                    $this->messageManager->addError(
                        __(
                            'Can\'t save description %1',
                            $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($description)
                        )
                    );
                }
            }

            // save sendquote model for setting date of last update
            if ($updatedItems) {
                try {
                    $sendquote->save();
                    $this->_objectManager->get(\Techspot\SendQuote\Helper\Data::class)->calculate();
                } catch (\Exception $e) {
                    $this->messageManager->addError(__('Can\'t update quote list'));
                }
            }

            if (isset($post['save_and_share'])) {
                $resultRedirect->setPath('*/*/share', ['id' => $sendquote->getId()]);
                return $resultRedirect;
            }

            if (isset($post['save_and_request'])) {
                $resultRedirect->setPath('*/*/request', ['id' => $sendquote->getId()]);
                return $resultRedirect;
            }
        }
        $resultRedirect->setPath('*/index/view', ['id' => $sendquote->getId()]);
        return $resultRedirect;
    }
}
