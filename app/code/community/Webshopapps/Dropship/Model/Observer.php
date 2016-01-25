<?php

/**
 * @category    Webshopapps
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.webshopapps.com/license/license.txt - Commercial license
 */

/**
 * Magento Webshopapps Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Webshopapps
 * @package     Webshopapps_Dropship
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     www.webshopapps.com/license/license.txt
 * @author      Karen Baker <sales@webshopapps.com>
 */
class Webshopapps_Dropship_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * event checkout_type_onepage_save_order_after
     */
    public function saveOrderAfter($observer)
    {
        try {
            if (!Mage::getStoreConfig('carriers/dropship/active')) {
                return;
            }

            $eventName = $observer->getEvent()->getName();

            switch ($eventName) {
                case 'sales_order_place_after':
                    $orderStore = $observer->getEvent()->getOrder()->getStore();
                    self::saveOrderWarehouseInfo($observer);
                    break;
                case 'sales_order_invoice_save_after':
                    $orderStore = $observer->getEvent()->getInvoice()->getStore();
                    break;
                default:
                    $orderStore = '';
            }

            if (!Mage::getStoreConfig('carriers/dropship/active', $orderStore)) {
                return null;
            }

            if (!Mage::helper('dropship')->isCreateShipmentEmail($orderStore)) {
                return null;
            }

            $createEmail = Mage::getStoreConfig('carriers/dropship/shipment_email', $orderStore);

            if (($createEmail == 'order' && $eventName == 'sales_order_place_after') ||
                ($createEmail == 'invoice' && $eventName == 'sales_order_invoice_save_after')) {
                // Send emails to all the warehouses involved
                Mage::helper('dropship/email')->salesOrderSaveAfter($observer);
            }

        } catch (Exception $e) {
            Mage::logException($e);
        }
	}

    private function saveOrderWarehouseInfo($obs)
    {
        try {
            $model = Mage::getModel('dropcommon/ordershipping');

            $orderNumber = $obs->getOrder()->getIncrementId();
            $warehouseDetails = json_decode($obs->getOrder()->getWarehouseShippingDetails());

            if(empty($warehouseDetails)) {
                return null;
            }

            foreach ($warehouseDetails as $warehouseDetail) {
                $data = array(
                    'order_increment' => $orderNumber,
                    'warehouse_id'    => $warehouseDetail->warehouse,
                    'shipping_price'  => $warehouseDetail->price,
                    'shipping_method' => $warehouseDetail->methodTitle,
                    'shipping_code'   => $warehouseDetail->code,
                );

                $model->setData($data);
                $model->save();
            }
        } catch(Exception $e) {
            Mage::helper('wsalogger/log')->postCritical('dropcommon',
                                                        'Additional Save',
                                                        'Unable to Save Warehouse Shipping Details to Table:'.
                                                        ' webshopapps_dropship_order_shipping');
            Mage::logException($e);
        }
    }

    /**
     * @param $observer
     */
    public function preDispatchShippingMethodSave($observer)
    {
        /**
         * @var $controller Mage_Checkout_OnepageController
         */
        $controller = $observer->getControllerAction();

        if ($controller->getRequest()->isPost()) {
            $data = $controller->getRequest()->getPost('shipping_method', '');
            if (!empty($data)) {
                $controller->getOnepage()->saveSingleWarehouseShippingMethod($data);
                return;
            }

            $data = $controller->getRequest()->getPost();

            foreach (array('dropship_date_input','dropship_extrainfo_input') as $dropshipExclId) {
                if (array_key_exists($dropshipExclId,$data)) {
                    unset($data[$dropshipExclId]);  // remove additional parameters
                }
            }

            $result = $controller->getOnepage()->saveWarehouseShippingMethod($data);


            /*
            $result will have error data if shipping method is empty
            */
            if(!$result) {
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request'=>$controller->getRequest(), 'quote'=>$controller->getOnepage()->getQuote()));
                $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

                $result['goto_section'] = 'payment';
                $result['update_section'] = array(
                    'name' => 'payment-method',
                    'html' => $this->_getPaymentMethodsHtml($controller)
                );

                if (Mage::helper('core')->isModuleEnabled('EcomDev_CheckItOut')) {
                    $reflection = new ReflectionObject($controller);
                    if ($reflection->hasMethod('_addHashInfo')) {
                        $method = $reflection->getMethod('_addHashInfo');
                        $method->setAccessible(true);
                        $method->invokeArgs($controller, array(&$result));
                        $method->setAccessible(false);
                    }
                }
            }
            $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            $controller->setFlag('', Mage_Checkout_OnepageController::FLAG_NO_DISPATCH, true);
            // Emulate regular postDispatch
            $controller->postDispatch();
        }
    }


    /**
     * Get payment method step html
     *
     * @param Mage_Checkout_OnepageController $controller
     * @return string
     */
    protected function _getPaymentMethodsHtml($controller)
    {
        $layout = $controller->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_paymentmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
}