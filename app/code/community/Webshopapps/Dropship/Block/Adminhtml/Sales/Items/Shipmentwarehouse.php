<?php

 /**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_Dropship
 * User         Joshua Stewart
 * Date         15/10/2013
 * Time         14:35
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

class Webshopapps_Dropship_Block_Adminhtml_Sales_Items_Shipmentwarehouse extends Mage_Adminhtml_Block_Sales_Order_Shipment_View_Items
{
    public function getSimpleWarehouseText() {
        $items = $this->getShipment()->getAllItems();
        $itemWarehouses = array();
        $useParent = Mage::getStoreConfig('carriers/dropship/use_parent');
        $checkVirtual = false; //Won't be a virtual item at this stage.

        foreach ($items as $item) {
            if (Mage::helper('dropcommon')->skipItem($item, $useParent, $checkVirtual)) {
                continue;
            }
            $itemWarehouses[] = Mage::helper('dropcommon')->getWarehouseTitle($item);
        }
        return Mage::helper('core')->jsonEncode($itemWarehouses);
    }

    public function getBundleWarehouseText() {
        $items = $this->getShipment()->getAllItems();
        $itemWarehouses = array();

        foreach ($items as $item) {
            if (!$item->getParentItem() || $item->getParentItem()->getProductType() != Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                continue;
            }
            // child of bundle
            $itemWarehouses[] = Mage::helper('dropcommon')->getWarehouseTitle($item);
        }
        return Mage::helper('core')->jsonEncode($itemWarehouses);
    }
}