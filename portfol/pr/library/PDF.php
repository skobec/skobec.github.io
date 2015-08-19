<?php

class PDF {

    private static function prepareSheet($sheet, $fields, $template_fields) {
        $fields = (array)$fields;
        $new_sheet = $sheet;
        foreach($fields as $f_name => $f_value) {
            $name = '${'.$f_name.'}';
            $new_sheet = str_replace($name, nl2br($f_value), $new_sheet);
        }
        foreach($template_fields as $f_name => $f_value) {
            $name = '${'.$f_name.'}';
            $new_sheet = str_replace($name, nl2br($f_value), $new_sheet);
        }
        return $new_sheet;
    }

    public static function generateMultiple($out_file, $file_settings) {
        $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5);
        $mpdf->SetDisplayMode('fullwidth');

        $i = 0;
        foreach($file_settings as $setting) {
            $css = file_get_contents($setting->css_file);
            $mpdf->WriteHTML($css, 1);
            $cc = 0;
            $ctotal = count($setting->list);
    
            $template = new Template($setting->script_path.$setting->render_file);
            $template->template_fields = $setting->template_fields;
    
            foreach($setting->list as $item) {
                $template->item = $item;
                $template->template_fields['page_number'] = $cc + 1;
    
                ob_start();
                $template->draw();
                $new_sheet = ob_get_clean();
    
                if($cc == 0) {
                    $new_sheet = $new_sheet;
                }
                $mpdf->WriteHTML($new_sheet, 2);
                if(++$cc < $ctotal) {
                    $mpdf->AddPage();
                }
            }
        }

        $mpdf->Output($out_file);
    }

    /**
    * Генерация PDF файла
    * @author sciner
    * 
    * @param string $out_file
    * @param object[] $list
    * @param string $script_path
    * @param string $render_file
    * @param string $css
    * @param string[] $template_fields
    * @param string $list_format;
	* @param bool $is_legal_person
    */
    public static function generate($out_file, $list, $script_path, $render_file, $css_file, $template_fields, $list_format = 'A4', $is_legal_person = false) {
        $html_file = $out_file.'.html';
        if(isset($template_fields['logo'])) {
        	$logo = $template_fields['logo'];
        }
        $css = file_get_contents($css_file);
        if(file_exists($html_file)) {
			unlink($html_file);
        }
        $code128_ttf_file = realpath(dirname(__FILE__).'/../project/billing/public/fonts/code128.ttf');
        file_put_contents($html_file, <<<html
<!doctype html>
<html>
<head>
    <meta charset="UTF-8" />
    <style>
    	{$css}
		.main {
		    width: 100%;
		}
    	.ticket-table {
    		width: 100%;
    	}
		.ticket-table {
			page-break-inside: avoid;
		}
		@media print {
			.ticket-table {
				page-break-inside: avoid;
		    }
		}
		body {
			padding: 0px;
			margin: 0px;
		}
		@font-face
		{
			font-family: Code128Font;
			src:  url('{$code128_ttf_file}') format('truetype');
			font-weight: normal;
			font-style: normal;
		}
		.barcode128 {
			font-family: Code128Font;
			font-size: 52pt;
			white-space: pre;
		}
	</style>
</head>
<body>
html
    	, FILE_APPEND);
	    $cc = 0;
	    $view = new Zend_View();
	    $view->template_fields = $template_fields;
		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		$is_buffer = false;
		
	    $ctotal = count($list) - 1;
	    $buffer = new Mikron_Buffer();
	    while($item = $buffer->fetch($list, $is_legal_person ? 'Type_Billing_Organization_Invoice_Extended' : 'Type_Billing_Bill_Ticket_Extended')) {
	        $view->item = $item;
	        $view->template_fields['page_number'] = ++$cc;
	        ob_start();
	        try {
	            $view->setScriptPath($script_path);
                    if (is_array($render_file)) {
                        foreach ($render_file as $render_file_item) {
                            echo $view->render($render_file_item);
                        }
                    } else {
                        echo $view->render($render_file);    
                    }	            
	        } catch (Exception $ex) {
	            die($ex->getMessage());
	        }
	        $new_sheet = ob_get_clean();
	        file_put_contents($html_file, $new_sheet, FILE_APPEND);
	    }
		file_put_contents($html_file, <<<html
</body>
</html>
html
		, FILE_APPEND);
		$margin_top = 0;
	    $margin_right = 10;
	    $margin_bottom = 0;
	    $margin_left = 10;
		
	    if(IS_DEVELOPER_HOST) {
			echo file_get_contents($html_file);
			exit;
	    } else {
			// формирование pdf документа средствами wkhtmltopdf
			$orientation = 'portrait';
			if(strpos($list_format, '-L') !== false) {
				$orientation = 'landscape';
				$list_format = str_replace('-L', null, $list_format);
			}
            // запуск wkhtmltopdf идет без x-сервера, т.к. библиотека собрана как static версия
            $wkhtmltopdf = "wkhtmltopdf";

			$params = array(
				'page-size' => $list_format,
				'orientation' => $orientation,
				'margin-top' => $margin_top,
				'margin-right' => $margin_right,
				'margin-bottom' => $margin_bottom,
				'margin-left' => $margin_left,
			);
			$params = implode(' ', array_map(function ($v, $k) { if(is_null($v)) { $output =  '--'.$k; } else { $output = '--'.$k . ' ' . "\"{$v}\""; }; return $output; }, $params, array_keys($params)));;
			exec("{$wkhtmltopdf} {$params} {$html_file} {$out_file}");
		}
    }
}
