<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Block\Cart\Item\Renderer\Actions;

use Magento\Checkout\Block\Cart\Item\Renderer\Actions\Generic;
use Magento\Framework\View\Element\Template;
use Techspot\SendQuote\Helper\Data;

/**
 * @api
 * @since 100.0.2
 */
class MoveToSendquote extends Generic
{
    /**
     * @var Data
     */
    protected $sendquoteHelper;

    /**
     * @param Template\Context $context
     * @param Data $sendquoteHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $sendquoteHelper,
        array $data = []
    ) {
        $this->sendquoteHelper = $sendquoteHelper;
        parent::__construct($context, $data);
    }

    /**
     * Check whether "add to sendquote" button is allowed in cart
     *
     * @return bool
     */
    public function isAllowInCart()
    {
        return $this->sendquoteHelper->isAllowInCart();
    }

    /**
     * Get JSON POST params for moving from cart
     *
     * @return string
     */
    public function getMoveFromCartParams()
    {
        return $this->sendquoteHelper->getMoveFromCartParams($this->getItem()->getId());
    }
}
