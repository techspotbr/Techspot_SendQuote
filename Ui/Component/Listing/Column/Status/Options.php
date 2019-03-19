<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Ui\Component\Listing\Column\Status;

use Magento\Framework\Data\OptionSourceInterface;
use Techspot\SendQuote\Model\Sendquote;


/**
 * Class Options
 */
class Options implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {

            $statuses = array(
                ['value' => Sendquote::SENDQUOTE_STATUS_WAITING_ANSWER, 'label' => __('Waiting answer')],
                ['value' => Sendquote::SENDQUOTE_STATUS_ANSWERED, 'label' => __('Answered')],
                ['value' => Sendquote::SENDQUOTE_STATUS_IN_QUOTATION, 'label' => __('In quotation') ],
                ['value' => Sendquote::SENDQUOTE_STATUS_VARNISHED, 'label' => __('Varnished')]
            );
            $this->options = $statuses;
        }
        return $this->options;
    }
}
