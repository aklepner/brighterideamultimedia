<?php

/**
 * @category   Webshopapps
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */
class Webshopapps_Dropcommon_Helper_Shipcalculate extends Mage_Core_Model_Abstract
{
    static $_useParent;

    public function _construct() {
        self::$_useParent = Mage::getStoreConfig('carriers/dropship/use_parent');
        parent::_construct();
    }

    /**
     * Gets the list of dropship warehouses for the items in the cart.
     *
     * @param $country
     * @param $region
     * @param $postcode
     * @param null $items
     * @return array
     */
    public function getWarehouseDetails($country,$region,$postcode,$items=null)
    {
        $warehouseArr = array();
        $debug = Mage::helper('wsalogger')->isDebug('Webshopapps_Dropcommon');
        $allWh = Mage::getStoreConfig('carriers/dropship/common_warehouse') ?
            $this->findAllWarehousesInQuote($items, $country, $region) : array();

        foreach($items as $item) {
            if (Mage::helper('dropcommon')->skipItem($item, self::$_useParent)) {
                continue;
            }

            $warehouseChanged = false;

            if ($item->getParentItem()) {
                if ($item->getParentItem()->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                    //All the details we need are on the parent product except warehouse
                    continue;
                }
            }

            if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE && !self::$_useParent) {
                foreach ($item->getChildren() as $child) {
                    $warehouse = $this->determineWhichWarehouse($child,$country,$region,$postcode,$warehouseChanged,$allWh);
                    break;
                }
            } else {
                $warehouse = $this->determineWhichWarehouse($item,$country,$region,$postcode,$warehouseChanged,$allWh);
            }

            if (!array_key_exists($warehouse,$warehouseArr)) {
                $warehouseArr[$warehouse]=array($item);
                if ($debug) {
                    Mage::helper('wsalogger/log')->postDebug('dropcommon','Warehouse Details, Warehouse being added:',$warehouse);
                }
            } else {
                $warehouseArr[$warehouse][]=$item;
            }
        }
        return $warehouseArr;
    }

    public function getWarehouseAttribute($item) {
    	if ($item->getParentItem()!=null &&
			self::$_useParent ) {
    			$product = $item->getParentItem()->getProduct();
    	}  else {
    		$product = Mage::getModel('catalog/product')->load($item->getProductId());
    	}
    	if(is_object($product)){
    		return $product->getData('warehouse');
    	}

        return null;
    }

    public function findAllWarehousesInQuote($items, $country, $region='')
    {
        $warehouses = array();

        foreach($items as $item) {
            $applicableWhs = $this->_getApplicableWarehouses($item);
            $this->_filterWarehousesByCountry($country,$applicableWhs,array(),$region);
            $warehouses[$item->getId()] = $applicableWhs;
        }
        return $warehouses;
    }


    public function isMultipleWarehouses($country,$region,$postcode,$items=null)
    {
    	$oldWarehouse=-1;
        $allWh = Mage::getStoreConfig('carriers/dropship/common_warehouse') ?
            $this->findAllWarehousesInQuote($items, $country, $region) : array();

        foreach($items as $item) {
            $warehouseChanged = false;
            $warehouse = $this->determineWhichWarehouse($item,$country,$region,$postcode,$warehouseChanged,$allWh);

            if (Mage::helper('dropcommon')->skipItem($item, self::$_useParent)) {
                continue;
            }
            if ($oldWarehouse==-1) {
                $oldWarehouse=$warehouse;
            } else if ($warehouse!=$oldWarehouse) {
                return true;
            }

        }
		return false;
    }

    /**
     * Function will get the warehouses from the attribute. It will then filter out any warehouses which aren't available
     * to the delivery country. If > 1 warehouse is still present then perform further filtering
     *
     * @param       $item
     * @param       $country
     * @param       $region
     * @param       $postcode
     * @param bool  $warehouseChanged
     * @param array $allWh
     * @return bool|mixed|null
     */
    public function determineWhichWarehouse($item,$country,$region,$postcode,&$warehouseChanged=false,$allWh=array())
    {
        $warehousesAttribute = $this->_getApplicableWarehouses($item);
        if(count($warehousesAttribute)){
            $filteredWarehouses = $this->_filterWarehousesByCountry($country, $warehousesAttribute, array(), $region)->getData();
        } else { //No warehouses assigned. Use Default warehouse and don't filter it.
            $filteredWarehouses = $warehousesAttribute;
        }
        $warehouses = $this->getWarehouseIds($filteredWarehouses);

        switch (count($warehouses)) {
            case 0:
                $warehouse = $this->_getDefaultWarehouse($country,$region,$postcode);
                $warehouseChanged = true;
                break;
            case 1:
                $warehouse = $warehouses[0];
                break;
            default:
                // multiple warehouses selected
                $warehouse = $this->_findWarehouseFromMultiple($country,$region,$postcode,$warehouses,$allWh);
                $warehouseChanged = true;
                break;
        }
        $item->setWarehouse($warehouse);
        return $warehouse;
    }

    private function getWarehouseIds($warehouses)
    {
        $warehouseIds = array();

        foreach ($warehouses as $warehouse) {
            $warehouseIds[] = $warehouse['dropship_id'];
        }

        return $warehouseIds;
    }

    protected function _getDefaultWarehouse($country,$region,$postcode) {

        $defaultWarehouse = Mage::getStoreConfig('carriers/dropship/default_warehouse');

        switch ($defaultWarehouse) {
            case 'none':
                return null;
                break;
            case 'all':
                $warehouses = Mage::getSingleton('dropcommon/dropship')->getAllDropshipIds();
                return $this->_findWarehouseFromMultiple($country,$region,$postcode,$warehouses);
            default:
                return $defaultWarehouse;
                break;
        }
    }

    protected function _filterWarehousesByCountry($country, &$warehouses, $allWh=array(), $region='')
    {
        $debug = Mage::helper('wsalogger')->isDebug('Webshopapps_Dropcommon');
        $log = Mage::helper('wsalogger/log');

        if ($debug) {
            $log->postDebug('dropcommon','Filtering Warehouses by Allowed Countries', $country);
            $log->postDebug('dropcommon','Filtering Warehouses by Allowed Regions', $region);
        }

        $collection = Mage::getModel('dropcommon/dropship')->getCollection()->addCountryFilter($country,$warehouses);
        $warehouses = $this->getWarehouseIds($collection);

        if ($debug) {
            $log->postDebug('dropcommon','Post Country Filter # Warehouses', count($collection));
            $log->postDebug('dropcommon','Post Country Filter Warehouse IDs', $warehouses);
        }

        if(count($warehouses)) { //DROP-122
            $collection = Mage::getModel('dropcommon/dropship')->getCollection()->addRegionFilter($region, $warehouses);
            $warehouses = $this->getWarehouseIds($collection);
        } else {
            $log->postWarning('dropcommon','Post Country Filter Removed All Warehouses', 'No warehouses are available for item when shipping to: '.$country);
        }

        if ($debug) {
            $log->postDebug('dropcommon','Post Region Filter # Warehouses', count($collection));
            $log->postDebug('dropcommon','Post Region Filter Warehouses IDs', $warehouses);
        }

        if(count($allWh)) {
            if ($debug) {
                $log->postDebug('dropcommon','Finding Super Warehouses', "Trying to Find Super Warehouse");
            }

            $superWarehouses = array();

            foreach($allWh as $aw) {
                $commonWarehouses = array_intersect($warehouses,$aw);

                if(count($commonWarehouses)) {
                    $comWareData = Mage::getModel('dropcommon/dropship')->getCollection()
                        ->addTypeFilter($commonWarehouses)
                        ->getData();

                    foreach($comWareData as $comWare) {
                        if($comWare['warehouse_type'] ==
                            Webshopapps_Dropcommon_Model_Adminhtml_System_Config_Source_Type::STATUS_SUPER) {
                            $superWarehouses[] = $comWare['dropship_id'];
                        }
                    }
                }
            }

            $superWarehouses = array_unique($superWarehouses);

            if ($debug) {
                $log->postDebug('dropcommon','Super Warehouses', $superWarehouses);
            }

            if (count($superWarehouses)) {
                $warehouses = $collection->addIdFilter($superWarehouses);
                return $warehouses;
            }
        }

        $warehouses = $collection;

        return $collection;
    }

    protected function _findWarehouseFromMultiple($country,$region,$postcode,$warehouses,$allWh=array())
    {
        $debug = Mage::helper('wsalogger')->isDebug('Webshopapps_Dropcommon');
        $collection = $this->_filterWarehousesByCountry($country, $warehouses, $allWh, $region);
        $warehouseIds = $this->getWarehouseIds($warehouses);

        switch (count($collection)) {
            case 0:
                // no warehouse assigned for this destination
                if ($debug) {
                    Mage::helper('wsalogger/log')->postDebug('dropcommon','No warehouses found for destination',$country);
                }
                return null;
                break;
            case 1:
                foreach ($collection as $colData) {
                    $data=$colData->getData();
                    if ($debug) {
                        Mage::helper('wsalogger/log')->postDebug('dropcommon','Warehouse Selection Based on Destination',$data['dropship_id']);
                    }
                    return $data['dropship_id'];
                }
                break;
            default:

                /**
                 * Multiple returned, but may have cut down.
                 * Now lets see if any are primary warehouses. These should be used over the nearest.
                 * DROP-69
                 **/

                $primaryWarehouse = $this->getPrimaryWarehouses($warehouseIds);

                if(!$primaryWarehouse){
                    return Mage::helper('dropcommon')->getNearestWarehouse($country,$region,$postcode,$warehouseIds);
                } else {
                    return $primaryWarehouse;
                }

                break;
        }

        return null;
    }

    protected function getPrimaryWarehouses($warehouses)
    {
        if(!Mage::getStoreConfig('carriers/dropship/common_warehouse')) {
            return false;
        }

        $primaryWarehouses = array();
        $warehouses = Mage::getModel('dropcommon/dropship')->getCollection()->addTypeFilter($warehouses)->getData();

        foreach ($warehouses as $warehouse) {
            if($warehouse['warehouse_type'] ==
                Webshopapps_Dropcommon_Model_Adminhtml_System_Config_Source_Type::STATUS_PRIMARY){
                $primaryWarehouses[] = $warehouse['dropship_id'];
            }
        }

        if(count($primaryWarehouses) > 1 || !count($primaryWarehouses)) {
            return false;
        }

        return $primaryWarehouses[0];
    }

    protected function _getApplicableWarehouses($item)
    {
        $wareAttr = $this->getWarehouseAttribute($item);
        $debug = Mage::helper('wsalogger')->isDebug('Webshopapps_Dropcommon');

    	if (!empty($wareAttr)) {
    	    $warehouses=explode(',',$wareAttr);
    	} else {
    	    $warehouses=array();
    	}

        /**
         * Changed this logic around to cater for Magento bug in inventory levels of configurable products
         * DROP-114
         */
		if (Mage::getStoreConfigFlag('carriers/dropship/instock_default')) {
            $inStock = true;

            if($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                $children = $item->getChildren();

                foreach ($children as $child) {
                    if(!Mage::helper('dropcommon')->isStocked($child)) {
                        $inStock = false;
                        break;
                    }
                }
            } elseif (!Mage::helper('dropcommon')->isStocked($item)) {
                $inStock = false;
            }

            if ($inStock) {
                // use default
                $warehouses = array();
                if ($debug) {
                    Mage::helper('wsalogger/log')->postDebug('dropcommon','Item in stock, using default. Id:',$item->getProductId());
                }
            }
		}

        return $warehouses;
    }

    /**
     * Retrives list of carriers for the items passed in
     *
     * @param $items
     * @return array
     */
    public function getCarriersForItems($items)
    {
        $carrierArr = array();

        foreach ($items as $item) {
            $warehouses = $this->_getAllWarehousesForItem($item);
            foreach ($warehouses as $warehouse) {
                $warehouseDetails = Mage::getModel('dropcommon/dropship')->load($warehouse);
                $carriersAllowed = $warehouseDetails->getShippingMethods();

                foreach ($carriersAllowed as $carrier) {
                    if (!in_array($carrier,$carrierArr)) {
                        $carrierArr[]=$carrier;
                    }
                }
            }
        }

        return $carrierArr;
    }

    protected function _getAllWarehousesForItem($item)
    {
        $warehouses = $this->_getApplicableWarehouses($item);

        if (empty($warehouses)) {
            $defaultWarehouse = Mage::getStoreConfig('carriers/dropship/default_warehouse');
            switch ($defaultWarehouse) {
                case 'none':
                    return null;
                    break;
                case 'all':
                    $warehouses = Mage::getSingleton('dropcommon/dropship')->getAllDropshipIds();
                    break;
                default:
                    $warehouses = array($defaultWarehouse);
                    break;

            }
        }

        return $warehouses;
    }

    /**
     * Used in the Address Object
     * @param $items
     * @param $country
     * @param $region
     * @param $postcode
     */
    public function setWarehouseOnItems($items,$country,$region,$postcode)
    {
        $warehouseChanged = false;
        $allWh = Mage::getStoreConfig('carriers/dropship/common_warehouse') ?
            $this->findAllWarehousesInQuote($items, $country, $region) : array();

        foreach($items as $item) {
            $item->setWarehouse($this->determineWhichWarehouse($item,$country,$region,$postcode,$warehouseChanged,$allWh));
        }
    }
}
