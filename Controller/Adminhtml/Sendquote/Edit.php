<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Controller\Adminhtml\Sendquote;

use Magento\Framework\Registry;
use Magento\Framework\App\ObjectManager;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Techspot_SendQuote::actions_edit';

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @param Registry $registry
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Registry $registry,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->registry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * Quotation information page
     *
     * @return void
     */
    public function execute()
    {
        $quotation = $this->quotationLoad();

        if ($quotation) {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getLayout()->getBlock('sendquote_edit')
                ->updateBackButtonUrl($this->getRequest()->getParam('come_from'));
            $resultPage->setActiveMenu('Techspot_SendQuote::sendquote');
            $resultPage->getConfig()->getTitle()->prepend(__('Quotation'));
            $resultPage->getConfig()->getTitle()->prepend("#" . $quotation->getSendquoteId());
            return $resultPage;
        } else {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('noroute');
            return $resultForward;
        }
    }

    protected function quotationLoad()
    {
        try {
            $id = $this->getRequest()->getParam('sendquote_id');
            $model = $this->_objectManager->create(\Techspot\SendQuote\Model\Sendquote::class);
            $quotation = $model->load($id);
            /*
            foreach($quotation->getItemCollection() as $item){
                var_dump($item);
            }
            */

            $this->registry->register('current_quotation', $quotation);
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Sendquote capturing error'));
            return false;
        }

        return $quotation;
    }
}
