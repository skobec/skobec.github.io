<?php

class Plugin_Menu {

    private static $variables = array();
    private static $get_params = array();
    private static $hiddenItems = array();
    private static $disabledItems = array();
    private static $activeItems = array();

    private static $selectedItems = array();

    /**
    * Array of menus associating with layouts
    */
    protected static $_menus = array();

    public static function setVariable($name, $value) {
        self::$variables[$name] = $value;
    }

    public static function getVariable($name, $default_value = null) {
        return array_key_exists($name, self::$variables) ? self::$variables[$name] : $default_value;
    }

    /**
    * @author sciner
    * @since 24.07.2013
    */
    public static function getActiveItem($menu_id) {
        if(array_key_exists($menu_id, self::$selectedItems)) {
            return self::$selectedItems[$menu_id];
        }
    }

    /**
    *	Добавление GET параметров в генерируемые ссылки элементов меню
    */
	public static function addParam($params = array()) {
        foreach($params as $name => $value) {
        	self::$get_params[$name] = $value;
        }
    }

    /**
    * Добавление пунктов меню
    * 
    * @param string $menu_id
    * @param Plugin_Menu_Item[] $items
    * @param string $offset добавление элемента со смещением
    */
    public static function add($menu_id, $items, $offset = null) {
        $items = Functions::castAll($items, 'Plugin_Menu_Item');
        if(isset(self::$_menus[$menu_id]) && is_array(self::$_menus[$menu_id]) && count(self::$_menus[$menu_id])) {
                if($offset) {
                    $index = 1;
                    foreach (self::$_menus[$menu_id] as $key => $value) {
                        if ($value->code === $offset)
                        {
                            array_splice(self::$_menus[$menu_id], $index, 0, $items);
                            break;
                        }
                        $index++; 
                    }
                } else {
                    foreach($items as $item_code => $item) {
                            self::$_menus[$menu_id][] = $item;
                    }
                }
        } else {
	        self::$_menus[$menu_id] = $items;
	        self::$hiddenItems[$menu_id] = array();
	        self::$disabledItems[$menu_id] = array();
	        self::$activeItems[$menu_id] = array();
	}
    }

    public static function isAvailable($menu_id, $item_code) {
        $disabled = array_key_exists($menu_id, self::$disabledItems) && array_key_exists($item_code, self::$disabledItems[$menu_id]);
        $hidden = array_key_exists($menu_id, self::$hiddenItems) && array_key_exists($item_code, self::$hiddenItems[$menu_id]);
        $menu_exists = array_key_exists($menu_id, self::$_menus);
        $menu_item_exists = false;
        if($menu_exists) {
            foreach(self::$_menus[$menu_id] as $item) {
                if($item->code == $item_code) {
                    $menu_item_exists = true;
                }
            }
        }
        return !$disabled && !$hidden && $menu_exists && $menu_item_exists;
    }

    /**
     * Скрытие одного или нескольких пунктов меню
     * 
     * @param string $menuID Идентификатор меню
     * @param mixed $itemCodes Строка или массив кодов скрываемых пунктов меню
     */
    public static function hideItem($menu_id, $item_codes) {
        if(!is_array($item_codes)) {
            $item_codes = array($item_codes);
        }
        foreach($item_codes as $code) {
            self::$hiddenItems[$menu_id][] = $code;
        }
    }

    public static function disableItem($menu_id, $item_codes) {
        if(!is_array($item_codes)) {
            $item_codes = array($item_codes);
        }
        foreach($item_codes as $code) {
            self::$disabledItems[$menu_id][] = $code;
        }
    }

    /**
    * Добавление одного или нескольких пунктов меню
    * 
    * @param string $menuID Идентификатор меню
    * @param string $code
    * @param string $title
    * @param string $uri
    * @param string $class
    * 
    * @return bool
    */
    public static function addItem($menu_id, $code, $title, $uri, $class) {
        self::$_menus[$menu_id][$code] = array('title' => $title, 'uri' => $uri, 'class' => $class);
        return true;
    }

    public static function setActive($menu_id, $code) {
        //if(!array_key_exists($menu_id, self::$_menus)) {
        //    return false;
        //}
        self::$activeItems[$menu_id][] = $code;
        return true;
    }

    public static function get($id) {
        return self::$_menus[$id];
    }

    public static function draw($id, $ul_class = null, $params = array()) {
        if(!array_key_exists($id, self::$_menus)) {
            return false;
        }
        self::recvDraw($id, self::$_menus[$id], $ul_class,$params);
    }

    private static function recvDraw($id, $items, $ul_class = null,$params=array()) {
        $index = 0;
        $count = count($items);
        $hiddenItems = array_key_exists($id, self::$hiddenItems) ? self::$hiddenItems[$id] : array();
        $disabledItems = array_key_exists($id, self::$disabledItems) ? self::$disabledItems[$id] : array();
        $activeItems = array_key_exists($id, self::$activeItems) ? self::$activeItems[$id] : array();
        ?>
           <ul id="<?=$id?>" <?if(!is_null($ul_class)){echo " class=\"{$ul_class}\"";}?>>
            <?if(($ul_class=='site-left-menu') && defined ("IS_NEW_DESIGN")){?>
                <li style='padding: .7em 1.1em;' class="menu-main-header">Разделы:</li>
            <?}?>
               <?/** @var \Plugin_Menu_Item $menu */
            foreach($items as $menu) {
                $menu = (object)$menu;
                if(in_array($menu->code, $hiddenItems) || $menu->hidden) {
                    continue;
                }
                $uri = $menu->uri;
                reset(self::$variables);
                foreach(self::$variables as $name => $value) {
                    $uri = str_replace('{$'.$name.'}', $value, $uri);
                    $menu->title = str_replace('{$'.$name.'}', $value, $menu->title);
                }
                foreach(self::$get_params as $name => $value) {
                	if(strpos($uri, '?')) {
                		$uri .= '&';
                	} else {
                		$uri .= '?';
                	}
                	$uri .= $name.'='.$value;
                }
                $class = $menu->class;
                $itemOptions = '';
                foreach ($menu->itemOptions as $key=>$val){
                    $itemOptions.=$key.'="'.$val.'"';
                }
                $linkOptions='';
                foreach ($menu->linkOptions as $key=>$val){
                    $linkOptions.=$key.'="'.$val.'"';
                }
                
                if($index == 0) {$class .= ' first';} elseif ($index == $count-1) {$class .= ' last';}
                $index++;
                if(in_array($menu->code, $activeItems)) {
                    self::$selectedItems[$id] = $menu;
                    
                    $active_class = isset($params['active_class']) ? $params['active_class'] : 'active';
                    $class .= " $active_class";
                }
                if(in_array($menu->code, $disabledItems)) {
                    $class .= ' disabled';
                    ?><li class="<?=$class;?>" <?=$itemOptions?>><a <?=$linkOptions?>><?=$menu->title;?></a></li><?
                } else {
                    ob_start();
                    if(in_array($menu->code, $activeItems)) {
                        if(isset($menu->child) && is_array($menu->child)) {                            
                            self::recvDraw($id.'/'.$menu->code, $menu->child, null);
                        }
                    }
                    $submenu = ob_get_clean();
                    ?><li class="<?=$class;?>" <?=$itemOptions?>><a href="<?=$uri;?>" <?=$linkOptions?>><?=$menu->title;?></a><?=$submenu?></li><?
                }
             } ?>
           </ul>
        <?php
    }

}