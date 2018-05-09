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
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\TeamMember\Controller\Adminhtml\Manage;
use Magento\Backend\App\Action\Context;

class MassStatus extends \Magento\Backend\App\Action
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;


    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry         $coreRegistry
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
    }
        
    
    /**
     * Promo quote edit action
     *
     * @return                                  void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $inline = $this->getRequest()->getParam('inline', 0);
        $memberIds = $this->getRequest()->getParam('memberid');
        $status    = $this->getRequest()->getParam('status', '');
        if($inline) {
            $memberIds = array($memberIds);
        }
       
        if(!is_array($memberIds)) {
                 $this->messageManager->addError($this->__('Please select member(s)'));
        } else {
        	$counter = 0;
            try {
               foreach($memberIds as $_memberIds){
               	 $model = $this->_objectManager->create('Ced\TeamMember\Model\TeamMember')->load($memberIds);
               	 $model->setStatus($status);
               	 $model->save();
               	 $counter++;
               	 $this->_objectManager->create('Ced\TeamMember\Helper\Data')->sendEmail($_memberIds,$status);
               }
               
                $this->messageManager->addSuccessMessage(__('Total of %1 record(s) have been updated.', $counter));                
              
            }catch (\Magento\Framework\Exception\CouldNotSaveException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__($e->getMessage().' An error occurred while updating the member(s) status.'));
            }
        }
        $this->_redirect('*/*/index');
    } 
}
