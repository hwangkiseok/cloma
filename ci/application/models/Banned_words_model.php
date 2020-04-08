<?php
/**
 * 금칙어 관련 모델
 */
class Banned_words_model extends W_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 금칙어 조회
     * @param $word
     * @return mixed
     */
    public function get_banned_words_row($word) {
        if( empty($word) ) {
            return false;
        }

        $sql = "SELECT * FROM banned_words_tb WHERE bw_word = '{$word}' ; ";
        $oResult = $this->db->query($sql);
        $aResult = $oResult->row();

        return $aResult;
    }//end of get_banned_words_row()

    /**
     * 금칙어 목록 배열
     * @return mixed
     */
    public function get_banned_words_array() {
        $result_array   = array();

        $sql = "SELECT * FROM banned_words_tb";
        $oResult = $this->db->query($sql);
        $aResult = $oResult->result();

        foreach ($aResult as $row) {
            $result_array[] = $row->bw_word;
        }

        return $result_array;
    }//end of get_banned_words_array()

}//end of class Banned_words_model