<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
//ini_set('memory_limit' , '512M');
//ini_set('max_execution_time' , '900');

define('ROOT', '/home/developer/Code/PHP/xml-decoder');
require ROOT.'/vendor/autoload.php';

$uploadfile = __DIR__.'/files/'.$_FILES['file']['name'];
if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
    switch ($_POST['type']) {
        case 'paymaster': parsePaymasterFile($uploadfile); break;
        case 'financier': parseFinancierFile($uploadfile); break;
        case 'bluecoins': parseBluecoinsFile($uploadfile); break;
    }
} else {
    echo 'Ошибка! Не удалось загрузить файл на сервер!';
};

function parsePaymasterFile($file) {
    $loader = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    $sheet = $loader->getActiveSheet();
    $highestRow = $sheet->getHighestRow();

    $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();

    $spreadsheet->getActiveSheet()->setCellValue('A1', 'Date');
    $spreadsheet->getActiveSheet()->setCellValue('B1', 'Category');
    $spreadsheet->getActiveSheet()->setCellValue('C1', 'Account name');
    $spreadsheet->getActiveSheet()->setCellValue('D1', 'Sum');
    $spreadsheet->getActiveSheet()->setCellValue('E1', 'Currency');
    $spreadsheet->getActiveSheet()->setCellValue('F1', 'Type');
    $spreadsheet->getActiveSheet()->setCellValue('G1', 'Tag');
    $spreadsheet->getActiveSheet()->setCellValue('H1', 'Comment text');

    $offset = 0; $new_row = 0;
    //Reads the data from spreadsheet
    for ($row = 2; $row <= $highestRow; $row++)
    {
        $new_row = $row + $offset;
        $date = $sheet->getCellByColumnAndRow(1, $row)->getFormattedValue();
        $category = $sheet->getCellByColumnAndRow(2, $row)->getValue();
        $account = $sheet->getCellByColumnAndRow(3, $row)->getValue();
        $summ = $sheet->getCellByColumnAndRow(4, $row)->getValue();
        $type = $sheet->getCellByColumnAndRow(6, $row)->getValue();
        $tag = $sheet->getCellByColumnAndRow(7, $row)->getValue();
        $comment = $sheet->getCellByColumnAndRow(8, $row)->getValue();
        if ($type == '') {
            //echo date('d.m.Y', strtotime($date))." | ".$summ."\n";
            //echo date('d.m.Y', strtotime($date))."\n";
            $spreadsheet->getActiveSheet()->setCellValue('A'.$new_row, date('d.m.Y', strtotime($date)));
            $spreadsheet->getActiveSheet()->setCellValue('B'.$new_row, 'Перевод');
            $spreadsheet->getActiveSheet()->setCellValue('C'.$new_row, $account);
            $spreadsheet->getActiveSheet()->setCellValue('D'.$new_row, str_replace('.',',',$summ));
            $spreadsheet->getActiveSheet()->setCellValue('E'.$new_row, 'KZT');
            $spreadsheet->getActiveSheet()->setCellValue('F'.$new_row, 'Expense');
            $spreadsheet->getActiveSheet()->setCellValue('H'.$new_row, 'Перевод 1');
            //----------------------------------------------------------------------------------
            $spreadsheet->getActiveSheet()->setCellValue('A'.($new_row+1), date('d.m.Y', strtotime($date)));
            $spreadsheet->getActiveSheet()->setCellValue('B'.($new_row+1), 'Перевод');
            $spreadsheet->getActiveSheet()->setCellValue('C'.($new_row+1), $category);
            $spreadsheet->getActiveSheet()->setCellValue('D'.($new_row+1), str_replace('.',',',$summ));
            $spreadsheet->getActiveSheet()->setCellValue('E'.($new_row+1), 'KZT');
            $spreadsheet->getActiveSheet()->setCellValue('F'.($new_row+1), 'Income');
            $spreadsheet->getActiveSheet()->setCellValue('H'.($new_row+1), 'Перевод 2');

            $offset++;
        } else {
            $spreadsheet->getActiveSheet()->setCellValue('A'.$new_row, date('d.m.Y', strtotime($date)));
            switch ($category) {
                case 'Транспорт (Автобус)':
                    $category = 'Автобус';
                    break;
                case 'Транспорт (Такси)':
                    $category = 'Такси';
                    break;
                case 'Транспорт (Поездки)':
                    $category = 'Билеты';
                    break;
                case 'Платежи (Общие)':
                    $category = 'Общие';
                    break;
                case 'Продукты (Необходимое)':
                    if (strpos($comment, 'Корм')) {
                        $category = 'Домашние животные';
                    } else {
                        $category = 'Продукты';
                        $tag = 'Необходимое';
                    }
                    break;
                case 'Продукты (Необязательное)':
                    $category = 'Продукты';
                    $tag = 'Необязательное';
                    break;
                case 'Досуг':
                    $category = 'Развлечения';
                    break;
                case 'Покупки (Разное)':
                    if (strpos($comment, 'Корм')) {
                        $category = 'Домашние животные';
                    } else {
                        $category = 'Разное';
                    }
                    break;
                case 'Покупки (Для дома )':
                    if (strpos($comment, 'Корм')) {
                        $category = 'Домашние животные';
                    } else {
                        $category = 'Товары для дома';
                    }
                    break;
                case 'Покупки (Машина)':
                    $category = 'Товары для авто';
                    break;
                case 'Покупки (Одежда )':
                    $category = 'Одежда';
                    break;
                case 'Покупки (Для самочувствия )':
                    $category = 'Здоровье';
                    break;
                case 'Подарки (Благотворительность)':
                    $category = 'Благотворительность';
                    break;
                case 'Платежи (Общие)':
                    $category = 'Общие';
                    break;
                case 'Платежи (Кредит)':
                    $category = 'Кредит';
                    break;
                case 'Платежи (Коммунальные)':
                    $category = 'Квартплата';
                    break;
                case 'Платежи (Баланс телефона)':
                    $category = 'Баланс телефона';
                    break;
                case 'Платежи (Баланс такси)':
                    $category = 'Баланс такси';
                    break;
                case 'Коррекция':
                    $category = 'Разовые расходы';
                    break; 
                case 'Кафе':
                    $category = 'Ресторан';
                    break;
            }
            $spreadsheet->getActiveSheet()->setCellValue('B'.$new_row, $category);
            $spreadsheet->getActiveSheet()->setCellValue('C'.$new_row, $account);
            $spreadsheet->getActiveSheet()->setCellValue('D'.$new_row, str_replace('.',',',$summ));
            $spreadsheet->getActiveSheet()->setCellValue('E'.$new_row, 'KZT');
            $spreadsheet->getActiveSheet()->setCellValue('F'.$new_row, $type);
            $spreadsheet->getActiveSheet()->setCellValue('G'.$new_row, $tag);
            $spreadsheet->getActiveSheet()->setCellValue('H'.$new_row, $comment);
        }
    }
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="paymaster import file.xlsx"');

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');

}

