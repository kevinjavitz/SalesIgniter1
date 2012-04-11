<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxZipsearch extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('zipsearch');

	}

	public function show(){
	    $stores = Doctrine_Query::create()
		    ->select()
		    ->from('Stores')
		    ->orderBy('stores_location')
		    ->execute();	    

	    ob_start();
	    ?>
            	<div class="location-block"><div class="l2"><div class="l3">
                	<div class="img"><img src="/templates/travelbabees/images/left-map.png" width="188" height="129" alt=""></div>
                    <div class="text">
                    	Please enter the delivery zip code or select a city from the list below
                    </div>
                    <div class="zip-code bcont">
                       <form action="/multiStore/zip/default.php" method="get">
                    	<em><input type="text" name="zip" class="input" value=""></em>
                        <input type="submit" value="Search" class="submit">
                        </form>
                    </div>

                    <div class="country">
			<?php foreach ($stores as $current_store): ?>
			    <?php if (!empty($current_store->stores_location)): ?>
				<div class="item"><a href="http://<?php echo $current_store->stores_domain; ?>/index/default.php"><?php echo $current_store->stores_location; ?></a></div>
			    <?php endif; ?>
			<?php endforeach ;?>
                    </div>
                </div></div></div>
	    <?php
	    $content = ob_get_clean();


	    
	    $this->setBoxContent($content);	    
	    return $this->draw();
	}
}