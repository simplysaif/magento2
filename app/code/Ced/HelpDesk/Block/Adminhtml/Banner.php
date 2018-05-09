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
 * @category    Ced
 * @package     Ced_HelpDesk
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\HelpDesk\Block\Adminhtml;

class Banner extends \Magento\Backend\Block\Template
{
    /*
     *@var \Magento\Framework\App\ObjectManager::getInstance() objectmanager
     */
    public $objectManager;
    /*
     * @param \Magento\Backend\Block\Template\Context $context
     */
    public function __construct(\Magento\Backend\Block\Template\Context $context){
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct($context);
    }
    /*
     * get banner Image from system config
     */
    public function bannerInfo(){
        $bannerImage = $this->objectManager->create('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/frontend/banner');
        return $bannerImage;
    }
    /*
    * get Welcome Message from system config
    */
    public function welcomeMessage(){
        return $this->objectManager->create('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/frontend/welcome_msg');
    }
    /*
   * get Welcome Description from system config
   */
    public function welcomeDesc(){
        return $this->objectManager->create('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/frontend/welcone_desc');
    }
}