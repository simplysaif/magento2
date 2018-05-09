<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced
 * @package     Ced_Blog
 * @author   	CedCommerce Magento Core Team <Ced_MagentoCoreTeam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Ced\Blog\Controller\Adminhtml\Category;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;

/**
 *  category profile  Controller
 *
 * @author     CedCommerce Magento Core Team <Ced_MagentoCoreTeam@cedcommerce.com>
 */
class Url extends \Magento\Backend\App\AbstractAction
{
	/**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
	
	/**
     *
     * @var \Magento\Framework\App\ObjectManager $objectManager
     */
    protected $_objectManager;

    /**
     * Url constructor.
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {

    	parent::__construct($context);
    	$this->resultPageFactory = $resultPageFactory;
    }
	/**
	 * @param execute
	 */
	 public function execute()
	 {
	 	$url_key = $this->getRequest()->getParam('url_key');

	 	$collection = $this->_objectManager->create('Ced\Blog\Model\Blogcat')->getCollection()
	 				  ->addFieldToFilter('url',$url_key);
	 

	 }
}