<?php

class Prodom_Application_Generator {
    
    /**
    * Возвращает список проектов (модулей)
    * @since 12.08.2013
    * @author sciner
    */
    private static function getApplicationListMenuStructure() {
        $list = service::Engine()->getApplicationList();
        $resp = array();
        foreach($list as $item) {
            $resp[] = (object)array(
                'id' => $item->id,
                'code' => $item->code,
                'title' => $item->title,
            );
        }
        return $resp;
    }

    /**
    * put your comment there...
    * 
    * @param string $project_code Код application
    */
    private static function getMenuStructure($project_code) {
        $resp = service::Engine()->getMenu($project_code);
        $app_list = array();
        foreach($resp as $item) {
            if(!array_key_exists($item->application_id, $app_list)) {
                $app_list[$item->application_id] = (object)array(
                    'id' => $item->application_id,
                    'code' => $item->application_code,
                    'title' => $item->application_title,
                    'module_list' => array(),
                );
            }
            if($item->module_id) {
                if(!array_key_exists($item->module_id, $app_list[$item->application_id]->module_list)) {
                    $app_list[$item->application_id]->module_list[$item->module_id] = (object)array(
                        'id' => $item->module_id,
                        'code' => $item->module_code,
                        'title' => $item->module_title,
                        'controller_list' => array(),
                    );
                }
            }
            if($item->controller_id) {
                if(!array_key_exists($item->controller_id, $app_list[$item->application_id]->module_list[$item->module_id]->controller_list)) {
                    $app_list[$item->application_id]->module_list[$item->module_id]->controller_list[$item->controller_id] = (object)array(
                        'id' => $item->controller_id,
                        'code' => $item->controller_code,
                        'title' => $item->controller_title,
                        'action_list' => array(),
                    );
                }
            }
            if($item->action_id) {
                if(!array_key_exists($item->action_id, $app_list[$item->application_id]->module_list[$item->module_id]->controller_list[$item->controller_id]->action_list)) {
                    $app_list[$item->application_id]->module_list[$item->module_id]->controller_list[$item->controller_id]->action_list[$item->action_id] = (object)array(
                        'id' => $item->action_id,
                        'code' => $item->action_code,
                        'title' => str_replace('.0', '.', $item->action_title),
                    );
                }
            }
        }
        return $app_list;
    }

    private static function generateMenu($app_list) {
        // $project_menu_list = array();
        // $project_menu = "Plugin_Menu::add('project-menu', array(\n";
        $main_menu = array();
        $controller_menu_list = array();
        foreach($app_list as $app) {
            $rd = Settings::get('root_domain');
            // $project_menu .= "\tnew Plugin_Menu_Item(array('code' => '{$app->code}', 'title' => '{$app->title}', 'uri' => '//{$app->code}.{$rd}', 'class' => null)),\n";
            $module_menu = "\tPlugin_Menu::add('{$app->code}-menu', array(\n";
            foreach($app->module_list as $module) {
                $controller_menu = "\t\tPlugin_Menu::add('{$module->code}', array(\n";
                $default_module_index = null;
                $module_index_checker = false;
                foreach($module->controller_list as $controller) {
                    if($controller->code == 'index') {
                        $module_index_checker = true;
                    }
                    if(!$default_module_index) {
                        $default_module_index = $controller->code;
                    }
                    $controller_menu .= "\t\t\tnew Plugin_Menu_Item(array('code' => '{$controller->code}', 'title' => '{$controller->title}', 'uri' => '/{$module->code}/{$controller->code}/', 'class' => null, 'child' => array(\n";
                    //$index_action_is_present = false;
                    foreach($controller->action_list as $action) {
                        if($action->code == 'index') {
                            continue;
                        }
                        $controller_menu .= "\t\t\t\tnew Plugin_Menu_Item(array('code' => '{$action->code}', 'title' => '{$action->title}', 'uri' => '/{$module->code}/{$controller->code}/{$action->code}/', 'class' => null)),\n";
                    }
                    $controller_menu .= "\t\t\t))),\n";
                }
                $controller_menu .= "\t\t));";
                $controller_menu_list[] = $controller_menu;
                if($module_index_checker) {
                    $default_module_index = 'index';
                }
                $module_menu .= "\t\tnew Plugin_Menu_Item(array('code' => '{$module->code}', 'title' => '{$module->title}', 'uri' => '/{$module->code}/{$default_module_index}', 'class' => null)),\n";
            }
            $module_menu .= "\t));";
            $main_menu[] = $module_menu;
        }
        // $project_menu .= '));';
        // $project_menu_list[] = $project_menu;
        return array_merge($main_menu, $controller_menu_list);
    }

