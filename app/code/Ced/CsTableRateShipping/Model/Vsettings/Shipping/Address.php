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
 * @package     Ced_CsTableRateShipping
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

 
namespace Ced\CsTableRateShipping\Model\Vsettings\Shipping;
 
use Magento\Framework\Api\AttributeValueFactory;
 
class Address extends \Ced\CsMarketplace\Model\FlatAbstractModel
{
    protected $_code = 'address';
    protected $_fields = array();
    protected $_codeSeparator = '-';
    protected $_objectManager;
    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        $this->_objectManager = $objectInterface;

        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
           
    }
    
    
    /**
     * Get current store
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore() 
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        if($storeId) {
            return $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStore($storeId); 
        }
        else { 
            return $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStore(); 
        }
    }
     
    /**
     * Get current store
     *
     * @return Mage_Core_Model_Store
     */
    public function getStoreId() 
    {
        return $this->getStore()->getId();
    }
    
    
    /**
     * Get the code
     *
     * @return string
     */
    public function getCode() 
    {
        return $this->_code;
    }
    
    /**
     * Get the code separator
     *
     * @return string
     */
    public function getCodeSeparator() 
    {
        return $this->_codeSeparator;
    }
    
    /**
     *  Retreive input fields
     *
     * @return array
     */
    public function getFields() 
    {
        $this->_fields = array();
        $this->_fields['country_id'] = array('type'=>'select','required'=>true,'values'=> $this->_objectManager->get('Magento\Config\Model\Config\Source\Locale\Country')->toOptionArray());
        $this->_fields['region_id'] = array('type'=>'select','required'=>true,'values'=>array(array('label'=>__('Please select region, state or province'),'value'=>'')));
        $this->_fields['region'] = array('type'=>'text','required'=>true);
        $this->_fields['region']['after_element_html'] = "Note: Please leave blank or enter * for all regions.";
        $this->_fields['postcode'] = array('type'=>'text','required'=>true);
        $this->_fields['postcode']['after_element_html']="";
        $this->_fields['condition_name'] = array('type'=>'select' ,'values'=>$this->_objectManager->get('Magento\OfflineShipping\Model\Config\Source\Tablerate')->toOptionArray());
        $this->_fields['condition_value'] = array('type'=>'text','required'=>true, 'class' => 'validate-not-negative-number');
        $this->_fields['price'] = array('type'=>'text','required'=>true, 'class' => 'validate-not-negative-number');
        return $this->_fields;
    }
    
    /**
     * Retreive labels
     *
     * @param  string $key
     * @return string
     */
    public function getLabel($key) 
    {
        switch($key) {
        case 'label'  :  
            return __('Origin Address Details'); break;
        case 'country_id' : 
            return __('Country'); break;
        case 'region_id' : 
            return __('State/Province'); break;
        case 'region' : 
            return ""; break;
        case 'postcode' : 
            return __('Zip/Postal Code'); break;
        case 'condition_name' : 
            return __('Condition Name'); break;
        case 'condition_value' : 
            return __('Condition Value'); break;
        case 'dest_zip' : 
            return __('Destination ZIP Code'); break;
        case 'price' : 
            return __('Price'); break;
        default : 
            return __($key); break;
        }
    }
}
