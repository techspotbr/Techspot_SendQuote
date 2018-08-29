<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Sendquote RSS URL to Email Block
 */
namespace Techspot\SendQuote\Block\Rss;

/**
 * Class EmailLink
 *
 * @api
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 * @since 100.0.2
 */
class EmailLink extends Link
{
    /**
     * @var string
     */
    protected $_template = 'rss/email.phtml';

    /**
     * @return string
     */
    protected function getLinkParams()
    {
        $params = parent::getLinkParams();
        $sendquote = $this->sendquoteHelper->getSendquote();
        $sharingCode = $sendquote->getSharingCode();
        if ($sharingCode) {
            $params['sharing_code'] = $sharingCode;
        }
        return $params;
    }
}
