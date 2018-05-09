<?php 
/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * https://cedcommerce.com/license-agreement.txt
  *
  * @category    Ced
  * @package     Ced_CsMultiSeller
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
  * @license      https://cedcommerce.com/license-agreement.txt
  */

namespace Ced\CsMultiSeller\Observer; 
use Magento\Framework\Event\Observer;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Event\ObserverInterface;

use Magento\Config\Controller\Adminhtml\System\Config\Save;
Class HideProducts implements ObserverInterface
{

	/**
	 * @var \Magento\Framework\ObjectManagerInterface
	 */
	protected $_objectManager;
	protected $request;
	protected $response;
	
	/**
	 * @return void
	 */
	public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, RequestInterface $request, ResponseInterface $response)
	{	
		$this->_objectManager = $objectManager;
		$this->request = $request;
		$this->response = $response;
	}
	
	/**
	 * Filter Catalog List Collection 
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{//die('die here');

		$productCollection = $observer->getEvent()->getCollection();
		//print_r($productCollection->getData());die;
		if ($productCollection) {
			$products = array();
			$collection = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getCollection()->addFieldToFilter('is_multiseller',array('eq' =>1));
			foreach ($collection as $row) {
				array_push($products,$row->getProductId());
			}
			$proArray= array();
			foreach ($productCollection as $product) {
				if(in_array($product->getId(),$products)) {
					$proArray [] = $product->getId ();
				}
			}
			if(count($proArray)){
				foreach ($proArray as $key) {
					$productCollection->removeItemByKey($key);
				}
			}
			$observer->getEvent()->setCollection($productCollection);
		}
		
		/* $objectManager = $this->_objectManager;
		if($objectManager->get('Magento\Backend\Model\Auth\Session')->isLoggedIn()){		
			$params = $this->request->getPost();
			$params['section'] = 'ced_csmarketplace';
			$params['is_csgroup'] = 2;
			$response=json_decode (json_encode($params),1);
			
			$this->request->setParams($response);
			$req = $objectManager->get('Magento\Framework\App\Action\Context');
			$configuration = $objectManager->get('Magento\Config\Controller\Adminhtml\System\Config\Save',$this->request, $this->response);
			//($configuration);die;
			$configuration->dispatch($this->request); */
		}
	
	}
