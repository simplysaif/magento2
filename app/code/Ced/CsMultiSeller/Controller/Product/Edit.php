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
 
namespace Ced\CsMultiSeller\Controller\Product;

class Edit extends \Ced\CsMarketplace\Controller\Vendor
{	

	protected $urlBuilder;
	 
    protected $session;
    
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlModel;
    
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_resultPageFactory;
     
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;
    
    /**
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     * @param UrlFactory $urlFactory
     * @param ModuleManager $moduleManager
     * @param UrlBuilder $urlBuilder
     */
    public function __construct(
    		\Magento\Framework\App\Action\Context $context,
    		\Magento\Customer\Model\Session $customerSession,
    		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
    		\Magento\Framework\UrlFactory $urlFactory,
    		\Magento\Framework\Module\Manager $moduleManager,
    		 \Magento\Backend\Model\Url $urlBuilder
    ) {
    	$this->session = $customerSession;
    	$this->resultPageFactory = $resultPageFactory;
    	$this->urlModel = $urlFactory;
    	$this->_resultPageFactory  = $resultPageFactory;
    	$this->_moduleManager = $moduleManager;
    	$this->urlBuilder = $urlBuilder;
    	parent::__construct($context,$customerSession,$resultPageFactory,$urlFactory,$moduleManager);
    	//     $this->resultJsonFactory = $this->_objectManager->create('Magento\Framework\Controller\Result\JsonFactory');
    }
	
    /*  public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {	
    
    	
    	if($this->_objectManager->get('Magento\Framework\Registry')->registry('ced_csmarketplace_current_store'))
    
    		$this->_objectManager->get('Magento\Framework\Registry')->unRegister('ced_csmarketplace_current_store');
    
    	if($this->_objectManager->get('Magento\Framework\Registry')->registry('ced_csmarketplace_current_website'))
    
    		$this->_objectManager->get('Magento\Framework\Registry')->unRegister('ced_csmarketplace_current_website');
    
    	$this->_objectManager->get('Magento\Framework\Registry')->register('ced_csmarketplace_current_store',$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId());
    
    	$this->_objectManager->get('Magento\Framework\Registry')->register('ced_csmarketplace_current_website',$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getWebsiteId());
    	parent::dispatch($request);
    
    }

 */
	/**
	 * Edit product Form
	 */
    public function execute()
    {
		
		 if(!$this->_getSession()->getVendorId())        
        	return;   

    	if(!$this->_objectManager->get('Ced\CsMultiSeller\Helper\Data')->isEnabled()) {        
        	$this->_redirect('\Ced\CsMarketplace\Controller\Vendor\Index');        
        	return;        
        }		
     	$id = $this->getRequest()->getParam('id');
     	$collection = $this->_objectManager->get('\Magento\Catalog\Model\Product')->load($id);

     	$this->_objectManager->get('\Magento\Framework\Registry')->register('current_product_edit', $collection);
     
		$vendorId = $this->_getSession()->getVendorId();
		$vendorProduct=0;
		if($id&&$vendorId){
			$vendorProduct = $this->_objectManager->get('Ced\CsMultiSeller\Model\Multisell')->isAssociatedProduct($vendorId,$id);
		}

		if(!$vendorProduct){
			$this->_redirect ('\Ced\CsMultiSeller\Controller\Product\Index');   
			return;
		}

		$this->mode = \Ced\CsMarketplace\Model\Vproducts::EDIT_PRODUCT_MODE;
		$product = $this->_initProduct();

		$resultPage = $this->resultPageFactory->create();

		//$this->_initLayoutMessages ( 'customer/session' );

		/* $navigationBlock = $this->getLayout()->getBlock('csmarketplace_vendor_navigation');

		if ($navigationBlock) {

			$navigationBlock->setActive('csmultiseller/product/index');

		} */
		//$resultPage->getLayout()->getBlock('store_switcher');
		/* if ($switchBlock = $resultPage->getLayout()->getBlock('store_switcher')) {
			$switchBlock->setDefaultStoreName($this->__('Default Values'))
			->setWebsiteIds($product->getWebsiteIds())
			->setSwitchUrl(
					$this->getUrl('', array('_current'=>true, 'active_tab'=>null, 'tab' => null, 'store'=>null))
			);
		} */
		if (!$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->isSingleStoreMode()
				&&
				($switchBlock = $resultPage->getLayout()->getBlock('store_switcher'))
		) {
			$switchBlock->setDefaultStoreName(__('Default Values'))
			->setWebsiteIds($product->getWebsiteIds())
			->setSwitchUrl(
					$this->urlBuilder->getUrl(
							'csmultiseller/*/*',
							['_current' => true, 'active_tab' => null, 'tab' => null, 'store' => null]
					)
			);
		}
		$resultPage->getConfig()->getTitle()->set(__('Edit Product'));
		
		 return $resultPage;       

	}
	
