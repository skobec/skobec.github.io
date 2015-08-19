<?php

if (IS_DEVELOPER_HOST) {
    define('GATEWAY_HOST', 'general.api.omsk');
} else {
    define('GATEWAY_HOST', Constant::VAR_PROJECT_API_URI);
}

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/../api');
define('GATEWAY_LOCAL', dirname(__FILE__) . '/../api');

/**
 * Веб-сервисы
 */
class Service extends Prodom_Api_Services {

    /**
     * @return Service_Billing_Home
     */
    public static function Billing_Home() {
        return self::getProxy(GATEWAY_HOST, __FUNCTION__, SITE_API_KEY);
    }

    /**
     * @return Service_Admin_Locality
     */
    public static function Admin_Locality() {
        return self::getProxy(GATEWAY_HOST, __FUNCTION__, SITE_API_KEY);
    }

    /**
     * @return Service_Billing_Account
     */
    public static function Billing_Account() {
        return self::getLocalProxy(GATEWAY_LOCAL, __FUNCTION__);
    }

    /**
     * @return Service_Log
     */
    public static function Log() {
        return self::getLocalProxy(GATEWAY_LOCAL, __FUNCTION__);
    }

    /**
     * @return Service_Admin_Street
     */
    public static function Admin_Street() {
        return self::getProxy(GATEWAY_HOST, __FUNCTION__, SITE_API_KEY);
    }

    /**
     * @return Service_Admin_Import
     */
    public static function Admin_Import() {
        return self::getLocalProxy(GATEWAY_LOCAL, __FUNCTION__);
    }

    /**
     * @return Service_Sc_Dictionary
     */
    public static function Sc_Dictionary() {
        return self::getProxy(GATEWAY_HOST, __FUNCTION__, SITE_API_KEY);
    }

    /**
     * @return Service_Custom
     */
    public static function Custom() {
        return self::getProxy(GATEWAY_HOST, __FUNCTION__, SITE_API_KEY);
    }

    /**
     * @return Service_Map
     */
    public static function Map() {
        return self::getProxy(GATEWAY_HOST, __FUNCTION__, SITE_API_KEY);
    }

    public static function Cladr() {
        return self::getProxy(GATEWAY_HOST, __FUNCTION__, SITE_API_KEY);
    }

    /**
     * @return Service_Application
     */
    public static function Application() {
        return self::getLocalProxy(GATEWAY_LOCAL, __FUNCTION__);
    }

    /**
     * @return Service_Issue
     */
    public static function Issue() {
        return self::getProxy(GATEWAY_HOST, __FUNCTION__, SITE_API_KEY);
    }

    /**
     * @return Service_Other
     */
    public static function Other() {
        return self::getProxy(GATEWAY_HOST, __FUNCTION__, SITE_API_KEY);
    }

    /**
     * @return Service_Dictionary
     */
    public static function Dictionary() {
        return self::getProxy(GATEWAY_HOST, __FUNCTION__, SITE_API_KEY);
    }

    /**
     * @return Service_Engine
     */
    public static function Engine() {
        return self::getProxy(GATEWAY_HOST, __FUNCTION__, SITE_API_KEY);
    }

    /**
     * @return Service_Address
     */
    public static function Address() {
        return self::getLocalProxy(GATEWAY_LOCAL, __FUNCTION__);
    }

    /**
     * Функции работы с пользователями
     * @return Service_User
     */
    public static function User() {
        return self::getProxy(GATEWAY_HOST, __FUNCTION__, SITE_API_KEY);
    }

    /**
     * E-mail шлюз очереди сообщений
     * @return Service_Email
     */
    public static function Email() {
        return self::getProxy(GATEWAY_HOST, __FUNCTION__, SITE_API_KEY);
    }

    /**
     * @return Service_Organization
     */
    public static function Organization() {
        return self::getProxy(GATEWAY_HOST, __FUNCTION__, SITE_API_KEY);
    }

    /**
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @return Service_Event
     */
    public static function Event() {
        return self::getLocalProxy(GATEWAY_LOCAL, __FUNCTION__, SITE_API_KEY);
    }

    /**
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @return Service_EventPlan
     */
    public static function EventPlan() {
        return self::getLocalProxy(GATEWAY_LOCAL, __FUNCTION__, SITE_API_KEY);
    }

    /**
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @return Service_EventComplete
     */
    public static function EventComplete() {
        return self::getLocalProxy(GATEWAY_LOCAL, __FUNCTION__, SITE_API_KEY);
    }
    
    /**
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @return Service_Scenario
     */
    public static function Scenario() {
        return self::getLocalProxy(GATEWAY_LOCAL, __FUNCTION__, SITE_API_KEY);
    }

    /**
     * 
     * @author sciner
     * @return Service_Widget
     */
    public static function Widget() {
        return self::getLocalProxy(GATEWAY_LOCAL, __FUNCTION__, SITE_API_KEY);
    }
    
    /**
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @return Service_Program
     */
    public static function Program() {
        return self::getLocalProxy(GATEWAY_LOCAL, __FUNCTION__, SITE_API_KEY);
    }
    
    /**
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @return Service_Region
     */
    public static function Region() {
        return self::getLocalProxy(GATEWAY_LOCAL, __FUNCTION__, SITE_API_KEY);
    }
    
    /**
     * 
     * @author Maxim Tugaev <tiugaev@etton.ru, tugmaks@ya.ru>
     * @return Service_Thesaurus
     */
    public static function Thesaurus() {
        return self::getLocalProxy(GATEWAY_LOCAL, __FUNCTION__, SITE_API_KEY);
    }

    /**
     * @return Service_Admin
     */
    public static function Admin() {
        return self::getLocalProxy(GATEWAY_LOCAL, __FUNCTION__);
    }

}
