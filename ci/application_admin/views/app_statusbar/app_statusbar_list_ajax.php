<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <th>No.</th>
            <th>상태바 색상코드</th>
            <th class="<?php echo $sort_array['asb_regdatetime'][1];?>" onclick="form_submit('sort_field=asb_regdatetime&sort_type=<?php echo $sort_array['asb_regdatetime'][0]; ?>');">등록일시</th>
            <th class="<?php echo $sort_array['asb_procdatetime'][1];?>" onclick="form_submit('sort_field=asb_procdatetime&sort_type=<?php echo $sort_array['asb_procdatetime'][0]; ?>');">적용일시</th>
            <th class="<?php echo $sort_array['asb_state'][1];?>" onclick="form_submit('sort_field=asb_state&sort_type=<?php echo $sort_array['asb_state'][0]; ?>');">적용상태</th>
            <th class="<?php echo $sort_array['asb_usestate'][1];?>" onclick="form_submit('sort_field=asb_usestate&sort_type=<?php echo $sort_array['asb_usestate'][0]; ?>');">사용여부</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($app_statusbar_list as $row) {
            ?>

            <tr role="row">
                <td style="width:60px;"><?php echo number_format($list_number); ?></td>
                <td><span style="display:inline-block;width:130px;height:30px;line-height:30px;border:1px solid #aaa;color:#fff;background:<?php echo $row->asb_color; ?>;"><?php echo $row->asb_color; ?></span></td>
                <td><?php echo get_datetime_format($row->asb_regdatetime); ?></td>
                <td><?php echo get_datetime_format($row->asb_procdatetime); ?></td>
                <td><?php echo get_config_item_text($row->asb_state, 'app_statusbar_state'); ?></td>
                <td>
                    <?php if ( $row->asb_state == '1' ) { ?>

                        <?php if ( $row->asb_usestate == 'Y' ) { ?>
                            <button type="button" class="btn btn-success btn-xs" onclick="app_statusbar_usestate_toggle('<?php echo $row->asb_num; ?>');"><?php echo get_config_item_text($row->asb_usestate, 'app_statusbar_usestate', false); ?></button>
                        <?php } else { ?>
                            <button type="button" class="btn btn-warning btn-xs" onclick="app_statusbar_usestate_toggle('<?php echo $row->asb_num; ?>');"><?php echo get_config_item_text($row->asb_usestate, 'app_statusbar_usestate', false); ?></button>
                        <?php } ?>

                    <?php } else { ?>

                        <?php echo get_config_item_text($row->asb_usestate, "app_statusbar_usestate"); ?>

                    <?php } ?>
                </td>
                <td style="width:100px;line-height:25px;">
                    <?php if ( $row->asb_state != '3' ) { ?>
                        <button type="button" class="btn btn-primary btn-xs" onclick="app_statusbar_update_pop('<?php echo $row->asb_num; ?>')">수정</button>
                    <?php } ?>
                    <?php if ( $row->asb_state != '2'  ) { ?>
                        <button type="button" class="btn btn-danger btn-xs" onclick="app_statusbar_delete('<?php echo $row->asb_num; ?>')">삭제</button>
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