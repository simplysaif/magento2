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
 * @package     Ced_CsProductReview
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */


namespace Ced\CsProductReview\Model;

class Review extends \Magento\Framework\Model\AbstractModel
{
	/**
	 * Customer Session Model
	 * @var \Magento\Customer\Model\Session $session
	 */
	protected $_customerSession;
	
	protected $_vproducts=[];
	
	/**
	 * 
	 * @param \Magento\Framework\Model\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
	 * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
	 * @param \Magento\Customer\Model\Session $sesion
	 * @param array $data
	 */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
    	\Magento\Customer\Model\Session $session,
        array $data = []
    ) {
    	$this->_customerSession = $session;
        parent::__construct($context, $registry, null, null, $data);
    }
    /**
     * 
     * @see \Magento\Framework\Model\AbstractModel::_construct()
     */
    public function _construct()
    {
        $this->_init('Ced\CsProductReview\Model\ResourceModel\Review');
    }

    /**
     * 
     * @param number $vendorId
     * @return multitype:
     */
   public function getVendorProductIds($vendorId=0){
   
   		$vendorId=$vendorId?$vendorId:$this->_customerSession->getVendorId();
   		$vcollection = \Magento\Framework\App\ObjectManager::getInstance()->create('Ced\CsMarketplace\Model\Vproducts')->getVendorProducts('',$vendorId,0);
   		return $vcollection->getColumnValues('product_id');
   }
}
