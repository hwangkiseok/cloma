<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <th>No.</th>
            <th>제목/메시지/요약내용</th>
            <th>아이콘</th>
            <th>이미지</th>
            <th class="<?php echo $sort_array['ap_target_url'][1];?>" onclick="form_submit('sort_field=ap_target_url&sort_type=<?php echo $sort_array['ap_target_url'][0]; ?>');">이동URL</th>
            <th class="<?php echo $sort_array['ap_noti_type'][1];?>" onclick="form_submit('sort_field=ap_noti_type&sort_type=<?php echo $sort_array['ap_noti_type'][0]; ?>');">알림타입</th>
            <th class="<?php echo $sort_array['ap_badge'][1];?>" onclick="form_submit('sort_field=ap_badge&sort_type=<?php echo $sort_array['ap_badge'][0]; ?>');">뱃지</th>
            <th class="<?php echo $sort_array['ap_state'][1];?>" onclick="form_submit('sort_field=ap_state&sort_type=<?php echo $sort_array['ap_state'][0]; ?>');">발송상태</th>
            <th class="<?php echo $sort_array['ap_display_state'][1];?>" onclick="form_submit('sort_field=ap_display_state&sort_type=<?php echo $sort_array['ap_display_state'][0]; ?>');">노출</th>
            <th class="<?php echo $sort_array['ap_regdatetime'][1];?>" onclick="form_submit('sort_field=ap_regdatetime&sort_type=<?php echo $sort_array['ap_regdatetime'][0]; ?>');">등록일시</th>
            <th class="<?php echo $sort_array['ap_reserve_datetime'][1];?>" onclick="form_submit('sort_field=ap_reserve_datetime&sort_type=<?php echo $sort_array['ap_reserve_datetime'][0]; ?>');">예약일시</th>
            <th class="<?php echo $sort_array['ap_proc_datetime'][1];?>" onclick="form_submit('sort_field=ap_proc_datetime&sort_type=<?php echo $sort_array['ap_proc_datetime'][0]; ?>');">발송일시</th>
            <th class="<?php echo $sort_array['ap_success_cnt'][1];?>" onclick="form_submit('sort_field=ap_success_cnt&sort_type=<?php echo $sort_array['ap_success_cnt'][0]; ?>');">발송성공<br/>(FCM)</th>
            <th class="<?php echo $sort_array['ap_fail_cnt'][1];?>" onclick="form_submit('sort_field=ap_fail_cnt&sort_type=<?php echo $sort_array['ap_fail_cnt'][0]; ?>');">발송실패</th>
            <th class="<?php echo $sort_array['ap_receive_cnt'][1];?>" onclick="form_submit('sort_field=ap_receive_cnt&sort_type=<?php echo $sort_array['ap_receive_cnt'][0]; ?>');">수신</th>
            <th>클릭</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

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


            $pattern = '/goo.gl/';
            preg_match($pattern, $row->ap_target_url, $matches);
            //클릭
            if(!empty($matches)){
                $click_count = get_google_shorturl_click_count($row->ap_target_url);
                if($click_count == ''){
                    $click_count = '0';
                }
            }else{
                $click_count = '0';
            }
            ?>

            <tr role="row">
                <td style="width:50px;"><?php echo number_format($list_number); ?></td>
                <td style="word-break:break-all;text-align:left;<?php echo $background_color; ?>">
                    <p <?php echo $title_color; ?>><?php echo rawurldecode($row->ap_subject); ?></p>
                    <p <?php echo $message_color; ?>><?php echo $row->ap_message; ?></p>
                    <?php if( !empty($row->ap_summary) ) { ?>
                    <p class="push_summary"><?php echo $row->ap_summary; ?></p>
                    <?php } ?>
                </td>
                <td style="width:85px;"><?php echo create_img_tag($row->ap_icon, 0, 80); ?></td>
                <td style="width:85px;"><?php echo create_img_tag($row->ap_image, 0, 80); ?></td>
                <td style="width:100px;word-break:break-all;">
                    <a href="<?php echo $row->ap_target_url; ?>" target="_blank"><?php echo $row->ap_target_url; ?></a>
                    <?php if( preg_match("/\/goo.gl\//i", $row->ap_target_url) !== false ) : ?>
                        <br /><a href="#none" onclick="new_win_open('<?php echo $row->ap_target_url; ?>.info', '', '800', '600');" target="_blank"><i class="fa fa-bar-chart" aria-hidden="true"></i></a>
                    <?php endif; ?>
                </td>
                <td style="width:75px;"><?php echo get_config_item_text($row->ap_noti_type, 'app_push_noti_type'); ?></td>
                <td style="width:50px;"><?php echo get_config_item_text($row->ap_badge, 'app_push_badge'); ?></td>
                <td style="width:75px;"><?php echo get_config_item_text($row->ap_state, 'app_push_state'); ?></td>
                <td style="width:60px;">
                    <?php if ( $row->ap_display_state == 'Y' ) { ?>
                        <button type="button" class="btn btn-success btn-xs"><?php echo get_config_item_text($row->ap_display_state, 'app_push_display_state', false); ?></button>
                    <?php } else { ?>
                        <button type="button" class="btn btn-warning btn-xs"><?php echo get_config_item_text($row->ap_display_state, 'app_push_display_state', false); ?></button>
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
                    <?php echo number_format($row->ap_receive_cnt); ?><br />
                    <?php echo ($row->ap_success_cnt_fcm > 0 && $row->ap_receive_cnt > 0) ? "(" . number_format($row->ap_receive_cnt / $row->ap_success_cnt_fcm * 100, 2) . "%)" : "";  ?>
                </td>
                <td style="width:50px;">

                    <?if(is_numeric($click_count)){?>
                        <?php echo number_format($click_count); ?>
                        <?php echo ($click_count > 0 && $row->ap_receive_cnt > 0 && $row->ap_receive_cnt > $click_count) ? "(" . number_format($click_count / $row->ap_receive_cnt * 100, 2) . "%)" : "";  ?>
                    <?}else{?>
                        <?php echo $click_count; ?>
                    <?}?>
                </td>

            </tr>

            <?php
            $list_number--;
        }//end of foreach()
        ?>
        </tbody>
    </table>
</div>

<div class="row text-center">
    <?php echo $pagination; ?>
</div>