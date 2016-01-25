<?php
class Concalma_Dropship_Adminhtml_WarehouseController extends Mage_Adminhtml_Controller_action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('dropship/warehouseman')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Dropship Manager'), Mage::helper('adminhtml')->__('Dropship Manager'));
  echo "come here";
        return $this;
    }

    public function indexAction()
    {
    	echo "pilot";
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
    	echo 'opoint';
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('dropship/warehouse')->load($id);
         echo '1pint';
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

            $this->_addContent($this->getLayout()->createBlock('dropship/adminhtml_warehouse_edit'))
                ->_addLeft($this->getLayout()->createBlock('dropship/adminhtml_warehouse_edit_tabs'));

             //$this->_addContent($this->getLayout()->createBlock('dropship/adminhtml_warehouse_edit'))
             // ;
            
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dropship')->__('Warehouse does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
    	echo "test";
        $this->_forward('edit');
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {

            $model = Mage::getModel('dropship/warehouse');
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
}
