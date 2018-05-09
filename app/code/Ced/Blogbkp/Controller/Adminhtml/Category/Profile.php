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
  * @category  Ced
  * @package   Ced_Blog
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\Blog\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;

use Magento\Framework\View\Result\PageFactory;

use Magento\Backend\App\Action;


/**
 *  category profile  Controller
 * @author CedCommerce Magento Core Team <Ced_MagentoCoreTeam@cedcommerce.com>
 */

class Profile extends \Magento\Backend\App\AbstractAction
{

    /**
     * @var auth
     */

    protected $_auth;

    /**
     * @param Action\Context
     */

    public function __construct(Action\Context $context)
    {

        parent::__construct($context);

    }

    /**
     * @param execute
     */

    public function execute()
    {

        $email = $this->getRequest()->getParam('email');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $objectManager->create('Magento\Customer\Model\Customer')
            ->setWebsiteId(1)
            ->loadByEmail($email);
        $data = $collection->getEntityId();
        echo $data;
    }
}
