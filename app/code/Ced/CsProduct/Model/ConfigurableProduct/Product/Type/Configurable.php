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
 * @package     Ced_CsProduct
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsProduct\Model\ConfigurableProduct\Product\Type;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Config;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Catalog\Model\Product\Gallery\ReadHandler as GalleryReadHandler;

/**
 * Configurable product type implementation
 *
 * This type builds in product attributes and existing simple products
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Configurable extends \Magento\Catalog\Model\Product\Type\AbstractType
{
    /**
     * Product type code
     */
    const TYPE_CODE = 'configurable';

    /**
     * Cache key for Used Product Attribute Ids
     *
     * @var string
     */
    protected $usedProductAttributeIds = '_cache_instance_used_product_attribute_ids';

    /**
     * Cache key for Used Product Attributes
     *
     * @var string
     */
    protected $usedProductAttributes = '_cache_instance_used_product_attributes';

    /**
     * Cache key for Used Attributes
     *
     * @var string
     */
    protected $_usedAttributes = '_cache_instance_used_attributes';

    /**
     * Cache key for configurable attributes
     *
     * @var string
     */
    protected $_configurableAttributes = '_cache_instance_configurable_attributes';

    /**
     * Cache key for Used product ids
     *
     * @var string
     */
    protected $_usedProductIds = '_cache_instance_product_ids';

    /**
     * Cache key for used products
     *
     * @var string
     */
    protected $_usedProducts = '_cache_instance_products';

    /**
     * Product is composite
     *
     * @var bool
     */
    protected $_isComposite = true;

    /**
     * Product is configurable
     *
     * @var bool
     */
    protected $_canConfigure = true;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Catalog product type configurable
     *
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
     */
    protected $_catalogProductTypeConfigurable;

    /**
     * Attribute collection factory
     *
     * @var
     * \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute\CollectionFactory
     */
    protected $_attributeCollectionFactory;

    /**
     * Product collection factory
     *
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * Configurable attribute factory
     *
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory
     */
    protected $configurableAttributeFactory;

    /**
     * Eav attribute factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory
     */
    protected $_eavAttributeFactory;

    /**
     * Type configurable factory
     *
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory
     */
    protected $typeConfigurableFactory;

    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var \Magento\Framework\Cache\FrontendInterface
     */
    private $cache;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var GalleryReadHandler
     */
    private $productGalleryReadHandler;

    /**
     * @var Config
     */
    private $catalogConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * Configurable constructor.
     * @param \Magento\Catalog\Model\Product\Option $catalogProductOption
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\Product\Type $catalogProductType
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Psr\Log\LoggerInterface $logger
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory $typeConfigurableFactory
     * @param \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $eavAttributeFactory
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory $configurableAttributeFactory
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $catalogProductTypeConfigurable
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param \Magento\Framework\Cache\FrontendInterface|null $cache
     * @param \Magento\Customer\Model\Session|null $customerSession
     */
    public function __construct(
        \Magento\Catalog\Model\Product\Option $catalogProductOption,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Product\Type $catalogProductType,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory $typeConfigurableFactory,
        \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $eavAttributeFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory $configurableAttributeFactory,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\CollectionFactory $productCollectionFactory,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $catalogProductTypeConfigurable,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor,
        \Magento\Framework\Cache\FrontendInterface $cache = null,
        \Magento\Customer\Model\Session $customerSession = null
    ) {
        $this->typeConfigurableFactory = $typeConfigurableFactory;
        $this->_eavAttributeFactory = $eavAttributeFactory;
        $this->configurableAttributeFactory = $configurableAttributeFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_attributeCollectionFactory = $attributeCollectionFactory;
        $this->_catalogProductTypeConfigurable = $catalogProductTypeConfigurable;
        $this->_scopeConfig = $scopeConfig;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->cache = $cache;
        $this->customerSession = $customerSession;
        parent::__construct(
            $catalogProductOption,
            $eavConfig,
            $catalogProductType,
            $eventManager,
            $fileStorageDb,
            $filesystem,
            $coreRegistry,
            $logger,
            $productRepository
        );

    }

    /**
     * @deprecated
     * @return \Magento\Framework\Cache\FrontendInterface
     */
    private function getCache()
    {
        if (null === $this->cache) {
            $this->cache = ObjectManager::getInstance()->get(\Magento\Framework\Cache\FrontendInterface::class);
        }
        return $this->cache;
    }

    /**
     * @deprecated
     * @return \Magento\Customer\Model\Session
     */
    private function getCustomerSession()
    {
        if (null === $this->customerSession) {
            $this->customerSession = ObjectManager::getInstance()->get(\Magento\Customer\Model\Session::class);
        }
        return $this->customerSession;
    }

    /**
     * Return relation info about used products
     *
     * @return \Magento\Framework\DataObject Object with information data
     */
    public function getRelationInfo()
    {
        $info = new \Magento\Framework\DataObject();
        $info->setTable(
            'catalog_product_super_link'
        )->setParentFieldName(
            'parent_id'
        )->setChildFieldName(
            'product_id'
        );
        return $info;
    }

    /**
     * Retrieve Required children ids
     * Return grouped array, ex array(
     *   group => array(ids)
     * )
     *
     * @param  array|int $parentId
     * @param  bool $required
     * @return array
     */
    public function getChildrenIds($parentId, $required = true)
    {
        return $this->_catalogProductTypeConfigurable->getChildrenIds($parentId, $required);
    }

    /**
     * Retrieve parent ids array by required child
     *
     * @param  int|array $childId
     * @return array
     */
    public function getParentIdsByChild($childId)
    {
        return $this->_catalogProductTypeConfigurable->getParentIdsByChild($childId);
    }

    /**
     * Check attribute availability for super product creation
     *
     * @param  \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
     * @return bool
     */
    public function canUseAttribute(\Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute)
    {
        return $attribute->getIsGlobal() == \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL &&
            $attribute->getIsVisible() &&
            $attribute->usesSource() &&
            $attribute->getIsUserDefined();
    }

    /**
     * Declare attribute identifiers used for assign subproducts
     *
     * @param   array $ids
     * @param   \Magento\Catalog\Model\Product $product
     * @return  \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     * @deprecated use \Magento\ConfigurableProduct\Model\Product\Type\Configurable::setUsedProductAttributes instead
     */
    public function setUsedProductAttributeIds($ids, $product)
    {
        $usedProductAttributes = [];
        $configurableAttributes = [];

        foreach ($ids as $attributeId) {
            $usedProductAttributes[] = $this->getAttributeById($attributeId, $product);
            $configurableAttributes[] = $this->configurableAttributeFactory->create()->setProductAttribute(
                $this->getAttributeById($attributeId, $product)
            );
        }
        $product->setData($this->usedProductAttributes, $usedProductAttributes);
        $product->setData($this->usedProductAttributeIds, $ids);
        $product->setData($this->_configurableAttributes, $configurableAttributes);

        return $this;
    }

    /**
     * Set list of used attributes to product
     *
     * @param ProductInterface $product
     * @param array $ids
     * @return $this
     */
    public function setUsedProductAttributes(ProductInterface $product, array $ids)
    {
        $usedProductAttributes = [];

        foreach ($ids as $attributeId) {
            $usedProductAttributes[] = $this->getAttributeById($attributeId, $product);
        }
        $product->setData($this->usedProductAttributes, $usedProductAttributes);
        $product->setData($this->usedProductAttributeIds, $ids);

        return $this;
    }

    /**
     * Retrieve identifiers of used product attributes
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getUsedProductAttributeIds($product)
    {
        if (!$product->hasData($this->usedProductAttributeIds)) {
            $usedProductAttributeIds = [];
            foreach ($this->getUsedProductAttributes($product) as $attribute) {
                $usedProductAttributeIds[] = $attribute->getId();
            }
            $product->setData($this->usedProductAttributeIds, $usedProductAttributeIds);
        }
        return $product->getData($this->usedProductAttributeIds);
    }

    /**
     * Retrieve used product attributes
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getUsedProductAttributes($product)
    {
        if (!$product->hasData($this->usedProductAttributes)) {
            $usedProductAttributes = [];
            $usedAttributes = [];
            foreach ($this->getConfigurableAttributes($product) as $attribute) {
                if (!is_null($attribute->getProductAttribute())) {
                    $id = $attribute->getProductAttribute()->getId();
                    $usedProductAttributes[$id] = $attribute->getProductAttribute();
                    $usedAttributes[$id] = $attribute;
                }
            }
            $product->setData($this->_usedAttributes, $usedAttributes);
            $product->setData($this->usedProductAttributes, $usedProductAttributes);
        }
        return $product->getData($this->usedProductAttributes);
    }

    /**
     * Retrieve configurable attributes data
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute[]
     */
    public function getConfigurableAttributes($product)
    {
        \Magento\Framework\Profiler::start(
            'CONFIGURABLE:' . __METHOD__,
            ['group' => 'CONFIGURABLE', 'method' => __METHOD__]
        );
        if (!$product->hasData($this->_configurableAttributes)) {
            $metadata = $this->getMetadataPool()->getMetadata(ProductInterface::class);
            $productId = $product->getData($metadata->getLinkField());
            $cacheId =  __CLASS__ . $productId . '_' . $product->getStoreId();
            $configurableAttributes = $this->getCache()->load($cacheId);
            $configurableAttributes = $this->hasCacheData($configurableAttributes);
            if ($configurableAttributes) {
                $configurableAttributes->setProductFilter($product);
            } else {
                $configurableAttributes = $this->getConfigurableAttributeCollection($product);
                $this->extensionAttributesJoinProcessor->process($configurableAttributes);
                $configurableAttributes->orderByPosition()->load();
                $this->getCache()->save(
                    serialize($configurableAttributes),
                    $cacheId,
                    array_merge($product->getIdentities(), [self::TYPE_CODE . '_' . $productId])
                );
            }
            $product->setData($this->_configurableAttributes, $configurableAttributes);
        }
        \Magento\Framework\Profiler::stop('CONFIGURABLE:' . __METHOD__);
        return $product->getData($this->_configurableAttributes);
    }

    /**
     * @param mixed $configurableAttributes
     * @return bool
     */
    protected function hasCacheData($configurableAttributes)
    {
        $configurableAttributes = $configurableAttributes ?: unserialize($configurableAttributes);
        if (is_array($configurableAttributes) && count($configurableAttributes)) {
            foreach ($configurableAttributes as $attribute) {
                /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute $attribute */
                if ($attribute->getData('options')) {
                    return $configurableAttributes;
                }
            }
        }
        return false;
    }

    /**
     * Reset the cached configurable attributes of a product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    public function resetConfigurableAttributes($product)
    {
        $metadata = $this->getMetadataPool()->getMetadata(ProductInterface::class);
        $productId = $product->getData($metadata->getLinkField());
        $product->unsetData($this->_configurableAttributes);
        $cacheId =  __CLASS__ . $productId . '_' . $product->getStoreId();
        $this->getCache()->remove($cacheId);
        $this->getCache()->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, [self::TYPE_CODE . '_' . $productId]);

        return $this;
    }

    /**
     * Retrieve Configurable Attributes as array
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getConfigurableAttributesAsArray($product)
    {
        $res = [];
        foreach ($this->getConfigurableAttributes($product) as $attribute) {
            $eavAttribute = $attribute->getProductAttribute();
            $storeId = 0;
            if ($product->getStoreId() !== null) {
                $storeId = $product->getStoreId();
            }
            $eavAttribute->setStoreId($storeId);
            /* @var $attribute \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute */
            $res[$eavAttribute->getId()] = [
                'id' => $attribute->getId(),
                'label' => $attribute->getLabel(),
                'use_default' => $attribute->getUseDefault(),
                'position' => $attribute->getPosition(),
                'values' => $attribute->getOptions() ? $attribute->getOptions() : [],
                'attribute_id' => $eavAttribute->getId(),
                'attribute_code' => $eavAttribute->getAttributeCode(),
                'frontend_label' => $eavAttribute->getFrontend()->getLabel(),
                'store_label' => $eavAttribute->getStoreLabel(),
                'options' => $eavAttribute->getSource()->getAllOptions(),
            ];
        }
        return $res;
    }

    /**
     * Retrieve configurable attribute collection
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute\Collection
     */
    public function getConfigurableAttributeCollection(\Magento\Catalog\Model\Product $product)
    {
        return $this->_attributeCollectionFactory->create()->setProductFilter($product);
    }

    /**
     * Retrieve subproducts identifiers
     *
     * @deprecated
     * @param  \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getUsedProductIds($product)
    {
        if (!$product->hasData($this->_usedProductIds)) {
            $usedProductIds = [];
            foreach ($this->getUsedProducts($product) as $product) {
                $usedProductIds[] = $product->getId();
            }
            $product->setData($this->_usedProductIds, $usedProductIds);
        }
        return $product->getData($this->_usedProductIds);
    }

    /**
     * Retrieve array of "subproducts"
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @param  array $requiredAttributeIds
     * @return array
     */
    public function getUsedProducts($product, $requiredAttributeIds = null)
    {
        \Magento\Framework\Profiler::start(
            'CONFIGURABLE:' . __METHOD__,
            ['group' => 'CONFIGURABLE', 'method' => __METHOD__]
        );
        if (!$product->hasData($this->_usedProducts)) {
            $metadata = $this->getMetadataPool()->getMetadata(ProductInterface::class);
            $productId = $product->getData($metadata->getLinkField());

            $key = md5(
                implode(
                    '_',
                    [
                        __METHOD__,
                        $productId,
                        $product->getStoreId(),
                        $this->getCustomerSession()->getCustomerGroupId(),
                        json_encode($requiredAttributeIds)
                    ]
                )
            );
            $collection = $this->getUsedProductCollection($product);
            $data = unserialize($this->getCache()->load($key));
            if (!empty($data)) {
                $usedProducts = [];
                foreach ($data as $item) {
                    $productItem = $collection->getNewEmptyItem()->setData($item);
                    $usedProducts[] = $productItem;
                }
            } else {
                $collection
                    ->setFlag('has_stock_status_filter', true)
                    ->addAttributeToSelect($this->getCatalogConfig()->getProductAttributes())
                    ->addFilterByRequiredOptions()
                    ->setStoreId($product->getStoreId());

                $requiredAttributes = ['name', 'price', 'weight', 'image', 'thumbnail', 'status', 'media_gallery'];
                foreach ($requiredAttributes as $attributeCode) {
                    $collection->addAttributeToSelect($attributeCode);
                }
                foreach ($this->getUsedProductAttributes($product) as $usedProductAttribute) {
                    $collection->addAttributeToSelect($usedProductAttribute->getAttributeCode());
                }
                $collection->addMediaGalleryData();
                $collection->addTierPriceData();
                $usedProducts = $collection->getItems();

                $this->getCache()->save(
                    serialize(array_map(
                        function ($item) {
                            return $item->getData();
                        },
                        $usedProducts
                    )),
                    $key,
                    array_merge(
                        $product->getIdentities(),
                        [
                            \Magento\Catalog\Model\Category::CACHE_TAG,
                            \Magento\Catalog\Model\Product::CACHE_TAG,
                            'price',
                            self::TYPE_CODE . '_' . $productId
                        ]
                    )
                );
            }
            $product->setData($this->_usedProducts, $usedProducts);
        }
        \Magento\Framework\Profiler::stop('CONFIGURABLE:' . __METHOD__);
        $usedProducts =  $product->getData($this->_usedProducts);
        return $usedProducts;
    }

    /**
     * Retrieve GalleryReadHandler
     *
     * @return GalleryReadHandler
     * @deprecated
     */
    protected function getGalleryReadHandler()
    {
        if ($this->productGalleryReadHandler === null) {
            $this->productGalleryReadHandler = ObjectManager::getInstance()
                ->get(GalleryReadHandler::class);
        }
        return $this->productGalleryReadHandler;
    }

    /**
     * Retrieve related products collection
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\Collection
     */
    public function getUsedProductCollection($product)
    {
        $collection = $this->_productCollectionFactory->create()->setFlag(
            'product_children',
            true
        )->setProductFilter(
            $product
        );
        if (!is_null($this->getStoreFilter($product))) {
            $collection->addStoreFilter($this->getStoreFilter($product));
        }

        return $collection;
    }

    /**
     * Before save process
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return $this
     */
    public function beforeSave($product)
    {
        $metadata = $this->getMetadataPool()->getMetadata(ProductInterface::class);
        $productId = $product->getData($metadata->getLinkField());

        $this->getCache()->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, [self::TYPE_CODE . '_' . $productId]);
        parent::beforeSave($product);

        $product->canAffectOptions(false);

        if ($product->getCanSaveConfigurableAttributes()) {
            $product->canAffectOptions(true);
            $data = $product->getConfigurableAttributesData();
            if (!empty($data)) {
                foreach ($data as $attribute) {
                    if (!empty($attribute['values'])) {
                        $product->setTypeHasOptions(true);
                        $product->setTypeHasRequiredOptions(true);
                        break;
                    }
                }
            }
        }
        foreach ($this->getConfigurableAttributes($product) as $attribute) {
            $product->setData($attribute->getProductAttribute()->getAttributeCode(), null);
        }

        return $this;
    }

    /**
     * Save configurable product depended data
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     * @throws \InvalidArgumentException
     * @deprecated the \Magento\ConfigurableProduct\Model\Product\SaveHandler::execute should be used instead
     */
    public function save($product)
    {
        parent::save($product);
        $metadata = $this->getMetadataPool()->getMetadata(ProductInterface::class);
        $cacheId =  __CLASS__ . $product->getData($metadata->getLinkField()) . '_' . $product->getStoreId();
        $this->cache->remove($cacheId);

        $extensionAttributes = $product->getExtensionAttributes();

        // this approach is needed for 3rd-party extensions which are not using extension attributes
        if (empty($extensionAttributes->getConfigurableProductOptions())) {
            $this->saveConfigurableOptions($product);
        }

        if (empty($extensionAttributes->getConfigurableProductLinks())) {
            $this->saveRelatedProducts($product);
        }
        return $this;
    }

    /**
     * Save configurable product attributes
     *
     * @param ProductInterface $product
     * @return void
     * @throws \Exception
     * @deprecated
     */
    private function saveConfigurableOptions(ProductInterface $product)
    {
        $data = $product->getConfigurableAttributesData();
        if (!$data) {
            return;
        }

        $metadata = $this->getMetadataPool()->getMetadata(ProductInterface::class);

        foreach ($data as $attributeData) {
            /** @var $configurableAttribute \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute */
            $configurableAttribute = $this->configurableAttributeFactory->create();
            if (!$product->getIsDuplicate()) {
                if (!empty($attributeData['id'])) {
                    $configurableAttribute->load($attributeData['id']);
                    $attributeData['attribute_id'] = $configurableAttribute->getAttributeId();
                } elseif (!empty($attributeData['attribute_id'])) {
                    $attribute = $this->_eavConfig->getAttribute(
                        \Magento\Catalog\Model\Product::ENTITY, $attributeData['attribute_id']
                    );
                    $attributeData['attribute_id'] = $attribute->getId();
                    if (!$this->canUseAttribute($attribute)) {
                        throw new \InvalidArgumentException(
                            'Provided attribute can not be used with configurable product'
                        );
                    }
                    $configurableAttribute->loadByProductAndAttribute($product, $attribute);
                }
            }
            unset($attributeData['id']);
            $configurableAttribute
                ->addData($attributeData)
                ->setStoreId($product->getStoreId())
                ->setProductId($product->getData($metadata->getLinkField()))
                ->save();
        }
        /** @var $configurableAttributesCollection \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute\Collection  */
        $configurableAttributesCollection = $this->_attributeCollectionFactory->create();
        $configurableAttributesCollection->setProductFilter($product);
        $configurableAttributesCollection->addFieldToFilter(
            'attribute_id',
            ['nin' => $this->getUsedProductAttributeIds($product)]
        );
        $configurableAttributesCollection->walk('delete');
    }

    /**
     * Save related products
     *
     * @param ProductInterface $product
     * @return void
     * @deprecated
     */
    private function saveRelatedProducts(ProductInterface $product)
    {
        $productIds = $product->getAssociatedProductIds();
        if (is_array($productIds)) {
            $this->typeConfigurableFactory->create()->saveProducts($product, $productIds);
        }
        $this->resetConfigurableAttributes($product);
    }

    /**
     * Check is product available for sale
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function isSalable($product)
    {
        $salable = parent::isSalable($product);

        if ($salable !== false) {
            $salable = false;
            if (!is_null($product)) {
                $this->setStoreFilter($product->getStoreId(), $product);
            }
            /** @var \Magento\Catalog\Model\Product $child */
            foreach ($this->getUsedProducts($product) as $child) {
                if ($child->isSalable()) {
                    $salable = true;
                    break;
                }
            }
        }

        return $salable;
    }

    /**
     * Check whether the product is available for sale
     * is alias to isSalable for compatibility
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsSalable($product)
    {
        return $this->isSalable($product);
    }

    /**
     * Retrieve used product by attribute values
     *  $attributesInfo = array(
     *      $attributeId => $attributeValue
     *  )
     *
     * @param  array $attributesInfo
     * @param  \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getProductByAttributes($attributesInfo, $product)
    {
        if (is_array($attributesInfo) && !empty($attributesInfo)) {
            $productCollection = $this->getUsedProductCollection($product)->addAttributeToSelect('name');
            foreach ($attributesInfo as $attributeId => $attributeValue) {
                $productCollection->addAttributeToFilter($attributeId, $attributeValue);
            }
            /** @var \Magento\Catalog\Model\Product $productObject */
            $productObject = $productCollection->getFirstItem();
            $productLinkFieldId = $productObject->getId();
            if ($productLinkFieldId) {
                return $this->productRepository->getById($productLinkFieldId);
            }

            foreach ($productCollection as $productObject) {
                $checkRes = true;
                foreach ($attributesInfo as $attributeId => $attributeValue) {
                    $code = $this->getAttributeById($attributeId, $product)->getAttributeCode();
                    if ($productObject->getData($code) != $attributeValue) {
                        $checkRes = false;
                    }
                }
                if ($checkRes) {
                    return $productObject;
                }
            }
        }
        return null;
    }

    /**
     * Retrieve Selected Attributes info
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getSelectedAttributesInfo($product)
    {
        $attributes = [];
        \Magento\Framework\Profiler::start(
            'CONFIGURABLE:' . __METHOD__,
            ['group' => 'CONFIGURABLE', 'method' => __METHOD__]
        );
        if ($attributesOption = $product->getCustomOption('attributes')) {
            $data = unserialize($attributesOption->getValue());
            $this->getUsedProductAttributeIds($product);

            $usedAttributes = $product->getData($this->_usedAttributes);

            foreach ($data as $attributeId => $attributeValue) {
                if (isset($usedAttributes[$attributeId])) {
                    /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
                    $attribute = $usedAttributes[$attributeId]->getProductAttribute();
                    $label = $attribute->getStoreLabel();
                    $value = $attribute;
                    if ($value->getSourceModel()) {
                        $value = $value->getSource()->getOptionText($attributeValue);
                    } else {
                        $value = '';
                    }

                    $attributes[] = ['label' => $label, 'value' => $value];
                }
            }
        }
        \Magento\Framework\Profiler::stop('CONFIGURABLE:' . __METHOD__);
        return $attributes;
    }

    /**
     * Prepare product and its configuration to be added to some products list.
     * Perform standard preparation process and then add Configurable specific options.
     *
     * @param \Magento\Framework\DataObject $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @param string $processMode
     * @return \Magento\Framework\Phrase|array|string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _prepareProduct(\Magento\Framework\DataObject $buyRequest, $product, $processMode)
    {
        $attributes = $buyRequest->getSuperAttribute();
        if ($attributes || !$this->_isStrictProcessMode($processMode)) {
            if (!$this->_isStrictProcessMode($processMode)) {
                if (is_array($attributes)) {
                    foreach ($attributes as $key => $val) {
                        if (empty($val)) {
                            unset($attributes[$key]);
                        }
                    }
                } else {
                    $attributes = [];
                }
            }

            $result = parent::_prepareProduct($buyRequest, $product, $processMode);
            if (is_array($result)) {
                //TODO: MAGETWO-23739 get id from _POST and retrieve product from repository immediately.

                /**
                 * $attributes = array($attributeId=>$attributeValue)
                 */
                $subProduct = true;
                if ($this->_isStrictProcessMode($processMode)) {
                    foreach ($this->getConfigurableAttributes($product) as $attributeItem) {
                        /* @var $attributeItem \Magento\Framework\DataObject */
                        $attrId = $attributeItem->getData('attribute_id');
                        if (!isset($attributes[$attrId]) || empty($attributes[$attrId])) {
                            $subProduct = null;
                            break;
                        }
                    }
                }
                if ($subProduct) {
                    $subProduct = $this->getProductByAttributes($attributes, $product);
                }

                if ($subProduct) {
                    $subProductLinkFieldId = $subProduct->getId();
                    $product->addCustomOption('attributes', serialize($attributes));
                    $product->addCustomOption('product_qty_' . $subProductLinkFieldId, 1, $subProduct);
                    $product->addCustomOption('simple_product', $subProductLinkFieldId, $subProduct);

                    $_result = $subProduct->getTypeInstance()->processConfiguration(
                        $buyRequest,
                        $subProduct,
                        $processMode
                    );
                    if (is_string($_result) && !is_array($_result)) {
                        return $_result;
                    }

                    if (!isset($_result[0])) {
                        return __('You can\'t add the item to shopping cart.')->render();
                    }

                    /**
                     * Adding parent product custom options to child product
                     * to be sure that it will be unique as its parent
                     */
                    if ($optionIds = $product->getCustomOption('option_ids')) {
                        $optionIds = explode(',', $optionIds->getValue());
                        foreach ($optionIds as $optionId) {
                            if ($option = $product->getCustomOption('option_' . $optionId)) {
                                $_result[0]->addCustomOption('option_' . $optionId, $option->getValue());
                            }
                        }
                    }

                    $productLinkFieldId = $product->getId();
                    $_result[0]->setParentProductId($productLinkFieldId)
                        ->addCustomOption('parent_product_id', $productLinkFieldId);
                    if ($this->_isStrictProcessMode($processMode)) {
                        $_result[0]->setCartQty(1);
                    }
                    $result[] = $_result[0];
                    return $result;
                } else {
                    if (!$this->_isStrictProcessMode($processMode)) {
                        return $result;
                    }
                }
            }
        }

        return $this->getSpecifyOptionMessage()->render();
    }

    /**
     * Check if product can be bought
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkProductBuyState($product)
    {
        parent::checkProductBuyState($product);
        $option = $product->getCustomOption('info_buyRequest');
        if ($option instanceof \Magento\Quote\Model\Quote\Item\Option) {
            $buyRequest = new \Magento\Framework\DataObject(unserialize($option->getValue()));
            $attributes = $buyRequest->getSuperAttribute();
            if (is_array($attributes)) {
                foreach ($attributes as $key => $val) {
                    if (empty($val)) {
                        unset($attributes[$key]);
                    }
                }
            }
            if (empty($attributes)) {
                throw new \Magento\Framework\Exception\LocalizedException($this->getSpecifyOptionMessage());
            }
        }
        return $this;
    }

    /**
     * Retrieve message for specify option(s)
     *
     * @return \Magento\Framework\Phrase
     */
    public function getSpecifyOptionMessage()
    {
        return __('You need to choose options for your item.');
    }

    /**
     * Prepare additional options/information for order item which will be
     * created from this product
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getOrderOptions($product)
    {
        $options = parent::getOrderOptions($product);
        $options['attributes_info'] = $this->getSelectedAttributesInfo($product);
        if ($simpleOption = $product->getCustomOption('simple_product')) {
            $options['simple_name'] = $simpleOption->getProduct()->getName();
            $options['simple_sku'] = $simpleOption->getProduct()->getSku();
        }

        $options['product_calculations'] = self::CALCULATE_PARENT;
        $options['shipment_type'] = self::SHIPMENT_TOGETHER;

        return $options;
    }

    /**
     * Check is virtual product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function isVirtual($product)
    {
        if ($productOption = $product->getCustomOption('simple_product')) {
            if ($optionProduct = $productOption->getProduct()) {
                /* @var $optionProduct \Magento\Catalog\Model\Product */
                return $optionProduct->isVirtual();
            }
        }
        return parent::isVirtual($product);
    }

    /**
     * Return true if product has options
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function hasOptions($product)
    {
        if ($product->getOptions()) {
            return true;
        }

        $attributes = $this->getConfigurableAttributes($product);
        if (count($attributes)) {
            return true;
        }

        return false;
    }

    /**
     * Return product weight based on simple product
     * weight or configurable product weight
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return float
     */
    public function getWeight($product)
    {
        if ($product->hasCustomOptions() && ($simpleProductOption = $product->getCustomOption('simple_product'))) {
            $simpleProduct = $simpleProductOption->getProduct();
            if ($simpleProduct) {
                return $simpleProduct->getWeight();
            }
        }

        return $product->getData('weight');
    }

    /**
     * Implementation of product specify logic of which product needs to be assigned to option.
     * For example if product which was added to option already removed from catalog.
     *
     * @param  \Magento\Catalog\Model\Product|null $optionProduct
     * @param  \Magento\Quote\Model\Quote\Item\Option $option
     * @param  \Magento\Catalog\Model\Product|null $product
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function assignProductToOption($optionProduct, $option, $product)
    {
        if ($optionProduct) {
            $option->setProduct($optionProduct);
        } else {
            $option->getItem()->setHasConfigurationUnavailableError(true);
        }
        return $this;
    }

    /**
     * Retrieve products divided into groups required to purchase
     * At least one product in each group has to be purchased
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getProductsToPurchaseByReqGroups($product)
    {
        return [$this->getUsedProducts($product)];
    }

    /**
     * Get sku of product
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getSku($product)
    {
        $simpleOption = $product->getCustomOption('simple_product');
        if ($simpleOption) {
            $optionProduct = $simpleOption->getProduct();
            $simpleSku = null;
            if ($optionProduct) {
                $simpleSku = $simpleOption->getProduct()->getSku();
            }
            $sku = parent::getOptionSku($product, $simpleSku);
        } else {
            $sku = parent::getSku($product);
        }

        return $sku;
    }

    /**
     * Prepare selected options for configurable product
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @param  \Magento\Framework\DataObject $buyRequest
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function processBuyRequest($product, $buyRequest)
    {
        $superAttribute = $buyRequest->getSuperAttribute();
        $superAttribute = is_array($superAttribute) ? array_filter($superAttribute, 'intval') : [];

        $options = ['super_attribute' => $superAttribute];

        return $options;
    }

    /**
     * Prepare and retrieve options values with product data
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getConfigurableOptions($product)
    {
        return $this->_catalogProductTypeConfigurable->getConfigurableOptions(
            $product,
            $this->getUsedProductAttributes($product)
        );
    }

    /**
     * Delete data specific for Configurable product type
     *
     * @param \Magento\Catalog\Model\Product $product
     */
    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product)
    {
        $this->typeConfigurableFactory->create()->saveProducts($product, []);
        /** @var $configurableAttribute \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute */
        $configurableAttribute = $this->configurableAttributeFactory->create();
        $configurableAttribute->deleteByProduct($product);
    }

    /**
     * Retrieve product attribute by identifier
     * Difference from abstract: any attribute is available, not just the ones from $product's attribute set
     *
     * @param  int $attributeId
     * @param  \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    public function getAttributeById($attributeId, $product)
    {
        $attribute = parent::getAttributeById($attributeId, $product);
        return $attribute ?: $this->_eavAttributeFactory->create()->load($attributeId);
    }

    /**
     * Set image for product without image if possible
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    public function setImageFromChildProduct(\Magento\Catalog\Model\Product $product)
    {
        if (!$product->getData('image') || $product->getData('image') === 'no_selection') {
            foreach ($this->getUsedProducts($product) as $childProduct) {
                if ($childProduct->getData('image') && $childProduct->getData('image') !== 'no_selection') {
                    $product->setImage($childProduct->getData('image'));
                    break;
                }
            }
        }
        return parent::setImageFromChildProduct($product);
    }

    /**
     * Get MetadataPool instance
     * @return MetadataPool
     */
    private function getMetadataPool()
    {
        if (!$this->metadataPool) {
            $this->metadataPool = ObjectManager::getInstance()->get(MetadataPool::class);
        }
        return $this->metadataPool;
    }

    /**
     * Get Config instance
     * @return Config
     * @deprecated
     */
    private function getCatalogConfig()
    {
        if (!$this->catalogConfig) {
            $this->catalogConfig = ObjectManager::getInstance()->get(Config::class);
        }
        return $this->catalogConfig;
    }
}
