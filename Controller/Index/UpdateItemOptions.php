<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Controller\Index;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultFactory;
use Techspot\SendQuote\Controller\SendquoteProviderInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UpdateItemOptions extends \Techspot\SendQuote\Controller\AbstractIndex
{
    /**
     * @var SendquoteProviderInterface
     */
    protected $sendquoteProvider;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @param Action\Context $context
     * @param Session $customerSession
     * @param SendquoteProviderInterface $sendquoteProvider
     * @param ProductRepositoryInterface $productRepository
     * @param Validator $formKeyValidator
     */
    public function __construct(
        Action\Context $context,
        Session $customerSession,
        SendquoteProviderInterface $sendquoteProvider,
        ProductRepositoryInterface $productRepository,
        Validator $formKeyValidator
    ) {
        $this->_customerSession = $customerSession;
        $this->sendquoteProvider = $sendquoteProvider;
        $this->productRepository = $productRepository;
        $this->formKeyValidator = $formKeyValidator;
        parent::__construct($context);
    }

    /**
     * Action to accept new configuration for a sendquote item
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $resultRedirect->setPath('*/*/');
        }

        $productId = (int)$this->getRequest()->getParam('product');
        if (!$productId) {
            $resultRedirect->setPath('*/');
            return $resultRedirect;
        }

        try {
            $product = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            $product = null;
        }

        if (!$product || !$product->isVisibleInCatalog()) {
            $this->messageManager->addError(__('We can\'t specify a product.'));
            $resultRedirect->setPath('*/');
            return $resultRedirect;
        }

        try {
            $id = (int)$this->getRequest()->getParam('id');
            /* @var \Techspot\SendQuote\Model\Item */
            $item = $this->_objectManager->create(\Techspot\SendQuote\Model\Item::class);
            $item->load($id);
            $sendquote = $this->sendquoteProvider->getSendquote($item->getSendquoteId());
            if (!$sendquote) {
                $resultRedirect->setPath('*/');
                return $resultRedirect;
            }

            $buyRequest = new \Magento\Framework\DataObject($this->getRequest()->getParams());

            $sendquote->updateItem($id, $buyRequest)->save();

            $this->_objectManager->get(\Techspot\SendQuote\Helper\Data::class)->calculate();
            $this->_eventManager->dispatch(
                'sendquote_update_item',
                ['sendquote' => $sendquote, 'product' => $product, 'item' => $sendquote->getItem($id)]
            );

            $this->_objectManager->get(\Techspot\SendQuote\Helper\Data::class)->calculate();

            $message = __('%1 has been updated in your Quotations.', $product->getName());
            $this->messageManager->addSuccess($message);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t update your Quotations right now.'));
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }
        $resultRedirect->setPath('*/*', ['sendquote_id' => $sendquote->getId()]);
        return $resultRedirect;
    }
}
