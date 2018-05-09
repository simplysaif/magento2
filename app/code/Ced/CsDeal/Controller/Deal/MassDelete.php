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
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsDeal\Controller\Deal;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class MassDelete extends \Ced\CsMarketplace\Controller\Vendor
{

    protected $resultPageFactory;

    public $_customerSession;

    protected $_scopeConfig;

    protected $_eventManager;

    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        CustomerRepositoryInterface $customerRepository,
        DataObjectHelper $dataObjectHelper,
        \Magento\Framework\UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->customerRepository = $customerRepository;
        $this->_customerSession = $customerSession;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
    }
        
    
    /**
     * Promo quote edit action
     *
     * @return                                  void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $ids = $this->getRequest()->getParam('deal_id');
        $deal_ids = explode(',', $ids);
        try{
            foreach($deal_ids as $deal_id){
                $model = $this->_objectManager->create('Ced\CsDeal\Model\Deal')
                                                ->load($deal_id);
                $this->_eventManager->dispatch('controller_action_predispatch_ced_csdeal_delete', array('deal' => $model));
                $model->delete();
            }
        
        }catch(Exception $e){
            $msg = $e->getMessage();
            $this->messageManager->addError(__($msg));
            $this->_redirect('csdeal/deal/listi');
            return;
        }
        $this->messageManager->addSuccess(__('Deal(s) deleted successfully'));
        $this->_redirect('csdeal/deal/listi');
        return;               
    }
    
}
