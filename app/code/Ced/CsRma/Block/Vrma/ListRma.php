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
 * @package     Ced_CsRma
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 
namespace Ced\CsRma\Block\Vrma;

use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\RequestInterface;

class ListRma extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{
	protected $_filterCollection;
	
	public $_vendorUrl;

	protected $urlModel;
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $_objectManager;
	
	/**
	 * Set the Vendor object and Vendor Id in customer session
	 */
    public function __construct(
        Context $context,
		Session $customerSession,
		\Ced\CsMarketplace\Model\Url $vendorUrl,
		UrlFactory $urlFactory,
		\Magento\Framework\ObjectManagerInterface $objectManager
    ) {
		parent::__construct($context, $customerSession, $objectManager, $urlFactory);
		$this->_vendorUrl = $vendorUrl;
		$this->urlModel = $urlFactory;
		$this->_objectManager = $objectManager;

		$vendorId = $this->getVendorId();
		$requestCollection = $this->_objectManager->get('Ced\CsRma\Model\Request')->getCollection()
				->addFieldToFilter('vendor_id',$vendorId);
		$filterCollection = $this->filterRma($requestCollection);
		$this->setVrma($requestCollection);
		
	}
	

	public function filterRma($requestCollection)
	{
		$params = $this->_objectManager->get('Ced\CsMarketplace\Model\Session')->getData('rma_filter');
		if(is_array($params) && count($params)>0) {
			foreach($params as $field=>$value) {
				if(is_array($value)) {

					if(isset($value['from']) && urldecode($value['from'])!="") {
						$from = urldecode($value['from']);
						if($field =='updated_at'){
							$from = date("Y-m-d 00:00:00",strtotime($from));
						} 
						$requestCollection->addFieldToFilter($field, array('gteq'=>$from));
					}
					if(isset($value['to'])  && urldecode($value['to'])!=""){

						$to = urldecode($value['to']);
						
						if($field=='updated_at'){
							$to = date("Y-m-d 59:59:59",strtotime($to));
						}
						$requestCollection->addFieldToFilter($field, array('lteq'=>$to));
					}
				} else if(urldecode($value)!=""){
					$requestCollection->addFieldToFilter($field, array("like"=>'%'.urldecode($value).'%'));
				}
		
			}
		}
		return $requestCollection;
	}
	
	
	/**
	 * prepare list layout
	 *
	 */
	protected function _prepareLayout() 
	{
		parent::_prepareLayout();
		$pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager', 'custom.pager');
		$pager->setAvailableLimit(array(5=>5,10=>10,20=>20,'all'=>'all'));
		$pager->setCollection($this->getVrma());
		$this->setChild('pager', $pager);
		return $this;
	}
	/**
	 * return the pager
	 *
	 */
	public function getPagerHtml() 
	{
		return $this->getChildHtml('pager');
	}
	
	/**
	 * return Back Url
	 *
	 */
	public function getBackUrl()
	{
		return $this->getUrl('*/*/index',array('_secure'=>true,'_nosid'=>true));
	}
	 /**
     * Return order view link
     *
     * @param string $order
     * @return String
     */
	public function getEditUrl($rma)
	{
		return $this->getUrl('*/*/edit', array('rma_id' =>$rma->getRmaRequestId(),'_secure'=>true,'_nosid'=>true));
	}
	
	public function getStoreValue($storeId)
	{
		$storeModel = $this->_objectManager->get('Magento\Store\Model\StoreRepository')->getById($storeId);
		return $storeModel->getName();

	}	
}
