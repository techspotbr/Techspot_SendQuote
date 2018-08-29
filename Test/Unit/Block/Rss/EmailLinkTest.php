<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Techspot\SendQuote\Test\Unit\Block\Rss;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class EmailLinkTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Techspot\SendQuote\Block\Rss\EmailLink */
    protected $link;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Techspot\SendQuote\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $sendquoteHelper;

    /** @var \Magento\Framework\App\Rss\UrlBuilderInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\Url\EncoderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlEncoder;

    protected function setUp()
    {
        $sendquote = $this->createPartialMock(\Techspot\SendQuote\Model\Sendquote::class, ['getId', 'getSharingCode']);
        $sendquote->expects($this->any())->method('getId')->will($this->returnValue(5));
        $sendquote->expects($this->any())->method('getSharingCode')->will($this->returnValue('somesharingcode'));
        $customer = $this->createMock(\Magento\Customer\Api\Data\CustomerInterface::class);
        $customer->expects($this->any())->method('getId')->will($this->returnValue(8));
        $customer->expects($this->any())->method('getEmail')->will($this->returnValue('test@example.com'));

        $this->sendquoteHelper = $this->createPartialMock(
            \Techspot\SendQuote\Helper\Data::class,
            ['getSendquote', 'getCustomer', 'urlEncode']
        );
        $this->urlEncoder = $this->createPartialMock(\Magento\Framework\Url\EncoderInterface::class, ['encode']);

        $this->sendquoteHelper->expects($this->any())->method('getSendquote')->will($this->returnValue($sendquote));
        $this->sendquoteHelper->expects($this->any())->method('getCustomer')->will($this->returnValue($customer));
        $this->urlEncoder->expects($this->any())
            ->method('encode')
            ->willReturnCallback(function ($url) {
                return strtr(base64_encode($url), '+/=', '-_,');
            });

        $this->urlBuilder = $this->createMock(\Magento\Framework\App\Rss\UrlBuilderInterface::class);
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->link = $this->objectManagerHelper->getObject(
            \Techspot\SendQuote\Block\Rss\EmailLink::class,
            [
                'sendquoteHelper' => $this->sendquoteHelper,
                'rssUrlBuilder' => $this->urlBuilder,
                'urlEncoder' => $this->urlEncoder,
            ]
        );
    }

    public function testGetLink()
    {
        $this->urlBuilder->expects($this->atLeastOnce())->method('getUrl')
            ->with($this->equalTo([
                'type' => 'sendquote',
                'data' => 'OCx0ZXN0QGV4YW1wbGUuY29t',
                '_secure' => false,
                'sendquote_id' => 5,
                'sharing_code' => 'somesharingcode',
            ]))
            ->will($this->returnValue('http://url.com/rss/feed/index/type/sendquote/sendquote_id/5'));
        $this->assertEquals('http://url.com/rss/feed/index/type/sendquote/sendquote_id/5', $this->link->getLink());
    }
}
