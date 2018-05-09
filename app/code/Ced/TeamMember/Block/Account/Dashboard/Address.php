<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ced\TeamMember\Block\Account\Dashboard;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\Address\Mapper;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class to manage customer dashboard addresses section
 */
class Address extends \Magento\Framework\View\Element\Template
{
   public $session;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
    	\Ced\TeamMember\Model\Session $session,
        Mapper $addressMapper,
        array $data = []
    ) {
    	$this->session = $session;
        parent::__construct($context, $data);
    }

    
    public function getTeamMember()
    {
    	return $this->session->getTeamMemberDataAsLoggedIn()->getFirstName();
    }
   
}
