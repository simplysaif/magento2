<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ced\GroupBuying\Pricing\Render;

use Magento\Catalog\Pricing\Price;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Pricing\Render\PriceBox as BasePriceBox;
use Magento\Msrp\Pricing\Price\MsrpPrice;

/**
 * Class for final_price rendering
 *
 * @method bool getUseLinkForAsLowAs()
 * @method bool getDisplayMinimalPrice()
 */
class FinalPriceBox extends \Magento\Catalog\Pricing\Render\FinalPriceBox
{
    /**
     * @return string
     */
    protected function _toHtml()
    {
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $loggedin = $_objectManager->create('Magento\Customer\Model\Session')->isLoggedIn();
        $forwishlistHtml='<div class="popup"></div><a href="#"  onclick="callFancyBox(this);return false;" value=""></a>';
        $controller = array('category','result','index','catalog_category');
        if(in_array($this->getRequest()->getControllerName(),$controller)){ 
            $style ="font-weight: bold; color:F249D0;";
            $product = $_objectManager->get('Magento\Catalog\Model\Product')->load($this->getSaleableItem()->getId()); 

             if($product->getHasOptions() || $product->getTypeId() == 'grouped'){
                $url = $this->getUrl('groupgift/product/view',array('id'=>$product->getId()));
            }           
            else{
                $url = $this->getUrl('groupbuying/registry/index',array('id'=>$product->getId()));
            }

            if($product->getGroupBuy()!=1){
                
            $forwishlistHtml='<div class="popup"></div><a href="#" style="'.$style.'" onclick="callFancyBox(this);return false;" value="'.$url.'"></a>';
             }
             
            
             if(($product->getGroupBuy()==1)&&($loggedin))
               {
                 $forwishlistHtml='<div class="popup"></div><a href="#" style="'.$style.'" onclick="callFancyBox(this);return false;" value="'.$url.'">Group Buying</a>';
                 return parent::_toHtml().$forwishlistHtml;  
               } 
            
            return parent::_toHtml().$forwishlistHtml;    
       
           }



    }
}
