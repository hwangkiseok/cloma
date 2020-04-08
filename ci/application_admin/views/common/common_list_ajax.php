<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <th>No.</th>
            <th class="<?php echo $sort_array['cm_code'][1];?>" onclick="form_submit('sort_field=cm_code&sort_type=<?php echo $sort_array['cm_code'][0]; ?>');">구분</th>
            <th class="<?php echo $sort_array['cm_content'][1];?>" onclick="form_submit('sort_field=cm_content&sort_type=<?php echo $sort_array['cm_content'][0]; ?>');">내용</th>
            <th class="<?php echo $sort_array['cm_datetime'][1];?>" onclick="form_submit('sort_field=cm_datetime&sort_type=<?php echo $sort_array['cm_datetime'][0]; ?>');">등록/수정일시</th>
            <th class="<?php echo $sort_array['au_name'][1];?>" onclick="form_submit('sort_field=au_name&sort_type=<?php echo $sort_array['au_name'][0]; ?>');">작성자</th>
            <th class="<?php echo $sort_array['cm_usestate'][1];?>" onclick="form_submit('sort_field=cm_usestate&sort_type=<?php echo $sort_array['cm_usestate'][0]; ?>');">활성/비활성</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($common_list as $row) {
            ?>

            <tr role="row">
                <td><?php echo number_format($list_number); ?></td>
                <td><?php echo get_config_item_text($row->cm_code, 'common_code'); ?></td>
                <td>
                    <?if($row->cm_code == 3){?>
                        <span style="color:<?= $this->config->item($row->cm_content, 'product_info_tab_view_text_color'); ?>  ;"> <?= $this->config->item($row->cm_content, 'product_info_tab_view'); ?> </span>
                    <?}else{?>
                        <?php echo strcut_utf8(strip_tags($row->cm_content), 50); ?>
                    <?}?>
                </td>
                <td><?php echo get_datetime_format($row->cm_datetime); ?></td>
                <td><?php echo $row->au_name; ?></td>
                <td><?php echo get_config_item_text($row->cm_usestate, 'common_usestate'); ?></td>
                <td>
                    <a href="#none" class="btn btn-primary btn-xs" onclick="common_update_pop('<?php echo $row->cm_num; ?>')">수정</a>
                    <a href="#none" class="btn btn-danger btn-xs" onclick="common_delete('<?php echo $row->cm_num; ?>')">삭제</a>
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