<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 폼 검증 추가
 */
class MY_Form_validation extends CI_Form_validation {

    /**
     * 한글,영문,숫자,데쉬,언더바만 가능하게 한다. (UTF-8 기준)
     * @param $str
     * @return bool
     */
    public function korean_alpha_dash($str)
    {
        return ( preg_match('/[^\x{1100}-\x{11FF}\x{3130}-\x{318F}\x{AC00}-\x{D7AF}0-9a-zA-Z_-]/u',$str)) ? FALSE : TRUE;
    }


    /**
     * 영문, 숫자, 조합 필수
     * @param $str
     * @return bool
     */
    public function alpha_numeric_combi($str)
    {
        return (preg_match("/^.*(?=.*\d)(?=.*[a-zA-Z]).*$/", $str)) ? TRUE : FALSE;
    }


    /**
     * 도메인 형식 체크 (.com, .co.kr, ...)
     * @param $str
     */
    public function is_domain($str)
    {
        return (preg_match("/\b(\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $str)) ? TRUE : FALSE;
    }

    /**
     * 중복체크 (자신 제외)
     * @param $str		: 검색값
     * @param $field	: 검색필드(입력예=>테이블.필드.ID필드.ID값)
     * @return bool
     */
    public function is_unique_except_self($str, $field)
    {
        list($table, $field, $id_field, $id)=explode('.', $field);

        $query = "select count(*) as cnt ";
        $query .= "from " . $table . " ";
        $query .= "where " . $field . " = '" . $str . "' ";
        $query .= "and " . $id_field . " != '" . $id . "' ";
        $count = $this->CI->db->query($query)->row('cnt');

        if( !empty($count) ) {
            return false;
        }

        return true;
    }

    /**
     * 휴대폰번호 체크
     * @param $str
     */
    public function valid_mobile($str)
    {
        return (preg_match("/^\d{3}-\d{3,4}-\d{4}$/", $str)) ? TRUE : FALSE;
    }

    /**
     * 사용여부 체크 (Y|N)
     * @param $str
     * @return bool
     */
    public function valid_usestate($str) {
        return ($str == 'Y' || $str == 'N') ? TRUE : FALSE;
    }

}//end of class MY_Form_validation
?>