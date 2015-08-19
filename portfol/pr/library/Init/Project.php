<?php

class Init_Project extends Prodom_Helper_Init {

    function postInit() {
        //!IS_DEVELOPER_HOST ? Config::read(CONFIG_DIR.'/db.json'): Config::read(CONFIG_DIR.'/db_local.json');
    	Config::read(CONFIG_DIR.'/db.json');
        if(in_array($this->controllerName, array('free', 'error', 'cross'))) {
            return true;
        }
    }

}
