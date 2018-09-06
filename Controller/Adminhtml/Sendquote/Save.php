<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Controller\Adminhtml\Sendquote;

use Magento\Backend\App\Action;
use Magento\Sales\Model\Order\Shipment\Validation\QuantityValidator;

/**
 * Class Save
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Magento\Backend\App\Action
{

    protected $authSession;
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Techspot_SendQuote::sendquote';

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param  \Magento\Backend\Model\Auth\Session $authSession
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->authSession = $authSession;
        parent::__construct($context);
    }

    /**
     * Save quotation
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $formKeyIsValid = $this->_formKeyValidator->validate($this->getRequest());
        $isPost = $this->getRequest()->isPost();
        if (!$formKeyIsValid || !$isPost) {
            $this->messageManager->addError(__('We can\'t save the quotation right now.'));
            return $resultRedirect->setPath('sales/order/index');
        }

        $quotationId    = $this->getRequest()->getParam('sendquote_id');
        $quotation      = $this->quotationLoad($quotationId);

        if($quotation->getSendquoteId()){

            $data = $this->getRequest()->getParam('quotation');
            
            $timezoneInterface = $this->_objectManager->create('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
            $formattedDate = $timezoneInterface->date($data['shelf_life'], null, true, false);
            $formattedDate = $formattedDate->format('Y-m-d');

            $quotation->setShelfLife($formattedDate);
            $quotation->setStatus($data['status']);
            $quotation->save();
                        
            foreach($data['items'] as $itemId => $itemData){
                $item = $this->_objectManager->create(\Techspot\SendQuote\Model\Item::class)->load($itemId);
                if ($item->getSendquoteId() != $quotationId) {
                    continue;
                }

                try {
                    $item->setUserId($this->authSession->getUser()->getId());
                    $item->setCustomPrice($itemData['custom-price'])->save();
                } catch (\Exception $e) {
                    $this->messageManager->addError(__('Can\'t update quotation item: %1', $itemId));
                }
            }
        }
        $this->_redirect('sendquote/sendquote/view', ['sendquote_id' => $quotationId]);
    }


    protected function quotationLoad($sendquoteId)
    {
        $sendquote = $this->_objectManager->create(\Techspot\SendQuote\Model\Sendquote::class)->load($sendquoteId);
        return $sendquote;
    }
}
