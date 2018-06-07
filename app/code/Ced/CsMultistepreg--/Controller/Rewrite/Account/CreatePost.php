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
 * @package     Ced_CsMultistepregistration
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsMultistepreg\Controller\Rewrite\Account;
use Ced\CsMarketplace\Model\Account\Redirect as AccountRedirect;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Helper\Address;
use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Ced\CsMarketplace\Model\Url as CustomerUrl;
use Magento\Customer\Model\Registration;
use Magento\Framework\Escaper;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\InputException;
use Ced\CsMarketplace\Model\VendorFactory;
use Ced\CsMarketplace\Helper\Data;
class CreatePost extends \Ced\CsMarketplace\Controller\Account\CreatePost{

    /**<<<<<<< HEAD
     *
     *
     * @var AccountManagementInterface
     */
    protected $vendorAcManagement;

    /**
     * 
     * @var CsAddressHelper 
     */
    protected $csAddressHelper;

    /**
     * 
     * @var CsFormFactory 
     */
    protected $csFormFactory;

    /**
     * 
     * @var CsSubscriberFactory 
     */
    protected $csSubscriberFactory;

    /**
     * 
     * @var CsRegionDataFactory 
     */
    protected $csRegionDataFactory;

    /**
     * 
     * @var CsAddressDataFactory 
     */
    protected $csAddressDataFactory;

    /**
     * 
     * @var vendorRegistration 
     */
    protected $vendorRegistration;

    /**
     * 
     * @var customerDataFactory 
     */
    protected $customerDataFactory;

    /**
     * 
     * @var VendorUrl 
     */
    protected $vendorUrl;

    /**
     * 
     * @var CsEscaper 
     */
    protected $csEscaper;

    /**
     * 
     * @var CustomerExtractor 
     */
    protected $customerExtractor;

    /**
     * 
     * @var VendorUrlModel 
     */
    protected $vendorUrlModel;

    /**
     * 
     * @var DataObjectHelper 
     */
    protected $dataObjectHelper;

    /**
     *
     * @var Session
     */
    protected $vendorSession;

    /**
     *
     * @var AccountRedirect
     */
    private $accountRedirect;

    /**
     * @param Context                    $context
     * @param Session                    $customerSession
     * @param ScopeConfigInterface       $scopeConfig
     * @param StoreManagerInterface      $storeManager
     * @param AccountManagementInterface $vendorAcManagement
     * @param Address                    $csAddressHelper
     * @param UrlFactory                 $urlFactory
     * @param FormFactory                $csFormFactory
     * @param SubscriberFactory          $csSubscriberFactory
     * @param RegionInterfaceFactory     $csRegionDataFactory
     * @param AddressInterfaceFactory    $csAddressDataFactory
     * @param CustomerInterfaceFactory   $customerDataFactory
     * @param CustomerUrl                $vendorUrl
     * @param Registration               $vendorRegistration
     * @param Escaper                    $csEscaper
     * @param CustomerExtractor          $customerExtractor
     * @param DataObjectHelper           $dataObjectHelper
     * @param AccountRedirect            $accountRedirect
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        AccountManagementInterface $vendorAcManagement,
        Address $csAddressHelper,
        UrlFactory $urlFactory,
        FormFactory $csFormFactory,
        SubscriberFactory $csSubscriberFactory,
        RegionInterfaceFactory $csRegionDataFactory,
        AddressInterfaceFactory $csAddressDataFactory,
        CustomerInterfaceFactory $customerDataFactory,
        CustomerUrl $vendorUrl,
        Registration $vendorRegistration,
        Escaper $csEscaper,
        CustomerExtractor $customerExtractor,
        DataObjectHelper $dataObjectHelper,
        AccountRedirect $accountRedirect,
                VendorFactory $Vendor,
        Data $datahelper
    ) {
        $this->vendorSession = $customerSession;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->vendorAcManagement = $vendorAcManagement;
        $this->csAddressHelper = $csAddressHelper;
        $this->csFormFactory = $csFormFactory;
        $this->csSubscriberFactory = $csSubscriberFactory;
        $this->csRegionDataFactory = $csRegionDataFactory;
        $this->csAddressDataFactory = $csAddressDataFactory;
        $this->customerDataFactory = $customerDataFactory;
        $this->vendorUrl = $vendorUrl;
        $this->vendorRegistration = $vendorRegistration;
        $this->csEscaper = $csEscaper;
        $this->customerExtractor = $customerExtractor;
        $this->vendorUrlModel = $urlFactory->create();
        $this->dataObjectHelper = $dataObjectHelper;
        $this->accountRedirect = $accountRedirect;
        parent::__construct($context,$customerSession,$scopeConfig,$storeManager,
        $vendorAcManagement,
        $csAddressHelper,
        $urlFactory,
        $csFormFactory,
        $csSubscriberFactory,
        $csRegionDataFactory,
        $csAddressDataFactory,
        $customerDataFactory,
        $vendorUrl,
        $vendorRegistration,
        $csEscaper,
        $customerExtractor,
        $dataObjectHelper,
        $accountRedirect,$Vendor,$datahelper);
    }

    /**
     * Add address to customer during create account
     *
     * @return AddressInterface|null
     */
    protected function extractAddress()
    {
        if (!$this->getRequest()->getPost('create_address')) {
            return null;
        }

        $addressForm = $this->csFormFactory->create('customer_address', 'customer_register_address');
        $allowedAttributes = $addressForm->getAllowedAttributes();

        $vendorAddressData = [];

        $regionDataObject = $this->csRegionDataFactory->create();
        foreach ($allowedAttributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $value = $this->getRequest()->getParam($attributeCode);
            if ($value === null) {
                continue;
            }
            switch ($attributeCode) {
            case 'region_id':
                $regionDataObject->setRegionId($value);
                break;
            case 'region':
                $regionDataObject->setRegion($value);
                break;
            default:
                $vendorAddressData[$attributeCode] = $value;
            }
        }
        $csAddressDataObject = $this->csAddressDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $csAddressDataObject,
            $vendorAddressData,
            '\Magento\Customer\Api\Data\AddressInterface'
        );
        $csAddressDataObject->setRegion($regionDataObject);

