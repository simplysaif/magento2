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
  * @package     VendorsocialLogin
  * @author      CedCommerce Core Team <connect@cedcommerce.com>
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://cedcommerce.com/license-agreement.txt
  */

/**
 * VendorsocialLogin 	Linkedin controller
 *
 * @category   	Ced
 * @package    	Ced_VendorsocialLogin
 * @author		CedCommerce Magento Core Team <connect@cedcommerce.com>
 */
    
namespace Ced\VendorsocialLogin\Controller\Linkedin;
use Magento\Framework\App\Action\NotFoundException;
 
class Connect extends \Magento\Framework\App\Action\Action
 
{
	/**
     * @var \Ced\VendorsocialLogin\Helper\Linkedin
     */
    protected $_helperLinkedin;
	/**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
	/**
     * @var \Ced\VendorsocialLogin\Model\Linkedin\Oauth2\Client
     */
	protected $_client;
	protected $_accountRedirect;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Ced\VendorsocialLogin\Model\Linkedin\Oauth2\Client $client
     * @param \Ced\VendorsocialLogin\Helper\Linkedin $helperLinkedin
	 * @param \Magento\Customer\Model\Account\Redirect $accountRedirect

     */

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Ced\VendorsocialLogin\Model\Linkedin\Oauth2\Client $client,
        \Ced\VendorsocialLogin\Helper\Linkedin $helperLinkedin,
		\Magento\Customer\Model\Account\Redirect $accountRedirect
    ){
		$this->_customerSession = $customerSession;
		$this->_client = $client;
		$this->_accountRedirect = $accountRedirect;
		$this->_helperLinkedin = $helperLinkedin;
		parent::__construct($context);
    }

 

    /**

     * Dispatch request

     */
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */

    public function execute()

    {
        try {
            $this->_connectCallback();
			}
       catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        }  catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __("Some error during Linkedin login.")
                );
            }
		return $this->_sendResponse();
    }
	
	
	/**

     * connect to linkedin account

     */

    protected function _connectCallback() {
        $errorCode = $this->getRequest()->getParam('error');
        $code = $this->getRequest()->getParam('code');
        $state = $this->getRequest()->getParam('state');

        if(!($errorCode || $code) && !$state) {
            // Direct route access - deny
            return;
        }
        if(!$state || $state != $this->_customerSession->getLinkedinCsrf()) {
 			throw new \Magento\Framework\Exception\LocalizedException(
                __('Security check failed. Please try again.')
            );
           
        }
		$this->_customerSession->setLinkedinCsrf('');

        if($errorCode) {
            /* Linkedin API read light - abort*/
            if($errorCode === 'access_denied') {
                $this->messageManager
                    ->addNotice(
                        __('Linkedin Connect process aborted.')
                    );
                return;
            }

           throw new \Magento\Framework\Exception\LocalizedException(
                sprintf(
                    __('Sorry, "%s" error occured. Please try again.'),
                    $errorCode
                )
            );
            return;
        }


        if ($code) {
            $client = $this->_client;
			$userInfoApi = array(
                        'id',
                        'first-name',
                        'last-name',
                        'headline',
                        'picture-url',
                        'email-address',
                        'phone-numbers',
                        'location'
                    );
            $userInfo = $client->api('/people/~:('.implode(',', $userInfoApi).')?format=json');
			
			$token = $client->getAccessToken();
            $customersByLinkedinId = $this->_helperLinkedin
                ->getCustomersByLinkedinId($userInfo->id);

				if($this->_customerSession->isLoggedIn()) {
                /* Logged in user*/
                if($customersByLinkedinId->count()){
                    /* Linkedin account already connected to other account - deny*/
                   $this->messageManager
                        ->addNotice(
                            __('Your Linkedin account is already connected to one of our store accounts.')
                        );
                    return;
                }


                /* Connect from account dashboard - attach*/
                $customer = $this->_customerSession->getCustomer();
                $this->_helperLinkedin->connectByLinkedinId(
                    $customer,
                    $userInfo->id,
                    $token
                );
               $this->messageManager->addSuccess(
                    __('Your Linkedin account is now connected to your new user accout at our store. You can login next time by the Linkedin SocialLogin button or Store user account. Account confirmation mail has been sent to your email.')
                );
                return;
            }



            if($customersByLinkedinId->count()) {
                /* Existing connected user - login*/
                $customer = $customersByLinkedinId->getFirstItem();
                $this->_helperLinkedin->loginByCustomer($customer);
               $this->messageManager->addSuccess(
                        __('You have successfully logged in using your Linkedin account.')
                    );
                return;
            }



            $customersByEmail = $this->_helperLinkedin
                ->getCustomersByEmail($userInfo->emailAddress);

            if($customersByEmail->count()) {                
                /* Email account already exists - attach, login*/
                $customer = $customersByEmail->getFirstItem();
                $this->_helperLinkedin->connectByLinkedinId(
                    $customer->getId(),
                    $userInfo->id,
                    $token
                );



                $this->messageManager->addSuccess(
                    __('We find you already have an account at our store. Your Linkedin account is now connected to your store account. Account confirmation mail has been sent to your email.')
                );



                return;

            }



            /* New connection - create, attach, login*/

            if(empty($userInfo->firstName)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Sorry, could not retrieve your Linkedin first name. Please try again.')
                );

            }



            if(empty($userInfo->lastName)) {
               throw new \Magento\Framework\Exception\LocalizedException(

                    __('Sorry, could not retrieve your Linkedin last name. Please try again.')

                );

            }

 
            $this->_helperLinkedin->connectByCreatingAccount(

                $userInfo->emailAddress,

                $userInfo->firstName,

                $userInfo->lastName,

                $userInfo->id,

                $token

            );


            $this->messageManager->addSuccess(

                __('Your Linkedin account is now connected to your new user accout at our store. You can login next time by the Linkedin SocialLogin button or Store user account. Account confirmation mail has been sent to your email.')

            );
			


        }
    }

	/**

     * success login redirect to the customer account

     */
	protected function _sendResponse()
    {
		$resultRedirect = $this->resultRedirectFactory->create();
		$resultRedirect->setPath('customer/account');
		return $resultRedirect;

    }
	

}
