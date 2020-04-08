<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 적립금 관련 컨트롤러
 */
class Point extends A_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('point_model');
        $this->load->model('review_model');

        $this->inid = $this->config->item('order_cpid');
    }

    /**
     * 회원관리 > 적립금목록 (Ajax)
     */
    public function point_list_ajax()
    {
        ajax_request_check();

        //request
        $req['print_type'] = trim($this->input->post_get('print_type', true));  //출력타입(''|select)

        //회원적립금목록 요청
        $param = array(
            'inid' => $this->inid
        );

        $result_list = $this->point_model->getPointMasterList($param);

        $data = array();

        if( !empty($result_list) ) {
            foreach ($result_list as $key => $item) {
                $data_item = array();
                $data_item['uid'] = $item->pt_uid;
                $data_item['name'] = $item->pt_name;
                $data[] = $data_item;
            }//end of foreach()
        }//end of if()

        if( $req['print_type'] == "select" ) {
            $html = '';
            $html .= '<select name="point_select" class="form-control">';
            $html .= '  <option value="">* 적립금선택 *</option>';

            foreach ($data as $key => $item) {
                $html .= '<option value="' . $item['uid'] . '" style="color:#337ab7;" >' . "[" . $item['uid'] . "]" . $item['name'] . '</option>';
            }//end of foreach()

            $html .= '</select>';

            echo $html;
        }
        else {
            echo json_encode_no_slashes($data);
        }

    }//end of point_list_ajax()

    /**
     * 회원관리 > 적립금목록 (Ajax)
     */
    public function point_member_list_ajax()
    {
        ajax_request_check();

        //request
        $req['m_num'] = trim($this->input->post_get('m_num', true));
        if( empty($req['m_num']) ) {
            $this->output->set_status_header('403');
            exit;
        }

        //model
        $this->load->model('member_model');

        $member_row = $this->member_model->get_member_row(array('m_num' => $req['m_num']));
        if( empty($member_row) ) {
            $this->output->set_status_header('403');
            exit;
        }

        $param_data = array(
            'inid' => $this->inid,
            'm_key' => $member_row->m_key
        );

        //회원적립금목록(전체) 요청
        $result_list = $this->point_model->getPointMemberList($param_data);

        $data = array();
        if( !empty($result_list) ) {

            foreach ($result_list as $key => $item) {
                $data_item = array();
                $data_item['uid'] = $item->pm_uid;
                $data_item['code'] = $item->pm_point_id;
                $data_item['part_used'] = 'N'; // 적립금 중 일부사용 여부
                $data_item['expire_yn'] = $item->pm_expire_yn;
                $data_item['active_yn'] = $item->pm_active_yn;
                $data_item['use_yn'] = $item->pm_use_yn;

                if(($item->pm_enddate < current_datetime() && $item->pm_enddate != '') || $item->pm_expire_yn == 'Y') {
                    $data_item['use_state_text'] = "기간만료"; // 적립금상태
                    $data_item['delete_able'] = 'N'; // 삭제가능여부
                } else {
                    if($item->pm_use_yn == "Y")  {
                        $data_item['use_state_text'] = "사용완료";
                        $data_item['delete_able'] = 'N';
                    } else {
                        if($item->pm_active_yn == 'Y') {
                            if($item->pm_points != $item->pm_rest_points) {
                                $data_item['part_used'] = 'Y';

                                $data_item['use_state_text'] = "일부사용";
                                $data_item['delete_able'] = 'N';
                            } else {
                                $data_item['use_state_text'] = "사용전";
                                $data_item['delete_able'] = 'Y';
                            }
                        } else {
                            if($item->pm_active_yn == 'C') {
                                $data_item['use_state_text'] = "적립금취소";
                                $data_item['delete_able'] = 'N';
                            } else {
                                $data_item['use_state_text'] = "비활성(구매확정전)";
                                $data_item['delete_able'] = 'Y';
                            }
                        }
                    }
                }

                $data_item['name'] = $item->pt_name;
                $data_item['org_points'] = number_format($item->pm_org_points);
                $data_item['used_points'] = number_format($item->pm_org_points - $item->pm_rest_points);
                $data_item['rest_points'] = number_format($item->pm_rest_points);

                $data_item['reg_date'] = date("Y-m-d H:i:s", strtotime($item->pm_regdate));
                $data_item['use_startdate'] = ( !empty($item->pm_startdate) ) ? date("Y-m-d", strtotime($item->pm_startdate)) : "";
                $data_item['use_enddate'] = ( !empty($item->pm_enddate) ) ? date("Y-m-d", strtotime($item->pm_enddate)) : "";

                $data[] = $data_item;
            }//end of foreach()

            $cnt = count($result_list);
        }//end of if()

        //json
        if( is_json_request() ) {
            echo json_encode_no_slashes($data);
        }
        //html
        else {
            $this->load->view("/point/point_member_list_ajax", array(
                'data'  => $data,
                'cnt'   => $cnt
            ));
        }
    }//end of point_member_list_ajax()

    /**
     * 회원관리 > 적립금발급 (Ajax)
     */
    public function point_issue_ajax()
    {
        $this->load->model('member_model');

        ajax_request_check();

        $point_type = trim($this->input->post_get('uid', true));
        $m_num = trim($this->input->post_get('m_num', true));

        $point_master = $this->point_model->getPointMaster($point_type);

        $point_value = $point_master->pt_issue_value;
        $point_period = $point_master->pt_period;

        $point_startdate = date('Ymd' . '000000');
        $point_enddate = date('Ymd', strtotime("+" . $point_period . " days")) . "235959";

        $member_row = $this->member_model->get_member_row(array('m_num' => $m_num));

        $data = array(
            're_num' => '',
            'point_type' => $point_type,
            'point_value' => $point_value,
            'point_start' => $point_startdate,
            'point_end' => $point_enddate,
            'm_key' => $member_row->m_key,
            'm_authno' => $member_row->m_authno,
            'reg_type' => 'I',
            'reg_tag' => 'admin > > member > added',
            'last_type' => 'E'
        );

        $result = $this->point_model->insertPointMember($data, 'Y');

        // 통계데이터 지급/보상
        $point_stat_data = array(
            'ps_type' => 'P',
            'ps_sub_type' => 'B',
            'ps_category' => 'B',
            'ps_point' => $point_value,
            'ps_member_key' => $member_row->m_key,

            'ps_member_hp' => $member_row->m_authno?$member_row->m_authno:$member_row->m_order_phone,
            'ps_member_name' => $member_row->m_nickname,

            'ps_ordernum' => '',
            'ps_parent_id' => $result,
            'ps_regdate' => date('Y-m-d H:i:s'),
            'ps_moddate' => date('Y-m-d H:i:s')
        );

        $this->point_model->setPointStat($point_stat_data);

        if($result){
            $rResult = array('success' => true , 'msg' => '적립금 처리완료');
        } else {
            $rResult = array('success' => false , 'msg' => '적립금 적립실패! 다시시도하세요.');
        }

        echo json_encode_no_slashes($rResult);

    }//end of coupon_issue_ajax()

    /**
     * 회원관리 > 적립금삭제 (Ajax)
     */
    public function point_delete_ajax()
    {
        $this->load->model('member_model');

        ajax_request_check();

        $id = $this->input->post('uid');
        $m_num = $this->input->post_get('m_num');

        $member_row = $this->member_model->get_member_row(array('m_num' => $m_num));

        $data = array(
            'id' => $id,
            'm_key' => $member_row->m_key,
            'reg_type' => 'D',
            'reg_tag' => 'admin > review > delete'
        );

        $result = $this->point_model->deletePointMember($data);
        $row_pt = $this->point_model->getPoints($id);

        // 통계데이터 회수/취소회수
        $point_stat_data = array(
            'ps_type' => 'M',
            'ps_sub_type' => 'C',
            'ps_category' => 'Z',
            'ps_point' => $row_pt->pm_rest_points,
            'ps_member_key' => $member_row->m_key,
            'ps_ordernum' => '',
            'ps_member_hp' => $member_row->m_authno?$member_row->m_authno:$member_row->m_order_phone,
            'ps_member_name' => $member_row->m_nickname,
            'ps_parent_id' => $id,
            'ps_regdate' => date('Y-m-d H:i:s')
        );

        $this->point_model->setPointStat($point_stat_data);

//        $point_stat_data = array(
//            'ps_type' => 'M',
//            'ps_sub_type' => 'C',
//            'ps_category' => 'Z',
//            'ps_point' => $row_pt->pm_rest_points
//        );
//
//        $this->point_model->updatePointStat($point_stat_data, $id);

        if($result){
            $rResult = array('success' => true , 'msg' => '삭제처리 완료');
        } else {
            $rResult = array('success' => false , 'msg' => '삭제처리 실패! 다시시도하세요.');
        }

        echo json_encode_no_slashes($rResult);

    }//end of point_delete_ajax()

    /**
     * 회원관리 > 현재적립금 (Ajax)
     */
    public function point_member_points_ajax()
    {
        ajax_request_check();

        $this->load->model('member_model');

        //request
        $req['m_num'] = trim($this->input->post_get('m_num', true));
        if( empty($req['m_num']) ) {
            $this->output->set_status_header('403');
            exit;
        }

        $member_row = $this->member_model->get_member_row(array('m_num' => $req['m_num']));

        if( empty($member_row) ) {
            $this->output->set_status_header('403');
            exit;
        }

        $param_data = array(
            'inid' => $this->inid,
            'm_key' => $member_row->m_key
        );

        $rs1 = $this->point_model->getPointMemberStatus($param_data, 1);
        $rs2 = $this->point_model->getPointMemberStatus($param_data, 2);

        $result = array('now_point' => $rs1->point, 'pre_point' => $rs2->point);

        echo json_encode_no_slashes($result);
    }

    /**
     * 리뷰 적립금 적용 ajax요청시
     */
    public function insertReviewPointMemberAjax()
    {
        ajax_request_check();
        $re_num = $this->input->post_get('re_num'); // 리뷰페이지 글 넘버
        $reward_type = $this->input->post_get('reward_type'); // A: 텍스트, B: 포토, C:지급안함

        if(empty($re_num)) {
            $rResult = array('success' => false, 'msg' => '잘못된 요청입니다.(no parameters)');
            exit;
        }

        $this->insertReviewPointMember($re_num, $reward_type, $chk_ajax = 'Y');
    }

    /**
     * 리뷰 > 적립금 적용
     */
    public function insertReviewPointMember($re_num, $reward_type, $chk_ajax)
    {
        $row_review = $this->review_model->chkReviewReward($re_num); // 기지급 체크

        if($row_review->re_reward == 'N') { // 미지급일 경우
            $confirm_yn = 'Y';

            if($reward_type == 'A') {
                $point_type = '3'; //텍스트
            } elseif($reward_type == 'B') {
                $point_type = '4'; //텍스트
            }

            $point_master = $this->point_model->getPointMaster($point_type);

            $point_value = $point_master->pt_issue_value;
            $point_period = $point_master->pt_period;
            $point_startdate = date('Ymd' . '000000');
            $point_enddate = date('Ymd', strtotime("+" . $point_period . " days")) . "235959";

            $member_row = $this->point_model->_getReviewMemberInfo($re_num); // 글번호로 멤버 정보 가져오기

            if($reward_type != 'C') {

                $data = array(
                    're_num' => $re_num,
                    'point_type' => $point_type,
                    'point_value' => $point_value,
                    'point_start' => $point_startdate,
                    'point_end' => $point_enddate,
                    'm_key' => $member_row->m_key,
                    'm_authno' => $member_row->m_authno,
                    'reg_type' => 'I',
                    'reg_tag' => 'admin > review > added',
                    'last_type' => 'C'
                );

                $result = $this->point_model->insertPointMember($data, $confirm_yn);
            }

            $this->review_model->updateReviewReward($re_num, 'Y', $reward_type); // 업데이트 re_reward => 적립완료

            if($result) {
                // 통계데이터 지급/보상
                $point_stat_data = array(
                    'ps_type' => 'P',
                    'ps_sub_type' => 'B',
                    'ps_category' => 'D',
                    'ps_point' => $point_value,
                    'ps_member_key' => $member_row->m_key,
                    'ps_ordernum' => '',
                    'ps_member_hp' => $member_row->m_authno?$member_row->m_authno:$member_row->m_order_phone,
                    'ps_member_name' => $member_row->m_nickname,
                    'ps_parent_id' => $result,
                    'ps_regdate' => date('Y-m-d H:i:s'),
                    'ps_moddate' => date('Y-m-d H:i:s')

                );

                //log_message('zs','review point :: data :: '.json_encode_no_slashes($point_stat_data));

                $this->point_model->setPointStat($point_stat_data);
            }
        }

        if($chk_ajax == 'Y') { // ajax 호출이면

            if($reward_type != 'C') {
                if ($result) {
                    $rResult = array('success' => true, 'msg' => '적립금 처리완료');
                } else {
                    $rResult = array('success' => false, 'msg' => '적립금 적립실패! 다시시도하세요.');
                }
            } else {
                $rResult = array('success' => true, 'msg' => '처리완료');
            }

            echo json_encode_no_slashes($rResult);

        }
    }

    /**
     * 리뷰 > 기지급 체크
     */
    public function chkReviewReward()
    {
        ajax_request_check();

        $re_num = $this->input->post('re_num');

        //적립금 기지급 체크
        $row_review = $this->review_model->chkReviewReward($re_num); // 기지급 체크

        $reward_type = ''; // 적립된 적립금 이름
        if($row_review->re_reward == 'Y') {

            if($row_review->re_reward_type != 'C') {
                $pm_data = $this->point_model->getPointMemberData($re_num);

                if($pm_data->pm_point_id == '3') {
                    $reward_type = '(텍스트) 지급완료';
                } elseif($pm_data->pm_point_id == '4') {
                    $reward_type = '(포토) 지급완료';
                }    
            } else {
                $reward_type = '지급안함';
            }
        }

        $rResult = array('reward_yn' => $row_review->re_reward , 'reward_type' => $reward_type);

        echo json_encode_no_slashes($rResult);

    }

    /**
     * 리뷰 > 적립금 삭제
     */
    public function deleteReviewPointMember()
    {
        ajax_request_check();

        $re_num = $this->input->post('re_num');

        $point_member = $this->point_model->getPointMemberData($re_num);
        $id = $point_member->pm_uid;
        $m_key = $point_member->pm_member_key;

        // 현재 적립금 잔액 체크
        $param_data = array(
            'inid' => $this->inid,
            'm_key' => $m_key
        );

        //$review_point = $this->point_model->getPointMemberData($m_key, $re_num); // 해당 리뷰에 지급된 적립금
        $now_point = $this->point_model->getPointMemberStatus($param_data, 1); // 현재 총 적립금
        $member_row = $this->point_model->_getReviewMemberInfo($re_num); // 글번호로 멤버 정보 가져오기

        if ($now_point->point < $point_member->pm_rest_points) {
            $rResult = array('success' => false , 'msg' => '삭제될 적립금 잔액이 부족합니다.');
        } else {

            if($point_member->pm_use_yn == 'Y' && ($point_member->pm_rest_points == 0 || ($point_member->pm_points != $point_member->pm_rest_points))) {
                // 사용된 적립금은 삭제 금지
                $rResult = array('success' => false , 'msg' => '이미 사용을 하여 삭제가 불가합니다.');
            } else {

                $data = array(
                    'id' => $id,
                    're_num' => $re_num,
                    'm_key' => $m_key,
                    'reg_type' => 'D',
                    'reg_tag' => 'admin > member > delete'
                );

                $this->review_model->updateReviewReward($re_num, 'N', ''); // 업데이트 re_reward => 미적립

                if($point_member) {

                    $result = $this->point_model->deletePointMember($data);

                    if ($result) {
                        $row_pt = $this->point_model->getPoints($id);

                        // 통계데이터 회수/취소회수
                        $point_stat_data = array(
                            'ps_type' => 'M',
                            'ps_sub_type' => 'C',
                            'ps_category' => 'R',
                            'ps_point' => $row_pt->pm_rest_points,
                            'ps_member_key' => $m_key,
                            'ps_ordernum' => '',
                            'ps_member_hp' => $member_row->m_authno?$member_row->m_authno:$member_row->m_order_phone,
                            'ps_member_name' => $member_row->m_nickname,
                            'ps_parent_id' => $id,
                            'ps_regdate' => date('Y-m-d H:i:s')
                        );

                        $this->point_model->setPointStat($point_stat_data);

//                        $point_stat_data = array(
//                            'ps_type' => 'M',
//                            'ps_sub_type' => 'C',
//                            'ps_category' => 'R',
//                            'ps_point' => $row_pt->pm_rest_points
//                        );
//
//                        $this->point_model->updatePointStat($point_stat_data, $id);

                        $rResult = array('success' => true , 'msg' => '적립금 취소처리 완료');
                    } else {
                        $rResult = array('success' => false , 'msg' => '적립금 취소처리 실패! 다시시도하세요.');
                    }
                } else {
                    $rResult = array('success' => true , 'msg' => '적립금 취소처리 완료');
                }
            }
        }

        echo json_encode_no_slashes($rResult);
    }

    /**
     * 리뷰 적립금 일괄 적용
     */
    public function batchReviewApply() {

        $re_list = $this->input->post('re_list'); // 적용대상 리뷰리스트
        $re_type = $this->input->post('re_type'); // 적용타입 A:텍스트, B:포토, C:지급안함

        if(empty($re_list)) {
            $result = false;
        } else {
            foreach($re_list as $re_num) {
                $this->insertReviewPointMember($re_num, $re_type, $chk_ajax = 'N');
            }
            $result = true;
        }

        if ($result) {
            $rResult = array('success' => true , 'msg' => '일괄처리 완료');
        } else {
            $rResult = array('success' => false , 'msg' => '일괄처리 실패. 다시시도하세요.');
        }

        echo json_encode_no_slashes($rResult);
    }

    /**
     * 회원관리 > 적립금사용내역
     */
    public function point_used_history()
    {
        $uid = $this->input->get('uid');

        $result = $this->point_model->getPointUsedHistory($uid);

        echo "<table class=\"table table-bordered table-responsive table-hover\">";
        echo "<tr style='background: #ddd'>";
        echo "<th>No</th>";
        echo "<th>주문번호</th>";
        echo "<th>사용적립금</th>";
        echo "<th>구매상품명</th>";
        echo "<th>상품금액</th>";
        echo "<th>취소여부</th>";
        echo "</tr>";

        foreach($result as $key => $val) {

            $style = '';
            if($val->pu_cancel_yn == 'Y') {
                $style = "style='background :#bababa;'";
            }

            echo "<tr " . $style . ">";
            echo "<td>" . $val->pu_sortnum . "</td>";
            echo "<td>" . $val->op_oordernum . "</td>";
            echo "<td>" . number_format($val->pu_used_point) . "점</td>";
            echo "<td>" . $val->op_name . "[" . $val->op_option1 . "/" . $val->op_option2 . "]" . "</td>";
            echo "<td>" . number_format($val->op_price) . "원</td>";
            echo "<td>" . $val->pu_cancel_yn . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    }
}
{
}//end of class Point