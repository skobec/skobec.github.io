<?php
/**
 * Created by PhpStorm.
 * User: mart
 * Date: 13.08.15
 * Time: 9:28
 */
class Model_Admin {

    public static function getDb() {
        return Prodom_Connector::getConnection('db_general');
    }
    /**
     * Возвращает все регионы c учетом фильтра и пагинации
     *
     * @param array $filter
     * @param Type_Paginator $paginator Объект пагинатора
     * @return Type_Admin_Pages_List[]
     *
     * */
    public static function findAllByFilter($filter, $paginator) {
        $db = self::getDb();
        $query = $db
            ->select()
            ->from(['pg'=>'pages'])
//            ->where('parent_id IS NOT NULL')
            ->joinLeft(array('g' => 'page_groups'), 'g.id = pg.group_id', array('group_name' => 'name'))
            ->order('id');
//        if(isset($filter)) {
//            foreach ($filter as $cursor_where_item => $where_item)
//                $query->where($cursor_where_item. ' like ? ', "%{$where_item}%");
//        }
//        dumpr($query->assemble());
        if($paginator) {
            $query->limit($paginator->items_per_page, $paginator->getStartIndex());
        } else {
            throw new Exception('Некорректный диапазон списка', 500);
        }
//        $db->fetchAll($query);
//        dumpr($query->assemble());
        return new Type_Admin_Pages_List(array(
            'items' => Functions::castAll($db->fetchAll($query), 'Type_Admin_Pages_Item'),
            'paginator' => $paginator->getCalculated($db, $query),
        ));

    }

}