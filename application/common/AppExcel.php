<?php 
/**
 * 表格工具类
 * author：xjp
 * create：2017.4.8
 */
namespace app\common;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
require_once env('app_path').'common/PhpOffice/src/Bootstrap.php';

class AppExcel{
	const COLUMN_NUM = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
	/**
	 * 导出excel表格
	 * @param array $data
	 * @param string $filename
	 */
	public static function export($title, array $data = [],string $filename='simple.xls'){
		ini_set('max_execution_time', '0');
		
		$type = pathinfo($filename);
		$type = strtolower($type["extension"]);
		$ext = $type === 'xls' ? 'Xls' : 'Xlsx';
		// Create new Spreadsheet object
		$spreadsheet = new Spreadsheet();
		
		// Set document properties
		$spreadsheet->getProperties()->setCreator('Maarten Balliauw')
		->setLastModifiedBy('Maarten Balliauw')
		->setTitle('Office 2007 XLSX Test Document')
		->setSubject('Office 2007 XLSX Test Document')
		->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
		->setKeywords('office 2007 openxml php')
		->setCategory('Test result file');
		
		// Add some data
		$sheet = $spreadsheet->setActiveSheetIndex(0);
		$cnt = count($data);
		for($k = 0; $k < $cnt; $k++){
			$count = count($data[$k]);
			for($i = 0; $i < $count; $i++){
				$sheet->setCellValue(self::COLUMN_NUM[$i].($k + 1), $data[$k][$i]);
			}
		}
		
		// Rename worksheet
		$spreadsheet->getActiveSheet()->setTitle($title);
		
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$spreadsheet->setActiveSheetIndex(0);
		
		// Redirect output to a client’s web browser (Xlsx)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		
		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0
		
		$writer = IOFactory::createWriter($spreadsheet, $ext);
		$writer->save('php://output');
		exit;
	}
	/**
	 * 导入excel表格
	 * @param string $file
	 * @return array
	 */
	public static function import(string $filename){
		$spreadsheet = IOFactory::load($filename);
		$sheetData = [];
		//只取第一个sheet
		foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
			foreach ($worksheet->getRowIterator() as $row) {
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
				$rowData = [];
				foreach ($cellIterator as $cell) {
					if ($cell !== null) {
					    array_push($rowData, $cell->getFormattedValue());
					}
				}
				array_push($sheetData, $rowData);
			}
			break;
		}
		return $sheetData;
	}
}
?>