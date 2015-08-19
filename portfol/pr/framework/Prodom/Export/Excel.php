<?php
  
class Prodom_Export_Excel {

    /**
    * put your comment there...
    * 
    * @param string $file_name
    * @param string $www_file_name
    * @param mixed[] $data
    * @param bool $landscape_orientation
    */
    public static function export($file_name, $www_file_name, $data, $landscape_orientation = false) {

        ob_start();
        // создание эксель документа
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setTitle('Report');
        $objPHPExcel->getProperties()->setCreator('Lenar Zakirov')
                             ->setLastModifiedBy('Lenar Zakirov')
                             ->setTitle("Office 2007 XLSX Document")
                             ->setSubject("Office 2007 XLSX Document")
                             ->setDescription("Report document for Office 2007 XLSX, generated using PHP classes.")
                             ->setKeywords("office 2007 openxml php")
                             ->setCategory("Report result file");
        $sheet = $objPHPExcel->setActiveSheetIndex(0);

        if($landscape_orientation) {
            $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        }

        $i = 0;
        // стиль заголовка
        $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'wrap' => true,
                'wrapText' => true,
            ),
            'borders' => array(
                'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('argb' => 'FF444444'),
                        ),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startcolor' => array(
                    'rgb' => 'e3dfd6',
                ),
                'endcolor' => array(
                    'rgb' => 'e3dfc6',
                ),
            ),
        );
        
        // стиль многострочного поля
        $dataMultilineStyleArray = array(
            'alignment' => array(
                'wrap' => true,
                'wrapText' => true,
            ),
        );

        $row_offset = 1;

        foreach($data as $data_item) {
            if($data_item instanceof Type_Excel_Table) {
                // названия столбцов и их ширина
                $column_list = $data_item->column_list;
                $title_list = $data_item->title;
                if($title_list) {
                    if(!is_array($title_list)) {
                        $title_list = array($title_list);
                    }
                    foreach($title_list as $title) {
                        $title_cell_address = "A{$row_offset}";
                        // Заголовок файла
                        $sheet->mergeCells("{$title_cell_address}:".Prodom_ToolBox::ConvertToExcellLetter(count($column_list) - 1).$row_offset);
                        $sheet->getStyle($title_cell_address)
                            ->applyFromArray(array('alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                'wrap' => true,),
                                'font' => array(
                                        'bold' => true,
                                        'size' => '20'
                                    )
                                )
                            );
                        $sheet->getRowDimension($row_offset)->setRowHeight(35);
                        $sheet->setCellValue($title_cell_address, $title);
                        $row_offset++;
                    }
                    $row_offset++;                
                }
                // впечатывание заголовков таблицы
                foreach($column_list as $i => $column) {
                    // адрес ячейки
                    $cell = Prodom_ToolBox::ConvertToExcellLetter($i).($row_offset);
                    $sheet->setCellValue($cell, $column->title);
                    // применение стиля ячейки
                    $sheet->getStyle($cell)->applyFromArray($styleArray);
                    // задание ширины столбца
                    $sheet->getColumnDimension(Prodom_ToolBox::ConvertToExcellLetter($i))->setWidth($column->width);
                }
                $row_offset++;
                // обработка строк пришедшего отчета
                foreach($data_item->data as $item) {
                    $is_multiline = false;
                    foreach($column_list as $column_number => $column) {
                        $column_field = $column->field;
                        $cell = Prodom_ToolBox::ConvertToExcellLetter($column_number).$row_offset;
                        $sheet->setCellValue($cell, $item->$column_field);
                        if(strpos($item->$column_field, "\n")) {
                            $is_multiline = true;
                            $sheet->getStyle($cell)->applyFromArray($dataMultilineStyleArray);
                        }
                    }
                    if($is_multiline) {
                        $sheet->getRowDimension($row_offset)->SetRowHeight(-1);
                    }
                    $row_offset++;
                }
                $row_offset++;
            }
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file_name, $www_file_name);
        unset($objWriter);
        $garbage = ob_get_clean();

        return true;

    }

}
