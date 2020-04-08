<?php
/**
 * 적립금 관련 모델
 */
class Point_model extends A_Model
{
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * 리뷰등록 시 적립금 기지급 체크
     * @param $re_num
     * @return mixed
     */
    public function getPointMemberData($re_num)
    {
        $sns09db = $this->load->database("09sns", true);
        $member = $this->_getReviewMemberInfo($re_num);

        $query = "select * ";
        $query .= "from point_member ";
        $query .= "where pm_rel_key = '" . $re_num . "' and ";
        $query .= "pm_member_key = '" . $member->m_key . "'";
        $query .= "and (pm_point_id = 3 or pm_point_id = 4) ";

        return $sns09db->query($query)->row();
    }

    /*
     * 회원 적립금 리스트 (적립금 단위 %는 제외)
     */
    public function getPointMemberList($param)
    {
        $sns09db = $this->load->database("09sns", true);

        $query = "
        
            select * from 
              point_member a inner join point_master b on a.pm_point_id = b.pt_uid  
            where 
              a.pm_inid = '" . $param['inid'] . "' and 
              a.pm_member_key = '" . $param['m_key'] . "' and
              a.pm_points <> '0'
            order by 
              pm_regdate desc, pm_uid desc
        ";

        return $sns09db->query($query)->result();
    }

    /**
     * 회원 현재 적립금 (예정 및 적립금)
     * @param $param
     * @param $type
     * @return object
     */
    public function getPointMemberStatus($param, $type)
    {
        $sns09db = $this->load->database("09sns", true);

        $inid = $param['inid'];
        $m_key = $param['m_key'];

        $sel_query = " IFNULL(sum(pm_rest_points), '0') as point ";
        if($type == '1') { // 현재적립금
            $add_query = " and pm_active_yn = 'Y' and pm_use_yn = 'N' and pm_expire_yn = 'N' and pm_enddate >= '" . current_datetime() . "' ";
        } elseif($type == '2') { // 예정적립금
            $add_query = " and pm_active_yn = 'N' and pm_use_yn = 'N' and pm_expire_yn = 'N' ";
        }

        $query = "select ";
        $query .=  $sel_query;
        $query .= "from point_member ";
        $query .= "where pm_inid = '" . $inid . "' and ";
        $query .= "pm_member_key = '" . $m_key . "' ";
        $query .= $add_query;

        return $sns09db->query($query)->row();

    }

    /**
     * 적립금 지급
     * @param $data
     * @return bool
     */
    public function insertPointMember($data, $confirm_yn) {

        $sns09db = $this->load->database("09sns", true);

        $pm_rel_key = $data['re_num'];
        $pm_type = $data['point_type'];
        $pm_startdate = $data['point_start'];
        $pm_enddate = $data['point_end'];
        $pm_points = $data['point_value'];
        $pm_last_type = $data['last_type'];

        $m_authno = $data['m_authno'];
        $m_key = $data['m_key'];


        if($confirm_yn == 'Y') {
            $confirm_date = date('Y-m-d H:i:s');
        }

        $query_data = array(
            'pm_inid' => $this->config->item('order_cpid'),
            'pm_member_key'=> $m_key,
            'pm_member_authno' => $m_authno,
            'pm_point_id' => $pm_type,
            'pm_points' => $pm_points,
            'pm_org_points' => $pm_points,
            'pm_startdate' => $pm_startdate,
            'pm_enddate' => $pm_enddate,
            'pm_active_yn' => 'Y',
            'pm_use_yn' => 'N',
            'pm_rest_points' => $pm_points,
            'pm_rel_key' => $pm_rel_key,
            'pm_expire_yn' => 'N',
            'pm_writer' => '',
            'pm_regdate' => date('Y-m-d H:i:s'),
            'pm_moddate' => date('Y-m-d H:i:s'),
            'pm_confirmdate' => $confirm_date,
            'pm_date' => date('Y-m-d'),
            'pm_last_type' => $pm_last_type
        );

        $sns09db->insert("point_member", $query_data);

        $last_id = $sns09db->insert_id();
        $query = "select * from point_member where pm_uid = '" . $last_id . "'";
        $pm_data = $sns09db->query($query)->row_array();

        $log_data = $this->_pointMemberLogQuery($pm_data, $data['reg_type'], $data['reg_tag']);

        if($sns09db->affected_rows() > 0) {
            $_push_09 = $this->load->database("db_push_09", true);
            $_push_09->insert("point_member_log", $log_data); // Log쌓기
            return $last_id;
        } else {
            return '';
        }

    }

