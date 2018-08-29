<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Techspot\SendQuote\Test\Unit\Block\Adminhtml\Widget\Grid\Column\Filter;

use \Techspot\SendQuote\Block\Adminhtml\Widget\Grid\Column\Filter\Text;

class TextTest extends \PHPUnit\Framework\TestCase
{
    /** @var Text | \PHPUnit_Framework_MockObject_MockObject */
    private $textFilterBlock;

    protected function setUp()
    {
        $this->textFilterBlock = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))->getObject(
            \Techspot\SendQuote\Block\Adminhtml\Widget\Grid\Column\Filter\Text::class
        );
    }

    public function testGetCondition()
    {
        $value = "test";
        $this->textFilterBlock->setValue($value);
        $this->assertSame(["like" => $value], $this->textFilterBlock->getCondition());
    }
}
