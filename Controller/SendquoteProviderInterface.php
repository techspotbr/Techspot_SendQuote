<?php
/**
 *
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Controller;

/**
 * Interface \Techspot\SendQuote\Controller\SendquoteProviderInterface
 *
 */
interface SendquoteProviderInterface
{
    /**
     * Retrieve sendquote
     *
     * @param string $sendquoteId
     * @return \Techspot\SendQuote\Model\Sendquote
     */
    public function getSendquote($sendquoteId = null);
}
