<?php

class Prodom_Autoloader
{

    private $prodomRoot = null;

    public function Register() {
        $this->prodomRoot = dirname(__FILE__).'/../';
        return spl_autoload_register(array('Prodom_Autoloader', 'Load'));
    }

    public function Load($className) {
        if ((class_exists($className)) || (strpos($className, 'Prodom') === false)) {
            return false;
        }
        $classFilePath = $this->prodomRoot.str_replace('_', DIRECTORY_SEPARATOR, $className).'.php';
        if ((file_exists($classFilePath) === false) || (is_readable($classFilePath) === false)) {
            $x = explode('_', $className);
            $classFilePath = $this->prodomRoot.str_replace('_', DIRECTORY_SEPARATOR, $className.'_'.$x[count($x)-1]).'.php';
            if ((file_exists($classFilePath) === false) || (is_readable($classFilePath) === false)) {
                return false;
            }
        }
        require($classFilePath);
    }

}
