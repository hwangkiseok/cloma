<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <th>No.</th>
            <th>이미지</th>
            <th class="<?php echo $sort_array['aps_regdatetime'][1];?>" onclick="form_submit('sort_field=aps_regdatetime&sort_type=<?php echo $sort_array['aps_regdatetime'][0]; ?>');">등록일시</th>
            <th class="<?php echo $sort_array['aps_termlimit1'][1];?>" onclick="form_submit('sort_field=aps_termlimit1&sort_type=<?php echo $sort_array['aps_termlimit1'][0]; ?>');">시작일</th>
            <th class="<?php echo $sort_array['aps_termlimit2'][1];?>" onclick="form_submit('sort_field=aps_termlimit2&sort_type=<?php echo $sort_array['aps_termlimit2'][0]; ?>');">종료일</th>
            <th class="<?php echo $sort_array['aps_bg_color'][1];?>" onclick="form_submit('sort_field=aps_bg_color&sort_type=<?php echo $sort_array['aps_bg_color'][0]; ?>');">배경색</th>
            <th class="<?php echo $sort_array['aps_state'][1];?>" onclick="form_submit('sort_field=aps_state&sort_type=<?php echo $sort_array['aps_state'][0]; ?>');">적용상태</th>
            <th class="<?php echo $sort_array['aps_usestate'][1];?>" onclick="form_submit('sort_field=aps_usestate&sort_type=<?php echo $sort_array['aps_usestate'][0]; ?>');">사용여부</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($app_splash_list as $row) {
            ?>

            <tr role="row">
                <td style="width:60px;"><?php echo number_format($list_number); ?></td>
                <td><?php echo create_img_tag($row->aps_image, "", 100, ""); ?></td>
                <td><?php echo get_datetime_format($row->aps_regdatetime); ?></td>
                <td><?php echo get_datetime_format($row->aps_termlimit1); ?></td>
                <td><?php echo get_datetime_format($row->aps_termlimit2); ?></td>
                <td>
                    <div style="display: inline-block;width: 120px;line-height: 30px;background-color: #<?=$row->aps_bg_color?>;vertical-align: middle;border: 1px solid #ddd;">
                        #<?php echo $row->aps_bg_color; ?>
                    </div>

                </td>
                <td><?php echo get_config_item_text($row->aps_state, 'app_splash_state'); ?></td>
                <td>
                    <?php if ( $row->aps_state == '1' ) { ?>

                        <?php if ( $row->aps_usestate == 'Y' ) { ?>
                            <button type="button" class="btn btn-success btn-xs" onclick="app_splash_usestate_toggle('<?php echo $row->aps_num; ?>');"><?php echo get_config_item_text($row->aps_usestate, 'app_splash_usestate', false); ?></button>
                        <?php } else { ?>
                            <button type="button" class="btn btn-warning btn-xs" onclick="app_splash_usestate_toggle('<?php echo $row->aps_num; ?>');"><?php echo get_config_item_text($row->aps_usestate, 'app_splash_usestate', false); ?></button>
                        <?php } ?>

                    <?php } else { ?>

                        <?php echo get_config_item_text($row->aps_usestate, "app_splash_usestate"); ?>

                    <?php } ?>
                </td>
                <td style="width:100px;line-height:25px;">
                    <?php if ( $row->aps_state != '3' ) { ?>
                        <button type="button" class="btn btn-primary btn-xs" onclick="app_splash_update_pop('<?php echo $row->aps_num; ?>')">수정</button>
                    <?php } ?>
                    <?php if ( $row->asb_state != '2'  ) { ?>
                        <button type="button" class="btn btn-danger btn-xs" onclick="app_splash_delete('<?php echo $row->aps_num; ?>')">삭제</button>
                    <?php } ?>
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