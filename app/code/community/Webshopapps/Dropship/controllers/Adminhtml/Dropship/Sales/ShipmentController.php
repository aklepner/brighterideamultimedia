<?php


/**
 * @category   Webshopapps
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */
require_once 'Mage/Adminhtml/controllers/Sales/Order/ShipmentController.php';
class Webshopapps_Dropship_Adminhtml_Dropship_Sales_ShipmentController extends Mage_Adminhtml_Sales_Order_ShipmentController
{

  /**
     * Save shipment
     * We can save only new shipment. Existing shipments are not editable
     */
    public function shipAction()
    {
        $data = $this->getRequest()->getPost('shipment');
        if (!empty($data['comment_text'])) {
            Mage::getSingleton('adminhtml/session')->setCommentText($data['comment_text']);
        }

        try {
            if ($shipment = $this->_initShipment()) {
                $shipment->register();

                $comment = '';
                if (!empty($data['comment_text'])) {
                    $shipment->addComment($data['comment_text'], isset($data['comment_customer_notify']));
                    if (isset($data['comment_customer_notify'])) {
                        $comment = $data['comment_text'];
                    }
                }

                if (!empty($data['send_email'])) {
                    $shipment->setEmailSent(true);
                }

                $shipment->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
				$shipment->setDropshipStatus(Webshopapps_Dropship_Model_Shipping_Carrier_Source_ShipStatus::DROPSHIP_SHIPSTATUS_SHIPPED);
                $this->_saveShipment($shipment);
                $shipment->sendEmail(!empty($data['send_email']), $comment);
                $this->_getSession()->addSuccess($this->__('The shipment has been created.'));
                Mage::getSingleton('adminhtml/session')->getCommentText(true);
                $this->_redirect('adminhtml/sales_order_shipment/view', array('shipment_id' => $this->getRequest()->getParam('shipment_id')));
                return;
            } else {
                $this->_forward('noRoute');
                return;
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
        	$this->_getSession()->addError($this->__('Cannot save shipment.'));
        }
        $this->_redirect('adminhtml/sales_order/new', array('order_id' => $this->getRequest()->getParam('order_id')));
    }

    public function emailwareAction()
    {
     	$data = $this->getRequest()->getPost('shipment');
        if (!empty($data['comment_text'])) {
            Mage::getSingleton('adminhtml/session')->setCommentText($data['comment_text']);
        }

        $shipmentId = $this->getRequest()->getParam('shipment_id');
        if ($shipmentId) {
             Mage::helper('dropship/email')->sendNewShipmentEmail($shipmentId);
        	$this->_getSession()->addSuccess($this->__('The email has been sent.'));
        } else {
        	$this->_getSession()->addError($this->__('Cannot email warehouse.'));
        	$this->_redirect('adminhtml/sales_order/new', array('order_id' => $this->getRequest()->getParam('order_id')));
       	}
        $this->_redirect('adminhtml/sales_order_shipment/view', array('shipment_id' => $shipmentId));
    }
}