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
  * @package     Ced_Barcode
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */ 
namespace Ced\Barcode\Model;

use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;
use Zend_Barcode_Object_Ean13;

class Barcode extends  \Magento\Sales\Model\Order\Pdf\AbstractPdf 
{
     /**
     * Return PDF document
     *
     * @param array|Collection $invoices
     * @return \Zend_Pdf
     */
	
	
    public function getPdf($pids = [])
	 {
	    	
	    $model = \Magento\Framework\App\ObjectManager::getInstance();
	 	$request = $model->get('Magento\Framework\App\RequestInterface')->getParams();
	 
	     
	     if(isset($request['id']))
	     {
	     	$counter = 24;
	     }
	    else{
	    	$counter = count($pids);
	    }
	        
	     
    	$pdf = new \Zend_Pdf();
    	$style = new \Zend_Pdf_Style();
    	$this->_setFontBold($style, 10);
    	$page = $pdf->newPage(\Zend_Pdf_Page::SIZE_A4);
    	$pdf->pages[] = $page;
    	
    	$fontPath = $this->_rootDirectory->getAbsolutePath('lib/web/fonts/opensans/regular/opensans-400.ttf');
    	
    	$page->setFont(\Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_HELVETICA), 30);
    	\Zend_Barcode::setBarcodeFont($fontPath);
    	
