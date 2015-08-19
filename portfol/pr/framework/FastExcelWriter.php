<?php
/**
 * Класс служит для записи в Excel шаблон напрямую, без использования PhpExcel. 
 * Данные записываются в виде простых строк, без форматирования,
 * определения типа и др., но очень быстро. 
 * 
 * @author sharafanmaxim
 * @since 18.11.2014
 */
class FastExcelWriter {
    private $_templateFile;
    private $_header;
    private $_footer;
    private $_rows;
    private $_columnsCount;

    /** 
     * @param string $templateFile  Полный путь к шаблону с расширением .xml
     *                              Т.е. сперва формируем Excel затем сохраняем его в виде "таблица XML 2003"
     *                              и указываем путь. Excel должен иметь 1 лист!
     * @param int $columnsCount Кол-во столбцов во входных данных
     */
    public function __construct ($templateFile, $columnsCount) {
        $this->_columnsCount = $columnsCount;
        $this->_templateFile = $templateFile;
        $file_str = file_get_contents($templateFile);
        $arr = explode("</Table>", $file_str);
        $this->_header = array_shift($arr);
        $this->_footer = "</Table>" . array_shift($arr);
    }

    private function getRowsCount () {
        return isset($this->_rows) ? count($this->_rows) : 0;
    }

    private function getColumnsCount () {
        return $this->_columnsCount;
    }

    /**
     * Добавление строки в файл
     * 
     * @param Array $item Массив строк
     */
    public function addRow ($item) {
        $ind = 1;
        $str = "<Row>";
        foreach($item as $r) {
            $str.='<Cell ss:Index="' . $ind . '"><Data ss:Type="String">' . $r . '</Data></Cell>';
            $ind++;
        }
        $str.="</Row>";
        $this->_rows[] = $str;
    }

    /*
     * Добавление пустой строки
     */
    public function addEmpty () {
        $arr = array();
        $arr = array_pad($arr, $this->_columnsCount, '');
        $this->addRow($arr);
    }

    /**
     * Сохранение файла
     * 
     * @param string $pathToFile
     */
    public function save ($pathToFile) {
        $pattern = '/ss:ExpandedColumnCount="(\d+)"/';
        // Подсчитаем кол-во столбцов и подставим в текст
        preg_match_all($pattern, $this->_header, $matches_out);
        $cnt_was = intval($matches_out[1][0]);
        $cnt = $cnt_was + $this->getColumnsCount();
        $replacement = 'ss:ExpandedColumnCount="' . $cnt . '"';
        $this->_header = preg_replace($pattern, $replacement, $this->_header);
        // Подсчитаем кол-во строк и подставим в текст
        $pattern = '/ss:ExpandedRowCount="(\d+)"/';
        preg_match_all($pattern, $this->_header, $matches_out);
        $cnt_was = intval($matches_out[1][0]);
        $cnt = $cnt_was + $this->getRowsCount();
        $replacement = 'ss:ExpandedRowCount="' . $cnt . '"';
        $this->_header = preg_replace($pattern, $replacement, $this->_header);
       
        // Записываем файл
        $handle = fopen($pathToFile, "x+");
        fwrite($handle, $this->_header);
        if(count($this->_rows) > 0) {
            foreach($this->_rows as $row) {
                fwrite($handle, $row);
            }
        }        
        fwrite($handle, $this->_footer);
        fclose($handle);
    }

}
