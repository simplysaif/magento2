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
/**
 * Producty Edit block
 *
 * @category   Ced
 * @package    Ced_CsMultiSeller
 * @author 	   CedCommerce Core Team <connect@cedcommerce.com>
 */
namespace Ced\CsMultiSeller\Block;
class Edit extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{

	/*
	* @var PageFactory
	*/
	protected $resultPageFactory;
	
	/**
	 * @var \Magento\Framework\ObjectManagerInterface
	 */
	protected $_objectManager;
	
	protected $urlModel;
	
	protected $session;
	/**
	 * @param Context $context
	 * @param Session $customerSession
	 * @param PageFactory $resultPageFactory
	 */
	public function __construct(
			\Magento\Framework\View\Element\Template\Context $context,
			\Ced\CsMarketplace\Model\Session $customerSession,
			\Magento\Framework\ObjectManagerInterface $objectManager,
			\Magento\Framework\UrlFactory $urlFactory
	){
		
		$this->session = $customerSession;
		$this->urlModel = $urlFactory;
		$this->_objectManager = $objectManager;
		parent::__construct($context,$customerSession,$objectManager,$urlFactory);
	}
	
	
	/**
	 * Set Check Status 
	 */
	public function _construct(){
		parent::_construct();
		
		$this->setTitle(__('Edit Product'));
		$vendorId=$this->getVendorId();
		$id=$this->getRequest()->getParam('id');
		$status=0;
		if($id){
			$vproductsCollection = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getVendorProducts('',$vendorId,$id,1);
			$status=$vproductsCollection->getFirstItem()->getCheckStatus();
		}
		$this->setCheckStatus($status);
		
	}

	/**
	 * Return Delete Url
	 */ 
	public function getDeleteUrl($product)
	{
		return $this->getUrl('*/*/delete', array('id' => $product->getId()));
	}
	/**
	 * Return Back Url
	 */
	public function getBackUrl()
	{
		return $this->getUrl('*/*/index');
	}
	/**
	 * Return Save Url
	 */
    public function getSaveUrl()
    {
    	return $this->getUrl('*/*/save', array('_current'=>true, 'back'=>null));
    }
    
}