    	for($i = 0; $i<$counter; $i++)
    	{
    		
    		if(isset($request['id']))
    		{
    			$c = 0;
    		}
    		else
    		{
	    		$c = $i;
	    	}
	    		
	    
	    	
	    	$product = $model->create('Magento\Catalog\Model\Product')->load($pids[$c])->getData();
	    	$sku = $product['sku'];
	    	if($product['type_id'] =="configurable" || $product['type_id'] =="bundle" || $product['type_id'] =="grouped")
	    	{
	    		continue;
	    	}
	    	if(isset( $product['ean']))
	    	{
	    		$trackingNumber = $product['ean'];
	    		$trackingNumber = substr($trackingNumber,0,12);
	    	}
	    	else
	    	{
	    		$trackingNumber = $pids[$c];
	    	}
	    	//$trackingNumber = str_pad($trackingNumber, '12', '0', STR_PAD_LEFT);
	    	//$trackingNumber =15;
	    	//echo $trackingNumber; die("h");
	    	$enable = $model->create('Ced\Barcode\Helper\Data')->isEnable();
	    	if($enable == 0)
	    	{
	    		return;
	    	}
	    	$barcode_option = $model->create('Ced\Barcode\Helper\Data')->getBarcodeOption();
	    	$description_option = $model->create('Ced\Barcode\Helper\Data')->getDescriptionOption();
	    	$encode_option = $model->create('Ced\Barcode\Helper\Data')->getEncodeOption();
	    	
	    	if($i == 0)
	     	{
	     		$p= 0;
	     	}
     		else
     		{
     			$p = 1;
     		}
     		
            $barcodeOptions = array(
     				'text' => $trackingNumber,
     				'barHeight'=> 30,
     				'factor'=>2.2,
     		
     		);
     		
	       if($i>2)
	       {
	     	  $t = $i%3;
	     	  $x = $i/3;
	     	  $x = floor($x);
	     	  $z=1;
	       }
	       else
	       {
	          $t = $i%3;
	     	  $x = $i/3;
	     	  $x = floor($x);
	     	  $x =0;
	     	  $z=0;
	       }
	      
	     		
	     		 
	       $leftOffset = $model->create('Ced\Barcode\Helper\Data')->getConfig('barcode/active11/leftOffset');
	       $topOffset = $model->create('Ced\Barcode\Helper\Data')->getConfig('barcode/active11/topOffset');
	       
	       $rendererOptions = array('leftOffset' => ($t*198+$leftOffset),'topOffset'=>$topOffset+105*$x);
	     		$renderer = \Zend_Barcode::factory(
	                    $encode_option, 'pdf', $barcodeOptions, $rendererOptions
	            )->setResource($pdf)->draw();
	     		
	       $value = $model->create('Ced\Barcode\Helper\Data')->getConfig('barcode/active11/desopt');
	       $value = unserialize($value);
	     
	       foreach ($value as $k=>$val)
	     	{
	           if($val['print']== 1)
	              {
	                 $field = $val['field'];
	                 if($val['field'] == 'image')
	                    {
	                    	$image = '';
	                    	if(isset($product[$field]))
	                    	{
	                    		$image =  $product[$field];
	                    	}
	                       
	                        $imagePath = '/catalog/product' . $image;
	                       
	                        if ($this->_mediaDirectory->isFile($imagePath))
	                         {
	                            $image = \Zend_Pdf_Image::imageWithPath($this->_mediaDirectory->getAbsolutePath($imagePath));
	           					if($i >2)
	           					 {
	                				$t = $i%3;
						            $x = $i/3;
						            $x = floor($x);
	           					 }
								$position = explode(',', $val['position']);
								$page->drawImage($image, $position['0']+$t*(198), $position['1']-$x*105, $position['2']+$p*($t*198), ($position['1']-$x*105)+$position['3']-$position['1']);
	                           
	                        	$model =  \Magento\Framework\App\ObjectManager::getInstance();
	                            $image = $model->create('Ced\Barcode\Helper\Data')->getImgName();
	                            if ($image) 
	                            {
	                                $imagePath = '/ced/barcode/' . $image;
	                              	if ($this->_mediaDirectory->isFile($imagePath))
	                              	 {
	                                    $image = \Zend_Pdf_Image::imageWithPath($this->_mediaDirectory->getAbsolutePath($imagePath));
	                            		$xcordinates = $model->create('Ced\Barcode\Helper\Data')->getConfig('barcode/active11/logowidth');
	                                    $ycordinates = $model->create('Ced\Barcode\Helper\Data')->getConfig('barcode/active11/logolength');
	                                  
	                                    $xcordinates = explode(",",$xcordinates);
	                                    $ycordinates = explode(",",$ycordinates);
	                                    $page->drawImage($image,$xcordinates['0']+$t*(198),$ycordinates['0']-$x*105, $xcordinates['1']+$p*($t*198), ($ycordinates['0']-$x*105)+$ycordinates['1']-$ycordinates['0']);
	                                  
	                                 }
	                            }
	                                
	                            
	                        }
	                       else
	                        {
	                          $model =  \Magento\Framework\App\ObjectManager::getInstance();
	                          $image = $model->create('Ced\Barcode\Helper\Data')->getImgName();
	                          if ($image) 
	                           {
	                                $imagePath = '/ced/barcode/' . $image;
	                                if ($this->_mediaDirectory->isFile($imagePath))
	                                 {
	                                    $image = \Zend_Pdf_Image::imageWithPath($this->_mediaDirectory->getAbsolutePath($imagePath));
	                            
	                                    $xcordinates = $model->create('Ced\Barcode\Helper\Data')->getConfig('barcode/active11/logowidth');
	                                    $ycordinates = $model->create('Ced\Barcode\Helper\Data')->getConfig('barcode/active11/logolength');
	                                  
	                                    $xcordinates = explode(",",$xcordinates);
	                                    $ycordinates = explode(",",$ycordinates);
	                                 
	                                    $page->drawImage($image,$xcordinates['0']+$t*(198),$ycordinates['0']-$x*105, $xcordinates['1']+$p*($t*198), ($ycordinates['0']-$x*105)+$ycordinates['1']-$ycordinates['0']);
	                                    
	                                }
	                            }
	                            
	                        }
	                   }
	                  elseif($val['field'] == 'qty_increment')
	                    {
	                        
	                      $stockRegistry = $model->get('Magento\CatalogInventory\Api\StockRegistryInterface');
	                   	  $product = $model->create('Magento\Catalog\Model\Product')->load($pids[$c]);
	                      $stockitem = $stockRegistry->getStockItem(
	                      $product->getId(),
	                      $product->getStore()->getWebsiteId()
	                       );
	                   
	                   	  $qty_increment = $stockitem->getQtyIncrements();
	       				  if($i >2)
				            {
				                $t = $i%3;
				                $x = $i/3;
				                $x = floor($x);
				             
				            }
				            
				          
	            		  $pos = explode(',', $val['position']);
	            		
	            		  $page->drawText(__('Inner-Box'.':'.$qty_increment),$t*198+$pos['0'],$pos['1']-105*$x, 'UTF-8');
	            	   }
	            	   
	            	  elseif($val['field'] == 'price') 
	            	  {
	            	  	  $currencysymbol = $model->get('\Magento\Framework\Pricing\PriceCurrencyInterface');
	            	      $currencysymbol = $currencysymbol->getCurrency()->getCurrencySymbol();
	            	      
	                      $position = explode(',', $val['position']);
	                      $page->setFont(\Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_HELVETICA), 10);
	                      $pos = explode(',', $val['position']);
	                      if(isset($product[$field]))
	                      {
	                      	$page->drawText(__($currencysymbol.' '.round($product[$field],2)),$t*198+$position['0'],$position['1']-105*$x, 'UTF-8');
	                      }
	                     
	                  }
	            	 else
	                   {
	                      $position = explode(',', $val['position']);
	                      $page->setFont(\Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_HELVETICA), 10);
	                      $pos = explode(',', $val['position']);
	                      if(isset($product[$field]))
	                      {
	                      	$page->drawText(__($product[$field]),$t*198+$position['0'],$position['1']-105*$x, 'UTF-8');
	                      }
	                     
	                         
	                   }
	                   
	            
	             }
	          }
	          

	          $model =  \Magento\Framework\App\ObjectManager::getInstance();
	          $image = $model->create('Ced\Barcode\Helper\Data')->getImgName();
	          if ($image)
	          {
	          	$imagePath = '/ced/barcode/' . $image;
	          	if ($this->_mediaDirectory->isFile($imagePath))
	          	{
	          		$image = \Zend_Pdf_Image::imageWithPath($this->_mediaDirectory->getAbsolutePath($imagePath));
	          		 
	          		$xcordinates = $model->create('Ced\Barcode\Helper\Data')->getConfig('barcode/active11/logowidth');
	          		$ycordinates = $model->create('Ced\Barcode\Helper\Data')->getConfig('barcode/active11/logolength');
	          		 
	          		$xcordinates = explode(",",$xcordinates);
	          		$ycordinates = explode(",",$ycordinates);
	          
	          		$page->drawImage($image,$xcordinates['0']+$t*(198),$ycordinates['0']-$x*105, $xcordinates['1']+$p*($t*198), ($ycordinates['0']-$x*105)+$ycordinates['1']-$ycordinates['0']);
	          		
	          		 
	          	}
	          }
	       }
    
     	$this->_afterGetPdf();
        return $pdf;
    }

    
}