    public static function menu($project_code, $menu_file_name = null) {
        $file_name = $menu_file_name;
        if(!$menu_file_name) {
            $file_name = dirname(__FILE__).'/../../../project/'.$project_code.'/public/Menu.php';
        }
        // очистка файла
        file_put_contents($file_name, "<?"."php\n\n");
        // список апликейшенов
        $rd = Settings::get('root_domain');
        /*
        $project_menu = "Plugin_Menu::add('project-menu', array(\n";
        $app_list = self::getApplicationListMenuStructure();
        foreach($app_list as $app) {
            $project_menu .= "\tnew Plugin_Menu_Item(array('code' => '{$app->code}', 'title' => '{$app->title}', 'uri' => '//{$app->code}.{$rd}', 'class' => null)),\n";
        }
        $project_menu .= "));\n\n\n";
        file_put_contents($file_name, $project_menu, FILE_APPEND);
        */
        file_put_contents($file_name, "\tModule_Common::initMenu();\n\n", FILE_APPEND);
        // список модулей текущего измененного апликейшена
        $controller_list = self::getMenuStructure($project_code);
        $menus = self::generateMenu($controller_list);
        foreach($menus as $menu) {
            file_put_contents($file_name, $menu, FILE_APPEND);
            file_put_contents($file_name, "\n\n\n", FILE_APPEND);
        }
    }

    public static function controller($controller_id, $type = null) {
        $controller = Service::Engine()->getController($controller_id);
        $action_list = Service::Engine()->getActionList($controller_id);
        $module = Service::Engine()->getModule($controller->module_id);
        $application = Service::Engine()->getApplication($module->application_id);

        $controller_template = file_get_contents(dirname(__FILE__).'/../../../library/layout/template/controller.phtml');
        $action_template = file_get_contents(dirname(__FILE__).'/../../../library/layout/template/controller/action.phtml');
        $index_action = file_get_contents(dirname(__FILE__).'/../../../library/layout/template/controller/index.phtml');
        $index_action_checker = file_get_contents(dirname(__FILE__).'/../../../library/layout/template/controller/index_checker.phtml');
        
        $index_action_is_present = false;
        
        $action_buf = array();
        $default_action_name = null;
        foreach($action_list as $action) {
            if(!$default_action_name) {
                $default_action_name = $action->code;
            }
            if($action->code == 'index') {
                $index_action_is_present = true;
            }
            $at = $action_template;
            $at = str_replace('{$action}', $action->code, $at);
            $at = str_replace('{$application}', $application->code, $at);
            $at = str_replace('{$module}', $module->code, $at);
            $at = str_replace('{$controller}', $controller->code, $at);
            $action_buf[] = $at;
        }

        // проверка/перенаправлятор
        if($index_action_is_present) {
            $index_action_checker = null;
        } else {
            $action_buf[] = $index_action;
        }
        $ct = str_replace('{$index_checker}', $index_action_checker, $controller_template);

        $ct = str_replace('{$action_list}', implode("\r\n\r\n", $action_buf), $ct);
        $ct = str_replace('{$default_action_name}', $default_action_name, $ct);
        $ct = str_replace('{$module}', ucfirst($module->code), $ct);
        $ct = str_replace('{$controller}', ucfirst($controller->code), $ct);

        $controller_dir = dirname(__FILE__)."/../../../project/{$application->code}/application/{$module->code}/controllers";
        if(!file_exists($controller_dir)) {
            mkdir($controller_dir, 0777, true);
        }
        $controller_file = $controller_dir.'/'.ucfirst($controller->code).'Controller.php';
        file_put_contents($controller_file, $ct);

        // views //
        foreach($action_list as $action) {
            self::view($action->id);
        }

    }

    private static $application = null;
    private static $module = null;
    private static $controller = null;
    private static $action = null;

