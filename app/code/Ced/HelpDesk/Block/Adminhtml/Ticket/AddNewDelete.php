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
 * @package     Ced_HelpDesk
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\HelpDesk\Block\Adminhtml\Ticket;
 
class AddNewDelete extends \Magento\Framework\View\Element\Template
{
    /*
     * get current user role
     * @return string
     * */
    public function getUserData()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $user_id = $objectManager->create('Magento\Backend\Model\Auth\Session')->getUser()->getData('user_id');
        $data = $objectManager->create('Ced\HelpDesk\Model\Agent')
                              ->getCollection()
                              ->addFieldToFilter('user_id',$user_id)
                              ->getFirstItem()
                              ->getRoleName();
        return $data;
    }
}