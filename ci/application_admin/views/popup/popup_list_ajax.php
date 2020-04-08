<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <!--<th><input type="checkbox" id="all_list_check" /></th>-->
            <th>No.</th>
            <th class="<?php echo $sort_array['pu_division'][1];?>" onclick="form_submit('sort_field=pu_division&sort_type=<?php echo $sort_array['pu_division'][0]; ?>');">팝업종류</th>
            <th class="<?php echo $sort_array['pu_subject'][1];?>" onclick="form_submit('sort_field=pu_subject&sort_type=<?php echo $sort_array['pu_subject'][0]; ?>');">제목</th>
            <th class="<?php echo $sort_array['pu_target_url'][1];?>" onclick="form_submit('sort_field=pu_target_url&sort_type=<?php echo $sort_array['pu_target_url'][0]; ?>');">이동URL</th>
            <th class="<?php echo $sort_array['au_name'][1];?>" onclick="form_submit('sort_field=au_name&sort_type=<?php echo $sort_array['au_name'][0]; ?>');">작성자</th>
            <th class="<?php echo $sort_array['pu_regdatetime'][1];?>" onclick="form_submit('sort_field=pu_regdatetime&sort_type=<?php echo $sort_array['pu_regdatetime'][0]; ?>');">등록일</th>
            <th class="<?php echo $sort_array['pu_usestate'][1];?>" onclick="form_submit('sort_field=pu_usestate&sort_type=<?php echo $sort_array['pu_usestate'][0]; ?>');">상태</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($popup_list as $row) {
            ?>

            <tr role="row">
                <!--<td><input type="checkbox" name="num[]" value="--><?php //echo $row->pmd_division; ?><!--:--><?php //echo $row->pmd_product_num; ?><!--" /></td>-->
                <td><?php echo number_format($list_number); ?></td>
                <td><?php echo get_config_item_text($row->pu_division, 'popup_division'); ?></td>
                <td><?php echo $row->pu_subject; ?></td>
                <td>
                    <div style="position:relative;display:block;">
                        <?php if( !empty($row->pu_target_url) ) { ?><a href="<?php echo $row->pu_target_url; ?>" class="abs_link" target="_blank">#</a><?php } ?>
                        <?php echo $row->pu_target_url; ?>
                    </div>
                </td>
                <td><?php echo $row->au_name; ?></td>
                <td><?php echo get_date_format($row->pu_regdatetime); ?></td>
                <td><?php echo get_config_item_text($row->pu_usestate, 'popup_usestate'); ?></td>
                <td>
                    <a href="#none" class="btn btn-primary btn-xs" onclick="popup_update_pop('<?php echo $row->pu_num; ?>')">수정</a>
                    <a href="#none" class="btn btn-danger btn-xs" onclick="popup_delete('<?php echo $row->pu_num; ?>')">삭제</a>
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