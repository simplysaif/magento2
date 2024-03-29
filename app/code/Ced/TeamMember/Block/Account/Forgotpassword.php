<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ced\TeamMember\Block\Account;

use Magento\Customer\Model\Url;
use Magento\Framework\View\Element\Template;

/**
 * Customer account navigation sidebar
 */
class Forgotpassword extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Url
     */
    protected $customerUrl;

    /**
     * @param Template\Context $context
     * @param Url $customerUrl
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Url $customerUrl,
        array $data = []
    ) {
        $this->customerUrl = $customerUrl;
        parent::__construct($context, $data);
    }

    /**
     * Get login URL
     *
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->getUrl('teammember/account/login');
    }
}
