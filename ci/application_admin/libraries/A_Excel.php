<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . "third_party/ZS_PHPExcel/PHPExcel.php");

class A_Excel
{	/*default*/
    public $filePath;

	public function __construct($arrayParams)
	{
        /*default*/
        $this->filePath = $arrayParams['filePath'];
	}
	
/*
$oResult = $this->db->query('some query');
$this->load->library('/ZS/Excel','excel');
$aField	=  array(	'KOR_NAME'			=> '이름'
				,	'CELL_TEL'			=> '휴대폰번호'
				,	'EMAIL'				=> '이메일'
				,	'REG_DATE_STR'		=> '신청일'
				,	'REQUEST_GUBUN_STR'	=> '신청 구분'
				,	'TAX_GUBUN_STR'		=> '처리 구분'
				,	'REQ_STATUS_STR'	=> '현재 상태'
);
$this->excel->ExcelDown($oResult,'test',$aField);
* 
* 1. $aField에 select한 field가 없으면 해당 field는 엑셀로 출력안됨 
* 2. $oDbResult->result_array(); 실행 전 위 함수 실행할 것
*/
	public function ExcelDown($oResult,$file_name='',$aField)
	{

		if(!$file_name){
			$file_name = date('YmdHi').'.csv';
		}else{
			$file_name = $file_name.'.csv';
		}
		$CI =& get_instance();
		
		$CI->load->dbutil();
		$CI->load->helper('download');
		
		$data = $this->zs_csv_from_result($oResult,$aField);
		$data = mb_convert_encoding($data, 'euc-kr', $CI->config->item('charset'));
		
		force_download($file_name, $data);
		exit;
	}
	
	/**
	 * 
	 * 
	 * default page
	 * /back-end/core/system/database/DB_utility.php 
	 * function csv_from_result
	 * 
	 * Generate CSV from a query result object
	 *
	 * @param	object	$query		Query result object
	 * @param	string	$delim		Delimiter (default: ,)
	 * @param	string	$newline	Newline character (default: \n)
	 * @param	string	$enclosure	Enclosure (default: ")
	 * @return	string
	 */
	public function zs_csv_from_result($query,$aField)
	{
		/* init */
		$delim = ',';
		$newline = "\n";
		$enclosure = '"';
		
		if ( ! is_object($query) OR ! method_exists($query, 'list_fields'))
		{
			show_error('You must submit a valid result object');
		}

		$out 	= '';
		$i		= 0;
		
		$arr	= array();
		// First generate the headings from the table column names
		foreach ($query->list_fields() as $name)
		{
			
			if(count($aField) > 0){
				
				$bRet = false;
				foreach ($aField as $key => $value) {
					if($name == $key){
						$name = $value;
						$bRet = true;
						$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $name).$enclosure.$delim;
						break;
					}
				}
				
			if(!$bRet) $arr[] = $i;
			$i++;
			
			}else{
				$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $name).$enclosure.$delim;
			}
			
			
		}
		$out = substr($out, 0, -strlen($delim)).$newline;

		// Next blast through the result array and build out the rows
		while ($row = $query->unbuffered_row('array'))
		{
			$j		= 0;
			$line 	= array();
			
			foreach ($row as $item)
			{
				if(count($aField) > 0){
					if(!in_array($j, $arr)) $line[] = $enclosure.str_replace($enclosure, $enclosure.$enclosure, $item).$enclosure;
					$j++;
				}else{
					$line[] = $enclosure.str_replace($enclosure, $enclosure.$enclosure, $item).$enclosure;
				}
			}
			$out .= implode($delim, $line).$newline;
		}

		return $out;
	}

	// --------------------------------------------------------------------


    public function getData()
    {

/*
PHPExcel Fatal error: Class 'ZipArchive' not found in Path/Excel2007.php on line 94 error 로 아래 구문 추가
*/
        PHPExcel_Settings :: setZipClass (PHPExcel_Settings :: PCLZIP);


        $objPHPExcel = new PHPExcel();
        $objPHPExcel = PHPExcel_IOFactory::load($this->filePath);

        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        $fieldsData = $sheetData[1];

        return $sheetData;
        exit;

    }


}
