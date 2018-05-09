<?php 
/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * https://cedcommerce.com/license-agreement.txt
  *
  * @category    Ced
  * @package     Ced_CsMultiSeller
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
  * @license      https://cedcommerce.com/license-agreement.txt
  */

/**
 * Producty Edit block
 *
 * @category   Ced
 * @package    Ced_CsMultiSeller
 * @author 	   CedCommerce Core Team <connect@cedcommerce.com>
 */
namespace Ced\CsMultiSeller\Block;
use Magento\Framework\UrlFactory;
class Assign extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{
	/*
	* @var PageFactory
	*/
	protected $resultPageFactory;
	
	/**
	 * @var \Magento\Framework\ObjectManagerInterface
	 */
	protected $_objectManager;
	
	protected $urlModel;
	
	protected $session;
	/**
	 * @param Context $context
	 * @param Session $customerSession
	 * @param PageFactory $resultPageFactory
	 */
	public function __construct(
			\Magento\Framework\View\Element\Template\Context $context,
			\Ced\CsMarketplace\Model\Session $customerSession,
			\Magento\Framework\ObjectManagerInterface $objectManager,
			UrlFactory $urlFactory
	){
		
		$this->session = $customerSession;
		$this->urlModel = $urlFactory;
		$this->_objectManager = $objectManager;
		parent::__construct($context,$customerSession,$objectManager,$urlFactory);
	}
	
	
	/**
	 * Get set collection of products
	 *
	 */
	public function _construct(){
		parent::_construct();
		$id = $this->getRequest()->getParam('id',0);
		if($id){
			$prod = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($id);
			$this->setProductName($prod->getName());
		}
		$this->setTitle(__('Assign Product'));
	}
	
	/**
	 * Return Back Url
	 */
	public function getBackUrl()
	{
		return $this->getUrl('*/*/index');
	}
	
	/**
	 * Return Save Url
	 */
    public function getSaveUrl()
    {
    	return $this->getUrl('*/*/duplicate', array('_current'=>true, 'back'=>null));
    } 
}
