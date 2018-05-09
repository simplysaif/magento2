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
 * @package     Ced_CsMultistepregistration
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsMultistepreg\Block\Multistep;
use \Magento\Framework\View\Element\Template\Context;
use \Magento\Framework\ObjectManagerInterface;
use \Magento\Store\Model\StoreManagerInterface;
class Steps extends \Magento\Framework\View\Element\Template
{
	public $vendorData; 
	public $request;
	private $objectManager;
	private $storeManager;
	public function __construct(
			Context $context,\Magento\Framework\App\Request\Http $request,
			ObjectManagerInterface $objectmanager
	) {
		$this->setTemplate('csmultistepregistration/multisteps/steps.phtml');
		parent::__construct($context);
		$this->request = $request;
		$this->objectManager = $objectmanager;
		$id = $this->request->get('id');
		$this->objectManager = $objectmanager;
		$stepsObject = $this->objectManager->create('Ced\CsMarketplace\Model\Vendor')->load($id);
		$this->vendorData = $stepsObject->getData();
	}
	
	public function getcollection(){
		$stepsCollection = $this->objectManager->create('Ced\CsMultistepreg\Model\Steps')->getCollection();
		return $stepsCollection;
	}
	
	public function getStepattributes($step){
		if ($step) {
			$collection = $this->objectManager->create('Ced\CsMarketplace\Model\Vendor\Attribute')
								->setStoreId($this->_storeManager->getStore()->getId())
								->getCollection()->addFieldToFilter('registration_step_no',$step)
								->setOrder('sort_order','ASC');
			return $collection;
		}
	}
}
