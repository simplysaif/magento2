<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsMembership
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMembership\Cron;
class CheckExpire {
 
    protected $_logger;

    protected $_objectManager;
 
    public function __construct(
    	\Psr\Log\LoggerInterface $logger,
    	\Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_logger = $logger;
        $this->_objectManager = $objectManager;
    }
 
    public function execute() {
    	
        $cur_time   =  date('Y-m-d');
        $collection = $this->_objectManager->create('Ced\CsMembership\Model\Subscription')
                                            ->getCollection()
                                            ->addFieldToFilter('status',\Ced\CsMembership\Model\Status::STATUS_RUNNING);
        foreach ($collection as $subcription) {
           $model = $this->_objectManager->create('Ced\CsMembership\Model\Subscription');
           $end_date = date_create($subcription->getData('end_date'));
           $curr_date= date_create($cur_time);
           $interval = date_diff($curr_date,$end_date,false);
           if ($interval->format('%a') <= 0) {
                $model->load($subcription->getId());
                $model->setStatus(\Ced\CsMembership\Model\Status::STATUS_EXPIRED);
                $model->save();
                $qtyModel = $this->_objectManager->create('Ced\CsMembership\Model\Membership')->load($subcription->getSubcriptionId());
                $prvqty = $qtyModel->getQty();
                $newqty = $prvqty + 1;
                $qtyModel->setQty($newqty);
                $qtyModel->save();
                $stockItem = $this->_objectManager->create('Magento\CatalogInventory\Model\ResourceModel\Stock\Item')->loadByProductId($subcription->getProductId());
                $stockItem->setData('qty', $newqty);
                try{    
                    $this->_objectManager->create('Ced\CsMembership\Helper\Data')->sendExpireMail($subcription);
                }catch(\Exception $e){
                    $this->_logger->addDebug($e->getMessage());
                    //Mage::log($e->getMessege(),'1','mycroncheck.log',TRUE);
                }
            } else {
                continue;
            }
        }
    }
}