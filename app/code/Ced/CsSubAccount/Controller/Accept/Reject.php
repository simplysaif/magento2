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

class Reject extends \Magento\Framework\App\Action\Action
{

    public function __construct(
        Context $context
    )
    {
        parent::__construct($context);
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $model = $this->_objectManager->create('Ced\CsSubAccount\Model\AccountStatus')->load($this->getRequest()->getParam('cid'));
        if($model['status'] == \Ced\CsSubAccount\Model\AccountStatus::ACCOUNT_STATUS_ACCEPTED)
            $msg = 'You have already accepted the seller request';
        if($model['status'] == \Ced\CsSubAccount\Model\AccountStatus::ACCOUNT_STATUS_REJECTED)
            $msg = 'You have rejected the seller request';
        
        try {
            if($model['status'] == \Ced\CsSubAccount\Model\AccountStatus::ACCOUNT_STATUS_PENDING){
                $model->setStatus(\Ced\CsSubAccount\Model\AccountStatus::ACCOUNT_STATUS_REJECTED);
                $model->save();
                $msg = 'You have successfully rejected the seller request';
            }
            
        } catch (\Exception $e){
            $msg =  $e->getMessage();
        }

        $this->messageManager->addSuccess(__($msg));
        $this->_redirect("/");
        return;
    }

    
}
