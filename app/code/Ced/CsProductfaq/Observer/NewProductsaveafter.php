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
 * @category Ced
 * @package Ced_CsProductfaq
 * @author CedCommerce Core Team
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsProductfaq\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class NewProductsaveafter implements ObserverInterface
{
    
    protected $request;
    protected $catalogSession;
    protected $_objectManager;
    
    public function __construct(RequestInterface $request,\Magento\Framework\ObjectManagerInterface $ob,\Magento\Framework\Stdlib\DateTime\DateTime $date,\Magento\Catalog\Model\Session $catalogSession)
    {
        $this->catalogSession = $catalogSession;
        $this->request = $request;
        $this->_objectManager = $ob;
        $this->date = $date;
    }
    /**
     * Save Faq from product edit page
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
       
    	
        $product = $observer->getProduct(); 
        $productid = $product->getId();

        if($this->catalogSession->getFaqData() != NULL)
        {
        $data=$this->catalogSession->getFaqData()->toArray();
        $title = $data['title'];
        $faqs = array_values($title);
        $description =  $data['description'];
        $description = array_values($description);
        $date = $this->date->gmtDate();
	      
	        
	        
	        $productCollection = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')->load($productid,'product_id')->getData();
	        
	        if(count($productCollection)> 0)
	         {
	            $vendor_id = $productCollection['vendor_id'] ;
	             
	            $vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->load($vendor_id);
	            $Vname = $vendor->getName();
	        
	           for($i=0;$i<=count($faqs)-1;$i++)
            {
                $titles= $faqs[$i];
                $descriptions= $description[$i];
                $visible='1';
                try
                {
                if($titles) {
                
                $model= $this->_objectManager->create('Ced\Productfaq\Model\Productfaq');
                $model->setData('product_id', $productid)
                    ->setData('title', $titles)
                    ->setData('is_active', $visible)
                                ->setData('description', $descriptions)
                                ->setData('posted_by',"Vendor -".$Vname)
                                ->setData('publish_date', $date)
                                ->setData('vendor_id', $vendor_id);
                                $model->save();
                  }
                }
                   catch (Exception $e)
                {
                        
                }
             }
            
	        }
	  
    } 
    }  
}