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
 * @package     Ced_CsTableRateShipping
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsTableRateShipping\Controller\Rates;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Config\Controller\Adminhtml\System\ConfigSectionChecker;
use Magento\Framework\App\Filesystem\DirectoryList;

class View extends \Ced\CsMarketplace\Controller\Vendor
{
    protected $resultPageFactory;
    /**
      * * @param \Magento\Framework\App\Action\Context $context      
*/
    
 
    /**
     * Blog Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        if(!$this->_getSession()->getVendorId()) {
            return; 
        }
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->get('Ced\CsTableRateShipping\Model\Tablerate')->load($id);
        $registry = $this->_objectManager->get('\Magento\Framework\Registry');
        $registry->register('table_rate', $model);
        $resultPage = $this->resultPageFactory->create();        
        $resultPage->getConfig()->getTitle()->set(__('Table Rate Shipping Settings'));
        return $resultPage;
        
    }
}
