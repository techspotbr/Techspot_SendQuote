<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\DB\FieldDataConverterFactory;
use Magento\Framework\DB\DataConverter\SerializedToJson;
use Magento\Framework\DB\Select\QueryModifierFactory;
use Magento\Framework\DB\Select\InQueryModifier;
use Magento\Framework\DB\Query\Generator;

class UpgradeData implements UpgradeDataInterface
{
    private $eavSetupFactory;

    /**
     * @var FieldDataConverterFactory
     */
    private $fieldDataConverterFactory;

    /**
     * @var QueryModifierFactory
     */
    private $queryModifierFactory;

    /**
     * @var Generator
     */
    private $queryGenerator;

    /**
     * Constructor
     *
     * @param FieldDataConverterFactory $fieldDataConverterFactory
     * @param QueryModifierFactory $queryModifierFactory
     * @param Generator $queryGenerator
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        FieldDataConverterFactory $fieldDataConverterFactory,
        QueryModifierFactory $queryModifierFactory,
        Generator $queryGenerator
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->fieldDataConverterFactory = $fieldDataConverterFactory;
        $this->queryModifierFactory = $queryModifierFactory;
        $this->queryGenerator = $queryGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $this->upgradeToVersionTwoZeroOne($setup);
        }

        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $this->upgradeToVersionTwoZeroTwo($setup);
        }

        if (version_compare($context->getVersion(), '2.0.3', '<')) {
            $this->upgradeToVersionTwoZeroTree($setup);
        }

        if (version_compare($context->getVersion(), '2.0.4', '<')) {
            $this->upgradeToVersionTwoZeroFour($setup);
        }

        if (version_compare($context->getVersion(), '2.0.5', '<')) {
            $this->upgradeToVersionTwoZeroFive($setup);
        }

        if (version_compare($context->getVersion(), '2.0.6', '<')) {
            $this->upgradeToVersionTwoZeroSix($setup);
        }

        if (version_compare($context->getVersion(), '2.0.7', '<')) {
            $this->upgradeToVersionTwoZeroSeven($setup);
        }
    }

    /**
     * Upgrade to version 2.0.1, convert data for `value` field in `sendquote_item_option table`
     * from php-serialized to JSON format
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function upgradeToVersionTwoZeroOne(ModuleDataSetupInterface $setup)
    {
        $fieldDataConverter = $this->fieldDataConverterFactory->create(SerializedToJson::class);
        $queryModifier = $this->queryModifierFactory->create(
            'in',
            [
                'values' => [
                    'code' => [
                        'parameters',
                        'info_buyRequest',
                        'bundle_option_ids',
                        'bundle_selection_ids',
                        'attributes',
                        'bundle_selection_attributes',
                    ]
                ]
            ]
        );
        $fieldDataConverter->convert(
            $setup->getConnection(),
            $setup->getTable('sendquote_item_option'),
            'option_id',
            'value',
            $queryModifier
        );
        $select = $setup->getConnection()
            ->select()
            ->from(
                $setup->getTable('catalog_product_option'),
                ['option_id']
            )
            ->where('type = ?', 'file');
        $iterator = $this->queryGenerator->generate('option_id', $select);
        foreach ($iterator as $selectByRange) {
            $codes = $setup->getConnection()->fetchCol($selectByRange);
            $codes = array_map(
                function ($id) {
                    return 'option_' . $id;
                },
                $codes
            );
            $queryModifier = $this->queryModifierFactory->create(
                'in',
                [
                    'values' => [
                        'code' => $codes
                    ]
                ]
            );
            $fieldDataConverter->convert(
                $setup->getConnection(),
                $setup->getTable('sendquote_item_option'),
                'option_id',
                'value',
                $queryModifier
            );
        }
    }

    /**
     * Upgrade to version 2.0.2, add field custom_price in `sendquote_item table`
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function upgradeToVersionTwoZeroTwo(ModuleDataSetupInterface $setup)
    {

        $installer = $setup;

        $installer->startSetup();
       
        $installer->getConnection()
            ->addColumn(
                $installer->getTable('sendquote_item'),
                'price',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'comment' => 'Price'
                ]
            );
            
        $installer->getConnection()
            ->addColumn(
                $installer->getTable('sendquote_item'),
                'custom_price',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'comment' => 'Custom Price'
                ]
            );
        $installer->getConnection()
            ->addColumn(
                $installer->getTable('sendquote_item'),
                'user_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 10,
                    'nullable' => true,
                    'comment' => 'Price'
                ]
            );

        $installer->endSetup();
    }

    /**
     * Upgrade to version 2.0.3, add fields (status,shelf_life) in `sendquote table`
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function upgradeToVersionTwoZeroTree(ModuleDataSetupInterface $setup)
    {
        $installer = $setup;

        $installer->startSetup();

        $installer->getConnection()
            ->addColumn(
                $installer->getTable('sendquote'),
                'status',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'nullable' => false,
                    'comment' => 'Status',
                    'default' => '0'
                ]
            );

            $installer->getConnection()
            ->addColumn(
                $installer->getTable('sendquote'),
                'shelf_life',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => false,
                    'comment' => 'Shelf Live'
                ]
            );

        $installer->endSetup();
    }

    /**
     * Upgrade to version 2.0.4, add fields (created_at) in `sendquote table`
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function upgradeToVersionTwoZeroFour(ModuleDataSetupInterface $setup)
    {
        $installer = $setup;

        $installer->startSetup();

        $installer->getConnection()
            ->addColumn(
                $installer->getTable('sendquote'),
                'created_at',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => false,
                    'comment' => 'Created Date'
                ]
            );

        $installer->endSetup();
    }

    /**
     * Upgrade to version 2.0.5, remove index unique from customer_id in `sendquote table`
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function upgradeToVersionTwoZeroFive(ModuleDataSetupInterface $setup)
    {
        $installer = $setup;

        $installer->startSetup();

        //$installer->getConnection()->dropIndex($installer->getTable('sendquote'), 'SENDQUOTE_CUSTOMER_ID');
       
        $installer->endSetup();
    }

    /**
     * Upgrade to version 2.0.6, add index (DEFAULT, NOT UNIQUE) from customer_id in `sendquote table`
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function upgradeToVersionTwoZeroSix(ModuleDataSetupInterface $setup)
    {
        $installer = $setup;

        $installer->startSetup();

        //addIndex($tableName, $indexName, $fields, $indexType = self::INDEX_TYPE_INDEX, $schemaName = null);

        $installer->getConnection()->addIndex(
            'sendquote',
            'SENDQUOTE_CUSTOMER_ID',
            'customer_id',
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
        );
        $installer->endSetup();
    }

    /**
     * Upgrade to version 2.0.7, add product attribute only_for_quotation
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function upgradeToVersionTwoZeroSeven(ModuleDataSetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'only_for_quotation',
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => __('Only For Quotation'),
                'input' => 'boolean',
                'class' => '',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple,configurable,virtual,bundle,downloadable'
            ]
        );
    }
}
