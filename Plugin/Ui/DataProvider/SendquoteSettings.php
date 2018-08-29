<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Techspot\SendQuote\Plugin\Ui\DataProvider;

use Techspot\SendQuote\Helper\Data;

/**
 * Plugin on Data Provider for frontend ui components (Components are responsible
 * for rendering product on front)
 * This plugin provides allowSendquote setting
 */
class SendquoteSettings
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * SendquoteSettings constructor.
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Add tax data to result
     *
     * @param \Magento\Checkout\CustomerData\Cart $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetData(\Magento\Catalog\Ui\DataProvider\Product\Listing\DataProvider $subject, $result)
    {
        $result['allowSendquote'] = $this->helper->isAllow();

        return $result;
    }
}