    static function view($action_id) {
        $action = self::$action = Service::Engine()->getAction($action_id);
        $controller = self::$controller = Service::Engine()->getController($action->controller_id);
        $module = self::$module = Service::Engine()->getModule($controller->module_id);
        $application = self::$application = Service::Engine()->getApplication($module->application_id);
        $action_group_list = Service::Engine()->getFieldGroupList($action_id);
        $field_list = Service::Engine()->getFieldList($action_id);

        $view_dir = dirname(__FILE__)."/../../../project/{$application->code}/application/{$module->code}/views/scripts/{$controller->code}";
        if(!file_exists($view_dir)) {
            mkdir($view_dir, 0777, true);
        }

        $tmpl_dir = dirname(__FILE__).'/../../../library/layout/template';

        $view_template = file_get_contents("{$tmpl_dir}/view.phtml");
        $control_template = file_get_contents("{$tmpl_dir}/control.phtml");
        $group_template = file_get_contents("{$tmpl_dir}/group.phtml");
        $inner_group_template = file_get_contents("{$tmpl_dir}/group_inner.phtml");
        $inner_table_template = file_get_contents("{$tmpl_dir}/inner_table.phtml");

        $templates = (object)array(
            'file' => file_get_contents("{$tmpl_dir}/view/file.phtml"),
            'bool' => file_get_contents("{$tmpl_dir}/view/bool.phtml"),
            'entity' => file_get_contents("{$tmpl_dir}/view/entity.phtml"),
            'date' => file_get_contents("{$tmpl_dir}/view/date.phtml"),
            'save' => file_get_contents("{$tmpl_dir}/view/save.phtml"),
            'string' => file_get_contents("{$tmpl_dir}/view/string.phtml"),
            'text' => file_get_contents("{$tmpl_dir}/view/textarea.phtml"),
            'list' => file_get_contents("{$tmpl_dir}/view/list.phtml"),
            'table_add' => file_get_contents("{$tmpl_dir}/table/add.phtml"),
            'table_list' => file_get_contents("{$tmpl_dir}/table/list.phtml"),
            'help' => file_get_contents("{$tmpl_dir}/help.phtml"),
            'inner_table_template' => $inner_table_template,
            'control_template' => $control_template,
            'group_template' => $group_template,
            'inner_group_template' => $inner_group_template,
        );

        $vt = $view_template;

        // поля вне групп
        $fields_without_group = self::controls($field_list, null, null, $templates);
        $vt = str_replace('{$fields_without_group}', $fields_without_group, $vt);

        $inner_tables = self::getInnerTables($action_id, null, $templates);
        $vt = str_replace('{$inner_tables}', $inner_tables, $vt);

        // кнопка сохранения
        $ctl = $control_template;
        $ctl = str_replace('{$code}', 'save-main', $ctl);
        $ctl = str_replace('{$title}', null, $ctl);
        $ctl = str_replace('{$input}', $templates->save, $ctl);

        $group_html = null;
        foreach($action_group_list as $group) {
            if(!$group->parent_id) {
                $group_html .= self::group($action_group_list, $group, $field_list, $templates);
            }
        }

        // проверка наличия полей корневой группы и добавление кнопки сохранения для нее
        $root_save = false;
        foreach($field_list as $item) {
        	if(!$item->group_id) {
        		$root_save = true;
        	}
        }
        $ctl = $root_save ? $ctl : '';
        $vt = str_replace('{$save}', $ctl, $vt);
        $vt = str_replace('{$groups}', $group_html, $vt);

        // Yahoooooo!!!
        $view_file = "{$view_dir}/{$action->code}.phtml";
        file_put_contents($view_file, $vt);
        
        // meta
        self::meta(null, $field_list);
    }

