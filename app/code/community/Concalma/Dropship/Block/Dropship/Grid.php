<?php
class Concalma_Dropship_Block_Adminhtml_Dropship_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('dropshipGrid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}
	protected function _prepareCollection()
	{
		$collection = Mage::getModel('dropship/dropship')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	protected function _prepareColumns()
	{
		$this->addColumn('id', array('header' => Mage::helper('dropship')->__('ID'),'align' =>'right','width' => '50px','index' => 'id',));
		$this->addColumn('title', array('header' => Mage::helper('dropship')->__('Title'),'align' =>'left','index' => 'title',));
		
		
		$this->addColumn('action',array(
'header' => Mage::helper('dropship')->__('Action'),
'width' => '100',
'type' => 'action',
'getter' => 'getId',
'actions' => array(array(
'caption' => Mage::helper('dropship')->__('Edit'),
'url' => array('base'=> '*/*/edit'),
'field' => 'id')),
'filter' => false,
'sortable' => false,
'index' => 'stores',
'is_system' => true,
));
		return parent::_prepareColumns();
	}
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
}
