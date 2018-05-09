<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ced\TeamMember\Controller\Account;

use Ced\TeamMember\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class ForgotPassword extends \Ced\TeamMember\Controller\TeamMember
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Session $teammemberSession,
        PageFactory $resultPageFactory
    ) {
        $this->session = $teammemberSession;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $teammemberSession, $resultPageFactory);
    }

    /**
     * Forgot customer password page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        /* $resultPage->getLayout()->getBlock('forgotPassword')->setEmailValue($this->session->getForgottenEmail());

        $this->session->unsForgottenEmail(); */

        return $resultPage;
    }
}
