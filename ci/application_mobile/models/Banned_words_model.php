<?php
/**
 * 금칙어 관련 모델
 */
class Banned_words_model extends M_Model {

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

        return $this->db->where("bw_word", $word)->get("banned_words_tb")->row();
    }//end of get_banned_words_row()

    /**
     * 금칙어 목록 배열
     * @return mixed
     */
    public function get_banned_words_array() {
        $result_array = array();
        $result_list = $this->db->get("banned_words_tb")->result();

        foreach ($result_list as $row) {
            $result_array[] = $row->bw_word;
        }

        return $result_array;
    }//end of get_banned_words_array()

}//end of class Banned_words_model