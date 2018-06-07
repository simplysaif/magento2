<?php

namespace  Magestore\Affiliateplus\Block\Product;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    public function getProductDetailsHtml(\Magento\Catalog\Model\Product $product)
    {
        $html = $this->getLayout()->createBlock('Magestore\Affiliateplus\Block\Product\Product')->setProduct($product)->setTemplate('Magestore_Affiliateplus::catalog/product/list.phtml')->toHtml();
        $renderer = $this->getDetailsRenderer($product->getTypeId());
        if ($renderer) {
            $renderer->setProduct($product);
            return $html.$renderer->toHtml();
        }
        return '';
    }
    public function xlog($message = 'null')
    {
        $log = print_r($message, true);
        \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Psr\Log\LoggerInterface')
            ->debug($log);
    }
}