    /*
     * 적립금 삭제
     */
    public function deletePointMember($data)
    {
        $sns09db = $this->load->database("09sns", true);

        $sql = "select * from point_member where pm_uid = '" . $data['id'] . "' and pm_use_yn = 'N' and pm_expire_yn = 'N'";

        $query = $sns09db->query($sql);
        $query_data = $query->row_array();

        $log_data = $this->_pointMemberLogQuery($query_data, $data['reg_type'], $data['reg_tag']);

        $sns09db->where('pm_uid', $data['id']);
        $sns09db->where('pm_member_key', $data['m_key']);
        $sns09db->update('point_member', array('pm_active_yn' => 'C', 'pm_writer' => ''));
        //$result = $sns09db->delete('point_member', array('pm_uid' => $data['id'], 'pm_member_key' => $data['m_key']));

        if($sns09db->affected_rows() > 0) {
            $_push_09 = $this->load->database("db_push_09", true);
            $_push_09->insert("point_member_log", $log_data); // Log쌓기
            return true;
        } else {
            return false;
        }
    }

    /**
     * 리뷰 > 게시글번호로 멤버 key 가져오기
     * @param $re_num
     */
    public function _getReviewMemberInfo($re_num)
    {
        // m_key 가져오기
        $sql = "select b.* from review_tb a ";
        $sql .= "inner join member_tb b on a.re_member_num = b.m_num ";
        $sql .= "where a.re_num = '" . $re_num ."'";

        $query = $this->db->query($sql);
        $row = $query->row();

        return $row;
    }

    /**
     * 적립금 마스터 가져오기
     * @param $point_type
     * @return object
     */
    public function getPointMaster($point_type)
    {
        $sns09db = $this->load->database("09sns", true);
        $query = "select * ";
        $query .= "from point_master ";
        $query .= "where pt_uid = '" . $point_type . "'";

        return $sns09db->query($query)->row();
    }

    /**
     * 적립금 마스터 가져오기
     * @param $point_type
     * @return object
     */
    public function getPointMasterList($param)
    {
    }

    /**
     * 리뷰등의 게시글의 pm_rel_key가 있을때 회원적립금 조회
     * @param $query
     * @param $type
     * @return object
     */
    /*
    public function getPointMember($m_key, $re_num)
    {
        $sns09db = $this->load->database("09sns", true);
        $query = "select * ";
        $query .= "from point_member ";
        $query .= "where pm_rel_key = '" . $re_num . "' and ";
        $query .= "pm_member_key = '" . $m_key . "' ";

        return $sns09db->query($query)->row();
    }
    */

    /*
     * * 로그 쿼리 재조립
     * @query point_member 데이터
     * @reg_type 적립, 삭제, 수정
     * @reg_tag 적립금 부모경로
     */
    public function _pointMemberLogQuery($data, $reg_type, $reg_tag)
    {
        $query_data = array(
            'pm_uid' => $data['pm_uid'],
            'pm_inid' => $data['pm_inid'],
            'pm_member_key'=> $data['pm_member_key'],
            'pm_member_authno' => $data['pm_member_authno'],
            'pm_point_id' => $data['pm_point_id'],
            'pm_points' => $data['pm_points'],
            'pm_startdate' => $data['pm_startdate'],
            'pm_enddate' => $data['pm_enddate'],
            'pm_active_yn' => $data['pm_active_yn'],
            'pm_regtag' => $reg_tag,
            'pm_regtype' => $reg_type,
            'pm_ref' => '',
            'pm_use_yn' => $data['pm_use_yn'],
            'pm_rel_key' => $data['pm_rel_key'],
            'pm_writer' => $data['pm_writer'],
            'pm_regdate' => date('Y-m-d H:i:s')
        );

        return $query_data;
    }

    public function getPointUsedHistory($uid)
    {
        $sns09db = $this->load->database("09sns", true);

        $query = "
            select * from 
              point_used a
              inner join smart_order_product b on a.pu_rel_key = b.op_uid
            where 
              a.pu_pm_uid = '" . $uid . "'
            order by 
              a.pu_sortnum
        ";

        return $sns09db->query($query)->result();
    }