function parseFinancierFile($file) {
    $loader = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    $sheet = $loader->getActiveSheet();
    $highestRow = $sheet->getHighestRow();

    $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();

    $spreadsheet->getActiveSheet()->setCellValue('A1', 'date');
    $spreadsheet->getActiveSheet()->setCellValue('B1', 'time');
    $spreadsheet->getActiveSheet()->setCellValue('C1', 'account');
    $spreadsheet->getActiveSheet()->setCellValue('D1', 'amount');
    $spreadsheet->getActiveSheet()->setCellValue('E1', 'currency');
    $spreadsheet->getActiveSheet()->setCellValue('F1', 'original amount');
    $spreadsheet->getActiveSheet()->setCellValue('G1', 'original currency');
    $spreadsheet->getActiveSheet()->setCellValue('H1', 'category');
    $spreadsheet->getActiveSheet()->setCellValue('I1', 'parent');
    $spreadsheet->getActiveSheet()->setCellValue('J1', 'payee');
    $spreadsheet->getActiveSheet()->setCellValue('K1', 'location');
    $spreadsheet->getActiveSheet()->setCellValue('L1', 'project');
    $spreadsheet->getActiveSheet()->setCellValue('M1', 'note');
    
    $offset = 0; $new_row = 0;
    //Reads the data from spreadsheet
    for ($row = 2; $row <= $highestRow; $row++)
    {
        $new_row = $row + $offset;
        $date = $sheet->getCellByColumnAndRow(1, $row)->getFormattedValue();
        $type = $sheet->getCellByColumnAndRow(2, $row)->getValue();
        $account = $sheet->getCellByColumnAndRow(3, $row)->getValue();
        $tmp = $sheet->getCellByColumnAndRow(4, $row)->getValue();
        if (mb_strpos($tmp, '(')) {
            $category = mb_substr($tmp, 0, mb_strpos($tmp, '(')-1);
            $parent = mb_substr($tmp, mb_strpos($tmp, '(')+1, mb_strlen($tmp)-mb_strpos($tmp, '(')-2);
        } else {
            $category = '';
            $parent = $tmp;
        }
        $summ = $sheet->getCellByColumnAndRow(5, $row)->getValue();
        $comment = $sheet->getCellByColumnAndRow(10, $row)->getValue();
        
        if ($type == 'Перевод') {
            $spreadsheet->getActiveSheet()->setCellValue('A'.$new_row, date('d.m.Y', strtotime($date)));
            $spreadsheet->getActiveSheet()->setCellValue('B'.$new_row, '00:00:00');
            $spreadsheet->getActiveSheet()->setCellValue('C'.$new_row, $account);
            $spreadsheet->getActiveSheet()->setCellValue('D'.$new_row, '-'.str_replace('.',',',$summ));
            $spreadsheet->getActiveSheet()->setCellValue('E'.$new_row, 'KZT');
            $spreadsheet->getActiveSheet()->setCellValue('H'.$new_row, '');
            $spreadsheet->getActiveSheet()->setCellValue('I'.$new_row, '');
            $spreadsheet->getActiveSheet()->setCellValue('M'.$new_row, $comment);
            //----------------------------------------------------------------------------------
            $spreadsheet->getActiveSheet()->setCellValue('A'.($new_row+1), date('d.m.Y', strtotime($date)));
            $spreadsheet->getActiveSheet()->setCellValue('B'.($new_row+1), '00:00:00');
            $spreadsheet->getActiveSheet()->setCellValue('C'.($new_row+1), $parent);
            $spreadsheet->getActiveSheet()->setCellValue('D'.($new_row+1), str_replace('.',',',$summ));
            $spreadsheet->getActiveSheet()->setCellValue('E'.($new_row+1), 'KZT');
            $spreadsheet->getActiveSheet()->setCellValue('H'.($new_row+1), '');
            $spreadsheet->getActiveSheet()->setCellValue('I'.($new_row+1), '');
            $spreadsheet->getActiveSheet()->setCellValue('M'.($new_row+1), $comment);

            $offset++;
        } else if ($type == 'Расход') {
            $spreadsheet->getActiveSheet()->setCellValue('A'.$new_row, date('d.m.Y', strtotime($date)));
            $spreadsheet->getActiveSheet()->setCellValue('B'.$new_row, '00:00:00');
            $spreadsheet->getActiveSheet()->setCellValue('C'.$new_row, $account);
            $spreadsheet->getActiveSheet()->setCellValue('D'.$new_row, '-'.str_replace('.',',',$summ));
            $spreadsheet->getActiveSheet()->setCellValue('E'.$new_row, 'KZT');
            $spreadsheet->getActiveSheet()->setCellValue('H'.$new_row, $category);
            $spreadsheet->getActiveSheet()->setCellValue('I'.$new_row, $parent);
            $spreadsheet->getActiveSheet()->setCellValue('M'.$new_row, $comment);
        } else if ($type == 'Доход') {
            $spreadsheet->getActiveSheet()->setCellValue('A'.$new_row, date('d.m.Y', strtotime($date)));
            $spreadsheet->getActiveSheet()->setCellValue('B'.$new_row, '00:00:00');
            $spreadsheet->getActiveSheet()->setCellValue('C'.$new_row, $account);
            $spreadsheet->getActiveSheet()->setCellValue('D'.$new_row, str_replace('.',',',$summ));
            $spreadsheet->getActiveSheet()->setCellValue('E'.$new_row, 'KZT');
            $spreadsheet->getActiveSheet()->setCellValue('H'.$new_row, $category);
            $spreadsheet->getActiveSheet()->setCellValue('I'.$new_row, 'Доход');
            $spreadsheet->getActiveSheet()->setCellValue('M'.$new_row, $comment);
        }
    }
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.(substr($file, strripos($file, '/')+1, strlen($file)-strripos($file, '/')-5)).'.xlsx"');

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');

}

