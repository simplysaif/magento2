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
 * @package     Ced_CsDeal
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsDeal\Model;
use Magento\Framework\Api\AttributeValueFactory;
class Deal extends \Magento\Framework\Model\AbstractModel
{
	const STATUS_APPROVED          = '1';
	const STATUS_NOT_APPROVED      = '0';
	const STATUS_PENDING           = '2';
	
	protected static $_states;
	protected static $_statuses;
	protected $_codeSeparator = '-';
	protected $_objectManager;
	
	public function __construct(
			\Magento\Framework\Model\Context $context,
			\Magento\Framework\Registry $registry,
			\Magento\Framework\ObjectManagerInterface $objectInterface,
			array $data = []
	) {
	
		$this->_objectManager = $objectInterface;
	    parent::__construct($context,$registry);
	
	}
	
    protected function _construct()
    {
        $this->_init('Ced\CsDeal\Model\ResourceModel\Deal');
    }

    public function getMassActionArray() {
    	return array (
    			'-1'  => __(''),
    			self::STATUS_APPROVED  => __('Approved'),
    			self::STATUS_NOT_APPROVED => __('Disapproved') ,
    			self::STATUS_PENDING => __('Approval Pending')
    	);
    }
    
    public function getVendorDealProductIds($id) {
    	return $this->getResource()->getVendorDealProductIds($id);
    }
    
    public function changeVdealStatus($dealId,$checkstatus){
    	$errors=array();
    		if($dealId){
    			$row=$this->_objectManager->get('Ced\CsDeal\Model\Deal')->load($dealId);
                //print_r($row->getData());die;
    			if($row->getAdminStatus() != $checkstatus){
    				switch ($checkstatus){
    					case \Ced\CsDeal\Model\Deal::STATUS_APPROVED:
    						$row->setAdminStatus(\Ced\CsDeal\Model\Deal::STATUS_APPROVED);
    						$errors['success']=1;
                            $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($row->getProductId());
                            $product->setSpecialPrice($row->getDealPrice());
                            $product->setSpecialFromDate($row->getStartDate());
                            $product->setSpecialFromDateIsFormated(true);
                            $product->setSpecialToDate($row->getEndDate());
                            $product->setSpecialToDateIsFormated(true);
                            $product->save();
    						break;
    									
    					case \Ced\CsDeal\Model\Deal::STATUS_NOT_APPROVED:
    						$row->setAdminStatus(\Ced\CsDeal\Model\Deal::STATUS_NOT_APPROVED);
    						$errors['success']=1;
                            $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($row->getProductId());
                            $product->setSpecialPrice(null);
                            $product->getResource()->saveAttribute($product, 'special_price');
                            $product->save();
    						break;
    				}
    				$row->save();
    			}
    			else
    				$errors['success']=1;
    		}else{
    			$errors['error']=1;
    		}
    	return $errors;
    }
}
 
?>