    public function setPointStat($data)
    {
        $sns09db = $this->load->database("09sns", true);
        $sns09db->insert("point_stat_tb", $data);

        if(is_numeric($data['ps_point'])){

            if($data['ps_type'] == 'P' && $data['ps_sub_type'] == 'B' && $data['ps_category'] == 'B'){ // 적립::지급::보상
                //월별 통계
                $sql = "INSERT INTO point_stat_day_tb
                        SET p_reward_points = {$data['ps_point']}
                        , 	stat_day = DATE_FORMAT(NOW(),'%Y%m')
                        ON DUPLICATE KEY
                        UPDATE p_reward_points = p_reward_points + {$data['ps_point']}
                ";
                $sns09db->query($sql);

                //회원별통계
                $sql = "INSERT INTO point_stat_member_tb
                        SET 
                          p_reward_point  = '{$data['ps_point']}'
                        , nickname        = '{$data['ps_member_name']}'
                        , hp              = '{$data['ps_member_hp']}'
                        , member_key      = '{$data['ps_member_key']}'
                        , reg_date        = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
                        ON DUPLICATE KEY UPDATE 
                          p_reward_point  = p_reward_point + '{$data['ps_point']}'
                        , mod_date        = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
                ";
//                log_message('zs',$sql);
                $sns09db->query($sql);
            }

            if($data['ps_type'] == 'P' && $data['ps_sub_type'] == 'B' && $data['ps_category'] == 'D'){// 적립::지급::구매평
                //월별통계
                $sql = "INSERT INTO point_stat_day_tb
                        SET p_review_points = {$data['ps_point']}
                        , 	stat_day = DATE_FORMAT(NOW(),'%Y%m')
                        ON DUPLICATE KEY
                        UPDATE p_review_points = p_review_points + {$data['ps_point']}
                ";
                $sns09db->query($sql);

                //회원별통계
                $sql = "INSERT INTO point_stat_member_tb
                        SET 
                          p_review_point  = '{$data['ps_point']}'
                        , nickname        = '{$data['ps_member_name']}'
                        , hp              = '{$data['ps_member_hp']}'
                        , member_key      = '{$data['ps_member_key']}'
                        , reg_date        = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
                        ON DUPLICATE KEY UPDATE 
                          p_review_point  = p_review_point + '{$data['ps_point']}'
                        , mod_date        = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
                ";
//                log_message('zs',$sql);
                $sns09db->query($sql);
            }

            if($data['ps_type'] == 'M' && $data['ps_sub_type'] == 'C' && $data['ps_category'] == 'Z'){// 회수::회수::기타
                $sql = "INSERT INTO point_stat_day_tb
                        SET m_etc_points = {$data['ps_point']}
                        , 	stat_day = DATE_FORMAT(NOW(),'%Y%m')
                        ON DUPLICATE KEY
                        UPDATE m_etc_points = m_etc_points + {$data['ps_point']}
                ";
                $sns09db->query($sql);

                //회원별통계
                $sql = "INSERT INTO point_stat_member_tb
                        SET 
                          m_etc_point     = '{$data['ps_point']}'
                        , nickname        = '{$data['ps_member_name']}'
                        , hp              = '{$data['ps_member_hp']}'
                        , member_key      = '{$data['ps_member_key']}'
                        , reg_date        = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
                        ON DUPLICATE KEY UPDATE 
                          m_etc_point     = m_etc_point + '{$data['ps_point']}'
                        , mod_date        = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
                ";
//                log_message('zs',$sql);
                $sns09db->query($sql);

            }

            if($data['ps_type'] == 'M' && $data['ps_sub_type'] == 'C' && $data['ps_category'] == 'R'){// 회수::회수::구매평취소
                $sql = "INSERT INTO point_stat_day_tb
                        SET m_etc_points = {$data['ps_point']}
                        , 	stat_day = DATE_FORMAT(NOW(),'%Y%m')
                        ON DUPLICATE KEY
                        UPDATE m_etc_points = m_etc_points + {$data['ps_point']}
                ";
                $sns09db->query($sql);

                //회원별통계
                $sql = "INSERT INTO point_stat_member_tb
                        SET 
                          m_etc_point     = '{$data['ps_point']}'
                          , nickname      = '{$data['ps_member_name']}'
                        , hp              = '{$data['ps_member_hp']}'
                        , member_key      = '{$data['ps_member_key']}'
                        , reg_date        = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
                        ON DUPLICATE KEY UPDATE 
                          m_etc_point     = m_etc_point + '{$data['ps_point']}'
                        , mod_date        = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
                ";
//                log_message('zs',$sql);
                $sns09db->query($sql);

            }

        }

    }

    public function getPoints($uid)
    {
        $sns09db = $this->load->database("09sns", true);

        $query = "select * from point_member where pm_uid = '" . $uid . "'";

        return $sns09db->query($query)->row();
    }

    public function updatePointStat($data, $id) {

        log_message('zs','----- updatePointStat Admin CALL :: data ==> '.json_encode_no_slashes($data).' :: id ==> '.$id);

//        $sns09db = $this->load->database("09sns", true);
//
//        $sns09db->where('ps_parent_id', $id);
//        $sns09db->update('point_stat_tb', $data);
//
//        if(is_numeric($data['ps_point'])){
//
//            if($data['ps_type'] == 'M' && $data['ps_sub_type'] == 'C' && $data['ps_category'] == 'Z'){// 회수::회수::기타
//                $sql = "INSERT INTO point_stat_day_tb
//                        SET m_etc_points = {$data['ps_point']}
//                        , 	stat_day = DATE_FORMAT(NOW(),'%Y-%m-%d')
//                        ON DUPLICATE KEY
//                        UPDATE m_etc_points = m_etc_points + {$data['ps_point']}
//                ";
//                $sns09db->query($sql);
//            }
//            if($data['ps_type'] == 'M' && $data['ps_sub_type'] == 'C' && $data['ps_category'] == 'R'){// 회수::회수::주문취소 회수
//                $sql = "INSERT INTO point_stat_day_tb
//                        SET m_cancel_points = {$data['ps_point']}
//                        , 	stat_day = DATE_FORMAT(NOW(),'%Y-%m-%d')
//                        ON DUPLICATE KEY
//                        UPDATE m_cancel_points = m_cancel_points + {$data['ps_point']}
//                ";
//                $sns09db->query($sql);
//            }
//
//        }

    }

}