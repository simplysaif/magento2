<?php 

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced
 * @package     Ced_CsDeal
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
namespace Ced\CsDeal\Controller\Setting;

class Save extends \Ced\CsMarketplace\Controller\Vendor
{
    public function execute()
    {
		if($this->getRequest()->isPost()){
			$post_data = $this->getRequest()->getPost();
			$vendor_id = $post_data['vendor_id'];
			$status = $post_data['status'];
			$deal_list = $post_data['deal_list'];
			$timer_list = $post_data['timer_list'];
			$deal_message = $post_data['deal_message'];
			$store = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStore()->getId();
			$setting_id = $post_data['setting_id'];
			unset($post_data['setting_id']);
			if($setting_id){
				$model = $this->_objectManager->create('Ced\CsDeal\Model\Dealsetting')->load($setting_id);
			}else{
				$model = $this->_objectManager->create('Ced\CsDeal\Model\Dealsetting');
			}
			$model->setData('vendor_id',$vendor_id)
			      ->setData('status',$status)
			      ->setData('deal_list',$deal_list)
			      ->setData('timer_list',$timer_list)
			      ->setData('deal_message',$deal_message)
			      ->setData('store',$store);
			$model->save();
			$this->messageManager->addSuccessMessage(__('Setting saved Successfully.'));
			$this->_redirect('*/*/');
		}else{
			$this->_redirect('*/*/');
		}

	}
    
}
