<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Techspot\SendQuote\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Techspot\SendQuote\Api\SendquoteRepositoryInterface;
use Techspot\SendQuote\Model\ResourceModel\Metadata;
use Magento\Framework\Exception\NoSuchEntityException;
use Techspot\SendQuote\Api\Data\SendquoteSearchResultInterfaceFactory as SearchResultFactory;

/**
 * Class SendquoteRepository
 */
class SendquoteRepository implements SendquoteRepositoryInterface
{
    /**
     * \Techspot\SendQuote\Api\Data\SendquoteInterface[]
     *
     * @var array
     */
    protected $registry = [];

    /**
     * @var Metadata
     */
    protected $metadata;

    /**
     * @var SearchResultFactory
     */
    protected $searchResultFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * SendquoteRepository constructor.
     * @param Metadata $sendquoteMetadata
     * @param SearchResultFactory $searchResultFactory
     * @param CollectionProcessorInterface|null $collectionProcessor
     */
    public function __construct(
        Metadata $sendquoteMetadata,
        SearchResultFactory $searchResultFactory,
        CollectionProcessorInterface $collectionProcessor = null
    ) {
        $this->metadata = $sendquoteMetadata;
        $this->searchResultFactory = $searchResultFactory;
        $this->collectionProcessor = $collectionProcessor ?: $this->getCollectionProcessor();
    }

    /**
     * Load entity
     *
     * @param int $id
     * @return mixed
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function get($id)
    {
        if (!$id) {
            throw new \Magento\Framework\Exception\InputException(__('ID required'));
        }
        if (!isset($this->registry[$id])) {
            /** @var \Techspot\SendQuote\Api\Data\SendquoteInterface $entity */
            $entity = $this->metadata->getNewInstance()->load($id);
            if (!$entity->getEntityId()) {
                throw new NoSuchEntityException(__('Requested entity doesn\'t exist'));
            }
            $this->registry[$id] = $entity;
        }
        return $this->registry[$id];
    }

    /**
     * @return \Techspot\SendQuote\Api\Data\SendquoteInterface
     */
    public function create()
    {
        return $this->metadata->getNewInstance();
    }

    /**
     * Find entities by criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Techspot\SendQuote\Api\Data\SendquoteInterface[]
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Techspot\SendQuote\Model\ResourceModel\Sendquote\Collection $collection */
        $collection = $this->searchResultFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $collection->setSearchCriteria($searchCriteria);
        return $collection;
    }

    /**
     * Register entity to delete
     *
     * @param \Techspot\SendQuote\Api\Data\SendquoteInterface $entity
     * @return bool
     */
    public function delete(\Techspot\SendQuote\Api\Data\SendquoteInterface $entity)
    {
        $this->metadata->getMapper()->delete($entity);
        unset($this->registry[$entity->getEntityId()]);
        return true;
    }

    /**
     * Delete entity by Id
     *
     * @param int $id
     * @return bool
     */
    public function deleteById($id)
    {
        $entity = $this->get($id);
        return $this->delete($entity);
    }

    /**
     * Perform persist operations for one entity
     *
     * @param \Techspot\SendQuote\Api\Data\SendquoteInterface $entity
     * @return \Techspot\SendQuote\Api\Data\SendquoteInterface
     */
    public function save(\Techspot\SendQuote\Api\Data\SendquoteInterface $entity)
    {
        $this->metadata->getMapper()->save($entity);
        $this->registry[$entity->getEntityId()] = $entity;
        return $this->registry[$entity->getEntityId()];
    }

    /**
     * Retrieve collection processor
     *
     * @deprecated 100.2.0
     * @return CollectionProcessorInterface
     */
    private function getCollectionProcessor()
    {
        if (!$this->collectionProcessor) {
            $this->collectionProcessor = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface::class
            );
        }
        return $this->collectionProcessor;
    }
}
