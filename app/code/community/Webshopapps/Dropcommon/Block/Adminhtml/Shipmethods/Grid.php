<?php

/**
 * @category   Webshopapps
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */
class Webshopapps_Dropcommon_Block_Adminhtml_Shipmethods_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function _construct()
  {
      parent::_construct();
      $this->setId('shipmethodsGrid');
      $this->setDefaultSort('shipmethods_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('dropcommon/shipmethods')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('shipmethods_id', array(
          'header'    => Mage::helper('dropcommon')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'shipmethods_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('dropcommon')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('dropcommon')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('dropcommon')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('dropcommon')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('dropcommon')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('dropcommon')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('shipmethods_id');
        $this->getMassactionBlock()->setFormFieldName('shipmethods');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('dropcommon')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('dropcommon')->__('Are you sure?')
        ));

     
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}