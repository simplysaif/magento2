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
  * @package   Ced_CsImportExport
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\CsImportExport\Model\Import\Product;

class CategoryProcessor
{
    /**
     * Delimiter in category path.
     */
    const DELIMITER_CATEGORY = '/';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryColFactory;

    /**
     * Categories text-path to ID hash.
     *
     * @var array
     */
    protected $categories = [];

    /**
     * Categories id to object cache.
     *
     * @var array
     */
    protected $categoriesCache = [];

    /**
     * Instance of catalog category factory.
     *
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * Failed categories during creation
     *
     * @var array
     */
    protected $failedCategories = [];
    protected $_objectManager;
    /**
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryColFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryColFactory,
    		\Magento\Framework\ObjectManagerInterface $objectInterface,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
        $this->categoryColFactory = $categoryColFactory;
        $this->categoryFactory = $categoryFactory;
        $this->_objectManager = $objectInterface;
        $this->initCategories();
    }

    /**
     * @return $this
     */
    protected function initCategories()
    {
        if (empty($this->categories)) {
            $collection = $this->categoryColFactory->create();
            $collection->addAttributeToSelect('name')
                ->addAttributeToSelect('url_key')
                ->addAttributeToSelect('url_path');
            /* @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection */
            foreach ($collection as $category) {
                $structure = explode(self::DELIMITER_CATEGORY, $category->getPath());
                $pathSize = count($structure);

                $this->categoriesCache[$category->getId()] = $category;
                if ($pathSize > 1) {
                    $path = [];
                    for ($i = 1; $i < $pathSize; $i++) {
                        $path[] = $collection->getItemById((int)$structure[$i])->getName();
                    }
                    $index = implode(self::DELIMITER_CATEGORY, $path);
                    $this->categories[$index] = $category->getId();
                }
            }
        }
        return $this;
    }

    /**
     * Creates a category.
     *
     * @param string $name
     * @param int $parentId
     *
     * @return int
     */
    protected function createCategory($name, $parentId)
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $this->categoryFactory->create();
        if (!($parentCategory = $this->getCategoryById($parentId))) {
            $parentCategory = $this->categoryFactory->create()->load($parentId);
        }
        $category->setPath($parentCategory->getPath());
        $category->setParentId($parentId);
        $category->setName($name);
        $category->setIsActive(true);
        $category->setIncludeInMenu(true);
        $category->setAttributeSetId($category->getDefaultAttributeSetId());
        $category->save();
        $this->categoriesCache[$category->getId()] = $category;

        return $category->getId();
    }


    /**
     * Returns ID of category by string path creating nonexistent ones.
     *
     * @param string $categoryPath
     *
     * @return int
     */
    protected function upsertCategory($categoryPath)
    {
        if (!isset($this->categories[$categoryPath])) {
            $pathParts = explode(self::DELIMITER_CATEGORY, $categoryPath);
            $parentId = \Magento\Catalog\Model\Category::TREE_ROOT_ID;
            $path = '';

            foreach ($pathParts as $pathPart) {
                $path .= $pathPart;
                if (!isset($this->categories[$path])) {
                    $this->categories[$path] = $this->createCategory($pathPart, $parentId);
                }
                $parentId = $this->categories[$path];
                $path .= self::DELIMITER_CATEGORY;
            }
        }

        return $this->categories[$categoryPath];
    }

    /**
     * Returns IDs of categories by string path creating nonexistent ones.
     *
     * @param string $categoriesString
     * @param string $categoriesSeparator
     *
     * @return array
     */
    public function upsertCategories($categoriesString, $categoriesSeparator)
    {
    	
        $categoriesIds = [];
        $categories = explode($categoriesSeparator, $categoriesString);
        $cat_mode = $this->_objectManager->create('Ced\CsMarketplace\Helper\Data')->getStoreConfig('ced_vproducts/general/category_mode');
        if($cat_mode){
	        $options = explode(",", $this->_objectManager->create('Ced\CsMarketplace\Helper\Data')->getStoreConfig('ced_vproducts/general/category'));
	        foreach ($categories as $category) {
	            try {
	            	if(in_array($this->categories[$category], $options)) {
	                	$categoriesIds[] = $this->upsertCategory($category);
	            	}
	            } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
	                $this->addFailedCategory($category, $e);
	            }
	        }
      	}
      else{
      	foreach ($categories as $category) {
      		if(isset($this->categories[$category])) {
      			$categoriesIds[] = $this->categories[$category];
      		}
      	}
      }
        return $categoriesIds;
    }

    /**
     * Add failed category
     *
     * @param string $category
     * @param \Magento\Framework\Exception\AlreadyExistsException $exception
     *
     * @return array
     */
    private function addFailedCategory($category, $exception)
    {
        $this->failedCategories[] =
            [
                'category' => $category,
                'exception' => $exception,
            ];
        return $this;
    }

    /**
     * Return failed categories
     *
     * @return array
     */
    public function getFailedCategories()
    {
        return  $this->failedCategories;
    }

    /**
     * Get category by Id
     *
     * @param int $categoryId
     *
     * @return \Magento\Catalog\Model\Category|null
     */
    public function getCategoryById($categoryId)
    {
        return isset($this->categoriesCache[$categoryId]) ? $this->categoriesCache[$categoryId] : null;
    }
}
