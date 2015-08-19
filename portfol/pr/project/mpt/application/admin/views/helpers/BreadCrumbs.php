<?php

class Zend_View_Helper_BreadCrumbs extends Zend_View_Helper_Abstract {

    function breadCrumbs(array $items, $last_text = null) {
    	?><div class="top-toolbar"><?
    	if(count($items) > 1) {
			$item = $items[1];
			$item = (object)$item;
			?><h3><?=htmlspecialchars($item->title)?></h3><?
    	} elseif($last_text) {
			?><h3><?=htmlspecialchars($last_text)?></h3><?
    	}
        if(count($items)) {
            Breadcrumb::draw($items, $last_text);
        }
        ?></div><?
    }

}