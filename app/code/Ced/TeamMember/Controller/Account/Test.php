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
 * @package     Ced_TeamMember
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 

namespace Ced\TeamMember\Controller\Account;

//use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;

class Test extends \Ced\TeamMember\Controller\TeamMember
{

  
    
    public function execute()
    {      
    	$data = $this->_objectManager->create('Ced\TeamMember\Model\Session');
    	if($data->getLoggedIn())
    	{
    	 var_dump($data->getLoggedIn());
    	 echo $data->getTeamMemberId();
    	 echo 'Session exist';
    	}
    	else 
    		echo "session expires";
    	
    	
    	die('here');
    }
}
