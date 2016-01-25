<?php
class Concalma_Dropship_Adminhtml_DropshipController extends Mage_Adminhtml_Controller_action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('dropship/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Dropship Manager'), Mage::helper('adminhtml')->__('Dropship Manager'));
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    // created by LuuThanhThuy
    public function testAction()
    {
       $this->loadLayout();
        //$block = new Mage_Adminhtml_Block_Widget_Grid_Container();
        //$block->setTemplate('hn_giftwrapper/giftbox.phtml');
       // $this->_addContent(Mage::getSingleton('giftwrapper/giftbox'));
        //-
       
        	//$this->_addContent($this->getLayout()->createBlock('blog/manage_blog_edit'));
        	 $this->renderLayout();
    }
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('dropship/dropship')->load($id);
        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('dropship_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('dropship/items');
            echo '2pooint';
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Warehouse Manager'), Mage::helper('adminhtml')->__('Warehouse Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Warehouse Manager'), Mage::helper('adminhtml')->__('Warehouse Manager'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('dropship/adminhtml_dropship_edit'))
                ->_addLeft($this->getLayout()->createBlock('dropship/adminhtml_dropship_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dropship')->__('Warehouse does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {

        	//hn debug
        	Mage::log('.......data.....'  , Zend_Log::INFO);
        	Mage::log($data, Zend_Log::INFO);
        	//end hn debug
            $model = Mage::getModel('dropship/dropship');
            if($this->getRequest()->getParam('id')) {
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));
            } else {
            	$model->setData($data);
            }
            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                $model->save();
                $this->updateEnableShippingService($model->getId(), $data['method']);
                $tit = $model->getData('title');
                $this->saveProductAtt($tit);
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('dropship')->__('Warehouse was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dropship')->__('Unable to find warehouse to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('dropship/dropship');

                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Warehouse was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $dropshipIds = $this->getRequest()->getParam('dropship');
        if (!is_array($dropshipIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($dropshipIds as $dropshipId) {
                    $dropship = Mage::getModel('dropship/dropship')->load($dropshipId);
                    $dropship->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($dropshipIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $dropshipIds = $this->getRequest()->getParam('dropship');
        if (!is_array($dropshipIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($dropshipIds as $dropshipId) {
                    $dropship = Mage::getSingleton('dropship/dropship')
                            ->load($dropshipId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($dropshipIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName = 'dropship.csv';
        $content = $this->getLayout()->createBlock('dropship/adminhtml_dropship_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName = 'dropship.xml';
        $content = $this->getLayout()->createBlock('dropship/adminhtml_dropship_grid')
                ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
    
    /**
     * exp
     * luuthuy205@gmail.com
     */
    public function saveProductAtt($tit) {
    	$query = "select max(`option_id`) as maximum from `eav_attribute_option_value`";
    	$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$rs = $db->query($query);
		$rows = $rs->fetchAll(PDO::FETCH_ASSOC);
		
		$option_id = $rows[0]['maximum'];
		$option_id = $option_id + 1;
		
		//$warehoue_Att = $this->getWarehouseAttribute();
		$warehouseAttId =   Mage::getResourceModel('eav/entity_attribute')
->getIdByCode('catalog_product','warehouse');
		
		//insert into option table eav_attribute_option`
		$query = "INSERT INTO `eav_attribute_option`(`option_id`,`attribute_id`,
		`sort_order`) VALUES ('$option_id', '$warehouseAttId','0')";
		$db->query($query);
		//insert into option  value table 
		$query = "INSERT INTO `eav_attribute_option_value`(`value_id`,`option_id`,
		`store_id`, `value`) VALUES (null, '$option_id','0', '$tit')";
		$db->query($query);
    }
    
    /**
     * public function 
     */
    
    public function getWarehouseAttribute() {
    	$att = Mage::getResourceModel('eav/entity_attribute_collection')
    	       ->setCodeFilter(`warehouse`)
                        ->getFirstItem();
         return $att;               
    }
    
    /**
     * 
     * 
     */
    public function randomAction() {
    	$query = "select * from `vstore` where `product_id` =".$productid;
    	
    	$rows = $db->fetchAll();
    	
    	$Arr_c = array();
    	foreach ($rows as $row) {
    		
    		if ( !in_array($row['customer_id'], $Arr_c)) {
    			$Arr_c[] = $row['customer_id'];
    		}
    	}
    	//choose the rand
    	$rand_keys = array_rand($Arr_c, 1);
    	return $rand_keys[0];
    }
    
     /**
     * @author Luu Thanh Thuy
     * description: get all the items in shopping cart
     * 
     */
    /**
	 * get All Items in the cart
	 */
	public function getAllItems() {
		$items = Mage::getSingleton('checkout/cart')->getQuote()->getAllItems();
		
		return $items;
	}
	
	
	/**
	 * get all the warehouse
	 * @author : Luu Thanh Thuy
	 * email : luuthuy205@gmail
	 * return array of warehouse Id
	 */
	public function getAllWarehouses () {
		$itemcollection = $this->getAllItems();

		$arr_warehouse = array();
		foreach ($items as $item) {

			$productId = $item->getProductId();
			$product = Mage::getModel('catalog/product')->load($productId);
			$warehouse = $product-> getResource()->getAttribute('warehouse')->getFrontend()->getValue($product);
				
			if ( !in_array($warehouse, $arr_warehouse ) ) {
				$arr_warehouse[] = $warehouse();

			}
		}
	}

	/**
	 * @ mailto : luuthuy205@gmail.com
	 * get all the items in warehouse when checkout
	 * @return array
	 */
	public function getItemsInWarehouse ($warehouseId) {
		$itemcollection = $this->getAllItems();

		$arr_item = array();
		foreach ($items as $item) {

			$productId = $item->getProductId();
			$product = Mage::getModel('catalog/product')->load($productId);
			$warehouse = $product-> getResource()->getAttribute('warehouse')->getFrontend()->getValue($product);
				
			if ($warehouse == $warehouseId ) {
				if ( !in_array($item->getId(), $arr_item) ) {
					$arr_item[] = $item->getId();
				}
			}
				
		}
    
}

/**
 * update the enable shipping services
 * @param id
 * @param array of enabled shipping services
 * 
 */
public  function updateEnableShippingService($id, $arr){
	$flat = 0;
	$table = 0;
	$free =0;
	$ups = 0;
	$fedex = 0;
	$usps = 0;
	$dhl = 0;
	if (in_array(0, $arr)) {
		$flat = 1;
		
	}
	
	if(in_array(1, $arr)) {
		$table = 1;
	}
	
	if( in_array(2, $arr)) {
		$free = 1;
	} 
	
	if (in_array(3, $arr)) {
		$ups = 1;
	}
	if (in_array(4, $arr)) {
		$fedex = 1;
	}
	
	if (in_array(5, $arr)) {
		$usps = 1;
	}
	
	if (in_array(6, $arr))  {
		$dhl = 1;
	}
	
	$db = Mage::getSingleton('core/resource')->getConnection('core_write');
	$table_prefix = Mage::getConfig()->getTablePrefix();
	$dropshipTbl = $table_prefix.'dropship';
	
	$query = "UPDATE $dropshipTbl SET flat_enable = $flat,table_enable =$table,free_enable= $free,ups_enable = $ups,fedex_enable= $fedex , usps_enable = $usps,dhl_enable=$dhl where id= $id";
	$db->query($query);
}
}