    private static function group($group_list, $group, $field_list, $templates) {
        $group_html = $group->parent_id ? $templates->inner_group_template : $templates->group_template;
        $group_html = str_replace('{$title}', $group->title, $group_html);
        $inner_tables = self::getInnerTables($group->action_id, $group->id, $templates);
        $group_html = str_replace('{$inner_tables}', $inner_tables, $group_html);
        $content = self::controls($field_list, $group->id, null, $templates);
        $hide_save_button = is_null($content);
        foreach($group_list as $in_group) {
           if($in_group->parent_id != $group->id) {
               continue;
           }
           $hide_save_button = false;
           $content .= self::group($group_list, $in_group, $field_list, $templates);
        }
        // кнопка сохранения
        if($hide_save_button || $group->parent_id) {
            $group_html = str_replace('{$save}', null, $group_html);            
        } else {
            $ctl = $templates->control_template;
            $ctl = str_replace('{$code}', "save-group-{$group->id}", $ctl);
            $ctl = str_replace('{$title}', null, $ctl);
            $ctl = str_replace('{$input}', $templates->save, $ctl);
            $group_html = str_replace('{$save}', $ctl, $group_html);
        }
        $group_html = str_replace('{$content}', $content, $group_html);
        return $group_html;
    }

    private static function getInnerTables($action_id, $group_id, $templates) {
        $tf = Service::Engine()->getActionTablesFieldList($action_id);
        $tables_fields = array();
        foreach($tf as $table) {
            $tables_fields[$table->table_id] = $table->fields;
        }
        $resp = array();
        $tables = Service::Engine()->getTableList($action_id);
        foreach($tables as $table) {
            /**
             * @author notfoolen
             * @since 2014.08.21
             * Скрыть таблицу 7.7 Минимальный взнос с собственника на капитальный ремонт с 1 м2 жилья, в руб
             */
            if($table->action_id = 165 && $table->code == 'minvznos') {
                continue;
            }
            if($table->group_id != $group_id) {
                continue;
            }
            $t = $templates->inner_table_template;
            $field_table_q = 'qqqqq-qqqqqqqq-qqqqqqq-qqqqqq-qqq'; // Service::Application()->getTableQ('table', $table->id);
            $t = str_replace('{$q}', $field_table_q, $t);
            $t = str_replace('{$inner_table_name}', $table->title, $t);
            $t = str_replace('{$code}', $table->code, $t);
            $controls = self::controls($tables_fields[$table->id], null, $table->id, $templates);
            $tmpl = str_replace('{$code}', $table->code, $templates->table_add);
            $table_row_template = str_replace('{$content}', $controls, $tmpl);
            $t = str_replace('{$table_row_template}', $table_row_template, $t);
            $t = str_replace('{$content}', $controls, $t);
            $resp[] = $t;
            self::meta($table, $tables_fields[$table->id]);
        }
        return implode("\r\n\r\n", $resp);
    }

    private static function controls($field_list, $group_id, $table_id, $templates) {
        $buf = null;
        foreach($field_list as $field) {
           if($field->options_hidden) {
               continue;
           }
           if($field->group_id != $group_id) {
                continue;
           }
           if($field->table_id != $table_id) {
                continue;
           }
           $help = $field->help;
           $required = $field->required == 1;
           switch($field->type) {
               case Engine::TYPE_BOOL:
                    $input = $templates->bool;
                    break;
               case Engine::TYPE_TEXT:
                    $input = $templates->text;
                    break;
               case Engine::TYPE_LIST:
                    $input = null;
                    if($field->table_id) {
                        $input = $templates->table_list;
                    } else {
                        $input = $templates->list;
                    }
                    break;
               case Engine::TYPE_DATE:
                    $input = $templates->date;
                    break;
               case Engine::TYPE_FILE:
                    $input = $templates->file;
                    break;
               case Engine::TYPE_ENTITY:
                    $input = $templates->entity;
                    break;
               default:
                    $input = $templates->string;
                    break;
           }
           $validators = null;
           if($field->required) {
               $validators[] = 'required';
           }
           if($field->type == 0) {
               $validators[] = 'custom[integer]';
           } elseif($field->type == 7) {
               $validators[] = 'custom[float]';
           }
           $validators = $validators ? 'validate['.implode(',', $validators).']' : null;
           $input = str_replace('{$validator}', $validators, $input);
           $input = str_replace('{$code}', $field->code, $input);
           $input = str_replace('{$title}', $field->title, $input);
           $input = str_replace('{$help}', $help ? str_replace('{$help}', $help, $templates->help) : null, $input);
           $ctl = $templates->control_template;
           $ctl = str_replace('{$code}', $field->code, $ctl);
           $ctl = str_replace('{$title}', $field->title. ($required ? '<span class="label-warning">*</span>' : null), $ctl);
           $ctl = str_replace('{$input}', $input, $ctl);
           $buf .= $ctl;
        }
        return $buf;
    }
    
