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

namespace Ced\CsMarketplace\Controller\Account;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
class Create extends \Magento\Customer\Controller\Account\Create
{
    /**
     * Dispatch request
     *
     * @param  RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $this->_eventManager->dispatch(
            'ced_csmarketplace_predispatch_action', [
            'session' => $this->session,
            ]
        );
        return parent::dispatch($request);
    }
    
    public function execute()
    {
    
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->session->isLoggedIn() && $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->authenticate($this->session->getCustomerId())) {          
            $resultRedirect->setPath('csmarketplace/vendor/');
            return $resultRedirect;
        }
        if ($this->session->isLoggedIn() && !$this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->authenticate($this->session->getCustomerId())) {          
            $resultRedirect->setPath('csmarketplace/account/approval');
            return $resultRedirect;
        }
                
        return $this->resultPageFactory->create();    
    }
    
}
