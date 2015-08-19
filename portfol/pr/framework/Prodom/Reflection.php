<?php

    class Prodom_Reflection {

        private static $cache = array();

        /**
        * Возвращает отвалидированный список аргументов функции
        * @author sciner
        * @since 2012-04-10
        * 
        * @param array $arguments
        * @param string $className
        * @param string $methodName
        * 
        * @throws Exception
        * @return array
        */
        public static function validateMethodArguments($arguments, $className, $methodName) {
            // документация по указанному меоду
            $method_info = (object)self::parseMethodDoc($className, $methodName);            
            // количество входных аргументов функции
            $parametersCount = count($method_info->params);
            // дополнение пришедших аргументов до нужного количества
            if(count($arguments) != $parametersCount) {
                $arguments = array_pad($arguments, $parametersCount, null);
            }
            self::equalParamType($arguments, $method_info->params, false, null);
            $resp = array();
            $i = 0;
            foreach($method_info->params as $name => $param) {
                $resp[$name] = $arguments[$i++];
            }
            return $resp; // $arguments;
        }

        /**
        * Обработчик аргументов вызываемой функции
        * 
        * @param mixed $args Набор проверяемых аргументов
        * @param mixed $params Описание типов проверяемых аргументов
        * @param mixed $complex Комплексный тип или скалярный
        * 
        * @return bool Признак успешности выполненной проверки
        */
        private static function equalParamType(&$arguments, $params, $complex = false, $type_path = null) {
            // проверка массива на ассоциативность
            if(!array_key_exists(0, $arguments)) {
                $complex = true;
                $arguments = (array)$arguments;
            }
            $pi = -1;
            foreach($params as $param_name => $param) {
                $type = $param['type'];
                if(is_null($type)) {
                    throw new Exception("В описании типа {$type_path} у аргумента {$param_name} не указан тип данных", Prodom_Api_Server::ERR_INTERNAL_ERROR);
                }
                $pi++;
                if($complex) {
                    if(!array_key_exists($param_name, $arguments)) {
                        $type_path = trim($type_path, '/ ');
                        throw new Exception("Параметр {$param_name} не задан в {$type_path}", Prodom_Api_Server::ERR_PARAM_TYPE);
                    }
                    $arg = &$arguments[$param_name];
                } else {
                    if(!array_key_exists($pi, $arguments)) {
                        $type_path = trim($type_path, '/ ');
                        throw new Exception("Параметр {$param_name} не задан в {$type_path}", Prodom_Api_Server::ERR_PARAM_TYPE);
                    }
                    $arg = &$arguments[$pi];
                }
	            if(array_key_exists('require', $param)) {
	                if($param['require'] == 1) {
	                    if(!$arguments[$param_name]) {
	                        $ex_param = $param['description'] ?: $param_name;
	                        throw new Exception("Обязательный параметр \"{$param['description']}\" не заполнен", Prodom_Api_Server::ERR_PARAM_TYPE);
	                    }
	                }
	            }
                $isScalarType = !is_array($type);
                if($isScalarType) {
                    if($arg === '') {
                        $arg = null;
                    }
                    // скалярный тип (строка, число, булево-значение)
                    if(is_null($arg) && array_key_exists('default', $param)) {
                        $arg = $param['default'];
                        if($arg == 'null') {
                            $arg = null;
                        }
                    }
                    if(!is_null($arg)) {
                        // проверка соответствия указанному типу данных
                        $type_correct = true;
                        switch($type) {
                            case 'int': {$type_correct = is_numeric($arg); break;}
                            case 'string': {$type_correct = is_scalar($arg); break;}
                            case 'float': {$type_correct = is_numeric($arg); break;}
                            case 'guid': {$type_correct = Valid::guid($arg); break;}
                            case 'numeric': {$type_correct = is_numeric($arg); break;}
                        }
                        if(!$type_correct) {
                            throw new Exception("Параметр {$param_name} должен иметь тип {$type}", Prodom_Api_Server::ERR_PARAM_TYPE);
                        }
                    }
                } else {
                    $typeName = array_keys($type);
                    $typeName = array_shift($typeName);				
                    // должен ли являться аргумент массивом указанного типа
                    $isArray = substr($typeName, -2, 2) == '[]';
                    $type = array_shift($type);
                    if($isArray) {
                        if(is_null($arg)) {
                            $arg = array();
                        }
                        // наименование проверяемого типа
                        $typeName = substr($typeName, 0, strlen($typeName) - 2);
                        foreach($arg as $index => $arg_item) {
                            // аргумент обязательно должен быть массивом
                            if(!is_array($arg_item) && !(is_object($arg_item) && get_class($arg_item) == $typeName)) {
                                throw new Exception("Параметр {$param_name} должен быть массивом типов {$typeName}", Prodom_Api_Server::ERR_PARAM_TYPE);
                            }
                            self::equalParamType($arg_item, $type, true, $type_path.'/'.$typeName);
                        }
                        // added by Notfoolen   
                        $arg = Functions::castAll($arg, $typeName);
                    } else {
                        if(is_null($arg)) {
                            continue;
                            // throw new Exception("Параметр {$param_name} должен быть объектом типа {$typeName}", Prodom_Api_Server::ERR_PARAM_TYPE);
                        } else {
                            if($typeName == 'Type_Paginator') {
                                $arg = (object)$arg;
                                $arg = new Paginator(null, $arg->items_per_page, $arg->total_pages, $arg->current_page);
                                continue;
                            }
                            if($typeName == 'Type_Sort') {
                            	$arg = (object)$arg;
                                if(!preg_match('/^[A-Za-z0-9_]+$/', $arg->field)) {
                                    $arg->field = 'id';
                                }
                                $dir = strtolower($arg->dir);
                                if(!in_array(strtolower($arg->dir), array('asc', 'desc'))) {
                                    $arg->dir = 'asc';
                                }
                            }
                            self::equalParamType($arg, $type, true, $type_path.'/'.$typeName);
                            // added by Notfoolen
                            $arg = Functions::cast($arg, $typeName);
                        }
                    }
                }
            }
        }

        /**
         * Возвращает массив методов веб службы с описанием параметров
         * 
         * @param string $serviceClass
         */
        public static function getMethods($serviceClass) {
            $methods = get_class_methods($serviceClass);
            $ret = array();
            foreach ($methods as $method) {
                if (substr($method, 0, 2) != '__') {
                    $ret[$method] = self::parseMethodDoc($serviceClass, $method);
                }
            }
            ksort($ret);
            return $ret;
        }

        public static function parseMethodDoc($serviceClass, $method) {
            $reflection = new ReflectionMethod($serviceClass, $method);
            $refParameters = $reflection->getParameters();
            $parametersCount = $reflection->getNumberOfParameters();
            $parameters = array();
            foreach ($refParameters as $refParam) {
                $parameters[] = $refParam->name;
            }
            $methodCall = $method . '(' . implode(', ', $parameters).')';
            $mret = array('call' => $methodCall, 'help' => null, 'author' => null, 'params' => array(), 'return' => null, 'acl' => null);
            foreach($parameters as $p) {
                $mret['params'][$p] = array('type' => null, 'name' => $p, 'description' => null);
            }
            $commentLines = explode("\r\n", $reflection->getDocComment());
            $comment = array();
            foreach ($commentLines as $line) {
                $line = str_replace('  *', null, $line);
                $line = trim($line, '* /');
                if (strlen($line)) {
                    $comment[] = $line;
                }
            }
            $methodAttr = trim(implode("\r\n", $comment));
            $methodAttr = str_replace("\r", null, $methodAttr);
            $methodAttr = str_replace("\t", "\n", $methodAttr);
            while (strpos($methodAttr, '  ') !== false) {
                $methodAttr = str_replace('  ', ' ', $methodAttr);
            }
            while (strpos($methodAttr, "\n\n") !== false) {
                $methodAttr = str_replace("\n\n", "\n", $methodAttr);
            }
            $methodAttr = str_replace("\n*", "\n", $methodAttr);
            $methodAttr = str_replace("\n @", "\n@", $methodAttr);
            $methodAttr = explode("\n", $methodAttr);
            foreach ($methodAttr as $index => $value) {
                $value = trim($value, "\r\t *\n");
                $methodAttr[$index] = $value;
            }
            $methodAttr = implode("\n", $methodAttr);
            $methodAttr = explode("\n@", $methodAttr);
            // dumpr($methodAttr);
            foreach ($methodAttr as $maIndex => $ma) {
                $ma = str_replace("\n", ' ', $ma);
                $ma = str_replace('  ', ' ', $ma);
                $methodAttr[$maIndex] = trim($ma);
            }
            if (count($methodAttr)) {
                if (substr($methodAttr[0], 0, 1) != '@') {
                    $mret['help'] = array_shift($methodAttr);
                } else {
                    // by Notfoolen
                    $methodAttr[0] = substr($methodAttr[0], 1, strlen($methodAttr[0]) - 1);
                }
                foreach ($methodAttr as $mal) {
                    $ma = explode(' ', $mal, 2);
                    $ma = array_pad($ma, 2, null);
                    $ma[0] = trim($ma[0], '* ' . "\t");
                    switch ($ma[0]) {
                        case 'param':
                            $param = explode(' ', $ma[1], 3);
                            $param = array_pad($param, 3, null);
                            $param_name = str_replace('$', null, $param[1]);
                            if(array_key_exists($param_name, $mret['params'])) {
                                $type_name = $param[0];
                                $type = self::getDataTypeDoc($type_name);
                                $mret['params'][$param_name] = array('type' => $type, 'name' => $param_name, 'description' => $param[2]);
                            }
                            break;
                        case 'author':
                            $mret['author'] .= $ma[1];
                            break;
                        case 'return':
                            $mret['return'] .= $ma[1];
                            break;
                        case 'acl':
                            $mret['acl'] .= $ma[1];
                            break;
                        default:
                            if(array_key_exists($ma[0], $mret)) {
                                $mret[$ma[0]] .= $ma[1];
                            } else {
                                $mret[$ma[0]] = $ma[1];
                            }
                            break;
                    }
                }
            }
            $return = explode(' ', $mret['return'], 2);
            $mret['return'] = array('type' => self::getDataTypeDoc($return[0]),
             'description' => count($return) == 2 ? $return[1] : null);
            return $mret;
        }

        public static function getDataTypeDoc($class_name) {
            if(array_key_exists($class_name, self::$cache)) {
                return self::$cache[$class_name];
            }
            if(strpos($class_name, '_') === false) {
                return $class_name;
            }
	        $reflection = new ReflectionClass(str_replace('[]', null, $class_name));
	        $refParameters = $reflection->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
	        $ret = array();
	        foreach($refParameters as $param) {
	            if(!$param->isStatic()) {
	                $ret[$param->name] = self::parseVarDocComment($class_name, $param->getDocComment());
	            }
	        }
            return self::$cache[$class_name] = array($class_name => $ret);
        }

        /**
        * Парсинг типа переменной и ее атрибутов
        * 
        * @param string $class_name
        * @param string $comment
        */
        private static function parseVarDocComment($class_name, $comment) {
            $mret = array('description' => null, 'type' => null, 'require' => 0);
            $commentLines = explode("\r\n", $comment);
            $comment = array();
            foreach ($commentLines as $line) {
                $line = str_replace('  *', null, $line);
                $line = trim($line, '* /');
                if (strlen($line)) {
                    $comment[] = $line;
                }
            }
            $methodAttr = trim(implode("\r\n", $comment));
            $methodAttr = str_replace("\r", null, $methodAttr);
            $methodAttr = str_replace("\t", "\n", $methodAttr);
            while (strpos($methodAttr, '  ') !== false) {
                $methodAttr = str_replace('  ', ' ', $methodAttr);
            }
            while (strpos($methodAttr, "\n\n") !== false) {
                $methodAttr = str_replace("\n\n", "\n", $methodAttr);
            }
            $methodAttr = str_replace("\n*", "\n", $methodAttr);
            $methodAttr = str_replace("\n @", "\n@", $methodAttr);
            // @sciner: На случай, если у описания нет комментария (добавляем "пустой" комментарий)
            $methodAttr = "\n{$methodAttr}";
            $methodAttr = explode("\n", $methodAttr);
            foreach ($methodAttr as $index => $value) {
                $value = trim($value, "\r\t *\n");
                $methodAttr[$index] = $value;
            }
            $methodAttr = implode("\n", $methodAttr);
            $methodAttr = explode("\n@", $methodAttr);
            foreach ($methodAttr as $maIndex => $ma) {
                $ma = str_replace("\n", ' ', $ma);
                $ma = str_replace('  ', ' ', $ma);
                $methodAttr[$maIndex] = trim($ma);
            }
            if (count($methodAttr)) {
                if (substr($methodAttr[0], 0, 1) != '@') {
                    $mret['description'] = array_shift($methodAttr);
                }
                foreach($methodAttr as $ma) {
                    $ma = explode(' ', $ma, 2);
                    $ma = array_pad($ma, 2, null);
                    $ma[0] = trim($ma[0], '* ' . "\t");
                    switch ($ma[0]) {
                        case 'require':
                            $mret['require'] = 1;
                            break;
                        case 'var':
                            $mret['type'] = $ma[1];
                            if((substr($mret['type'], 0, 5) == 'Type_') && ($mret['type'] != $class_name)) {
                                $mret['type'] = self::getDataTypeDoc($mret['type']);
                            }                            
                            break;
                        default:
                            if(array_key_exists($ma[0], $mret)) {
                                $mret[$ma[0]] .= $ma[1];
                            } else {
                                $mret[$ma[0]] = $ma[1];
                            }
                            break;
                    }
                }
            }
            return $mret;          
        }

    }
