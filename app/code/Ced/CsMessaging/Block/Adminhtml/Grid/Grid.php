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
 * @package   Ced_CsMessaging
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsMessaging\Block\Adminhtml\Grid;
 
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
 
    /**
     * @var \Cedcoss\Grid\Model\GridFactory
     */
    protected $_messagingFactory;
 
    /**
     * @var \Cedcoss\Grid\Model\Status
     */
    protected $_status;
 
    protected $backendHelper;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data            $backendHelper
<<<<<<< HEAD
=======
     * @param \Webkul\Grid\Model\GridFactory          $gridFactory
     * @param \Webkul\Grid\Model\Status               $status
>>>>>>> c04ef7ff918a8551632ae4e6a613804bef974bd0
     * @param \Magento\Framework\Module\Manager       $moduleManager
     * @param array                                   $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ced\CsMessaging\Model\MessagingFactory $messagingFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        //\Cedcoss\Grid\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        
        $this->_messagingFactory = $messagingFactory;
        // $this->_status = $status;
        $this->_scopeConfig = $scopeConfig;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }
 
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('gridGrid');
        $this->setDefaultSort('chat_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('grid_record');
    }
 
    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        
        /* 	$collection = Mage::getModel('csvendorchat/chat')->getCollection()->addFieldToFilter('vcount','1')->addFieldToFilter('role','vendor');
        $this->setCollection($collection);
    	
        return parent::_prepareCollection(); */
        
        $email=$this->_scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $collection = $this->_messagingFactory->create()->getCollection()
                                                          ->addFieldToFilter('role', 'vendor')
                                                          ->addFieldToFilter('receiver_email',$email);
        $collection->getSelect()->group('vendor_id');
        $this->setCollection($collection);
         //print_r($collection->getData());die;
        parent::_prepareCollection();
        return $this;
    }
 
    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        
        
        $this->addColumn(
            'sender_email',
            [
                'header' => __('Sender'),
                'index' => 'sender_email',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'postread',
            [
                'header' => __('Status'),
                'index' => 'postread',
                'class' => 'xxx'
            ]
        );
 
 
 
        $this->addColumn(
            'edit',
            [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getSenderId',
                'actions' => [
                    [
                        'caption' => __('Reply'),
                        'url' => [
                            'base' => '*/*/adminarea'
                        ],
                        'field' => 'sender_id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );
 
        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }
 
        return parent::_prepareColumns();
    }
 
    /**
     * @return $this
     */
   
 
    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
 
    
   
}
