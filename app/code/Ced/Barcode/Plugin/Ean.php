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
namespace Ced\Barcode\Plugin;

class Ean
{
	
    public function aroundSave( \Magento\Catalog\Model\Product $subject,
        \Magento\Catalog\Model\Product $result)
    {
    	
    	$id = $result->getId();

      $ob =  \Magento\Framework\App\ObjectManager::getInstance();
      $Registry = $ob->create('\Magento\Framework\Registry');	

      if($Registry->registry('datas') == NULL)
      {
        
        $product = $ob->create('Magento\Catalog\Model\Product')->load($id);
        if($product->getEan() != NULL)
        {
          $barcode = $product->getEan();
          $barcode = substr($barcode,0,12);
          $barcode = str_pad($barcode, '12', '0', STR_PAD_LEFT);
          $checksum = $this->GetCheckDigit($barcode);
          $barcode = substr_replace($barcode, $checksum, 12, 1);
      
          $product->setEan($barcode);
       
       }
     
      }
      return $product;
   }


    function GetCheckDigit($barcode)
    {
    	//Compute the check digit
    	
    	$sum=0;
    	for($i=1;$i<=11;$i+=2)
    	{
    		$sum+=3*$barcode[$i];
    	}
    	for($i=0;$i<=10;$i+=2)
    	{
    		$sum+=$barcode[$i];
    	}
    	$r=$sum%10;
    	if($r>0)
    	{
    		$r=10-$r;
    	}
    	
    	return $r;
    }
}
