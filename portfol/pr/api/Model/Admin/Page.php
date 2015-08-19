<?php
/**
 * Created by PhpStorm.
 * User: mart
 * Date: 13.08.15
 * Time: 9:28
 */
class Model_Admin_Page {

    public static function getDb() {
        return Prodom_Connector::getConnection('db_general');
    }

    /**
    * Возвращает список групп
    * @return Type_Page_Group[]
    */
    static function getGroupList() {
        return db::get()->page_group()->order('id desc');
    }

    /**
    * Возвращает список страниц по фильтру
    * 
    * @param Type_Page_Item $filter Филтр
    * 
    * @return Type_Page_Item[]
    */
    static function getPageList($filter) {
        $resp = db::get()->page_item()->order('id desc');
        if($filter->page_group_id) {
            $resp->where('page_group_id', (int)$filter->page_group_id);
        }
        return $resp;
    }

    /**
    * Создание скриншота страниц сайта
    * 
    * @param string $page_url
    * @param string $name
    * @param string $extn
    * @param string $size
    * @param string $format
    * 
    * @return bool
    */
    static function getPagePreview($page_url, $name, $extn = '1024x768', $size = '400', $format = 'jpeg'){
        $url = 'http://mini.s-shot.ru/'.$extn.'/'.$size.'/'.$format.'/?'.urlencode($page_url);
        $str = file_get_contents($url);
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/upload/page/item/preview/'.$name.'.'.$format, $str);
        return true;
    }

}