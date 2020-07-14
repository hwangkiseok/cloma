
<style>
    .wrap { position:relative; /*감싸는 레이어에 포지션 속성을 잡아주는 게 필수!(relative, absolute, fixed 중 택1*/ width:100%; height:20px; background:#F66; text-align:center; line-height:100px; margin:0 auto; color:#000; font-size:12px; }
    .over { position:absolute; top:100px; left:100px;/*위에 올라가는 레이어의 포지션은 top, bottom 둘 중 하나, left, right 둘 중 하나의 속성을 선택하여 잡아준다.*/ width:100%; height:20px; background:#FFFFCC; text-align:center; line-height:300px;}
</style>
<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <colgroup>
            <col style="width:40px;">
            <col style="width:300px;">
        </colgroup>
        <thead>
        <tr role="row" class="active">
            <th>No.</th>
            <th class="<?php echo $sort_array['apo_subject'][1];?>" onclick="form_submit('sort_field=apo_subject&sort_type=<?php echo $sort_array['apo_subject'][0]; ?>');">제목</th>
<!--            <th>이미지</th>-->
            <!--<th class="<?php echo $sort_array['apo_url'][1];?>" onclick="form_submit('sort_field=apo_url&sort_type=<?php echo $sort_array['apo_url'][0]; ?>');">이동URL</th>-->
            <th class="<?php echo $sort_array['apo_termlimit_datetime1'][1];?>" onclick="form_submit('sort_field=apo_termlimit_datetime1&sort_type=<?php echo $sort_array['apo_termlimit_datetime1'][0]; ?>');">노출시작일시</th>
            <th class="<?php echo $sort_array['apo_termlimit_datetime2'][1];?>" onclick="form_submit('sort_field=apo_termlimit_datetime2&sort_type=<?php echo $sort_array['apo_termlimit_datetime2'][0]; ?>');">노출종료일시</th>
            <th class="<?php echo $sort_array['apo_regdatetime'][1];?>" onclick="form_submit('sort_field=apo_regdatetime&sort_type=<?php echo $sort_array['apo_regdatetime'][0]; ?>');">등록일시</th>
<!--            <th>통계</th>-->
            <th class="<?php echo $sort_array['apo_display_yn'][1];?>" onclick="form_submit('sort_field=apo_display_yn&sort_type=<?php echo $sort_array['apo_display_yn'][0]; ?>');">노출</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($app_popup_list as $row) {

            $total_cnt = 0;
            $per_view = 0;
            $per_click = 0;
            $per_close = 0;
            if($row->apo_click_count > 0 || $row->apo_close_count > 0) {
                $total_cnt = $row->apo_display_count;

                $per_view = round($row->apo_view_count / $total_cnt * 100);
                $per_click = round($row->apo_click_count / $total_cnt * 100);
                $per_close = round($row->apo_close_count / $total_cnt * 100);
            }

            ?>

            <tr role="row">
                <td><?php echo number_format($list_number); ?></td>
                <td>
                    <?php echo rawurldecode($row->apo_subject); ?>
                </td>
                <!--<td style="width:85px;"><?php echo create_img_tag($row->apo_image, 0, 50); ?></td>
                <td style="width:100px;word-break:break-all;">
                    <button type="button" class="btn btn-xs btn-default" onclick="window.open('<?php echo $this->config->item('site_https') . $row->apo_url; ?>');"><?=($row->apo_content_type == "1") ? "상품" : "URL";?>확인</button>
                    <?php if( preg_match("/\/goo.gl\/|\/bit.ly\//i", $row->apo_url) !== false ) : ?>
                        <!--<br /><a href="#none" onclick="new_win_open('<?php echo $row->apo_url; ?>+', '', '800', '600');" target="_blank"><i class="fa fa-bar-chart" aria-hidden="true"></i></a>-->
                    <?php endif; ?>
                </td>
                -->
                <td style="width:80px;"><?php echo get_datetime_format($row->apo_termlimit_datetime1); ?></td>
                <td style="width:80px;"><?php echo get_datetime_format($row->apo_termlimit_datetime2); ?></td>
                <td style="width:80px;"><?php echo get_datetime_format($row->apo_regdatetime); ?></td>
                <!--
                <td style="width:100px;">

                    <div style="border: 1px solid #d5d5d5; list-style: none; text-align:center;">
                        <li style="float:left; padding: 2px 0px 0px 5px;">노출: <?php echo $per_view . "%"; ?> (<?php echo $row->apo_view_count > 0 ? number_format($row->apo_view_count) : '0'; ?> 건)</li>
                        <li class="chart" style="width:<?php echo $per_view; ?>%; height:20px; background-color: #dff0d8;"></li>
                    </div>

                    <div style="padding: 2px;"></div>

                    <div style="border: 1px solid #d5d5d5; list-style: none; text-align:center;">
                        <li style="float:left; padding: 2px 0px 0px 5px;">클릭: <?php echo $per_click . "%"; ?> (<?php echo $row->apo_click_count > 0 ? number_format($row->apo_click_count) : '0'; ?> 건)</li>
                        <li class="chart" style="width:<?php echo $per_click; ?>%; height:20px; background-color: #B2CCFF;"></li>
                    </div>
                </td>
                -->
                <td style="width:60px;">
                    <?php if ( $row->apo_display_yn == 'Y' ) { ?>
                        <button type="button" class="btn btn-success btn-xs" onclick="app_popup_display_toggle('<?php echo $row->apo_num; ?>');"><?php echo get_config_item_text($row->apo_display_yn, 'app_popup_display_yn', false); ?></button>
                    <?php } else { ?>
                        <button type="button" class="btn btn-warning btn-xs" onclick="app_popup_display_toggle('<?php echo $row->apo_num; ?>');"><?php echo get_config_item_text($row->apo_display_yn, 'app_popup_display_yn', false); ?></button>
                    <?php } ?>

                </td>
                <td style="width:50px;line-height:25px;">
                    <button type="button" class="btn btn-primary btn-xs" onclick="app_popup_update('<?php echo $row->apo_num; ?>');">수정</button>
                    <button type="button" class="btn btn-danger btn-xs" onclick="app_popup_delete('<?php echo $row->apo_num; ?>');">삭제</button>
                </td>
            </tr>

            <?php
            $list_number--;
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
<script>
    $(document).ready(function () {
        //$(".chart").effect("slide");
    });

</script>