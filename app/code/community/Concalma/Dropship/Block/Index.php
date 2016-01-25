<?php


class  Concalma_Dropship_Block_Index extends Mage_Core_Block_Template
{
	/**
	 * author :Luu Thanh Thuy luuthuy205@gmail.com
	 * Enter description here ...
	 */
	public function getBoughtItem() {
		return Mage::registry('boughitems');
	}
	public function getProductId() {
		return Mage::registry('productid');
	}

	public function getAllPost() {
		return Mage::registry('allpost');
	}

	public function getPostDetail() {

		//var_dump(Mage::registry('post'));
		return Mage::registry('post');
	}
	public function getMyExp() {
		//var_dump(Mage::registry('post'));
		return Mage::registry('myexp');
	}

	public function getCustomerId() {

		//var_dump(Mage::registry('post'));
		return Mage::registry('customerid');
	}

public function getMsg() {

		//var_dump(Mage::registry('post'));
		return Mage::registry('msg');
	}
	public function getBookmarkHtml($post){
		if (Mage::getStoreConfig('blog/blog/bookmarkslist'))
		{
			$this->setTemplate('aw_blog/bookmark.phtml');
			$this->setPost($post);
			return $this->toHtml();
		}
		return;
	}
	public function getTagsHtml($post){
		if (trim($post->getTags())){
			$this->setTemplate('aw_blog/line_tags.phtml');
			$this->setPost($post);
			return $this->toHtml();
		}
		return;
	}


	public function getCommentsEnabled(){
		return Mage::getStoreConfig('blog/comments/enabled');
	}

	public function getPages()
	{
		if ((int)Mage::getStoreConfig('blog/blog/perpage') != 0)
		{
			$collection = Mage::getModel('blog/blog')->getCollection()
			->setOrder('created_time ', 'desc');
				
			Mage::getSingleton('blog/status')->addEnabledFilterToCollection($collection);
				
			$currentPage = (int)$this->getRequest()->getParam('page');

			if(!$currentPage){
				$currentPage = 1;
			}
				
			$pages = ceil(count($collection) / (int)Mage::getStoreConfig('blog/blog/perpage'));
				
			$links = "";
				
			$route = Mage::helper('blog')->getRoute();
				
			if ($currentPage > 1)
			{
				$links = $links . '<div class="left"><a href="' . $this->getUrl($route. '/page/' . ($currentPage - 1)) . '" >&lt; '.$this->__('Newer Posts').'</a></div>';
			}
			if ($currentPage < $pages)
			{
				$links = $links .  '<div class="right"><a href="' . $this->getUrl($route .'/page/' . ($currentPage + 1)) . '" >'.$this->__('Older Posts').' &gt;</a></div>';
			}
			echo $links;
		}
	}

	public function getRecent()
	{
		if (Mage::getStoreConfig(AW_Blog_Helper_Config::XML_RECENT_SIZE) != 0)
		{
			$collection = Mage::getModel('blog/blog')->getCollection()
			->addPresentFilter()
			->addStoreFilter(Mage::app()->getStore()->getId())
			->setOrder('created_time ', 'desc');
				
			$route = Mage::helper('blog')->getRoute();
				
			Mage::getSingleton('blog/status')->addEnabledFilterToCollection($collection);
			$collection->setPageSize(Mage::getStoreConfig(AW_Blog_Helper_Config::XML_RECENT_SIZE));
			$collection->setCurPage(1);
			foreach ($collection as $item)
			{
				$item->setAddress($this->getUrl($route . "/" . $item->getIdentifier()));
			}
			return $collection;
		}
		else
		{
			return false;
		}
	}

	public function getCategories()
	{
		$collection = Mage::getModel('blog/cat')->getCollection()
		->addStoreFilter(Mage::app()->getStore()->getId())
		->setOrder('sort_order ', 'asc');

		$route = Mage::helper('blog')->getRoute();

		foreach ($collection as $item)
		{
			$item->setAddress($this->getUrl($route . "/cat/" . $item->getIdentifier()));
		}
		return $collection;
	}

	public function addTopLink()
	{
		if(Mage::helper('blog')->getEnabled()){
			$route = Mage::helper('blog')->getRoute();
			$title = Mage::getStoreConfig('blog/blog/title');
			$this->getParentBlock()->addLink($title, $route, $title, true, array(), 15, null, 'class="top-link-blog"');
		}
	}
	public function addFooterLink()
	{
		if(Mage::helper('blog')->getEnabled()){
			$route = Mage::helper('blog')->getRoute();
			$title = Mage::getStoreConfig('blog/blog/title');
			$this->getParentBlock()->addLink($title, $route, $title, true);
		}
	}

	public function closetags($html){
		return Mage::helper('blog/post')->closetags($html);
	}

	protected function _prepareLayout()
	{

		return parent::_prepareLayout();

	}


	public function _toHtml(){

		return parent::_toHtml();
	}
public function alreadyVote(){

		return Mage::registry('alreadyvote');
	}
	
/**
	 * get the Session
	 */
	protected function _getSession()
	{
		return Mage::getSingleton('customer/session');
	}

	/**
	 * whether the customer login or not
	 *
	 */
	public function  isLogin() {
			
		if ($this->_getSession()->isLoggedIn()) {

			return TRUE;
		}
		return false;
	}
}
