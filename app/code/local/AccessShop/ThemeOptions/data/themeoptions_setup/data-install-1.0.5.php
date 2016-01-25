<?php
/**
 * @author		AccessShop
 * @copyright	Copyright 2014
 */
$installer = $this;
$installer->startSetup();

//WYSIWYG hidden by default
Mage::getConfig()->saveConfig('cms/wysiwyg/enabled', 'hidden');

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$cmsBlocks = array(
    array(
        'title'         => 'Header Top Links',
        'identifier'    => 'block_header_links_right_middle',
        'content'       => "<ul class='custom-link list-inline'><li><a title='Blog' href='{{store_url=''}}blog'>Blog</a></li><li><a title='My Wishlist' href='{{store_url=''}}company/'>Company</a></li></ul>",
        'is_active'     => 1,
        'stores'        => 0
    ),
    array(
        'title'         => 'Home Carousel',
        'identifier'    => 'home-carousel',
        'content'       => '<div class="banner">
                            	<div class="sequence-theme">
                            			<div id="sequence">
                            
                            				<div class="banner-arrow-container">
                                                            <img class="sequence-prev" src="{{skin url="images/bt-prev.png"}}" alt="Previous Frame" />
                            				<img class="sequence-next" src="{{skin url="images/bt-next.png"}}" alt="Next Frame" />
                                                            </div>
                            
                            				<ul class="sequence-canvas">
                            					<li class="animate-in bg1">
                            <div class="banner-container">
                            						<h2 class="title">Access <span>magento</span></h2>
                            						<h3 class="subtitle">Provide your customers with rich shopping experience!</h3>
                            						<img class="model" src="{{skin url="images/christmastree.png"}}" alt="Model 1" />
                            </div>
                            					</li>
                            					<li class="bg2">
                            <div class="banner-container">
                            						<h2 class="title">Fully <span>responsive</span> design</h2>
                            						<h3 class="subtitle">This theme can adapt any mobile screen resolution</h3>
                            						<img class="model" src="{{skin url="images/computer.png"}}" alt="Model 2" />
                            </div>
                            					</li>
                            					<li class="bg3">
                            <div class="banner-container">
                            						<h2 class="title">Customizable Theme</h2>
                            						<h3 class="subtitle">This theme is extremely customizable and very easy to use.</h3>
                            						<img class="model" src="{{skin url="images/magentosheet.png"}}" alt="Model 3" />
                            </div>
                            					</li>
                            				</ul>
                            
                            				<ul class="sequence-pagination">
                            					<li>1</li>
                            					<li>2</li>
                            					<li>3</li>
                            				</ul>
                            
                            			</div>
                            		</div>
                            </div>',
        'is_active'     => 1,
        'stores'        => 0
    ),
    array(
        'title'         => 'Focus Content',
        'identifier'    => 'home-block-below-slider',
        'content'       => '<div class="focus-content">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="focus-img foucs-red-bg"><i class="fa fa-paint-brush fa-fw"></i></div>
                                        <div class="focus-desc">
                                            <div class="focus-caption">CUSTOMIZABLE DESIGN</div>
                                            <h2>Easy Css Customization</h2>
                                            <p>Everything is so clean that anything is easy to change.</p>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="focus-img foucs-green-bg"><i class="fa fa-bars fa-fw"></i></div>
                                        <div class="focus-desc">
                                            <div class="focus-caption">12-COLUMN GRID</div>
                                            <h2>Responsive Layout</h2>
                                            <p>Using Bootstrap 12-column grid is very easy and convenient.</p>
                                        </div>                            
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="focus-img foucs-blue-bg"><i class="fa fa-mobile fa-fw"></i></div>
                                        <div class="focus-desc">
                                            <div class="focus-caption">AMAZING SLIDER</div>
                                            <h2>Sequence Slider</h2>
                                            <p>You can attract anyone with impressive slider with animation effects.</p>
                                        </div>
                                    </div>
            
                                </div>
                            </div>',
        'is_active'     => 1,
        'stores'        => 0
    ),
    array(
        'title'         => 'Homepage Middle Banners',
        'identifier'    => 'home-middle-banners',
        'content'       => '<div class="home-middle-banners">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="box-left">
                                            <img src="{{skin url="images/large-promotional-banner.jpg"}}" />
                                        </div>
                                        <div class="box-right">
                                            <img src="{{skin url="images/small-seasonal-banner.jpg"}}" />
                                        </div>
                                    </div>
                                </div>
                            </div>',
        'is_active'     => 1,
        'stores'        => 0
    ),
    array(
        'title'         => 'Home Block Below Best Sellers',
        'identifier'    => 'home-block-below-best-sellers',
        'content'       => '<div class="responsive-box">
                                <div class="row">
                                <div class="col-sm-12">
                                                        <div class="responsive-features-left">
                                <div class="left-image wow slideInLeft">
                                                            <div class="responsive-display-img">
                                                                <img src="{{skin url="images/responsive-image.jpg"}}" align="resonsive" />
                                                            </div>
                                                        </div>
                                                        <div class="middle-text wow slideInRight">
                                                            <div class="responsive-desc">
                                                                <ul class="desc-counter">
                                                                    <li>
                                                                        <h3>Responsive Layout</h3>
                                                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut </p>
                                                                    </li>
                                                                    <li>
                                                                        <h3>Responsive Layout</h3>
                                                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut </p>
                                                                    </li>
                                                                    <li>
                                                                        <h3>Responsive Layout</h3>
                                                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut </p>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                </div>
                                <div class="responsive-features-right wow slideInRight">
                                                            <div class="right-image">
                                                                <img src="{{skin url="images/responsive-image-2.png"}}" align="resonsive" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>',
        'is_active'     => 1,
        'stores'        => 0
    ),
    array(
        'title'         => 'Home Block Brands',
        'identifier'    => 'home-block-brands',
        'content'       => '<div class="container">
                                    <div class="row">
                                        <div class="best-sales-title">
                                            <h2>Brands</h2>
                            <div class="slidercontrols">
                            <a class="prev" id="prev3" href="#">Prev</a>
                            <a class="next" id="next3" href="#">Next</a>
                            </div>
                            
                                        </div>
                                        <div class="col-sm-12">
                                            <ul class="brands carousel" data-carousel-prev="#prev3" data-carousel-next="#next3">
                                                <li><a href="#" class="col-md-2"><img src="{{skin url="images/brands/brand1.gif"}}" title="CHEMICEZ"/></a></li>
                                                <li><a href="#" class="col-md-2"><img src="{{skin url="images/brands/brand2.gif"}}" title="CHEMICEZ"/></a></li>
                                                <li><a href="#" class="col-md-2"><img src="{{skin url="images/brands/brand3.gif"}}" title="CHEMICEZ"/></a></li>
                                                <li><a href="#" class="col-md-2"><img src="{{skin url="images/brands/brand4.gif"}}" title="CHEMICEZ"/></a></li>
                                                <li><a href="#" class="col-md-2"><img src="{{skin url="images/brands/brand5.gif"}}" title="CHEMICEZ"/></a></li>
                                                <li><a href="#" class="col-md-2"><img src="{{skin url="images/brands/brand1.gif"}}" title="CHEMICEZ"/></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>',
        'is_active'     => 1,
        'stores'        => 0
    ),
    array(
        'title'         => 'Footer Top Block',
        'identifier'    => 'footer-top-block',
        'content'       => '<div class="container">
                                <div class="row">
                                    <div class="services first">
                                        <div class="service-img">
                                            <img src="{{skin url="images/communication-care.gif"}}" alt="communication" />
                                        </div>
                                        <div class="service-desc">
                                            <h4>Customer Care</h4>
                                            <p>+123456789</p>
                                        </div>
                                    </div>
                                    <div class="services">
                                        <div class="service-img">
                                            <img src="{{skin url="images/money-back.gif"}}" alt="communication" />
                                        </div>
                                        <div class="service-desc">
                                            <h4>Money Back Guarantee</h4>
                                            <p>At vero santam testa</p>
                                        </div>
                                    </div>
                                    <div class="services">
                                        <div class="service-img">
                                            <img src="{{skin url="images/special-gift.gif"}}" alt="communication" />
                                        </div>
                                        <div class="service-desc">
                                            <h4>Special Gift Cards</h4>
                                            <p>Get your free gifts</p>
                                        </div>
                                    </div>
                                    <div class="services last">
                                        <div class="service-img">
                                            <img src="{{skin url="images/free-shipping.gif"}}" alt="communication" />
                                        </div>
                                        <div class="service-desc">
                                            <h4>Free Shipping Order</h4>
                                            <p>On All order above $99</p>
                                        </div>
                                    </div>
                                </div>
                            </div>',
        'is_active'     => 1,
        'stores'        => 0
    ),
    array(
        'title'         => 'Footer Cms Links',
        'identifier'    => 'footer-cmslinks',
        'content'       => '<h4>Shopping Guide</h4>
                            <ul>
                            <li><a href="{{store url}}how-to-buy">How to buy</a></li>
                                <li><a href="{{store url}}faqs">FAQs</a></li>
                                <li><a href="{{store url}}payments">Payment</a></li>
                                <li><a href="{{store url}}shipments">Shipment</a></li>
                                <li><a href="{{store url}}where-is-my-order">Where is my order?</a></li>
                                <li><a href="{{store url}}return-policy">Return policy</a></li>
                            </ul>',
        'is_active'     => 1,
        'stores'        => 0
    ),
    array(
        'title'         => 'Footer Cms Links 1',
        'identifier'    => 'footer-cmslinks1',
        'content'       => '<h4>Style Advisor</h4>
                            <ul>
                            <li><a href="{{store url}}how-to-buy">How to buy</a></li>
                                <li><a href="{{store url}}faqs">FAQs</a></li>
                                <li><a href="{{store url}}payments">Payment</a></li>
                                <li><a href="{{store url}}shipments">Shipment</a></li>
                                <li><a href="{{store url}}where-is-my-order">Where is my order?</a></li>
                                <li><a href="{{store url}}return-policy">Return policy</a></li>
                            </ul>',
        'is_active'     => 1,
        'stores'        => 0
    ),
    array(
        'title'         => 'Footer Contact Info',
        'identifier'    => 'footer-contact-info',
        'content'       => '<h4>Contact Info :</h4>
                            <p>
            				<strong>Mathuri Sadan, 5th floor</strong><br>
            				 Ravi Bhawan, Kathmandu, Nepal<br>
            				<i class="fa fa-skype"></i> <a href="Skype:access-keys">access-keys</a><br><br>
                            <strong class="white">General enquiries:</strong> <a href="mailto:info@accessshopthemes.com">info@accessshopthemes.com</a><br />
                            <strong class="white">Support:</strong> <a href="mailto:support@accessshopthemes.com">support@accessshopthemes.com</a><br />
                            <strong class="white">Sales:</strong> <a href="mailto:sales@accessshopthemes.com">sales@accessshopthemes.com</a>
                            </p>',
        'is_active'     => 1,
        'stores'        => 0
    ),
    array(
        'title'         => 'Footer Social',
        'identifier'    => 'footer-social',
        'content'       => '<h4>Stay Connected</h4>
                            <ul class="list-inline footer-social">
                                <li><a href="#" class="ico-facebook"><i class="fa fa-facebook"></i></a></li>
                                <li><a href="#" class="ico-twitter"><i class="fa fa-twitter"></i></a></li>
                                <li><a href="#" class="ico-google"><i class="fa fa-google-plus"></i></a></li>
                                <li><a href="#" class="ico-linkedin"><i class="fa fa-linkedin"></i></a></li>
                            </ul>',
        'is_active'     => 1,
        'stores'        => 0
    ),
    array(
        'title'         => 'Footer Payment Cards',
        'identifier'    => 'footer-cards',
        'content'       => '<h4>Secure Payments</h4>
                            <ul class="list-inline footer-payment">
                                <li><a href="#" class="ico-amercian-express">Amercian Express</a></li>
                                <li><a href="#" class="ico-mastercard">Mastercard</a></li>
                                <li><a href="#" class="ico-payment">Payment</a></li>
                                <li><a href="#" class="ico-discover">Discover</a></li>
                            </ul>',
        'is_active'     => 1,
        'stores'        => 0
    ),
    array(
        'title'         => 'Footer About Us',
        'identifier'    => 'footer-company',
        'content'       => "AccessShop Themes - offers the best solution for your online store. Get feature-rich, fully responsive, beautifully designed modern Magento themes and customize them to your need. You can customize the theme to fit your need. Yes, customize almost everything. And find a variety of useful extensions and extend your magento shop to what and how you need.",
        'is_active'     => 1,
        'stores'        => 0
    ),
    array(
        'title'         => 'Education',
        'identifier'    => 'custom-tab1',
        'content'       => "Education on how to get products in the site.",
        'is_active'     => 1,
        'stores'        => 0
    )
);
foreach ($cmsBlocks as $data) {
    Mage::getModel('cms/block')->setData($data)->save();
}

$installer->endSetup();