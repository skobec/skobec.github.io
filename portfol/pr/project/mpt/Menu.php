<?php

Plugin_Menu::add('mpt-menu', array(
    new Plugin_Menu_Item(array('code' => 'main', 'title' => 'Главная', 'uri' => '/', 'class' => null)),
//        new Plugin_Menu_Item(array('code' => 'main/months', 'title' => 'По месяцам', 'uri' => '/index/months', 'class' => null)),
    new Plugin_Menu_Item(array('code' => 'main/columns', 'title' => 'Переключение графиков', 'uri' => '/index/columns', 'class' => null)),
    new Plugin_Menu_Item(array('code' => 'main/bar', 'title' => 'Примеры графиков', 'uri' => '/index/bar', 'class' => null)),
    new Plugin_Menu_Item(array('code' => 'main/programs', 'title' => 'По программам', 'uri' => '/index/programs', 'class' => null)),
    new Plugin_Menu_Item(array('code' => 'main/percentage', 'title' => 'По процентам', 'uri' => '/index/percentage', 'class' => null)),
    new Plugin_Menu_Item(array('code' => 'main/economic', 'title' => 'По отраслям', 'uri' => '/index/economic', 'class' => null)),
//    new Plugin_Menu_Item(array('code' => 'upload', 'title' => 'Загрузка', 'uri' => '/uploadfile/event', 'class' => null)),
    new Plugin_Menu_Item(array('code' => 'chart', 'title' => 'Графики', 'uri' => '/chart', 'class' => null)),
    new Plugin_Menu_Item(array('code' => 'chart/other', 'title' => 'Остальные графики', 'uri' => '/chart/other', 'class' => null)),
        )
);

Plugin_Menu::add('desktop-menu', array(
    new Plugin_Menu_Item(array('code' => 'education', 'title' => 'Обучающий', 'uri' => '/desktop/education', 'class' => 'dashboard-switcher-item', 'itemOptions' => array('data-item' => 'swithcer'), 'linkOptions' => array('class' => 'dashboard-switcher-link', 'data-action' => 'switcher'))),
    new Plugin_Menu_Item(array('code' => 'desktop', 'title' => 'Для граждан', 'uri' => '/desktop/index', 'class' => 'dashboard-switcher-item', 'itemOptions' => array('data-item' => 'swithcer'), 'linkOptions' => array('class' => 'dashboard-switcher-link', 'data-action' => 'switcher'))),
    new Plugin_Menu_Item(array('code' => 'media', 'title' => 'Для прессы', 'uri' => '/desktop/media', 'class' => 'dashboard-switcher-item', 'itemOptions' => array('data-item' => 'swithcer'), 'linkOptions' => array('class' => 'dashboard-switcher-link', 'data-action' => 'switcher'))),
    new Plugin_Menu_Item(array('code' => 'personal', 'title' => 'Персональный', 'uri' => '/desktop/personal', 'class' => 'dashboard-switcher-item', 'itemOptions' => array('data-item' => 'swithcer'), 'linkOptions' => array('class' => 'dashboard-switcher-link', 'data-action' => 'switcher'))),
    new Plugin_Menu_Item(array('code' => 'create', 'title' => 'Создать новый', 'uri' => '/desktop/create', 'class' => 'dashboard-switcher-item', 'itemOptions' => array('data-item' => 'swithcer'), 'linkOptions' => array('class' => 'dashboard-switcher-link', 'data-action' => 'switcher')))
));

// Админка
Plugin_Menu::add('menu-admin', array(
   new Plugin_Menu_Item(array('code' => 'main', 'title' => 'Главная', 'uri' => '/admin/', 'class' => null)),
   new Plugin_Menu_Item(array('code' => 'dictionary', 'title' => 'Справочники', 'uri' => '/admin/dictionary/', 'class' => null)),
   new Plugin_Menu_Item(array('code' => 'user', 'title' => 'Пользователи', 'uri' => '/admin/user/', 'class' => null)),
   new Plugin_Menu_Item(array('code' => 'pages', 'title' => 'Страницы', 'uri' => '/admin/pages/', 'class' => null)),
   new Plugin_Menu_Item(array('code' => 'scenarios', 'title' => 'Сценарии', 'uri' => '/admin/scenarios/', 'class' => null)),
   new Plugin_Menu_Item(array('code' => 'widgets', 'title' => 'Виджеты', 'uri' => '/admin/widgets/', 'class' => null)),
));



