<?php
namespace Ced\GroupBuying\Model\ResourceModel\Main;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
class	Collection	extends	AbstractCollection
	{
	    /**
	     * Define model & resource model	     */
	    protected function _construct()
	    {
	        $this->_init(
	            'Ced\GroupBuying\Model\Main',
	            'Ced\GroupBuying\Model\ResourceModel\Main'
	        );
	    }
	}