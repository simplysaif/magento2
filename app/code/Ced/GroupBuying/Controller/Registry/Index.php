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
 * @category  Ced
 * @package   Ced_GroupBuying
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 
 
namespace Ced\GroupBuying\Controller\Registry;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * Default vendor dashboard page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    protected $resultPageFactory;

    protected $_request;
    protected $_response;
    protected $_objectManager;
    protected $_coreRegistry = null;
    
     public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->_resultPageFactory  = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    public function execute()
    {    
        // $prod_model = $this->_objectManager->create('Magento\Catalog\Model\Product');
        //$original_product = $prod_model->load(1);
       // print_r($original_product->getStoreId());
       // print_r($original_product->getWebsiteIds());
        $params = $this->getRequest()->getParams();
        
        if (isset($params['product'])) {
            $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($params['product']);
                                    
            if (isset($params['qty'])) {
                $params['qty'] = 1;
            }
            
            if (isset($params['related_product'])) {
                $params['related_product'] = '';
            } 
            $price = 0;
            
            if(isset($params['product']) && $product->getTypeId() != 'grouped'){

                $cart = $this->_objectManager->create('Magento\Checkout\Model\Cart');
                $cart_count = count($cart->getQuote()->getAllVisibleItems());
                
                foreach ($cart->getQuote()->getAllItems() as $index => $item) {
                
                    if($item->getProduct()->getId() == $params['product']){
                        try{
                            $cart->getQuote()->removeItem($item->getId())->save();
                        }
                        catch(\Exception $e){
                            echo '1 - '.$e->getMessage();die;
                        }
                    }               
                }
                
                $cart->addProduct($product, $params)->save();

                foreach ($cart->getQuote()->getAllItems() as $key => $items) {
                
                    if($items->getProduct()->getId() == $params['product']){
                        $price = $items->getBaseRowTotalInclTax();      
                        try{
                            $cart->getQuote()->removeItem($items->getId())->save();                     
                        }
                        catch(\Exception $e){
                            echo '2 - '.$e->getMessage();die;
                        }
                    }
                }
                if($cart_count < 1){
                    $this->_objectManager->get('Magento\Checkout\Model\Session')->clearQuote();
                    $cart->truncate();
                    $cart->save();
                }
                
                $this->_coreRegistry->register('gift_price', $price);
            }
            
            if($product->getTypeId() == 'grouped'){
                
                foreach($params['super_group'] as $key => $value){
                
                    $child_prod = $this->_objectManager->create('Magento\Catalog\Model\Product')
                                    ->setStoreId($this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(null)->getId())
                                    ->load($key);
                                    
                    $child_price = $child_prod->getFinalPrice() * $value;
                    $price += $child_price;
                }
                $this->_coreRegistry->register('gift_price', $price);           
            }
        }
        
        $resultPage = $this->_resultPageFactory->create();
        return $resultPage;
        
    }
}
