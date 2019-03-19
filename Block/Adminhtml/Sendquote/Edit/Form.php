<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Block\Adminhtml\Sendquote\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;

/**
 * Sendquote view form
 *
 * @api
 * @author     Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
  
    /**
     * @var \Magebay\Hello\Model\System\Config\Status
     */
    protected $_status;
   /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param Status $status
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    /**
     * Retrieve source
     *
     * @return \Techspot\SendQuote\Model\Sendquote
     */
    public function getSource()
    {
        return $this->getSendquote();
    }

    /**
     * Retrieve sendquote model instance
     *
     * @return \Techspot\SendQuote\Model\Sendquote
     */
    public function getSendquote()
    {
        return $this->_coreRegistry->registry('current_quotation');
    }

    /**
     * Retrieve formated price
     *
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->getSendquote()->formatPrice($price);
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', ['sendquote_id' => $this->getSendquote()->getSendquoteId()]);
    }


    protected function _prepareForm()
    {
       /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
            'data' => [
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
            ]
            ]
        );

        $fieldset = $form->addFieldset(
            'options_fieldset',
            ['legend' => __('Detalhes do Orçamento'), 'class' => 'fieldset-wide fieldset-widget-options']
        );

        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        
        $fieldset->addField(
            'shelf_life',
            'date',
            [
                'name' => 'quotation[shelf_life]',
                'label' => __('Expirate Date'),
                'title' => __('Expirate Date'),
                'required' => true,
                'date_format' => $dateFormat,
                'time' => false
            ]
        );

        $fieldset->addField(
            'description',
            'editor',
            [
                'name' => 'quotation[description]',
                'label' => __('Comments'),
                'title' => __('Comments'),
                'rows' => '5',
                'cols' => '30',
                'wysiwyg' => true,
                'config' => $this->_wysiwygConfig->getConfig(),
                'required' => true
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'label'     => __('Status'),
                'title'     => __('Status'),
                'name'      => 'quotation[status]',
                'required'  => true,
                'class'     => 'required-entry',
                'options'   => \Techspot\SendQuote\Model\Sendquote::getStatusCode()
            ]
        );
        
        $form->setValues($this->getSource()->getData());
        $form->setUseContainer(false);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
