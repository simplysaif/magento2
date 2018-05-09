<?php 
namespace Ced\CsMarketplace\Controller\Vshops;


use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
	/**
	 * @var PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * @param Context $context
	 * @param PageFactory $resultPageFactory
	 */
	public function __construct(
		Context $context,
		PageFactory $resultPageFactory
	) {
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
	}
	/**
	 * Index action
	 *
	 * @return void
	 */
	public function execute()
	{
		
		if($this->_objectManager->get('csmarketplace/helper/acl')->isEnabled()){
			$this->_forward('noRoute');
			return;
		}
		$resultPage = $this->resultPageFactory->create();
		return $resultPage;
	}
}
?>