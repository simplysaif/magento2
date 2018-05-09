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
 * @package     Ced_CsSubAccount
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSubAccount\Controller\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class SaveResource extends \Ced\CsMarketplace\Controller\Vendor
{

    protected $resultPageFactory;

    public $_customerSession;

    protected $_scopeConfig;

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
        if(!$this->_getSession()->getVendorId())
            $this->_redirect('csmarketplace/account/login/');
        $post = $this->getRequest()->getPost();
        $id = $post['subVendorId'];  
        $resources = [];
        $resource = '';
        try{
            if($post['all'] == 1){
                $model = $this->_objectManager->create('Ced\CsSubAccount\Model\CsSubAccount')->load($id)->setRole('all');
                $model->save();
            }
            else{
                $resources = $post['resource'];
                if(!empty($resources)){
                    $resource = implode(',',$resources);
                }
                $model = $this->_objectManager->create('Ced\CsSubAccount\Model\CsSubAccount')->load($id)->setRole($resource);
                $model->save();  
            }
        }catch(\Exception $e){
            $this->messageManager->addError(__($e->getMessage()));
            $this->_redirect('cssubaccount/customer/index');
            return;
        }  
        $this->messageManager->addSuccess(__('Resources are successfully alloted to the Sub-Vendor'));
        $this->_redirect('cssubaccount/customer/index');
        return;         
    }
    
}
