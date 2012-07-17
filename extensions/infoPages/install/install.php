<?php
/*
	Info Pages Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class infoPagesInstall extends extensionInstaller {
	
	public function __construct(){
		parent::__construct('infoPages');
	}
	
	public function install(){

		parent::install();
		
		$Pages = Doctrine_Core::getTable('Pages');
		
		$page = $Pages->create();
		$page->sort_order = '1';
		$page->status = '1';
		$page->infobox_status = '1';
		$page->page_type = 'page';
		$page->page_key = 'conditions';
		$page->PagesDescription[1]->pages_title = 'Our Terms And Conditions';
		$page->PagesDescription[1]->pages_html_text = "<p>\r\n	Put your terms here.&nbsp;</p>\r\n";
		$page->PagesDescription[1]->intorext = '0';
		$page->PagesDescription[1]->language_id = '1';
		$page->save();
		
		$page = $Pages->create();
		$page->sort_order = '2';
		$page->status = '1';
		$page->infobox_status = '0';
		$page->page_type = 'page';
		$page->page_key = 'help_stream';
		$page->PagesDescription[1]->pages_title = 'Streaming Help';
		$page->PagesDescription[1]->pages_html_text = "<p>\r\n	This is to purchase a product stream. After you pay for your purchase and your payment is approved, you will get immediate access to this product&rsquo;s stream using a streaming media player (similar tohulu.com&nbsp;or streaming media sites).</p>\r\n";
		$page->PagesDescription[1]->intorext = '0';
		$page->PagesDescription[1]->language_id = '1';
		$page->save();
		
		$page = $Pages->create();
		$page->sort_order = '3';
		$page->status = '1';
		$page->infobox_status = '0';
		$page->page_type = 'page';
		$page->page_key = 'help_download';
		$page->PagesDescription[1]->pages_title = 'Download Help';
		$page->PagesDescription[1]->pages_html_text = "<p>\r\n	This is to purchase a downloadable file of this product that you can save to your computer. Your download will be available for 30 days from the time of your purchase, but the file on your computer will never expire.</p>\r\n";
		$page->PagesDescription[1]->intorext = '0';
		$page->PagesDescription[1]->language_id = '1';
		$page->save();
		
		$page = $Pages->create();
		$page->sort_order = '1';
		$page->status = '1';
		$page->infobox_status = '1';
		$page->page_type = 'block';
		$page->page_key = 'how_it_works';
		$page->PagesDescription[1]->pages_title = 'How It Works';
		$page->PagesDescription[1]->pages_html_text = "<table border=\"0\" cellpadding=\"6\" cellspacing=\"0\" style=\"width: 100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"middle\">\r\n				<img alt=\"\" height=\"72\" src=\"images/how_it_works_dvd.png\" width=\"72\" /></td>\r\n			<td align=\"left\">\r\n				<b style=\"font-family: arial; color: rgb(156,25,24); font-size: 14px\">Step 1</b><br />\r\n				<span style=\"font-family: arial; color: rgb(59,59,59); font-size: 12px\">Choose The product You Want</span></td>\r\n			<td align=\"middle\">\r\n				<img alt=\"\" height=\"45\" src=\"images/how_it_works_truck.png\" width=\"68\" /></td>\r\n			<td align=\"left\">\r\n				<b style=\"font-family: arial; color: rgb(156,25,24); font-size: 14px\">Step 2</b><br />\r\n				<span style=\"font-family: arial; color: rgb(59,59,59); font-size: 12px\">We ship you your requested titles</span></td>\r\n		</tr>\r\n		<tr>\r\n			<td align=\"middle\">\r\n				<img alt=\"\" height=\"51\" src=\"images/how_it_works_clock.png\" width=\"50\" /></td>\r\n			<td align=\"left\">\r\n				<b style=\"font-family: arial; color: rgb(156,25,24); font-size: 14px\">Step 3</b><br />\r\n				<span style=\"font-family: arial; color: rgb(59,59,59); font-size: 12px\">Watch your product(s) and keep them as long as you want</span></td>\r\n			<td align=\"middle\">\r\n				<img alt=\"\" height=\"48\" src=\"images/how_it_works_arrow.png\" width=\"50\" /></td>\r\n			<td align=\"left\">\r\n				<b style=\"font-family: arial; color: rgb(156,25,24); font-size: 14px\">Step 4</b><br />\r\n				<span style=\"font-family: arial; color: rgb(59,59,59); font-size: 12px\">When you are done return your items, and we&#39;ll ship you your next selections</span></td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n";
		$page->PagesDescription[1]->intorext = '0';
		$page->PagesDescription[1]->language_id = '1';
		$page->save();
		
		$page = $Pages->create();
		$page->sort_order = '4';
		$page->status = '1';
		$page->infobox_status = '0';
		$page->page_type = 'page';
		$page->page_key = 'ssl_check';
		$page->PagesDescription[1]->pages_title = 'Security Check';
		$page->PagesDescription[1]->pages_html_text = "<p>\r\n	We have detected that your browser has generated a different SSL Session ID used throughout our secure pages.<br />\r\n	<br />\r\n	For security measures you will need to logon to your account again to continue shopping online.<br />\r\n	<br />\r\n	Some browsers such as Konqueror 3.1 does not have the capability of generating a secure SSL Session ID automatically which we require. If you use such a browser, we recommend switching to another browser such as <a href=\"http://www.microsoft.com/ie/\" target=\"_blank\">Microsoft Internet Explorer</a>, <a href=\"http://channels.netscape.com/ns/browsers/download_other.jsp\" target=\"_blank\">Netscape</a>, or <a href=\"http://www.mozilla.org/releases/\" target=\"_blank\">Mozilla</a>, to continue your online shopping experience.<br />\r\n	<br />\r\n	We have taken this measurement of security for your benefit, and apologize upfront if any inconveniences are caused.<br />\r\n	<br />\r\n	Please contact the store owner if you have any questions relating to this requirement, or to continue purchasing products offline.</p>\r\n";
		$page->PagesDescription[1]->intorext = '0';
		$page->PagesDescription[1]->language_id = '1';
		$page->save();
		
		$page = $Pages->create();
		$page->sort_order = '5';
		$page->status = '1';
		$page->infobox_status = '0';
		$page->page_type = 'page';
		$page->page_key = 'cookie_usage';
		$page->PagesDescription[1]->pages_title = 'Cookie Usage';
		$page->PagesDescription[1]->pages_html_text = "<p>\r\n	We have detected that your browser does not support cookies, or has set cookies to be disabled.<br />\r\n	<br />\r\n	To continue shopping online, we encourage you to enable cookies on your browser.<br />\r\n	<br />\r\n	For <b>Internet Explorer</b> browsers, please follow these instructions:</p>\r\n<br />\r\n<ol>\r\n	<li>\r\n		Click on the Tools menubar, and select Internet Options</li>\r\n	<li>\r\n		Select the Security tab, and reset the security level to Medium</li>\r\n</ol>\r\n<p>\r\n	We have taken this measurement of security for your benefit, and apologize upfront if any inconveniences are caused.<br />\r\n	<br />\r\n	Please contact the store owner if you have any questions relating to this requirement, or to continue purchasing products offline.</p>\r\n";
		$page->PagesDescription[1]->intorext = '0';
		$page->PagesDescription[1]->language_id = '1';
		$page->save();
		
		$page = $Pages->create();
		$page->sort_order = '2';
		$page->status = '1';
		$page->infobox_status = '0';
		$page->page_type = 'popup';
		$page->page_key = 'info_shopping_cart';
		$page->PagesDescription[1]->pages_title = 'Visitors Cart / Members Cart';
		$page->PagesDescription[1]->pages_html_text = "<p class=\"main\">\r\n	<b><i>Visitors Cart</i></b></p>\r\n<p class=\"main\">\r\n	Every visitor to our online shop will be given a &#39;Visitors Cart&#39;. This allows the visitor to store their products in a temporary shopping cart. Once the visitor leaves the online shop, so will the contents of their shopping cart.</p>\r\n<p class=\"main\">\r\n	<b><i>Members Cart</i></b><br />\r\n	Every member to our online shop that logs in is given a &#39;Members Cart&#39;. This allows the member to add products to their shopping cart, and come back at a later date to finalize their checkout. All products remain in their shopping cart until the member has checked them out, or removed the products themselves.</p>\r\n<p class=\"main\">\r\n	<b><i>Info</i></b><br />\r\n	If a member adds products to their &#39;Visitors Cart&#39; and decides to log in to the online shop to use their &#39;Members Cart&#39;, the contents of their &#39;Visitors Cart&#39; will merge with their &#39;Members Cart&#39; contents automatically.</p>\r\n";
		$page->PagesDescription[1]->intorext = '0';
		$page->PagesDescription[1]->link_target = '1';
		$page->PagesDescription[1]->language_id = '1';
		$page->save();
		
		$page = $Pages->create();
		$page->sort_order = '6';
		$page->status = '1';
		$page->infobox_status = '0';
		$page->page_type = 'page';
		$page->page_key = 'gv_faq';
		$page->PagesDescription[1]->pages_title = 'Gift Voucher FAQ';
		$page->PagesDescription[1]->pages_html_text = "<p>\r\n	<a name=\"Top\"></a><a href=\"#faq1\">Purchasing Gift Vouchers</a><br />\r\n	<a href=\"#faq2\">How to send Gift vouchers</a><br />\r\n	<a href=\"#faq3\">Buying with Gift Vouchers</a><br />\r\n	<a href=\"#faq4\">Redeeming Gift Vouchers</a><br />\r\n	<a href=\"#faq5\">When problems occur</a><br />\r\n	<br />\r\n	<a name=\"faq1\"></a><b>Purchasing Gift Vouchers</b></p>\r\n<p>\r\n	Gift Vouchers are purchased just like any other item in our store. You can pay for them using the stores standard payment method(s). Once purchased the value of the Gift Voucher will be added to your own personal Gift Voucher Account. If you have funds in your Gift Voucher Account, you will notice that the amount now shows in he Shopping Cart box, and also provides a link to a page where you can send the Gift Voucher to some one via email.</p>\r\n<p>\r\n	<a name=\"faq2\"></a><b>How to Send Gift Vouchers</b></p>\r\n<p>\r\n	To send a Gift Voucher that you have purchased, you need to go to our Send Gift Voucher Page. You can find the link to this page in the Shopping Cart Box in the right hand column of each page. When you send a Gift Voucher, you need to specify the following:<br />\r\n	<br />\r\n	The name of the person you are sending the Gift Voucher to.<br />\r\n	The email address of the person you are sending the Gift Voucher to.<br />\r\n	The amount you want to send. (Note you don&#39;t have to send the full amount that is in your Gift Voucher Account.)<br />\r\n	A short message which will apear in the email.<br />\r\n	<br />\r\n	Please ensure that you have entered all of the information correctly, although you will be given the opportunity to change this as much as you want before the email is actually sent.</p>\r\n<p>\r\n	<a name=\"faq3\"></a><b>Buying with Gift Vouchers</b></p>\r\n<p>\r\n	If you have funds in your Gift Voucher Account, you can use those funds to purchase other items in our store. At the checkout stage, an extra box will appear. Clicking this box will apply those funds in your Gift Voucher Account. Please note, you will still have to select another payment method if there is not enough in your Gift Voucher Account to cover the cost of your purchase. If you have more funds in your Gift Voucher Account than the total cost of your purchase the balance will be left in you Gift Voucher Account for the future.</p>\r\n<p>\r\n	<a name=\"faq4\"></a><b>Redeeming Gift Vouchers</b></p>\r\n<p>\r\n	If you receive a Gift Voucher by email it will contain details of who sent you the Gift Voucher, along with possibly a short message from them. The Email will also contain the Gift Voucher Number. It is probably a good idea to print out this email for future reference. You can now redeem the Gift Voucher in two ways.<br />\r\n	1. By clicking on the link contained within the email for this express purpose. This will take you to the store&#39;s Redeem Voucher page. you will the be requested to create an account, before the Gift Voucher is validated and placed in your Gift Voucher Account ready for you to spend it on whatever you want.<br />\r\n	2. During the checkout procces, on the same page that you select a payment method there will be a box to enter a Redeem Code. Enter the code here, and click the redeem button. The code will be validated and added to your Gift Voucher account. You Can then use the amount to purchase any item from our store</p>\r\n<p>\r\n	<a name=\"faq5\"></a><b>When problems occur</b></p>\r\n<p>\r\n	For any queries regarding the Gift Voucher System, please contact the store administrator. Please make sure you give as much information as possible in the email.</p>\r\n";
		$page->PagesDescription[1]->intorext = '0';
		$page->PagesDescription[1]->language_id = '1';
		$page->save();
		
		$page = $Pages->create();
		$page->sort_order = '0';
		$page->status = '1';
		$page->infobox_status = '0';
		$page->page_type = 'popup';
		$page->page_key = 'cvv_help';
		$page->PagesDescription[1]->pages_title = 'CVV Help';
		$page->PagesDescription[1]->pages_html_text = "<p>\r\n	some text here.....</p>\r\n<table border=\"0\" bordercolor=\"#111111\" cellpadding=\"0\" cellspacing=\"0\" height=\"223\" id=\"AutoNumber1\" style=\"border-collapse: collapse;\" width=\"569\">\r\n	<tbody>\r\n		<tr>\r\n			<td>\r\n				<img border=\"0\" height=\"223\" src=\"images/cvv.jpg\" width=\"569\" /></td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n";
		$page->PagesDescription[1]->intorext = '0';
		$page->PagesDescription[1]->link_target = '1';
		$page->PagesDescription[1]->language_id = '1';
		$page->save();
		
		$page = $Pages->create();
		$page->sort_order = '4';
		$page->status = '1';
		$page->infobox_status = '0';
		$page->page_type = 'popup';
		$page->page_key = 'search_help';
		$page->PagesDescription[1]->pages_title = 'Search Help';
		$page->PagesDescription[1]->pages_html_text = "<p>\r\n	Keywords may be separated by AND and/or OR statements for greater control of the search results.<br />\r\n	<br />\r\n	For example, <u>Microsoft AND mouse</u> would generate a result set that contain both words. However, for <u>mouse OR keyboard</u>, the result set returned would contain both or either words.<br />\r\n	<br />\r\n	Exact matches can be searched for by enclosing keywords in double-quotes.<br />\r\n	<br />\r\n	For example, <u>&quot;notebook computer&quot;</u> would generate a result set which match the exact string.<br />\r\n	<br />\r\n	Brackets can be used for further control on the result set.<br />\r\n	<br />\r\n	For example, <u>Microsoft and (keyboard or mouse or &quot;visual basic&quot;)</u>.</p>\r\n";
		$page->PagesDescription[1]->intorext = '0';
		$page->PagesDescription[1]->link_target = '1';
		$page->PagesDescription[1]->language_id = '1';
		$page->save();
		
		$page = $Pages->create();
		$page->sort_order = '5';
		$page->status = '1';
		$page->infobox_status = '0';
		$page->page_type = 'popup';
		$page->page_key = 'ppr_deposit_info';
		$page->PagesDescription[1]->pages_title = 'Deposit Amount';
		$page->PagesDescription[1]->pages_html_text = "<p>\r\n	<span class=\"Apple-style-span\" style=\"border-collapse: separate; color: rgb(0, 0, 0); font-family: \'Times New Roman\'; font-style: normal; font-variant: normal; font-weight: normal; letter-spacing: normal; line-height: normal; orphans: 2; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; font-size: medium;\"><span class=\"Apple-style-span\" style=\"font-family: arial,FreeSans,Helvetica,sans-serif; font-size: 14px; line-height: 20px;\">The deposit amount will be added to your reservation price and refunded to you when your rental item is returned</span></span></p>\r\n";
		$page->PagesDescription[1]->intorext = '0';
		$page->PagesDescription[1]->link_target = '1';
		$page->PagesDescription[1]->language_id = '1';
		$page->save();
	}
	
	public function uninstall($remove = false){
		if (sysConfig::exists('EXTENSION_INFO_PAGES_ENABLED') === false) return;
		
		parent::uninstall($remove);
	}
}
