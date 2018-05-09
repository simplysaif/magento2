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

namespace Ced\CsTableRateShipping\Model\Resource\Carrier;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DirectoryList;
class Tablerate extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Import table rates website ID
     *
     * @var int
     */
    protected $_importWebsiteId     = 0;

    /**
     * Errors in import process
     *
     * @var array
     */
    protected $_importErrors        = array();

    /**
     * Count of imported table rates
     *
     * @var int
    */
    protected $_importedRows        = 0;

    /**
     * Array of unique table rate keys to protect from duplicates
     *
     * @var array
     */
    protected $_importUniqueHash    = array();

    /**
     * Array of countries keyed by iso2 code
     *
     * @var array
    */
    protected $_importIso2Countries;

    /**
     * Array of countries keyed by iso3 code
     *
     * @var array
     */
    protected $_importIso3Countries;

    /**
     * Associative array of countries and regions
     * [country_id][region_code] = region_id
     *
     * @var array
     */
    protected $_importRegions;

    /**
     * Import Table Rate condition name
     *
     * @var string
     */
    protected $_importConditionName;

    /**
     * Array of condition full names
     *
     * @var array
     */
    protected $_conditionFullNames  = array();

    /**
     * Define main table and id field name
     *
     * @return void
    */

    
    protected $_coreConfig;
    
    protected $_logger;
    
    protected $_storeManager;
    
    protected $_carrierTablerate;
    
    protected $_countryCollectionFactory;
    
    protected $_regionCollectionFactory;
    
    protected $_filesystem;
    
    protected $_customerSession;
    protected $_objectManager;
    
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $coreConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\OfflineShipping\Model\Carrier\Tablerate $carrierTablerate,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Session $customerSession,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_coreConfig = $coreConfig;
        $this->_logger = $logger;
        $this->_storeManager = $storeManager;
        $this->_carrierTablerate = $carrierTablerate;
        $this->_countryCollectionFactory = $countryCollectionFactory;
        $this->_regionCollectionFactory = $regionCollectionFactory;
        $this->_filesystem = $filesystem;
        $this->_objectManager = $objectManager;
        $this->_customerSession = $customerSession;
    }
    
    protected function _construct()
    {
        
        $this->_init('shipping_tablerate', 'pk');
    }

    /**
     * Return table rate array or false by rate request
     *
     * @param  Mage_Shipping_Model_Rate_Request $request
     * @return array|boolean
     */
    
    /**
     * Upload table rate file and import data from it
     *
     * @param  Varien_Object $object
     * @throws Mage_Core_Exception
     * @return Mage_Shipping_Model_Resource_Carrier_Tablerate
     */
    public function uploadAndImport(\Magento\Framework\DataObject $object)
    {
        if (empty($_FILES['groups']['tmp_name']['tablerate']['import'])) {
            return $this;
        }

        $csvFile = $_FILES['groups']['tmp_name']['tablerate']['import'];
        $website = $this->_storeManager->getWebsite($object->getScopeId());

        if (empty($_FILES['groups']['tmp_name']['tablerate']['import'])) {
            return $this;
        }

        $csvFile = $_FILES['groups']['tmp_name']['tablerate']['import'];
        $website = $this->_storeManager->getWebsite($object->getScopeId());

        $this->_importWebsiteId = (int)$website->getId();
        $this->_importUniqueHash = [];
        $this->_importErrors = [];
        $this->_importedRows = 0;
        $vendorId = $this->_customerSession->getVendorId();
        $tmpDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::SYS_TMP);
        $path = $tmpDirectory->getRelativePath($csvFile);
        $stream = $tmpDirectory->openFile($path);

        // check and skip headers
        $headers = $stream->readCsv();
        if ($headers === false || count($headers) < 5) {
            $stream->close();
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid Table Rates File Format.'));
        }

        if ($object->getData('groups/tablerate/fields/condition_name/inherit') == '1') {
            $conditionName = (string)$this->_coreConfig->getValue('carriers/tablerate/condition_name', 'default');
        } else {
            $conditionName = $object->getData('groups/tablerate/fields/condition_name/value');
        }
       
        $vsetting_model =$this->_objectManager->create('Ced\CsMarketplace\Model\Vsettings');
        $conditionName = $vsetting_model->getCollection()->addFieldToFilter('vendor_id', $vendorId)->getData();
        foreach($conditionName as $key => $value){
                
            if($value['key'] == 'shipping/tablerate/condition') {
                $conditionName = $value['value'];
            }        
                
        }
        if(is_array($conditionName)){
            $conditionName = 'package_weight';
        }
        $this->_importConditionName = $conditionName;
        $connection = $this->getConnection();
        $connection->beginTransaction();

        try {
            $rowNumber = 1;
            $importData = [];

            $this->_loadDirectoryCountries();
            $this->_loadDirectoryRegions();
            // delete old data by website and condition name
            $condition = [
                'website_id = ?' => $this->_importWebsiteId,
                'condition_name = ?' => $this->_importConditionName,
                'vendor_id = ?' => $vendorId
            ];
            $connection->delete($this->getMainTable(), $condition);
            while (false !== ($csvLine = $stream->readCsv())) {
                $rowNumber++;
                if (empty($csvLine)) {
                    continue;
                }

                $row = $this->_getImportRow($csvLine, $rowNumber);
                array_unshift($row, $vendorId);
                if ($row !== false) {
                    $importData[] = $row;
                }

                if (count($importData) == 5000) {
                    $this->_saveImportData($importData);
                    $importData = [];
                }
            }
            $this->_saveImportData($importData);
            $stream->close();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $connection->rollback();
            $stream->close();
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        } catch (\Exception $e) {
            $connection->rollback();
            $stream->close();
            $this->_logger->critical($e);
            //print_r($e->getMessage());
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Something went wrong while importing table rates.')
            );
        }

            $connection->commit();

        if ($this->_importErrors) {
            $error = __(
                'We couldn\'t import this file because of these errors: %1',
                implode(" \n", $this->_importErrors)
            );
            throw new \Magento\Framework\Exception\LocalizedException($error);
        }

            return $this;
    }
    /**
     * Load directory countries
     *
     * @return Mage_Shipping_Model_Resource_Carrier_Tablerate
     */
    protected function _loadDirectoryCountries()
    {
        if ($this->_importIso2Countries !== null && $this->_importIso3Countries !== null) {
            return $this;
        }

        $this->_importIso2Countries = [];
        $this->_importIso3Countries = [];

        /**
 * @var $collection \Magento\Directory\Model\ResourceModel\Country\Collection 
*/
        $collection = $this->_countryCollectionFactory->create();
        foreach ($collection->getData() as $row) {
            $this->_importIso2Countries[$row['iso2_code']] = $row['country_id'];
            $this->_importIso3Countries[$row['iso3_code']] = $row['country_id'];
        }

        return $this;
    }

    /**
     * Load directory regions
     *
     * @return Mage_Shipping_Model_Resource_Carrier_Tablerate
     */
    protected function _loadDirectoryRegions()
    {
        if ($this->_importRegions !== null) {
            return $this;
        }

        $this->_importRegions = [];

        /**
 * @var $collection \Magento\Directory\Model\ResourceModel\Region\Collection 
*/
        $collection = $this->_regionCollectionFactory->create();
        foreach ($collection->getData() as $row) {
            $this->_importRegions[$row['country_id']][$row['code']] = (int)$row['region_id'];
        }

        return $this;
    }
    
    /**
     * Return import condition full name by condition name code
     *
     * @param  string $conditionName
     * @return string
     */
    protected function _getConditionFullName($conditionName)
    {
        if (!isset($this->_conditionFullNames[$conditionName])) {
            $name = $this->_carrierTablerate->getCode('condition_name_short', $conditionName);
            $this->_conditionFullNames[$conditionName] = $name;
        }

        return $this->_conditionFullNames[$conditionName];
    }

    /**
     * Validate row for import and return table rate array or false
     * Error will be add to _importErrors array
     *
     * @param  array $row
     * @param  int   $rowNumber
     * @return array|false
     */
    protected function _getImportRow($row, $rowNumber = 0)
    {
        // validate row
        if (count($row) < 5) {
            $this->_importErrors[] = __('Invalid Table Rates format in the Row #%s', $rowNumber);
            return false;
        }

        // strip whitespace from the beginning and end of each row
        foreach ($row as $k => $v) {
            $row[$k] = trim($v);
        }

        // validate country
        if (isset($this->_importIso2Countries[$row[0]])) {
            $countryId = $this->_importIso2Countries[$row[0]];
        } elseif (isset($this->_importIso3Countries[$row[0]])) {
            $countryId = $this->_importIso3Countries[$row[0]];
        } elseif ($row[0] == '*' || $row[0] == '') {
            $countryId = '0';
        } else {
            $this->_importErrors[] = __('Invalid Country "%s" in the Row #%s.', $row[0], $rowNumber);
            return false;
        }
        
        // validate region
        if ($countryId != '0' && isset($this->_importRegions[$countryId][$row[1]])) {
            $regionId = $this->_importRegions[$countryId][$row[1]];
        } elseif ($row[1] == '*' || $row[1] == '') {
            $regionId = 0;
        } else {
            $this->_importErrors[] = __('Invalid Region/State "%s" in the Row #%s.', $row[1], $rowNumber);
            return false;
        }
        
        // detect zip code
        if ($row[2] == '*' || $row[2] == '') {
            $zipCode = '*';
        } else {
            $zipCode = $row[2];
        }
        // validate condition value
        $value = $this->_parseDecimalValue($row[3]);
         
        if ($value === false) {
            $this->_importErrors[] = __(
                'Please correct %1 "%2" in the Row #%3.',
                $this->_getConditionFullName($this->_importConditionName),
                $row[3],
                $rowNumber
            );
            return false;
        }
        
        
        if ($value === false) {
            $this->_importErrors[] = __('Invalid %s "%s" in the Row #%s.', $this->_getConditionFullName($this->_importConditionName), $row[3], $rowNumber);
            return false;
        }
        
        // validate price
        $price = $this->_parseDecimalValue($row[4]);
        if ($price === false) {
            $this->_importErrors[] = __('Invalid Shipping Price "%s" in the Row #%s.', $row[4], $rowNumber);
            return false;
        }

        // protect from duplicate
        $hash = sprintf("%s-%d-%s-%F", $countryId, $regionId, $zipCode, $value);
        if (isset($this->_importUniqueHash[$hash])) {
            $this->_importErrors[] = __('Duplicate Row #%s (Country "%s", Region/State "%s", Zip "%s" and Value "%s").', $rowNumber, $row[0], $row[1], $zipCode, $value);
            return false;
        }
        $this->_importUniqueHash[$hash] = true;

        return array(
        $this->_importWebsiteId,    // website_id
        $countryId,                 // dest_country_id
        $regionId,                  // dest_region_id,
        $zipCode,                   // dest_zip
        $this->_importConditionName,// condition_name,
        $value,                     // condition_value
        $price                      // price
        );
    }

    /**
     * Save import data batch
     *
     * @param  array $data
     * @return Mage_Shipping_Model_Resource_Carrier_Tablerate
     */
    protected function _saveImportData(array $data)
    {
        
        if (!empty($data)) {
            $columns = array('vendor_id','website_id', 'dest_country_id', 'dest_region_id', 'dest_zip',
            'condition_name', 'condition_value', 'price');
            $this->getConnection()->insertArray($this->getMainTable(), $columns, $data);
            $this->_importedRows += count($data);
        }
        return $this;
    }

    /**
     * Parse and validate positive decimal value
     * Return false if value is not decimal or is not positive
     *
     * @param  string $value
     * @return bool|float
     */
    protected function _parseDecimalValue($value)
    {
        if (!is_numeric($value)) {
            return false;
        }
        $value = (double)sprintf('%.4F', $value);
        if ($value < 0.0000) {
            return false;
        }
        return $value;
    }

    /**
     * Parse and validate positive decimal value
     *
     * @see        self::_parseDecimalValue()
     * @deprecated since 1.4.1.0
     * @param      string $value
     * @return     bool|float
     */
    protected function _isPositiveDecimalNumber($value)
    {
        return $this->_parseDecimalValue($value);
    }
    
    public function getRate(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        
        if($request->getVendorId()=='admin') {
            $vendor_id=0;
        }
        else {
            $vendor_id=$request->getVendorId();
        }
        $adapter = $this->getConnection();
        $bind = array(
        ':website_id' => (int) $request->getWebsiteId(),
            ':country_id' => $request->getDestCountryId(),
            ':region_id' => (int) $request->getDestRegionId(),
            ':postcode' => $request->getDestPostcode(),
               ':vendor_id' => $vendor_id
        );
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('website_id = :website_id')
            ->where('vendor_id = :vendor_id')
            ->order(array('dest_country_id DESC', 'dest_region_id DESC', 'dest_zip DESC', 'condition_value DESC'))
            ->limit(1);
    
        // Render destination condition
        $orWhere = '(' . implode(
            ') OR (', array(
            "dest_country_id = :country_id AND dest_region_id = :region_id AND dest_zip = :postcode",
            "dest_country_id = :country_id AND dest_region_id = :region_id AND dest_zip = ''",
    
            // Handle asterix in dest_zip field
            "dest_country_id = :country_id AND dest_region_id = :region_id AND dest_zip = '*'",
            "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = '*'",
            "dest_country_id = '0' AND dest_region_id = :region_id AND dest_zip = '*'",
            "dest_country_id = '0' AND dest_region_id = 0 AND dest_zip = '*'",
    
            "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = ''",
            "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = :postcode",
            "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = '*'",
            )
        ) . ')';
        //print_r($orWhere);die;
        $select->where($orWhere);
        // Render condition by condition name
        if (is_array($request->getConditionName())) {
            $orWhere = array();
            $i = 0;
            foreach ($request->getConditionName() as $conditionName) {
                $bindNameKey  = sprintf(':condition_name_%d', $i);
                $bindValueKey = sprintf(':condition_value_%d', $i);
                $orWhere[] = "(condition_name = {$bindNameKey} AND condition_value <= {$bindValueKey})";
                $bind[$bindNameKey] = $conditionName;
                $bind[$bindValueKey] = $request->getData($conditionName);
                $i++;
            }
    
            if ($orWhere) {
                $select->where(implode(' OR ', $orWhere));
            }
        } else {
            $bind[':condition_name']  = $request->getConditionName();
            $bind[':condition_value'] = $request->getData($request->getConditionName());
            $select->where('condition_name = :condition_name');
            $select->where('condition_value <= :condition_value');
        }
        
        $result = $adapter->fetchRow($select, $bind);
    
    
        // Normalize destination zip code
        if ($result && $result['dest_zip'] == '*') {
            $result['dest_zip'] = '';
        }
        
        return $result;
    }
    
}
