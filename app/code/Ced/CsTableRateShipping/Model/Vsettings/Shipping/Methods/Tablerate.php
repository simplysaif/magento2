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

namespace Ced\CsTableRateShipping\Model\Vsettings\Shipping\Methods;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Framework\UrlInterface;
class Tablerate extends \Ced\CsMultiShipping\Model\Vsettings\Shipping\Methods\AbstractModel
{
    protected $_code = 'tablerate';
    protected $_codeSeparator = '-';
    protected $_fields = array();
    protected $_scopeConfig;
    protected $_countryFactory;
    protected $_objectManager;
    protected $_storeManager;
    protected $urlBuilder;
    /**
     * Retreive input fields
     *
     * @return array
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Directory\Model\Config\Source\CountryFactory $countryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_countryFactory = $countryFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_objectManager = $objectManager;
        $this->urlBuilder = $urlBuilder;
    
    }
    
    public function getFields() 
    {
        $fields['active'] = array('type'=>'select',
                                'required'=>true,
                                'values'=>array(
                                    array('label'=>__('Yes'),'value'=>1),
                                    array('label'=>__('No'),'value'=>0)
                                )
                            );
        
        $condition = $this->_objectManager->get('Magento\OfflineShipping\Model\Config\Source\Tablerate')->toOptionArray();
        $fields['condition'] = array('type'=>'select',
                                    'values'=>$condition
                                    );    
        
        $url = $this->urlBuilder->getUrl('cstablerateshipping/export/exportTablerates', array('condition_name',$condition));
        $fields['export'] = array('type'=>'text', 'class'=>'hide', 'after_element_html'=>'<button onclick="window.location=\''.$url.'\'" class="btn btn-primary uptransform" type="button">Export CSV</button>');
         
        $fields['import'] = array('type'=>'file', 'name'=>"groups[tablerate][fields][import][value]");

        return $fields;
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
        case 'label' : 
            return __('Table Rate Shipping');break;
        case 'condition': 
            return __('Condition');break;
        case 'export': 
            return __('Export');break;
        case 'import': 
            return __('Import');break;
        case 'active': 
            return __('Active');break;
        default : 
            return parent::getLabel($key); break;
        }
    }
}
