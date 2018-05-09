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
 * @package     Ced_CsDeal
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 

namespace Ced\CsDeal\Block;
use Ced\CsMarketplace\Model\System\Config\Source\Vproducts\Type;
use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;

class Listi extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{
	protected $_filtercollection;
	protected $_type;
	protected $_dealFactory;
	const XML_PATH_TEMPLATE_ALLOW_SYMLINK = 'dev/template/allow_symlink';
	protected $customerSession;
	protected $_viewVars = [];
	protected $_baseUrl;
	protected $_jsUrl;
	public $_scopeConfig;
	protected $_allowSymlinks;
	protected $_filesystem;
	protected $_template;
	protected $templateEnginePool;
	protected $_storeManager;
	protected $_appState;
	protected $directory;
	private $mediaDirectory;
	protected $templateContext;
	protected $pageConfig;
	protected $resolver;
	protected $validator;
	protected $_customerFactory;
	protected $_objectManager;
	protected $urlModel;

	public function __construct(
			\Magento\Backend\Block\Template\Context $context,
			\Magento\Customer\Model\Session $customerSession,
			\Magento\Framework\ObjectManagerInterface $objectManager,
			UrlFactory $urlFactory,
			\Magento\Backend\Helper\Data $backendHelper,
			\Magento\Customer\Model\CustomerFactory $customerFactory,
			\Ced\CsDeal\Model\DealsettingFactory $dealFactory,
			array $data = []
	) {
		
		$this->_customerFactory = $customerFactory;
		$this->_messagingFactory = $messagingFactory;
		$this->_objectManager = $objectManager;
		$this->urlModel = $urlFactory;
		$this->customerSession = $customerSession;
		$this->validator = $context->getValidator();
        $this->resolver = $context->getResolver();
        $this->_filesystem = $context->getFilesystem();
        $this->templateEnginePool = $context->getEnginePool();
        $this->_storeManager = $context->getStoreManager();
        $this->_appState = $context->getAppState();
        $this->templateContext = $this;
        $this->pageConfig = $context->getPageConfig();
        parent::__construct($context, $customerSession, $objectManager, $urlFactory);
     
	}
}
