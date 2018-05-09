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
class ListBlock extends \Magento\Catalog\Block\Product\AbstractProduct
{
    
	
	protected $_moduleManager;
	protected $_objectManager;
	protected $scopeConfig;
	/**
	 * @param Context $context
	 * @param array $data
	 */
	public function __construct(
				\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
				\Magento\Catalog\Block\Product\Context $context, 
				array $data = [],
				\Magento\Framework\ObjectManagerInterface $objectManager,
				\Magento\Framework\Module\Manager $moduleManager)
	{
		$this->scopeConfig = $scopeConfig;
		$this->_moduleManager = $moduleManager;
		$this->_objectManager = $objectManager;
		parent::__construct($context, $data);
		$this->setTabTitle();
	}
	
	/**
	 * Set Tab Title
	 */
	public function setTabTitle()
	{
		$title = __('More Seller');
		$this->setTitle($title);
	}
	
	/**
	 * Set Product Collection
	 * @return void
	 */
	 public function _construct()
    {
        parent::_construct();
        if(!$this->_objectManager->get('Ced\CsMultiSeller\Helper\Data')->isEnabled())
        	return;

        $product_collection = array();
        $products=array();
        if($this->_objectManager->get('Magento\Framework\Registry')->registry('product')!=null){
	        $parentId = $this->_objectManager->get('Magento\Framework\Registry')->registry('product')->getEntityId();    
	        $collection = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')
	        			->getCollection()->addFieldToFilter('parent_id', array('eq'=>$parentId))
	        			->addFieldToFilter('is_multiseller', array('eq'=>1));
	        foreach ($collection as $row){
	        	array_push($products,$row->getProductId());
	        }
	        if(count($products)>0){
		        $product_collection = $this->_objectManager->get('Magento\Catalog\Model\ResourceModel\Product\Collection')
								        ->addAttributeToSelect('*')
								        ->addAttributeToFilter('entity_id', array('in'=>$products))
								        ->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
		        if ($this->_moduleManager->isEnabled('Magento_CatalogInventory')) {
		        	$product_collection->joinField('qty',
		        			'cataloginventory_stock_item',
		        			'qty',
		        			'product_id=entity_id',
		        			'{{table}}.stock_id=1',
		        			'left');
		        	$product_collection->joinField('is_in_stock',
		        			'cataloginventory_stock_item',
		        			'is_in_stock',
		        			'product_id=entity_id',
		        			'{{table}}.stock_id=1',
		        			'left');
		        }
		        $product_collection->joinField('check_status','ced_csmarketplace_vendor_products', 'check_status','product_id=entity_id',null,'left');
		        $product_collection->joinField('vendor_id','ced_csmarketplace_vendor_products', 'vendor_id','product_id=entity_id',null,'left');
		        $product_collection->addAttributeToFilter('qty', array('gt'=>0))
		       						->addAttributeToFilter('is_in_stock', array('eq'=>1));
		        $showMinPrice = $this->scopeConfig->getValue('ced_csmarketplace/ced_csmultiseller/minprice',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
		        if($showMinPrice){ //die('----');
		        	$product_collection->addAttributeToSort('price', 'ASC');
		        	//print_r($product_collection->getData());die('here');
				}else{
					$product_collection->addAttributeToSort('price', 'DESC');
				}
	        }
        } 
        //echo $product_collection->getSelect();die;
        $this->setProductCollection($product_collection);
        $minPrice=0;
         if($this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStoreConfig('ced_csmultiseller/general/minprice',$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(null)->getId())){
	        $i=0;
	       	if($product_collection){
		        foreach($product_collection as $row){
		        	if($i==0){
		        		$minPrice = $row->getPrice();
		        		$i++;
		        	}
		        	else if($row->getPrice()<$minPrice){	
		        		$minPrice = $row->getPrice();
		        		$i++;
			        }
			    }
	       	}
        }
        $this->setMinPrice($minPrice); 
    }
    
    /**
     * prepare list layout
     *
     */  
    protected function _prepareLayout()
    {//die('hcujy');
    	parent::_prepareLayout();
   /*  	if(!$this->_objectManager->get('Ced\CsMultiSeller\Helper\Data')->isEnabled())
    		return;
    	if($this->getProductCollection()){
	    	$pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
	    	$pager->setAvailableLimit(array(10=>10,20=>20,30=>30,'all'=>'all'));
	    	$pager->setCollection($this->getProductCollection());
	    	$this->setChild('pager', $pager);
    	}
    	return $this; */
    }
    
    /**
     * return the pager
     *
     */
   /*  public function getPagerHtml() {
    	if(count($this->getProductCollection())>10)
    		return $this->getChildHtml('pager');
    } */
}
