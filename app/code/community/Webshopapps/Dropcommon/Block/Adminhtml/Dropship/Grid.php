<?php

/**
 * @category   Webshopapps
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */
class Webshopapps_Dropcommon_Block_Adminhtml_Dropship_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function _construct()
    {
        parent::_construct();
        $this->setId('dropshipGrid');
        $this->setDefaultSort('dropship_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('dropcommon/dropship')->getCollection();

        foreach ($collection->getItems() as $warehouse) {
            $warehouseType = $warehouse->getWarehouseType();

            if($warehouseType != '' ) {
                switch ($warehouseType){
                    case Webshopapps_Dropcommon_Model_Adminhtml_System_Config_Source_Type::STATUS_SUPER:
                        $warehouse->setWarehouseType(
                            Webshopapps_Dropcommon_Model_Adminhtml_System_Config_Source_Type::STATUS_SUPER_TEXT
                        );
                        break;
                    case Webshopapps_Dropcommon_Model_Adminhtml_System_Config_Source_Type::STATUS_PRIMARY:
                        $warehouse->setWarehouseType(
                            Webshopapps_Dropcommon_Model_Adminhtml_System_Config_Source_Type::STATUS_PRIMARY_TEXT
                        );
                        break;
                    case Webshopapps_Dropcommon_Model_Adminhtml_System_Config_Source_Type::STATUS_STANDARD:
                        $warehouse->setWarehouseType(
                            Webshopapps_Dropcommon_Model_Adminhtml_System_Config_Source_Type::STATUS_STANDARD_TEXT
                        );
                        break;
                    default:
                        $warehouse->setWarehouseType(
                            Webshopapps_Dropcommon_Model_Adminhtml_System_Config_Source_Type::STATUS_STANDARD_TEXT
                        );
                }
            } else {
                $warehouse->setWarehouseType(Webshopapps_Dropcommon_Model_Adminhtml_System_Config_Source_Type::STATUS_STANDARD_TEXT);
            }
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('dropship_id', array(
            'header' => Mage::helper('dropcommon')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'dropship_id',
        ));

        $this->addColumn('title', array(
            'header' => Mage::helper('dropcommon')->__('Title'),
            'align' => 'left',
            'index' => 'title',
        ));


        $this->addColumn('description', array(
            'header' => Mage::helper('dropcommon')->__('Description'),
            'align' => 'left',
            'index' => 'description',
        ));


        $this->addColumn('country', array(
            'header' => Mage::helper('dropcommon')->__('Origin Country'),
            'align' => 'left',
            'index' => 'country',
        ));


        $this->addColumn('region', array(
            'header' => Mage::helper('dropcommon')->__('Origin Region'),
            'align' => 'left',
            'index' => 'region',
        ));


        $this->addColumn('zipcode', array(
            'header' => Mage::helper('dropcommon')->__('Origin Zip'),
            'align' => 'left',
            'index' => 'zipcode',
        ));

        $this->addColumn('warehouse_type', array(
            'header' => Mage::helper('dropcommon')->__('Type'),
            'align' => 'left',
            'index' => 'warehouse_type',
        ));

        $this->addColumn('action',
            array(
                'header' => Mage::helper('dropcommon')->__('Action'),
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('dropcommon')->__('Edit'),
                        'url' => array('base' => '*/*/edit'),
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            ));

        $this->addExportType('*/*/exportCsv', Mage::helper('dropcommon')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('dropcommon')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('dropship_id');
        $this->getMassactionBlock()->setFormFieldName('dropship');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('dropcommon')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('dropcommon')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('dropcommon_adminhtml/system_config_source_status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('dropcommon')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('dropcommon')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}