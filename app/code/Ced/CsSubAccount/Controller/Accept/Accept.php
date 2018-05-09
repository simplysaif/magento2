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

namespace Ced\CsSubAccount\Controller\Accept;

use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class Accept extends \Magento\Framework\App\Action\Action
{


    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * Date filter instance
     *
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $_dateFilter;

    protected $_messageManager;

    protected $_transportBuilder;

    protected $_storeManager;

    protected $_customerSession;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Date $dateFilter
     */
    public function __construct(
        Context $context, 
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        FileFactory $fileFactory,
        ForwardFactory $resultForwardFactory,
        Registry $coreRegistry, 
        Date $dateFilter,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_dateFilter = $dateFilter;
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $msg = '';
        $email = $this->_objectManager->create('Ced\CsSubAccount\Model\AccountStatus')->load($this->getRequest()->getParam('cid'))->getData();
        if(!count($email)){         
            $msg = 'Your Sub-vendor account is deleted.';
        }
        else
        {
            if($email['status'] == 1)
                    $msg = 'You are already associated with another seller.';
            if($email['status'] == 2)
                    $msg = 'You have already rejected the seller request.';
        }
        if($msg)
        { 
            $this->messageManager->addError(__($msg));
            $this->_redirect("/");
            return;
        }
        $this->_customerSession->setRequestId($this->getRequest()->getParam('cid'));
        $this->_customerSession->setParentVendor($this->getRequest()->getParam('vid'));
        $this->messageManager->addSuccess(__('You have accepted the seller request. Now Create your seller account'));
        $this->_redirect("cssubaccount/customer/create");
        return;
    }
}
