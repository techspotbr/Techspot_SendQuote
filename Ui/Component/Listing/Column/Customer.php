<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Customer extends Column
{
    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var View
     */
    protected $_customerViewHelper;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Helper\View $customerViewHelper,
        array $components = [],
        array $data = []
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_customerViewHelper = $customerViewHelper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$items) {

                if(null !== $items['customer_id']){
                    $model = $this->_customerFactory->create();
                    $customer = $model->load($items['customer_id']);
                    $customerName = $customer->getFirstname() .' '. $customer->getLastname();
                    $items['customer_id'] = $customerName;
                }
            }
        }
        return $dataSource;
    }
}