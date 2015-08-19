<?

class Prodom_Rtf_TemplateMaker {

    private $templates = array();
    private $header = null;
    private $footer = null;

    /**
    * Загрузка шаблонов
    * @author sciner
    * @since 27.12.2012
    * 
    * @return bool
    */
    public function readTemplatesFromRtfFile($fileName) {
        $resp = (object)array('header' => null, 'footer' => null, 'templates' => array());
        if(!file_exists($fileName)) {
            throw new Exception('Templates file not found');
        }
        $templates = file_get_contents($fileName); //открытие файла с шаблонами
        $templates = explode('$end', $templates);
        $this->footer = $templates[1];
        $templates = $templates[0];
        $templates = explode('$template_start/', $templates);
        $this->header = array_shift($templates);
        foreach($templates as $template) {
            $out = explode('$template_end', $template);
            $template = $out[0];
            $temp = explode('/', $template, 2);
            $name = $temp[0];
            $body = $temp[1];
            $resp->templates[$name] = (object)array(
                'body' => $body,
            );
        }
        $this->templates = $resp->templates;
        return true;
    }
    
    public function getHeader() {
        return $this->header;
    }
    
    public function getFooter() {
        return $this->footer;
    }
    
    public function getAppliedTemplate($templateId, $fields) {
        $body =  $this->templates[$templateId]->body;
        krsort($fields, SORT_STRING);
        foreach($fields as $code => $value) {
            $body = str_replace('$'.$code, $this->rtfTextEncode($value), $body);
        }
        return $body;
    }

    private function rtfTextEncode($text) {
        if($text == null) {
            return $text;
        }
        if(is_numeric($text)) {
            return $text;
        }
        $text = json_encode($text);
        $text = trim($text, '"');
        // normalize json unicode(russian) string
        $text = preg_replace_callback('/\\\u([a-f0-9]{4})/i', create_function('$m', 'return "\u".hexdec($m[1])."?";'), $text);
        $text = '\f1 '.$text;
        return $text;
    }

}
