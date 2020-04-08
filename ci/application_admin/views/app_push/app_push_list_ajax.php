<?if($req['rctly'] == 'Y' ){?>
    <p><?=number_format(count($app_push_list))?>개(총 <?=number_format($tot_reserve_push_cnt)?>개)</p>
<?}?>

<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <colgroup>
            <col style="width:40px;">
            <col style="width:300px;">
        </colgroup>
        <thead>
        <tr role="row" class="active">
            <th>No.</th>
            <th>제목/메시지/요약내용</th>
<!--            <th>아이콘</th>-->
            <th>푸시타입</th>
            <th>이미지</th>
            <th class="<?php echo $sort_array['ap_target_url'][1];?>" onclick="form_submit('sort_field=ap_target_url&sort_type=<?php echo $sort_array['ap_target_url'][0]; ?>');">이동URL</th>
            <!--<th class="<?php echo $sort_array['ap_noti_type'][1];?>" onclick="form_submit('sort_field=ap_noti_type&sort_type=<?php echo $sort_array['ap_noti_type'][0]; ?>');">알림타입</th>-->
            <th class="<?php echo $sort_array['ap_badge'][1];?>" onclick="form_submit('sort_field=ap_badge&sort_type=<?php echo $sort_array['ap_badge'][0]; ?>');">뱃지</th>
            <th class="<?php echo $sort_array['ap_state'][1];?>" onclick="form_submit('sort_field=ap_state&sort_type=<?php echo $sort_array['ap_state'][0]; ?>');">발송상태</th>
            <th class="<?php echo $sort_array['ap_display_state'][1];?>" onclick="form_submit('sort_field=ap_display_state&sort_type=<?php echo $sort_array['ap_display_state'][0]; ?>');">노출</th>
            <th class="<?php echo $sort_array['ap_regdatetime'][1];?>" onclick="form_submit('sort_field=ap_regdatetime&sort_type=<?php echo $sort_array['ap_regdatetime'][0]; ?>');">등록일시</th>
            <th class="<?php echo $sort_array['ap_reserve_datetime'][1];?>" onclick="form_submit('sort_field=ap_reserve_datetime&sort_type=<?php echo $sort_array['ap_reserve_datetime'][0]; ?>');">예약일시</th>
            <th class="<?php echo $sort_array['ap_proc_datetime'][1];?>" onclick="form_submit('sort_field=ap_proc_datetime&sort_type=<?php echo $sort_array['ap_proc_datetime'][0]; ?>');">발송일시</th>
            <th class="<?php echo $sort_array['ap_success_cnt'][1];?>" onclick="form_submit('sort_field=ap_success_cnt&sort_type=<?php echo $sort_array['ap_success_cnt'][0]; ?>');">발송성공<br/>(FCM)</th>
            <th class="<?php echo $sort_array['ap_fail_cnt'][1];?>" onclick="form_submit('sort_field=ap_fail_cnt&sort_type=<?php echo $sort_array['ap_fail_cnt'][0]; ?>');">발송실패</th>
            <th class="<?php echo $sort_array['ap_receive_cnt'][1];?>" onclick="form_submit('sort_field=ap_receive_cnt&sort_type=<?php echo $sort_array['ap_receive_cnt'][0]; ?>');">수신</th>
            <th title="방문자/방문율(발송송성공 중)">방문자</th>
            <th title="상품클릭/상품클릭률(방문자 중)">상품클릭</th>
            <?if($req['rctly'] != 'Y' ){?>
            <th>관리</th>
            <?}?>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));
        $prev_ap_reserve_date = "";
        $grp_no = 0;

        foreach($app_push_list as $row) {
            $background_color = "";
            $title_color = "";
            $message_color = "";
            if( !empty($row->ap_style) ) {
                $ap_style_array = json_decode($row->ap_style, true);
                if( isset($ap_style_array['background_color']) && !empty($ap_style_array['background_color']) ) {
                    $background_color = "background:" . $ap_style_array['background_color'];
                }
                if( isset($ap_style_array['title_color']) && !empty($ap_style_array['title_color']) ) {
                    $title_color = 'style="color:' . $ap_style_array['title_color'] . '"';
                }
                if( isset($ap_style_array['message_color']) && !empty($ap_style_array['message_color']) ) {
                    $message_color = 'style="color:' . $ap_style_array['message_color'] . '"';
                }
            }

            $ap_reserve_date = substr(number_only($row->ap_reserve_datetime), 0, 8);
            $bg_class = '';
            $border_class = '';
            if( $ap_reserve_date != $prev_ap_reserve_date ) {
                $border_class = ' bdr_t_1 ';
                $grp_no++;
            }
            if( !($grp_no % 2) ) {
                $bg_class = ' bg_gray ';
            }
            ?>

            <tr role="row" class="<?=$bg_class;?><?=$border_class;?>">
                <td style="width:50px;"><?php echo number_format($list_number); ?><br />(<?=$row->ap_num;?>)</td>
                <td style="word-break:break-all;text-align:left;<?php echo $background_color; ?>">
                    <p <?php echo $title_color; ?>><?php echo $row->ap_subject; ?></p>
                    <p <?php echo $message_color; ?>><?php echo $row->ap_message; ?></p>
                    <?php if( !empty($row->ap_summary) ) { ?>
                    <p class="push_summary"><?php echo $row->ap_summary; ?></p>
                    <?php } ?>
                </td>
                <!--<td style="width:85px;"><?php echo create_img_tag($row->ap_icon, 0, 80); ?></td>-->
                <td style="width:85px;">
                    <?if($row->ap_push_type == 'point'){?>
                        <span style="color:red;">적립금 푸시</span>
                    <?}else{?>
                        <span style="color:blue;">상품 푸시</span>
                    <?}?>
                </td>
                <td style="width:85px;"><?php echo create_img_tag($row->ap_image, 0, 80); ?></td>
                <td style="width:100px;word-break:break-all;">

                    <?php if( preg_match("/\/bit.ly\//i", $row->ap_target_url) == true) { //비틀리쇼트너인 경우 ) : ?>
                        <button type="button" class="btn btn-xs btn-default" onclick="window.open('<?=$row->ap_target_url; ?>');">상품확인</button>
                        <br><a href="#none" onclick="new_win_open('<?php echo $row->ap_target_url; ?>+', '', '800', '600');" target="_blank"><i class="fa fa-bar-chart" aria-hidden="true"></i></a>
                    <?php }else {?>
                        <button type="button" class="btn btn-xs btn-default" onclick="window.open('<?php echo $this->config->item('default_http').$row->ap_target_url; ?>');">상품확인</button>
                    <?} ?>
                </td>
                <!-- <td style="width:75px;"><?php echo get_config_item_text($row->ap_noti_type, 'app_push_noti_type'); ?></td>-->
                <td style="width:50px;"><?php echo get_config_item_text($row->ap_badge, 'app_push_badge'); ?></td>
                <td style="width:75px;"><?php echo get_config_item_text($row->ap_state, 'app_push_state'); ?></td>
                <td style="width:60px;">
                    <?php if ( $row->ap_display_state == 'Y' ) { ?>
                        <button type="button" class="btn btn-success btn-xs" onclick="app_push_display_toggle('<?php echo $row->ap_num; ?>');"><?php echo get_config_item_text($row->ap_display_state, 'app_push_display_state', false); ?></button>
                    <?php } else { ?>
                        <button type="button" class="btn btn-warning btn-xs" onclick="app_push_display_toggle('<?php echo $row->ap_num; ?>');"><?php echo get_config_item_text($row->ap_display_state, 'app_push_display_state', false); ?></button>
                    <?php } ?>
                </td>
                <td style="width:80px;"><?php echo get_datetime_format($row->ap_regdatetime); ?></td>
                <td style="width:80px;"><?php echo get_datetime_format($row->ap_reserve_datetime); ?></td>
                <td style="width:80px;"><?php echo get_datetime_format($row->ap_proc_datetime); ?></td>
                <td style="width:70px;">
                    <?php echo number_format($row->ap_success_cnt); ?>
                    <?php if ( !empty($row->ap_success_cnt_fcm) ) : ?>
                        <br />(<?php echo number_format($row->ap_success_cnt_fcm); ?>)
                    <?php endif; ?>
                </td>
                <td style="width:50px;"><?php echo number_format($row->ap_fail_cnt); ?></td>
                <td style="width:50px;">
                    <?php echo number_format($row->ap_receive_cnt); ?>
                    <?php echo ($row->ap_success_cnt > 0 && $row->ap_receive_cnt > 0) ? "<br />(" . number_format($row->ap_receive_cnt / $row->ap_success_cnt * 100, 2) . "%)" : "";  ?>
                </td>
                <td style="width:50px;">
                    <?=number_format($row->ap_view_cnt)?>
                    <br />
                    <span style="font-size:12px;">(<?=($row->ap_view_cnt) ? @number_format($row->ap_view_cnt / $row->ap_success_cnt * 100, 2) : "0.00";?>%)</span>
                </td>
                <td style="width:50px;">
                    <a href="#none" role="button" tabindex="0" data-container="body" data-trigger="focus" data-toggle="popover" data-placement="left" class="show_productClick" data-seq="<?=$row->ap_num?>"><?=number_format($row->ap_product_click_cnt)?></a>
                    <br />
                    <span style="font-size:12px;">(<?=($row->ap_product_click_cnt) ? number_format($row->ap_product_click_cnt / $row->ap_view_cnt * 100, 2) : "0.00";?>%)</span>
                </td> 
                <? if($req['rctly'] != 'Y' ){ ?>
                <td style="width:50px;line-height:25px;">
                    <?php //if ( $row->ap_state != '3' ) { ?>
                        <button type="button" class="btn btn-primary btn-xs" onclick="app_push_update_pop('<?php echo $row->ap_num; ?>');">수정</button>
                    <?php //} ?>
                    <?php if ( $row->ap_state == '1' ) { ?>
                        <button type="button" class="btn btn-danger btn-xs" onclick="app_push_delete('<?php echo $row->ap_num; ?>');">삭제</button>
                    <?php } ?>
                    <button type="button" class="btn btn-info btn-xs" onclick="app_push_test_send('<?php echo $row->ap_num; ?>');">테스트</button>
                </td>
                <? }//endif; ?>
            </tr>

            <?php
            $list_number--;
            $prev_ap_reserve_date = $ap_reserve_date;
        }//end of foreach()
        ?>
        </tbody>
    </table>
</div>

<?if($req['rctly'] != 'Y' ){?>
<div class="row text-center">
    <?php echo $pagination; ?>
</div>
<?}?>
