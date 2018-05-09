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
namespace Ced\CsProductfaq\Controller\Csfaq;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;


class Save extends \Ced\CsMarketplace\Controller\Vendor
{

  
    public function execute()
    {
        
        /* if(!$this->_getSession()->getVendorId())
            return; */
        
        $vid =$this->_objectManager->get('Magento\Customer\Model\Session')->getVendorId();
        if($vid == 0)
        {
            return;
        }
        
        $vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->load($vid);
        $Vname = $vendor->getName();
        $Vemail = $vendor->getEmail();
        $data = $this->getRequest()->getPostValue();
       
        if(!isset($data['vfaq']))
        {
        	return $this->_redirect('*/*/index');
        }
     //  $pids = $data['vfaq']['products'] ;
      // print_r($pids);die("kjgh");
        $description = $data['vfaq']['answer'];
        $email = $data['vfaq']['Email'];
        $date = $this->_objectManager->get('\Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate();
    
        /**
 * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect 
*/
        $resultRedirect = $this->resultRedirectFactory->create();
        $model = $this->_objectManager->create('Ced\Productfaq\Model\Productfaq');
        if ($data) {
            $id = $this->getRequest()->getParam('id');
           
           if ($id) {
                $model->load($id);
                $model->setData('product_id',$data['vfaq']['productId']);
                $model->setData('title',$data['vfaq']['title']);
                $model->setData('description',$data['vfaq']['answer']);
                $model->setData('email_id',$data['vfaq']['Email']);
                $model->setData('is_active',$data['vfaq']['status']);
                $model->setData('publish_date',$date);
               

                try {
                    $model->save();
                    if($data['vfaq']['Email']) {
                        $this->sendTransactional($email, $description,$Vname,$Vemail);
                    }
                    $this->messageManager->addSuccess(__('The FAQ has been saved.'));
                
                    if ($this->getRequest()->getParam('back')) {
                        return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                    }
                   $this->_redirect('*/*/index');
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while saving the faq.'));
                }

                
                return $resultRedirect->setPath('*/*/index');
            }
            else{
            
                $pids = $data['vfaq']['products'] ;
                
                foreach ($pids as $k=>$v)
                {
                    $model = $this->_objectManager->create('Ced\Productfaq\Model\Productfaq');
                    $model->setData('product_id',$v);
                    $model->setData('title',$data['vfaq']['title']);
                    $model->setData('description',$data['vfaq']['answer']);
                    $model->setData('email_id',$data['vfaq']['Email']);
                    $model->setData('is_active',$data['vfaq']['status']);
                    $model->setData('publish_date',$date);
                    $model->setData('posted_by',"Vendor -".$Vname);
                    $model->setData('vendor_id',$vid);
                    
                    try {
                        $model->save();
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        $this->messageManager->addError($e->getMessage());
                    } catch (\RuntimeException $e) {
                        $this->messageManager->addError($e->getMessage());
                    } catch (\Exception $e) {
                        $this->messageManager->addException($e, __('Something went wrong while saving the faq.'));
                    }
                }
                $this->messageManager->addSuccess(__('The FAQ has been saved.'));
              //  $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
    /*
     *Sending Email Notification to customer 
     **/
    public function sendTransactional($mail,$description,$Vname,$Vemail)
    {
    	
          $this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->suspend();
        try {
            $error = false;
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $Vsender = [
            'name' => $Vname,
            'email' =>$Vemail,
            ];
            
         
            $transport = $this->_objectManager->create('\Magento\Framework\Mail\Template\TransportBuilder')
                ->setTemplateIdentifier('send_faq_email_template') // this code we have mentioned in the email_templates.xml
                ->setTemplateOptions(
                    [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['answer' => $description])
                ->setFrom($Vsender)
                ->addTo($mail)
                ->getTransport();
       
            $transport->sendMessage(); ;
            $this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->resume();
            return;
        } catch (\Exception $e) {
           $this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.'.$e->getMessage())
            );
            $this->_redirect('*/*/');
            return;
        }
    }
}
