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
 * @package     Ced_RequestToQuote
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\RequestToQuote\Ui\DataProvider;

/**
 * Class QuotesDataProvider
 */
class PoDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    public $collection;

    protected $addFieldStrategies;

    protected $addFilterStrategies;
    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Ui\DataProvider\AddFieldToCollectionInterface[] $addFieldStrategies
     * @param \Magento\Ui\DataProvider\AddFilterToCollectionInterface[] $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Ced\RequestToQuote\Model\ResourceModel\Po\Collection $po,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->po = $po;
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
    }
    /**s
     * Get data
     *
     * @return array
     */
    public function getCollection(){
       /* $collection = $this->po->getSelect()->joinLeft(
           ['quote'=>$this->po->getTable('ced_requestquote')],
           'main_table.quote_id = quote.quote_id',
           ['quote_increment_id'=>'quote.quote_increment_id']);
        print_r($this->po->getData());die;*/
        $this->po
        ->join(
            ['ot'=>$this->po->getTable('ced_requestquote')],
            "main_table.quote_id = ot.quote_id",
            array('status' => 'main_table.status',
                'quote_increment_id' => 'ot.quote_increment_id')
        );
        return $this->po;
    }
    public function getData()
    {
        return [
            'totalRecords' => $this->po->getSize(),
            'items' => $this->po->getData(),
        ];

    }

    /**
     * Add field to select
     *
     * @param string|array $field
     * @param string|null $alias
     * @return void
     */
    public function addField($field, $alias = null)
    {
        if (isset($this->addFieldStrategies[$field])) {
            $this->addFieldStrategies[$field]->addField($this->getCollection(), $field, $alias);
        } else {
            parent::addField($field, $alias);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
       
        if (isset($this->addFilterStrategies[$filter->getField()])) {
            $this->addFilterStrategies[$filter->getField()]
                ->addFilter(
                    $this->getCollection(),
                    $filter->getField(),
                    [$filter->getConditionType() => $filter->getValue()]
                );
        } else {
            parent::addFilter($filter);
        }
    }
}
