<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Block\Adminhtml\Sendquote;

/**
 * Adminhtml quote view block
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Admin session
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_session;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Backend session
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendSession;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Backend\Model\Auth\Session $backendSession
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Backend\Model\Auth\Session $backendSession,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_backendSession = $backendSession;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Constructor
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _construct()
    {
        $this->_objectId = 'sendquote_id';
        $this->_controller = 'adminhtml_sendquote';
        $this->_mode = 'edit';
        $this->_session = $this->_backendSession;

        parent::_construct();

        //$this->buttonList->remove('save');
        $this->buttonList->remove('reset');
        //$this->buttonList->remove('delete');

        if (!$this->getSendquote()) {
            return;
        }

        if ($this->getSendquote()->getSendquoteId()) {
            $this->buttonList->add(
                'print',
                [
                    'label' => __('Print'),
                    'class' => 'print',
                    'onclick' => 'setLocation(\'' . $this->getPrintUrl() . '\')'
                ]
            );
        }
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
     * Get header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {

        return __(
            'Quotation #%1 | %2 | %4 (%3)',
            $this->getSendquote()->getSendquoteId(),
            $this->getSendquote()->getCustomerId(),
            '',
            $this->formatDate(
                $this->_localeDate->date(new \DateTime($this->getSendquote()->getCreatedAt())),
                \IntlDateFormatter::MEDIUM,
                true
            )
        );
    }

    /**
     * Get back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl(
            'sendquote/*/view',
            ['sendquote_id' => $this->getSendquote()->getSendquoteId()]
        );
    }

    /**
     * Get email url
     *
     * @return string
     */
    public function getEmailUrl()
    {
        return $this->getUrl(
            'sendquote/*/email',
            ['sendquote_id' => $this->getSendquote()->getSendquoteId()]
        );
    }

    /**
     * Get print url
     *
     * @return string
     */
    public function getPrintUrl()
    {
        return $this->getUrl('sendquote/*/print', ['sendquote_id' => $this->getSendquote()->getgetSendquoteId()]);
    }

    /**
     * Update back button url
     *
     * @param bool $flag
     * @return $this
     */
    public function updateBackButtonUrl($flag)
    {
        if ($flag) {
            if ($this->getSendquote()->getBackUrl()) {
                return $this->buttonList->update(
                    'back',
                    'onclick',
                    'setLocation(\'' . $this->getSendquote()->getBackUrl() . '\')'
                );
            }
            return $this->buttonList->update('back', 'onclick', 'setLocation(\'' . $this->getUrl('sendquote/sendquote/') . '\')');
        }
        return $this;
    }

    /**
     * Check whether is allowed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    
}
