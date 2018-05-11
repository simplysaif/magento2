<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdminGws\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Store\Model\Store;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Unit test for \Magento\AdminGws\Model\Models.
 */
class ModelsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\AdminGws\Model\Models
     */
    private $model;

    /**
     * @var \Magento\AdminGws\Model\Role|MockObject
     */
    private $role;

    /**
     * @var ObjectManagerHelper
     */
    private $objectManagerHelper;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->role = $this->getMockBuilder(\Magento\AdminGws\Model\Role::class)
            ->disableOriginalConstructor()
            ->setMethods(['hasStoreAccess', 'hasExclusiveStoreAccess', 'getStoreIds', 'getDisallowedStoreIds'])
            ->getMock();
        $this->model = $this->objectManagerHelper->getObject(
            \Magento\AdminGws\Model\Models::class,
            [
                'role' => $this->role,
            ]
        );
    }

    /**
     * @return void
     */
    public function testCmsPageSaveBefore()
    {
        $pageId = 1;
        $storeIds = [Store::DEFAULT_STORE_ID];

        /** @var \Magento\Cms\Model\ResourceModel\Page|MockObject $cmsPageResource */
        $cmsPageResource = $this->getMockBuilder(\Magento\Cms\Model\ResourceModel\Page::class)
            ->disableOriginalConstructor()
            ->setMethods(['lookupStoreIds'])
            ->getMock();
        $cmsPageResource->expects($this->once())->method('lookupStoreIds')
            ->with($pageId)
            ->willReturn($storeIds);

        $this->role->expects($this->once())->method('hasStoreAccess')
            ->with($storeIds)
            ->willReturn(true);
        $this->role->expects($this->once())->method('hasExclusiveStoreAccess')
            ->with($storeIds)
            ->willReturn(true);
        $this->role->expects($this->atLeastOnce())->method('getStoreIds')->willReturn($storeIds);
        $this->role->expects($this->once())->method('getDisallowedStoreIds')->willReturn([]);

        /** @var \Magento\Cms\Model\Page|MockObject $cmsPage */
        $cmsPage = $this->getMockBuilder(\Magento\Cms\Model\Page::class)
            ->disableOriginalConstructor()
            ->setMethods(['getResource', 'getId', 'getStoreId', 'setStoreId'])
            ->getMock();

        $cmsPage->expects($this->once())->method('getResource')->willReturn($cmsPageResource);
        $cmsPage->expects($this->exactly(2))->method('getId')->willReturn($pageId);
        $cmsPage->expects($this->once())->method('getStoreId')->willReturn($storeIds);
        $cmsPage->expects($this->once())->method('setStoreId')->with($storeIds);

        $this->model->cmsPageSaveBefore($cmsPage);
    }
}