function parseBluecoinsFile($file) {
    $loader = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    $sheet = $loader->getActiveSheet();
    $highestRow = $sheet->getHighestRow();

    $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();

    $spreadsheet->getActiveSheet()->setCellValue('A1', '(1)Type');
    $spreadsheet->getActiveSheet()->setCellValue('B1', '(2)Date');
    $spreadsheet->getActiveSheet()->setCellValue('C1', '(3)Item or Payee');
    $spreadsheet->getActiveSheet()->setCellValue('D1', '(4)Amount');
    $spreadsheet->getActiveSheet()->setCellValue('E1', '(5)Parent Category');
    $spreadsheet->getActiveSheet()->setCellValue('F1', '(6)Category');
    $spreadsheet->getActiveSheet()->setCellValue('G1', '(7)Account Type');
    $spreadsheet->getActiveSheet()->setCellValue('H1', '(8)Account');
    $spreadsheet->getActiveSheet()->setCellValue('I1', '(9)Notes');
    $spreadsheet->getActiveSheet()->setCellValue('J1', '(10) Label');
    $spreadsheet->getActiveSheet()->setCellValue('K1', '(11) Status');
    $spreadsheet->getActiveSheet()->setCellValue('L1', '(12) Split');

    $offset = 0; $new_row = 0;
    //Reads the data from spreadsheet
    for ($row = 2; $row <= $highestRow; $row++)
    {
        $new_row = $row + $offset;
        $date = $sheet->getCellByColumnAndRow(1, $row)->getFormattedValue();
        $type = $sheet->getCellByColumnAndRow(2, $row)->getValue();
        $account = $sheet->getCellByColumnAndRow(3, $row)->getValue();
        $tmp = $sheet->getCellByColumnAndRow(4, $row)->getValue();
        if (mb_strpos($tmp, '(')) {
            $category = mb_substr($tmp, 0, mb_strpos($tmp, '(')-1);
            $parent = mb_substr($tmp, mb_strpos($tmp, '(')+1, mb_strlen($tmp)-mb_strpos($tmp, '(')-2);
        } else {
            $category = '';
            $parent = $tmp;
        }
        $summ = $sheet->getCellByColumnAndRow(5, $row)->getValue();
        $comment = $sheet->getCellByColumnAndRow(10, $row)->getValue();
        if ($type == 'Перевод') {
            $spreadsheet->getActiveSheet()->setCellValue('A'.$new_row, 't');
            $spreadsheet->getActiveSheet()->setCellValue('B'.$new_row, date('n/j/Y', strtotime($date)));
            $spreadsheet->getActiveSheet()->setCellValue('C'.$new_row, '');
            $spreadsheet->getActiveSheet()->setCellValue('D'.$new_row, '-'.$summ);
            $spreadsheet->getActiveSheet()->setCellValue('E'.$new_row, '');
            $spreadsheet->getActiveSheet()->setCellValue('F'.$new_row, '');
            $spreadsheet->getActiveSheet()->setCellValue('G'.$new_row, 'Bank');
            $spreadsheet->getActiveSheet()->setCellValue('H'.$new_row, $account);
            $spreadsheet->getActiveSheet()->setCellValue('I'.$new_row, $comment);
            //----------------------------------------------------------------------------------
            $spreadsheet->getActiveSheet()->setCellValue('A'.($new_row+1), 't');
            $spreadsheet->getActiveSheet()->setCellValue('B'.($new_row+1), date('n/j/Y', strtotime($date)));
            $spreadsheet->getActiveSheet()->setCellValue('C'.($new_row+1), '');
            $spreadsheet->getActiveSheet()->setCellValue('D'.($new_row+1), $summ);
            $spreadsheet->getActiveSheet()->setCellValue('E'.($new_row+1), '');
            $spreadsheet->getActiveSheet()->setCellValue('F'.($new_row+1), '');
            $spreadsheet->getActiveSheet()->setCellValue('G'.($new_row+1), 'Bank');
            $spreadsheet->getActiveSheet()->setCellValue('H'.($new_row+1), $parent);
            $spreadsheet->getActiveSheet()->setCellValue('I'.($new_row+1), $comment);

            $offset++;
        } else if ($type == 'Расход') {
            $spreadsheet->getActiveSheet()->setCellValue('A'.$new_row, 'e');
            $spreadsheet->getActiveSheet()->setCellValue('B'.$new_row, date('n/j/Y', strtotime($date)));
            $spreadsheet->getActiveSheet()->setCellValue('C'.$new_row, '');
            $spreadsheet->getActiveSheet()->setCellValue('D'.$new_row, $summ);
            $spreadsheet->getActiveSheet()->setCellValue('E'.$new_row, $parent);
            $spreadsheet->getActiveSheet()->setCellValue('F'.$new_row, $category);
            $spreadsheet->getActiveSheet()->setCellValue('G'.$new_row, 'Bank');
            $spreadsheet->getActiveSheet()->setCellValue('H'.$new_row, $account);
            $spreadsheet->getActiveSheet()->setCellValue('I'.$new_row, $comment);
        } else if ($type == 'Доход') {
            $spreadsheet->getActiveSheet()->setCellValue('A'.$new_row, 'i');
            $spreadsheet->getActiveSheet()->setCellValue('B'.$new_row, date('n/j/Y', strtotime($date)));
            $spreadsheet->getActiveSheet()->setCellValue('C'.$new_row, '');
            $spreadsheet->getActiveSheet()->setCellValue('D'.$new_row, $summ);
            $spreadsheet->getActiveSheet()->setCellValue('E'.$new_row, $parent);
            $spreadsheet->getActiveSheet()->setCellValue('F'.$new_row, $category);
            $spreadsheet->getActiveSheet()->setCellValue('G'.$new_row, 'Bank');
            $spreadsheet->getActiveSheet()->setCellValue('H'.$new_row, $account);
            $spreadsheet->getActiveSheet()->setCellValue('I'.$new_row, $comment);
        }
    }
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="bluecoins import file.xlsx"');

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');

}

?>