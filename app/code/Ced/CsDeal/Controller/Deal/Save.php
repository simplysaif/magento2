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
 * @package     Ced_CsDeal
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
 
namespace Ced\CsDeal\Controller\Deal;

class Save extends \Ced\CsMarketplace\Controller\Vendor
{
    public function execute()
    {
    if($this->getRequest()->isPost()){
			$post_data=$this->getRequest()->getPost();
			
			/*if($post_data['days']==0){
				$spdata=implode(',',$post_data['specificdays']);
				$post_data['specificdays']=$spdata;
			}
			else
			{
				$post_data['specificdays'] = $post_data['days'];
			}*/
			$needApproval = $this->_objectManager->get('Ced\CsDeal\Helper\Data')->isApprovalNeeded();
			if($needApproval && !$this->getRequest()->getParam('deal_id'))
				$post_data['admin_status']='2';	
			else
				$post_data['admin_status']='1';		
			$model = $this->_objectManager->create('Ced\CsDeal\Model\Deal');
			$name = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($post_data['product_id'])->getName();

			$model->setData('product_id',$post_data['product_id'])
				  ->setData('product_name',$name)
				  //->setData('days',$post_data['specificdays'])
			      ->setData('start_date',$post_data['start_date'])
			      ->setData('end_date',$post_data['end_date'])
			      ->setData('vendor_id',$post_data['vendor_id'])
			      ->setData('admin_status',$post_data['admin_status'])
			      ->setData('status',$post_data['status'])
			      ->setData('deal_price',$post_data['deal_price'])
			      ->setDealId($this->getRequest()->getParam('deal_id'))->save();
			     // $this->_eventManager->dispatch('controller_action_predispatch_ced_csdeal_create', array('deal' => $model,));
			      //var_dump($this->getRequest()->getParam('deal_id'));die;
			if($post_data['admin_status'] == '1' || $this->getRequest()->getParam('deal_id')){
				$this->_eventManager->dispatch('controller_action_predispatch_ced_csdeal_create', array('deal' => $model,));
			}
			$this->messageManager->addSuccessMessage(__('Deal created Successfully.'));
			$this->_redirect('*/*/listi');		
            return;
		}else{
			$this->_redirect('*/*/create');	
		}

	}
    
}
