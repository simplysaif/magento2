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

namespace Ced\CsSubAccount\Controller\Account;

use Ced\CsMarketplace\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Ced\CsMarketplace\Model\Url as CustomerUrl;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Data\Form\FormKey\Validator;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LoginPost extends \Ced\CsMarketplace\Controller\Account\LoginPost
{
    /**
* 
     *
 * @var AccountManagementInterface 
*/
    protected $vendorAccountManagement;

    /**
* 
     *
 * @var Validator 
*/
    protected $formKeyValidator;

    /**
     * @var VendorAcRedirect
     */
    protected $vendorAcRedirect;

    /**
     * @var VendorUrl
     */
    protected $vendorUrl;

    /**
     * @var VendorSession
     */
    protected $vendorSession;

    /**
     * @param Context                    $context
     * @param Session                    $customerSession
     * @param AccountManagementInterface $vendorAccountManagement
     * @param CustomerUrl                $vendorHelperData
     * @param Validator                  $formKeyValidator
     * @param AccountRedirect            $vendorAcRedirect
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $vendorAccountManagement,
        CustomerUrl $vendorHelperData,
        Validator $formKeyValidator,
        AccountRedirect $vendorAcRedirect,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->vendorSession = $customerSession;
        $this->vendorAccountManagement = $vendorAccountManagement;
        $this->vendorUrl = $vendorHelperData;
        $this->_scopeConfig = $scopeConfig;
        $this->formKeyValidator = $formKeyValidator;
        $this->vendorAcRedirect = $vendorAcRedirect;
        parent::__construct($context, $customerSession, $vendorAccountManagement, $vendorHelperData, $formKeyValidator, $vendorAcRedirect);
    }

    /**
     * Login post action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        if ($this->vendorSession->isLoggedIn()) {
            /* || !$this->formKeyValidator->validate($this->getRequest())*/
            /**
             *
             *
             * @var \Magento\Framework\Controller\Result\Redirect $resultRedirect
             */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('csmarketplace/vendor/index');
            return $resultRedirect;
        }
        if ($this->getRequest()->isPost()) {
        
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $check = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')->loadByEmail($login['username']);
                    if($check && $check->getId()){
                        $customer = $this->vendorAccountManagement->authenticate($login['username'], $login['password']);
                        $this->vendorSession->setCustomerDataAsLoggedIn($customer);
                        $this->vendorSession->regenerateId();
                    }
                    else{
                        if(!$this->_scopeConfig->getValue('ced_cssubaccount/general/cssubaccount_active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)){
                            $this->messageManager->addError(__('Sub-vendor Account System is disabled by admin.'));
                            $this->_redirect('csmarketplace/account/login');
                            return;
                        }
                        $subvendor = $this->_objectManager->create('Ced\CsSubAccount\Model\CsSubAccount')->load($login['username'],'email')->getData();

                        if(!empty($subvendor) && $subvendor['status'] == \Ced\CsSubAccount\Model\CsSubAccount::ACCOUNT_APPROVE){
                            if($this->_objectManager->get('Magento\Framework\Encryption\Encryptor')->decrypt($subvendor['password']) == $login['password']){
                                $vendor = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')->load($subvendor['parent_vendor']);
                                $customer = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($vendor->getCustomerId());        
                                $this->vendorSession->setCustomerAsLoggedIn($customer);
                                $this->vendorSession->regenerateId();
                                $this->vendorSession->setSubVendorData($subvendor);
                                
                            }
                            else
                                $this->messageManager->addError(__('Invalid login or password.'));
                        }elseif(!empty($subvendor) && $subvendor['status'] == \Ced\CsSubAccount\Model\CsSubAccount::ACCOUNT_DISAPPROVE){
                            $this->messageManager->addError(__('Sub-vendor account has been disapproved by the vendor.'));
                        }
                        elseif(!empty($subvendor) && $subvendor['status'] == \Ced\CsSubAccount\Model\CsSubAccount::ACCOUNT_NEW){
                            $this->messageManager->addError(__('Your Sub-vendor account is under approval.'));
                        }



                    }
                    
                } catch (EmailNotConfirmedException $e) {
                    $value = $this->vendorUrl->getEmailConfirmationUrl($login['username']);
                    $message = __(
                        'This account is not confirmed.' .
                        ' <a href="%1">Click here</a> to resend confirmation email.',
                        $value
                    );
                    $this->messageManager->addError($message);
                    $this->vendorSession->setUsername($login['username']);
                } catch (AuthenticationException $e) {
                    $message = __('Invalid login or password.');
                    $this->messageManager->addError($message);
                    $this->vendorSession->setUsername($login['username']);
                } catch (\Exception $e) {
                    print_r($e->getMessage());die;
                    $this->messageManager->addError(__('Invalid login or password.'));
                }
            } else {
                $this->messageManager->addError(__('A login and a password are required.'));
            }
        }
        return $this->vendorAcRedirect->getRedirect();
    }
}
