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
 * @package     Ced_HelpDesk
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
/*
 * Copy this class from Magento\Ui\Component\MassAction\Filter
 */
namespace Ced\HelpDesk\Controller\Adminhtml\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\Data\Collection\AbstractDb;

class FilterCus 
{
    const SELECTED_PARAM = 'selected';

    const EXCLUDED_PARAM = 'excluded';

    /**
     * @var UiComponentFactory
     */
    protected $factory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var UiComponentInterface[]
     */
    protected $components = [];

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @param UiComponentFactory $factory
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        UiComponentFactory $factory,
        RequestInterface $request,
        FilterBuilder $filterBuilder
    ) {
        $this->factory = $factory;
        $this->request = $request;
        $this->filterBuilder = $filterBuilder;
    }

    public function getCollection(AbstractDb $collection)
    {
        $component = $this->getComponent();
        $this->prepareComponent($component);
        $dataProvider = $component->getContext()->getDataProvider();
        $dataProvider->setLimit(0, false);
        $ids = [];
        foreach ($dataProvider->getSearchResult()->getItems() as $document) {
            $ids[] = $document->getId();

        }
        $ob = \Magento\Framework\App\ObjectManager::getInstance();
       $var = $ob->create('Ced\HelpDesk\Model\Status')->getCollection()->getData();
        $collection->addFieldToFilter('id', ['in' => $ids]);
        return $this->applySelection($collection);
    }

        protected function applySelection(AbstractDb $collection)
    {
        $selected = $this->request->getParam(static::SELECTED_PARAM);
        $excluded = $this->request->getParam(static::EXCLUDED_PARAM);

        if ('false' === $excluded) {
            return $collection;
        }

        try {
            if (is_array($excluded) && !empty($excluded)) {
                $collection->addFieldToFilter('id', ['nin' => $excluded]);
            } elseif (is_array($selected) && !empty($selected)) {
                $collection->addFieldToFilter('id', ['in' => $selected]);
            } else {
                throw new LocalizedException(__('Please select item(s).'));
            }
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
        return $collection;
    }
    /**
     * Returns component by namespace
     *
     * @return UiComponentInterface
     * @throws LocalizedException
     */
    public function getComponent()
    {
        $namespace = $this->request->getParam('namespace');
        if (!isset($this->components[$namespace])) {
            $this->components[$namespace] = $this->factory->create($namespace);
        }
        return $this->components[$namespace];
    }

    public function applySelectionOnTargetProvider()
    {
        $selected = $this->request->getParam(static::SELECTED_PARAM);
        $excluded = $this->request->getParam(static::EXCLUDED_PARAM);
        if ('false' === $excluded) {
            return;
        }
        $component = $this->getComponent();
        $this->prepareComponent($component);
        $dataProvider = $component->getContext()->getDataProvider();
        try {
            if (is_array($excluded) && !empty($excluded)) {
                $this->filterBuilder->setConditionType('nin')
                    ->setField('id')
                    ->setValue($excluded);
                $dataProvider->addFilter($this->filterBuilder->create());
            } elseif (is_array($selected) && !empty($selected)) {
                $this->filterBuilder->setConditionType('in')
                    ->setField('id')
                    ->setValue($selected);
                $dataProvider->addFilter($this->filterBuilder->create());
            }
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * Call prepare method in the component UI
     *
     * @param UiComponentInterface $component
     * @return void
     */
    public function prepareComponent(UiComponentInterface $component)
    {
        foreach ($component->getChildComponents() as $child) {
            $this->prepareComponent($child);
        }
        $component->prepare();
    }

    /**
     * Returns RefererUrl
     *
     * @return string|null
     */
    public function getComponentRefererUrl()
    {
        $data = $this->getComponent()->getContext()->getDataProvider()->getConfigData();
        return (isset($data['referer_url'])) ? $data['referer_url'] : null;
    }

}