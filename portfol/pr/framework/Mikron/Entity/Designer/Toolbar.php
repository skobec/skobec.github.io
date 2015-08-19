<?php

class Mikron_Entity_Designer_Toolbar {

	static $attr = array();

    public static function addTag($name, $attr_list, $text, $position_index = null) {
        $tag = Functions::makeTag($name, $attr_list, $text);
        if(is_numeric($position_index)) {
        	self::$attr["order_{$position_index}"] = $tag;
		} else {
			self::$attr[] = $tag;
		}
    }

    static function draw() {
        if(count(self::$attr)) {
        	/**
        	* @author sciner
        	* @since 2014-11-24
        	*/
        	uksort(self::$attr, function($k1, $k2) {
				if(is_numeric($k1) && is_numeric($k2)) {
					if($k1 < $k2) {
						return -1;
					} elseif($k1 > $k2) {
						return 1;
					}
					return 0;
				} elseif(is_numeric($k1) && !is_numeric($k2)) {
					return -1;
				} elseif(!is_numeric($k1) && is_numeric($k2)) {
					return 1;
				} else {
					if($k1 < $k2) {
						return -1;
					} elseif($k1 > $k2) {
						return 1;
					}
					return 0;
				}
        	});
            ?>
            <div class="toolbar-panel">
            <?=implode("\n", self::$attr)?>
            </div>
            <?
        }
    }

}