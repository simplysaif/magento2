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
 * @package     Ced_CsTableRateShipping
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 
 
namespace Ced\CsTableRateShipping\Controller\Rates;

class Save extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * Default vendor dashboard page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if(!$this->_getSession()->getVendorId()) {
            return; 
        }
        if($this->getRequest()->isPost())
        {
          $data=$this->getRequest()->getPostValue();
          $vendor_id = $this->_getSession()->getVendorId();
          $_objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
          $website_id = $_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getWebsiteId();
          $dest_country_id = $data['groups']['address']['country_id'];
        }

        //validate destination region
      if (!empty($data['groups']['address']['region_id'])) {
        $dest_region_id = $data['groups']['address']['region_id'];
      }
//      elseif(isset($data['groups']['address']['region']) && $data['groups']['address']['region'] != '*')
//          {
//            $dest_region_id = $data['groups']['address']['region'];
//          }
      elseif ($data['groups']['address']['region'] == '*' || $data['groups']['address']['region'] == ''){
        $dest_region_id = 0;
      }
      else 
      {
        $this->messageManager->addError(__('Please enter valid region code.'));
        $this->_redirect('cstablerateshipping/rates/add');
        return;
      }

      //validate zip code
      if ($data['groups']['address']['postcode'] == '*' || $data['groups']['address']['postcode'] == '') {
        $dest_zip_code = '*';
      } else {
        $dest_zip_code =$data['groups']['address']['postcode'];
      }
        
      $condition_name = $data['groups']['address']['condition_name'];

      //validate condition value
      if($data['groups']['address']['condition_value'] === false)
      {
        $this->messageManager->addError(__('Please enter valid condition value.'));
        $this->_redirect('cstablerateshipping/rates/add');
        return;
      }
      $condition_value = $data['groups']['address']['condition_value'];

      //validate price
      if(!empty( $data['groups']['address']['price']))
      {
        $price = $data['groups']['address']['price'];
      }
      else
      {
        $this->messageManager->addError(__('Please enter price.'));
        $this->_redirect('cstablerateshipping/rates/add');
        return;
      }

      $model = $_objectManager->get('Ced\CsTableRateShipping\Model\Tablerate');
      $id = $this->getRequest()->getParam('id');
       
      if(!isset($id)){
        try{
          $model->setData('vendor_id',$vendor_id)
                ->setData('website_id',$website_id)
                ->setData('dest_country_id',$dest_country_id)
                ->setData('dest_region_id',$dest_region_id)
                ->setData('dest_zip',$dest_zip_code)
                ->setData('condition_name',$condition_name)
                ->setData('condition_value',$condition_value)
                ->setData('price',$price)->save();
          $this->messageManager->addSuccessMessage(__('New Rate has been saved.'));
          $this->_redirect('*/*/index');
        }
        catch(Exception $e){
          echo $e->getMessage();
          $this->messageManager->addError(__('Cannot enter duplicate data..'));
          $this->_redirect('cstablerateshipping/rates/add');
          return;
        } 
      }
      else 
      {
        $model->load($id);
        try{
        $model->setData('vendor_id',$vendor_id)
            ->setData('website_id',$website_id)
            ->setData('dest_country_id',$dest_country_id)
            ->setData('dest_region_id',$dest_region_id)
            ->setData('dest_zip',$dest_zip_code)
            ->setData('condition_name',$condition_name)
            ->setData('condition_value',$condition_value)
            ->setData('price',$price)->save();
        $this->messageManager->addSuccessMessage(__('Rate has been saved.'));
        $this->_redirect('*/*/index');
        }
        catch(Exception $e)
        {
          echo $e->getMessage();
          $this->messageManager->addError(__('Cannot enter duplicate data..'));
          $this->_redirect('cstablerateshipping/rates/add',array('id'=>$id));
          return;
        }
      }




        $section = $this->getRequest()->getParam('section', '');
        $groups = $this->getRequest()->getPost('groups', array());

        if(strlen($section) > 0 && $this->_getSession()->getData('vendor_id') && count($groups)>0) {
            $vendor_id = (int)$this->_getSession()->getData('vendor_id');
            try {
                $this->_objectManager->get('Ced\CsMultiShipping\Helper\Data')->saveShippingData($section, $groups, $vendor_id);
                $this->messageManager->addSuccess(__('The Shipping Methods has been saved.'));
                $this->_redirect('*/*/index');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('*/*/index');
                return;
            }
        }
        $this->_redirect('*/*/index');        
    }
}
