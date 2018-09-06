<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Block\Adminhtml\Sendquote\Edit;

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
        
        $form->setUseContainer(false);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