	/**
	 * Initialize product from request parameters
	 * @return Magento\Catalog\Model\Product
	 */
	
	protected function _initProduct()
	{
		if(!$this->_getSession()->getVendorId())
			return;
	
		$productId  = (int) $this->getRequest()->getParam('id');
		$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->setCurrentStore(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
	
		$product = $this->_objectManager->get('Magento\Catalog\Model\Product');
		 
		if (!$productId) {
			$product->setStoreId(0);
			if ($setId = (int) $this->getRequest()->getParam('set')) {
				$product->setAttributeSetId($setId);
			}
	
			if ($typeId = $this->getRequest()->getParam('type')) {
				$product->setTypeId($typeId);
			}
		}
	
		$product->setData('_edit_mode', true);
		if ($productId) {
			$storeId = 0;
			if($this->mode==\Ced\CsMarketplace\Model\Vproducts::EDIT_PRODUCT_MODE && $this->getRequest()->getParam('store')){
				$websiteId = $this->_objectManager->get('Magento\Store\Model\Store')->load($this->getRequest()->getParam('store'))->getWebsiteId();
				if($websiteId){
					if(in_array($websiteId,$this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getAllowedWebsiteIds())){
						$storeId=$this->getRequest()->getParam('store');
					}
				}
			}
	
			try {
				$product->setStoreId($storeId)->load($productId);
			} catch (Exception $e) {
				$product->setTypeId(\Magento\Catalog\Model\Product\Type::DEFAULT_TYPE);
				//Mage::logException($e);
			}
	
		}
	
		$attributes = $this->getRequest()->getParam('attributes');
		if ($attributes && $product->isConfigurable() &&
				(!$productId || !$product->getTypeInstance()->getUsedProductAttributeIds())) {
					$product->getTypeInstance()->setUsedProductAttributeIds(
							explode(",", base64_decode(urldecode($attributes)))
					);
				}
				 
				// Required attributes of simple product for configurable creation
	
				if ($this->getRequest()->getParam('popup')
						&& $requiredAttributes = $this->getRequest()->getParam('required')) {
							$requiredAttributes = explode(",", $requiredAttributes);
							foreach ($product->getAttributes() as $attribute) {
								if (in_array($attribute->getId(), $requiredAttributes)) {
									$attribute->setIsRequired(1);
								}
							}
						}
						 
						if ($this->getRequest()->getParam('popup')
								&& $this->getRequest()->getParam('product')
								&& !is_array($this->getRequest()->getParam('product'))
								&& $this->getRequest()->getParam('id', false) === false) {
									$configProduct = $this->_objectManager->get('Magento\Catalog\Model\ResourceModel\Product\Collection')
									->setStoreId(0)
									->load($this->getRequest()->getParam('product'))
									->setTypeId($this->getRequest()->getParam('type'));
	
	
									/* @var $configProduct Mage_Catalog_Model_Product */
	
									$data = array();
									foreach ($configProduct->getTypeInstance()->getEditableAttributes() as $attribute) {
										 
										/* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
	
										if(!$attribute->getIsUnique()
												&& $attribute->getFrontend()->getInputType()!='gallery'
												&& $attribute->getAttributeCode() != 'required_options'
												&& $attribute->getAttributeCode() != 'has_options'
												&& $attribute->getAttributeCode() != $configProduct->getIdFieldName()) {
													$data[$attribute->getAttributeCode()] = $configProduct->getData($attribute->getAttributeCode());
												}
									}
									$product->addData($data)
									->setWebsiteIds($configProduct->getWebsiteIds());
								}
									
								$this->_objectManager->get('\Magento\Framework\Registry')->register('product', $product);
								$this->_objectManager->get('\Magento\Framework\Registry')->register('current_product', $product);
								//	$this->setCurrentStore();
								return $product;
	}
}
