<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Techspot\SendQuote\Test\Unit\Controller\Index;

class PluginTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSession;

    /**
     * @var \Techspot\SendQuote\Model\AuthenticationStateInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $authenticationState;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $config;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $redirector;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    protected function setUp()
    {
        $this->customerSession = $this->getMockBuilder(\Magento\Customer\Model\Session::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'authenticate',
                'getBeforeSendquoteUrl',
                'setBeforeSendquoteUrl',
                'setBeforeSendquoteRequest',
                'getBeforeSendquoteRequest',
                'setBeforeRequestParams',
                'setBeforeModuleName',
                'setBeforeControllerName',
                'setBeforeAction',
            ])
            ->getMock();

        $this->authenticationState = $this->createMock(\Techspot\SendQuote\Model\AuthenticationState::class);
        $this->config = $this->createMock(\Magento\Framework\App\Config::class);
        $this->redirector = $this->createMock(\Magento\Store\App\Response\Redirect::class);
        $this->request = $this->createMock(\Magento\Framework\App\Request\Http::class);
    }

    protected function tearDown()
    {
        unset(
            $this->customerSession,
            $this->authenticationState,
            $this->config,
            $this->redirector,
            $this->request
        );
    }

    protected function getPlugin()
    {
        return new \Techspot\SendQuote\Controller\Index\Plugin(
            $this->customerSession,
            $this->authenticationState,
            $this->config,
            $this->redirector
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\NotFoundException
     */
    public function testBeforeDispatch()
    {
        $refererUrl = 'http://referer-url.com';
        $params = [
            'product' => 1,
        ];

        $actionFlag = $this->createMock(\Magento\Framework\App\ActionFlag::class);
        $indexController = $this->createMock(\Techspot\SendQuote\Controller\Index\Index::class);

        $actionFlag
            ->expects($this->once())
            ->method('set')
            ->with('', 'no-dispatch', true)
            ->willReturn(true);

        $indexController
            ->expects($this->once())
            ->method('getActionFlag')
            ->willReturn($actionFlag);

        $this->authenticationState
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->redirector
            ->expects($this->once())
            ->method('getRefererUrl')
            ->willReturn($refererUrl);

        $this->request
            ->expects($this->once())
            ->method('getParams')
            ->willReturn($params);

        $this->customerSession->expects($this->once())
            ->method('authenticate')
            ->willReturn(false);
        $this->customerSession->expects($this->once())
            ->method('getBeforeSendquoteUrl')
            ->willReturn(false);
        $this->customerSession->expects($this->once())
            ->method('setBeforeSendquoteUrl')
            ->with($refererUrl)
            ->willReturnSelf();
        $this->customerSession->expects($this->once())
            ->method('setBeforeSendquoteRequest')
            ->with($params)
            ->willReturnSelf();
        $this->customerSession->expects($this->once())
            ->method('getBeforeSendquoteRequest')
            ->willReturn($params);
        $this->customerSession->expects($this->once())
            ->method('setBeforeRequestParams')
            ->with($params)
            ->willReturnSelf();
        $this->customerSession->expects($this->once())
            ->method('setBeforeModuleName')
            ->with('sendquote')
            ->willReturnSelf();
        $this->customerSession->expects($this->once())
            ->method('setBeforeControllerName')
            ->with('index')
            ->willReturnSelf();
        $this->customerSession->expects($this->once())
            ->method('setBeforeAction')
            ->with('add')
            ->willReturnSelf();

        $this->config
            ->expects($this->once())
            ->method('isSetFlag')
            ->with('sendquote/general/active')
            ->willReturn(false);

        $this->getPlugin()->beforeDispatch($indexController, $this->request);
    }
}