        $csAddressDataObject->setIsDefaultBilling(
            $this->getRequest()->getParam('default_billing', false)
        )->setIsDefaultShipping(
            $this->getRequest()->getParam('default_shipping', false)
        );
        return $csAddressDataObject;
    }

    /**
     * Create vendor account action
     *
     * @return                                       void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
       
        /**
         *
         *
         * @var \Magento\Framework\Controller\Result\Redirect $resultRedirect
         */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->vendorSession->isLoggedIn() || !$this->vendorRegistration->isAllowed()) {
            $resultRedirect->setPath('csmarketplace/vendor/index');
            return $resultRedirect;
        }

        if (!$this->getRequest()->isPost()) {
            $vendorUrl = $this->vendorUrlModel->getUrl('*/*/login', ['_secure' => true,'create'=>true]);
            $resultRedirect->setUrl($this->_redirect->error($vendorUrl));
            return $resultRedirect;
        }

        $this->vendorSession->regenerateId();

        try {
            $vendorAddress = $this->extractAddress();
            $addresses = $vendorAddress === null ? [] : [$vendorAddress];

            $vendor = $this->customerExtractor->extract('customer_account_create', $this->_request);
            $vendor->setAddresses($addresses);

            $password = $this->getRequest()->getParam('password');
            $confirmation = $this->getRequest()->getParam('password_confirmation');
            $redirectUrl = $this->vendorSession->getBeforeAuthUrl();

            $this->checkPasswordConfirmation($password, $confirmation);

            $vendor = $this->vendorAcManagement->createAccount($vendor, $password, $redirectUrl);

            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $this->csSubscriberFactory->create()->subscribeCustomerById($vendor->getId());
            }
            
            /* $this->_eventManager->dispatch(
                'customer_register_successfully',
                ['account_controller' => $this, 'customer' => $vendor]
            ); */
            if($this->getRequest()->getParam('is_vendor') == 1) {
            	$venderData = $this->getRequest()->getParam('vendor');

            	$customerData = $vendor;
            	try {
                     $vData=$this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')->getCollection()->addAttributeToFilter('shop_url', $venderData['shop_url'] )->getData();
                        if(sizeof($vData)>0){
                            $this->messageManager->addError(__('Shop url already exist. Please Provide another Shop Url'));
                             $vendorUrl = $this->vendorUrlModel->getUrl('*/*/login', ['_secure' => true,'create'=>true]);
            $resultRedirect->setUrl($this->_redirect->error($vendorUrl));
            return $resultRedirect;
                        }
                    $vendordata = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')
            		->setCustomer($customerData)
            		->register($venderData);
            		 
            		if(!$vendordata->getErrors()) {
            			$vendordata->save();
            			
            			
            			
            		} elseif ($vendordata->getErrors()) {
            			foreach ($vendordata->getErrors() as $error) {
            				$this->_session->addError($error);
            			}
            			$this->_session->setFormData($venderData);
            		} else {
            			$this->_session->addError(__('Your vendor application has been denied'));
            		}
            	} catch (\Exception $e) {
            		$this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->logException($e);
            	}
            }
            $enabled = $this->_objectManager->create('Ced\CsMultistepreg\Helper\Data')->isEnabled();
            if($enabled != '1'){
            	$confirmationStatus = $this->vendorAcManagement->getConfirmationStatus($vendor->getId());
            	if ($confirmationStatus === AccountManagementInterface::ACCOUNT_CONFIRMATION_REQUIRED) {
            	    $email = $this->vendorUrl->getEmailConfirmationUrl($vendor->getEmail());
            	    $this->messageManager->addSuccessMessage(
            	        __(
            	            'You must confirm your account. Please check your email for the confirmation link or <a href="%1">click here</a> for a new link.',
            	            $email
            	        )
            	    );
            	    $vendorUrl = $this->vendorUrlModel->getUrl('*/*/index', ['_secure' => true]);
            	    $resultRedirect->setUrl($this->_redirect->success($vendorUrl));
            	} else {
            	    $this->vendorSession->setCustomerDataAsLoggedIn($vendor);
            	    $this->messageManager->addSuccessMessage($this->getSuccessMessage());
            	    $resultRedirect = $this->accountRedirect->getRedirect();
            	}
            }
            // $confirmationStatus = $this->vendorAcManagement->getConfirmationStatus($vendor->getId());
            // if ($confirmationStatus === AccountManagementInterface::ACCOUNT_CONFIRMATION_REQUIRED) {
            //     $email = $this->vendorUrl->getEmailConfirmationUrl($vendor->getEmail());
            //     // @codingStandardsIgnoreStart
            //     $this->messageManager->addSuccessMessage(
            //         __(
            //             'You must confirm your account. Please check your email for the confirmation link or <a href="%1">click here</a> for a new link.',
            //             $email
            //         )
            //     );
            //     // @codingStandardsIgnoreEnd
            //     $vendorUrl = $this->vendorUrlModel->getUrl('*/*/index', ['_secure' => true]);
            //     $resultRedirect->setUrl($this->_redirect->success($vendorUrl));
            // } else {
            //     $this->vendorSession->setCustomerDataAsLoggedIn($vendor);
            //     $this->messageManager->addSuccessMessage($this->getSuccessMessage());
            //     $resultRedirect = $this->accountRedirect->getRedirect();
            // }
            /* code here */
            $vendorId = $vendordata->getEntityId();
            if($enabled == '1'){
                $this->vendorSession->setCustomerDataAsLoggedIn($vendor);
	            $resultRedirect->setPath('csmultistep/multistep/index',array('id'=>$vendorId));
				return $resultRedirect;
            }
            return $resultRedirect;
        } catch (StateException $e) {
            $forgotPassUrl = $this->vendorUrlModel->getUrl('customer/account/forgotpassword');
            // @codingStandardsIgnoreStart
            $message = __(
                'There is already an account with this email address. If you are sure that it is your email address, <a href="%1">click here</a> to get your password and access your account.',
                $forgotPassUrl
            );
            // @codingStandardsIgnoreEnd
            $this->messageManager->addError($message); 
        } catch (InputException $e) {
            $this->messageManager->addError($this->csEscaper->escapeHtml($e->getMessage()));
            foreach ($e->getErrors() as $error) {
                $this->messageManager->addError($this->csEscaper->escapeHtml($error->getMessage()));
            }
        } catch (\Exception $e) {
            print_r($e->getMessage());
            $this->messageManager->addException($e, __('We can\'t save the vendor.'));
        }

        $this->vendorSession->setCustomerFormData($this->getRequest()->getPostValue());
        $defaultUrl = $this->vendorUrlModel->getUrl('*/*/login', ['_secure' => true,'create'=>true]);
        $resultRedirect->setUrl($this->_redirect->error($defaultUrl));
        return $resultRedirect;
    }

    /**
     * Make sure that password and password confirmation matched
     *
     * @param  string $password
     * @param  string $confirmationPass
     * @return void
     * @throws InputException
     */
    protected function checkPasswordConfirmation($password, $confirmationPass)
    {
        if ($password != $confirmationPass) {
            throw new InputException(__('Please make sure your passwords match.'));
        }
    }

    /**
     * Retrieve success message if vendor created
     *
     * @return string
     */
    protected function getSuccessMessage()
    {
        if ($this->csAddressHelper->isVatValidationEnabled()) {
            if ($this->csAddressHelper->getTaxCalculationAddressType() == Address::TYPE_SHIPPING) {
                // @codingStandardsIgnoreStart
                $successMessage = __(
                    'If you are a registered VAT customer, please <a href="%1">click here</a> to enter your shipping address for proper VAT calculation.',
                    $this->vendorUrlModel->getUrl('customer/address/edit')
                );
                // @codingStandardsIgnoreEnd
            } else {
                // @codingStandardsIgnoreStart
                $successMessage = __(
                    'If you are a registered VAT customer, please <a href="%1">click here</a> to enter your billing address for proper VAT calculation.',
                    $this->vendorUrlModel->getUrl('customer/address/edit')
                );
                // @codingStandardsIgnoreEnd
            }
        } else {
            $successMessage = __('Thank you for registering with %1.', $this->storeManager->getStore()->getFrontendName());
        }
        return $successMessage;
    }
} 