    /**
    * Метаинформация
    * 
    * @param object $table
    * @param object[] $field_list
    */
    private static function meta($table, $field_list) {
        $action = self::$action;
        $controller = self::$controller;
        $module = self::$module;
        $application = self::$application;
        $meta_dir = dirname(__FILE__)."/../../../library/meta/{$application->code}/{$module->code}/{$controller->code}";
        $table_list = null;
        if($table) {
            $meta_dir .= "/{$action->code}";
            $meta_file = "{$meta_dir}/{$table->code}.php";
        } else {
            $meta_file = "{$meta_dir}/{$action->code}.php";
            $table_list = Service::Engine()->getTableList($action->id);
        }
        if(!file_exists($meta_dir)) {
            mkdir($meta_dir, 0777, true);
        }
        $class_name = "{$application->code}_{$module->code}_{$controller->code}_{$action->code}";
        if($table) {
            $class_name .= "_{$table->code}";
        }
        $meta_name = "meta_{$class_name}";
        $action_title = htmlspecialchars($action->title == 'index' ? $controller->title : $action->title);
        $file = "<?php
class {$meta_name} {
    public static \$title = '{$action_title}';
    public static \$fields;
    public static \$table_list = array();
}

{$meta_name}::\$fields = (object)array(
";
        foreach($field_list as $field) {
            $field->_options = Service::Engine()->getFieldOptions($field->id);
            if($field->_options->hidden) {
                continue;
            }
            $file .= "'{$field->code}' => ".var_export($field, 1).",\n";
        }
        $file .= ");";
        if($table_list) {
            $file .= "\n\n{$meta_name}::\$table_list = (object)array(";
            foreach($table_list as $table) {
                $table->table_name = "{$class_name}_{$table->code}";
                $file .= "'{$table->code}' => ".var_export($table, 1).",\n";
            }
            $file .= ");";
        }
        $file = str_replace('stdClass::__set_state', '(object)', $file);
        file_put_contents($meta_file, $file);
    }

    /**
    * Функция по работе с директориями модулей
    * 
    * @param string $action действие 'create/update/delete'
    * @param string $project Код application
    * @param string $module_name код module
    * @param string $new_module_name код нового модуля, если действия = create
    * @param string $module_id код нового модуля, если действия = create
    * 
    * @return bool
    */
    public static function moduleDir($action, $project, $module_name, $old_module_name = null, $module_id = null) {
        $module_dir = dirname(__FILE__).'/../../../project/'.$project.'/application/';
        switch($action) {
            case 'create':
                if(!file_exists($module_dir.$module_name)) {
                    mkdir($module_dir.$module_name, 0777, true);
                }
                break;
            case 'update':
                if(!$old_module_name) {
                    throw new Exception('Ошибка переименование module');
                }
                // @sciner
                if($old_module_name != $module_name) {
                    if(file_exists($module_dir.$old_module_name)) {
                        rename($module_dir.$old_module_name, $module_dir.$module_name);
                    }
                }
                // @sciner
                if(!file_exists($module_dir.$module_name)) {
                    mkdir($module_dir.$module_name, 0777, true);
                }
                $controller_list = Service::Engine()->getControllerList($module_id);
                foreach($controller_list as $controller) {
                    self::controller($controller->id);
                }
                break;
            case 'delete':
                if(file_exists($module_dir.$module_name)) {
                    self::deleteDir($module_dir.$module_name);
                }
                break;
            default:
                break;
        }
        return true;
    }

    /**
    * Функция удаления директории мета информации
    * 
    * @param string $project Код application
    * @param string $module_name код module
    * 
    * @return bool
    */
    public static function metaDelete($project, $module_name) {
        $meta_module = dirname(__FILE__).'/../../../library/meta/'.$project.'/'.$module_name;
        if(file_exists($meta_module)) {
            self::deleteDir($meta_module);
        }
        return true;
    }

    private static function deleteDir($dir) {
        foreach(scandir($dir) as $file) {
            if ('.' === $file || '..' === $file) continue;
            if (is_dir("$dir/$file")) self::deleteDir("$dir/$file"); 
            else unlink("$dir/$file");
        }
        rmdir($dir);
    }

}
