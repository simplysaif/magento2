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
  * @package   Ced_CsSubAccount
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\CsSubAccount\Block\Customer;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Resources extends \Magento\Framework\View\Element\Template
{
    /**
     * Prepare link attributes as serialized and formatted string
     *
     * @return string
     */
    
    const XML_PATH_TEMPLATE_ALLOW_SYMLINK = 'dev/template/allow_symlink';

    /**
     * Assigned variables for view
     *
     * @var array
     */
    
    protected $customerSession;
    
    protected $_viewVars = [];

    /**
     * Base URL
     *
     * @var string
     */
    protected $_baseUrl;

    /**
     * JS URL
     *
     * @var string
     */
    protected $_jsUrl;

    /**
     * Is allowed symlinks flag
     *
     * @var bool
     */
    protected $_allowSymlinks;

    /**
     * Filesystem instance
     *
     * @var Filesystem
     */
    protected $_filesystem;

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template;

    /**
     * Template engine pool
     *
     * @var \Magento\Framework\View\TemplateEnginePool
     */
    protected $templateEnginePool;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Application state
     *
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * Root directory instance
     *
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $directory;

    /**
     * Media directory instance
     *
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    private $mediaDirectory;

    /**
     * Template context
     *
     * @var \Magento\Framework\View\Element\BlockInterface
     */
    protected $templateContext;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Magento\Framework\View\Element\Template\File\Resolver
     */
    protected $resolver;

    /**
     * @var \Magento\Framework\View\Element\Template\File\Validator
     */
    protected $validator;
    public $_objectManager;
    public $_scopeConfig;
    
    
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = [],
        \Magento\Framework\ObjectManagerInterface $objectInterface
    ) {
        parent::__construct($context, $data);
        $this->setData('area','adminhtml');
        $this->customerSession = $customerSession;
        $this->_storeManager = $context->getStoreManager();
        $this->_objectManager = $objectInterface;
        $id = $context->getRequest()->getParam('id', false);
        $role = $this->_objectManager->create('Ced\CsSubAccount\Model\CsSubAccount')->load($id)->getRole();
        if($role)
        {
            $res = explode(',',$role);
            $this->setSelectedResources($res);
        }
        else {
            $res = array('all');
            $this->setSelectedResources($res);
        }
        
    }
    

    public function getCustomerCollection()
    {
        return $this->_objectManager->get('Magento\Customer\Model\Customer')->getCollection();
        
    }


    public function getHeader()
    {
        return  __('Assign Resources To Sub - Vendors');
    }

    protected function _prepareLayout() {
        
        $this->addChild(
                    'back_button',
                    'Magento\Backend\Block\Widget\Button',
                    [
                        'label' => __('Back'),
                        'title' => __('Back'),
                        'onclick' => 'window.location.href="'.$this->getUrl(
                            'cssubaccount/customer/index').'"',
                        'class' => 'action-back'
                    ]
                );

        $this->addChild(
                    'send_button',
                    'Magento\Backend\Block\Widget\Button',
                    [
                        'label' => __('Save'),
                        'title' => __('Save'),
                        'class' => 'action-save primary'
                    ]
                );
    
        return parent::_prepareLayout();
    }

   public function isEverythingAllowed()
    {
        return in_array('all', $this->getSelectedResources());
    }
    
    /**
     * Compare two nodes of the Resource Tree
     *
     * @param array $a
     * @param array $b
     * @return boolean
     */
    protected function _sortTree($a, $b)
    {
        return $a['sort_order']<$b['sort_order'] ? -1 : ($a['sort_order']>$b['sort_order'] ? 1 : 0);
    }
    
    /**
     * Get Node Json
     *
     * @param mixed $node
     * @param int $level
     * @return array
     */
    protected function _getNodeJson($node, $level = 0)
    {
        $item = array();
        $selres = $this->getSelectedResources();
    
        if ($level != 0) {
            $item['text'] = __((string)$node->title);
            $item['sort_order'] = isset($node->sort_order) ? (string)$node->sort_order : 0;
            $item['id'] = (string)$node->attributes()->aclpath;
    
            if (in_array($item['id'], $selres))
                $item['checked'] = true;
        }
        if (isset($node->children)) {
            $children = $node->children->children();
        } else {
            $children = $node->children();
        }
        if (empty($children)) {
            return $item;
        }
    
        if ($children) {
            $item['children'] = array();
            foreach ($children as $child) {
                if ($child->getName() != 'title' && $child->getName() != 'sort_order') {
                    if (!(string)$child->title) {
                        continue;
                    }
                    if ($level != 0) {
                        $item['children'][] = $this->_getNodeJson($child, $level+1);
                    } else {
                        $item = $this->_getNodeJson($child, $level+1);
                    }
                }
            }
            if (!empty($item['children'])) {
                usort($item['children'], array($this, '_sortTree'));
            }
        }
        return $item;
    }

    public function getTree()
    {
        
        $root = array();    
        
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_context = $this->_objectManager->create('Magento\Framework\App\Helper\Context');
        $resources = $this->_context->getScopeConfig()->getValue('vendor_acl');
        $i = 0 ; 
        foreach($resources['resources']['vendor']['children'] as $key => $value){
            $arr2 = array();
           //echo $value['title'].'--------';
            if($value['title'] === 'Dashboard and Profile'){
                continue;
            }
            if(isset($value['paths']))
            {
                $value['path']= $value['paths'];

            }
            if(isset($value['ifconfig']) && !$this->_scopeConfig->getValue($value['ifconfig'] ,\Magento\Store\Model\ScopeInterface::SCOPE_STORE) && !isset($value['dependsonparent']))
                 continue;
             elseif(isset($value['ifconfig']) && !$this->_scopeConfig->getValue($value['ifconfig'] ,\Magento\Store\Model\ScopeInterface::SCOPE_STORE)) 
            {
                
                $value = $value['dependsonparent'][$key];
            }
            
            if(isset($value['children']) && is_array($value['children']) && !empty($value['children']))
            {
                $j = 0;
                foreach($value['children'] as $key2=>$value2)
                {
                    if(isset($value2['ifconfig']) && !$this->_scopeConfig->getValue($value2['ifconfig'] ,\Magento\Store\Model\ScopeInterface::SCOPE_STORE))
                        continue;
                  $children = array('attr' => array('data-id' => $value2['path']) , 'data' => $value2['title'] , 'state' => 'open','path'=>$value2['path']);
                  $arr2[$j] = $children;
                  $j++;
                  
                }
                if(isset($value['path'])){
                if($value['path'] == "#")
                $arr = array('attr' => array('data-id' => $value['path'].$key) , 'data' => $value['title'] , 'state' => 'open','path'=>$value['path'],'children'=>$arr2);
                else 
                    $arr = array('attr' => array('data-id' => $value['path']) , 'data' => $value['title'] , 'state' => 'open','path'=>$value['path'],'children'=>$arr2);
                }
            }
            else 
            {
                
                $arr = array('attr' => array('data-id' => $value['path']) , 'data' => $value['title'] , 'state' => 'open','path'=>$value['path'],'children'=>$arr2);
            }
            
            $root[$i] =  $arr ;
            $i++;   
        }
        return $root;
    }
    
}
