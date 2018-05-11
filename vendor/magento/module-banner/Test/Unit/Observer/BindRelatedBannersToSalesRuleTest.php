<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Banner\Test\Unit\Observer;

class BindRelatedBannersToSalesRuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Banner\Observer\BindRelatedBannersToSalesRule
     */
    protected $bindRelatedBannersToSalesRuleObserver;

    /**
     * @var \Magento\Framework\Event\Observer
     */
    protected $eventObserver;

    /**
     * @var \Magento\Banner\Model\ResourceModel\BannerFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $bannerFactory;

    /**
     * @var \Magento\Framework\Event|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $event;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $http;

    protected function setUp()
    {
        $this->bannerFactory = $this->getMockBuilder(\Magento\Banner\Model\ResourceModel\BannerFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->bindRelatedBannersToSalesRuleObserver = new \Magento\Banner\Observer\BindRelatedBannersToSalesRule(
            $this->bannerFactory
        );
    }

    /**
     * @param [] $banners
     *
     * @dataProvider bindRelatedBannersDataProvider
     */
    public function testBindRelatedBannersToSalesRule($banners)
    {
        $this->event = $this->getMockBuilder(\Magento\Framework\Event::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRule', 'getId'])
            ->getMock();
        $this->http = $this->getMockBuilder(\Magento\Framework\App\Request\Http::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRelatedBanners', 'getId'])
            ->getMock();
        $banner = $this->getMockBuilder(\Magento\Banner\Model\ResourceModel\Banner::class)
            ->disableOriginalConstructor()
            ->setMethods(['bindBannersToSalesRule'])
            ->getMock();
        $banner->expects($this->once())->method('bindBannersToSalesRule')->with(1, $banners)->will(
            $this->returnSelf()
        );
        $this->event->expects($this->any())->method('getRule')->will($this->returnValue($this->http));
        $this->http->expects($this->once())->method('getId')->will($this->returnValue(1));
        $this->http->expects($this->any())->method('getRelatedBanners')->will(
            $this->returnValue($banners)
        );
        $this->eventObserver = $this->getMockBuilder(\Magento\Framework\Event\Observer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->eventObserver->expects($this->any())->method('getEvent')->will($this->returnValue($this->event));
        $this->bannerFactory->expects($this->once())->method('create')->will($this->returnValue($banner));
        $this->assertInstanceOf(
            \Magento\Banner\Observer\BindRelatedBannersToSalesRule::class,
            $this->bindRelatedBannersToSalesRuleObserver->execute($this->eventObserver)
        );
    }

    public function bindRelatedBannersDataProvider()
    {
        return [
            [
                [],
            ],
            [
                'banner1',
                'banner2'
            ]
        ];
    }
}
