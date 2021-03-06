<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Model\ResourceModel\Sendquote\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

/**
 * Order grid collection
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * Initialize dependencies.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'sendquote',
        $resourceModel = \Techspot\SendQuote\Model\ResourceModel\Sendquote::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $this->join(
            [$this->getTable('customer_entity')],
            'main_table.customer_id = '.$this->getTable('customer_entity').'.entity_id',
            array('firstname', 'lastname', 'email')
        );

        return $this;
    }
}
