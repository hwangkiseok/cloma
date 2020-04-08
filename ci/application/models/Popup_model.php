<?php
/**
 * 팝업 관련 모델
 */
class Popup_model extends W_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->to_datetime = date('YmdHis');
    }

    /**
     * 앱팝업 정보 추출
     * @param $apo_num
     */
    public function get_app_popup_row($where_array=array()) {

        if( empty($where_array) ) {
            return false;
        }


        return $this->db->where($where_array)->get('app_popup_tb')->row_array();
    }//end get_app_popup_row;

    /**
     * 오늘의 팝업 검색
     * @return object
     */
    public function getTodayPopup($exe_class,$iIgnoreId = "")
    {

        $whereQueryString = '';


        $able_type[] = 1; //오픈조건 : 안드로인경우
//        $able_type[] = 3; //오픈조건 : 둘다
//        if(is_app_1()) $able_type[] = 2; //오픈조건 : 아이폰인경우
//        else if(is_app()) $able_type[] = 1; //오픈조건 : 안드로인경우

        if($iIgnoreId > 0){
            $whereQueryString .= " AND apo_num > {$iIgnoreId} ";
        }

        $query = "
            select * 
            from app_popup_tb 
            where (apo_termlimit_yn = 'N' or (apo_termlimit_yn = 'Y' and apo_termlimit_datetime1 <= '" . $this->to_datetime. "' and apo_termlimit_datetime2 >= '" . $this->to_datetime . "')) 
                and apo_display_yn = 'Y' 
                and find_in_set('{$exe_class}',apo_view_page) 
                and apo_os_type in ?
                {$whereQueryString}
            order by apo_num desc 
        ";

        return $this->db->query($query,array($able_type))->result_array();
    }

    public function setDisplayCount($apo_num_arr){

        $sql = "UPDATE app_popup_tb SET apo_display_count = apo_display_count + 1 WHERE apo_num IN (".implode(',',$apo_num_arr).")";
        return $this->db->query($sql);

    }

    /**
     * 팝업 사용자기준 체크 유무
     * @param $param
     * @return object
     */
    public function getUserPopupChk($param)
    {
        $query = "  select 
                        *
                      , CASE WHEN apox_expire_datetime >= '" . $this->to_datetime . "' THEN 'Y' ELSE 'N' END AS expired_yn 
                    from app_popup_expire_tb 
                    where 1 
                    and apox_popup_num = '{$param['id']}' 
                    and apox_member_num = '{$param['m_num']}' 
        ";
        return $this->db->query($query)->row_array();
    }

    /**
     * 팝업 상품클릭 또는 닫기시 처리 및 카운트 증가
     * @param m_num 회원넘버
     * @param apox_popup_num 팝업아이디
     * @param expire_day 만료일
     * @param mode : click, close
     */
    public function appPopupChk($param)
    {
        if( $param['apox_popup_num'] && $param['m_num'] ) {
            if (!empty($param['m_num'])) {
                if ($param['apox_popup_num']) {
                    // 클릭시 카운터 증가
                    if ($param['mode'] == 'click') {
                        $this->db->set('apo_click_count', 'apo_click_count + 1', false);
                        $this->db->where('apo_num', $param['apox_popup_num']);
                        $this->db->update('app_popup_tb');
                    }
                    // 닫기시 카운터 증가
                    else if( $param['mode'] == 'close' ) {
                        $this->db->set('apo_close_count', 'apo_close_count + 1', false);
                        $this->db->where('apo_num', $param['apox_popup_num']);
                        $this->db->update('app_popup_tb');
                    }
                }

                $apox_click_datetime = date('YmdHis');
                $apox_expire_datetime = date("Ymd", strtotime("+" . $param['expire_day'] . " days")).'000000';

                //$dparam = array(
                //    'id' => $param['apox_popup_num'],
                //    'm_num' => $param['m_num']
                //);
                //$rRow = $this->getUserPopupChk($dparam);

                //팝업 만료 row
                $sql = "select * from app_popup_expire_tb where apox_popup_num = ? and apox_member_num = ?";
                $query = $this->db->query($sql, array($param['apox_popup_num'], $param['m_num']));
                $apox_row = $query->row_array();

                // 체크내용이 없다면 insert
                if( empty($apox_row) ) {
                    $sql = "
                      insert ignore into app_popup_expire_tb
                      set 
                            apox_popup_num = '" . $param['apox_popup_num'] . "'
                          , apox_member_num = '" . $param['m_num'] ."'
                          , apox_click_datetime ='" . $apox_click_datetime . "'
                          , apox_expire_datetime ='" . $apox_expire_datetime . "'
                          , apox_view_cnt = 1
                    ";
                    $this->db->query($sql);
                    if($param['apox_popup_num'] == '54'){
//                        log_message('zs','================ 자동출석 sql 2');
//                        log_message('zs',$sql);
                    }

                }
                // 체크내용이 있으면 update
                else {
                    $sql = "
                        update app_popup_expire_tb
                        set 
                              apox_click_datetime ='" . $apox_click_datetime . "'
                            , apox_expire_datetime ='" . $apox_expire_datetime . "'
                            , apox_view_cnt = apox_view_cnt + 1
                        where
                            apox_popup_num = '" . $param['apox_popup_num'] . "'
                            and apox_member_num = '" . $param['m_num'] ."'
                    ";
                    $this->db->query($sql);
                    if($param['apox_popup_num'] == '54'){
//                        log_message('zs','================ 자동출석 sql 1');
//                        log_message('zs',$sql);
                    }

                }
            }
        }
        else {
            exit;
        }
    }//end appPopupChk;


    /**
     * 팝업 상품클릭 또는 닫기시 처리 및 카운트 증가
     * @param m_num 회원넘버
     * @param apox_popup_num 팝업아이디
     * @param expire_day 만료일
     * @param mode : click, close
     */
    public function appPopupChk_v2($param)
    {
        if( $param['apox_popup_num'] && $param['m_num'] ) {

            if (!empty($param['m_num'])) {


                if(zsDebug()){
                    //zsView($param);;exit;
                }

                if ($param['pop_num']) {
                    // 클릭시 카운터 증가
                    if ($param['mode'] == 'click') {
                        $this->db->set('apo_click_count', 'apo_click_count + 1', false);
                        $this->db->where('apo_num', $param['pop_num']);
                        $this->db->update('app_popup_tb');
                    }
                    // 닫기시 카운터 증가
                    else if( $param['mode'] == 'close' ) {
                        $this->db->set('apo_close_count', 'apo_close_count + 1', false);
                        $this->db->where('apo_num', $param['pop_num']);
                        $this->db->update('app_popup_tb');
                    }
                }

                $apox_popup_num_arr = explode('||',$param['apox_popup_num']);

                foreach ($apox_popup_num_arr as $v) {

                    //팝업 만료 row
                    if( $param['expire_day'] > 0 ) {

                        $apox_click_datetime = date('YmdHis');
                        $apox_expire_datetime = date("Ymd", strtotime("+" . $param['expire_day'] . " days")).'000000';

                        $sql = "select * from app_popup_expire_tb where apox_popup_num = ? and apox_member_num = ?";
                        $query = $this->db->query($sql, array($v, $param['m_num']));
                        $apox_row = $query->row_array();

                        // 체크내용이 없다면 insert
                        if( empty($apox_row) ) {
                            $sql = "
                              insert ignore into app_popup_expire_tb
                              set 
                                    apox_popup_num = '" . $v . "'
                                  , apox_member_num = '" . $param['m_num'] ."'
                                  , apox_click_datetime ='" . $apox_click_datetime . "'
                                  , apox_expire_datetime ='" . $apox_expire_datetime . "'
                                  , apox_view_cnt = 1
                            ";
                            $this->db->query($sql);

                        }
                        // 체크내용이 있으면 update
                        else {
                            $sql = "
                                update app_popup_expire_tb
                                set 
                                      apox_click_datetime ='" . $apox_click_datetime . "'
                                    , apox_expire_datetime ='" . $apox_expire_datetime . "'
                                    , apox_view_cnt = apox_view_cnt + 1
                                where
                                    apox_popup_num = '" . $v . "'
                                    and apox_member_num = '" . $param['m_num'] ."'
                            ";
                            $this->db->query($sql);

                        }

                    }

                }

            }

        }
        else {
            exit;
        }
    }//end appPopupChk_v2;

}//end class App_popup_model;