<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <!--<th><input type="checkbox" id="all_list_check" onclick="all_check(this);" /></th>-->
            <th>No.</th>
            <th class="<?php echo $sort_array['bn_order'][1];?>" onclick="form_submit('sort_field=bn_order&sort_type=<?php echo $sort_array['bn_order'][0]; ?>');">순서</th>
            <th class="<?php echo $sort_array['bn_division'][1];?>" onclick="form_submit('sort_field=bn_division&sort_type=<?php echo $sort_array['bn_division'][0]; ?>');">배너종류</th>
            <th>이미지</th>
            <th class="<?php echo $sort_array['bn_regdatetime'][1];?>" onclick="form_submit('sort_field=bn_regdatetime&sort_type=<?php echo $sort_array['bn_regdatetime'][0]; ?>');">등록일</th>
            <th class="<?php echo $sort_array['bn_subject'][1];?>" onclick="form_submit('sort_field=bn_subject&sort_type=<?php echo $sort_array['bn_subject'][0]; ?>');">제목</th>
            <th class="<?php echo $sort_array['bn_target_url'][1];?>" onclick="form_submit('sort_field=bn_target_url&sort_type=<?php echo $sort_array['bn_target_url'][0]; ?>');">이동URL</th>
            <th class="<?php echo $sort_array['au_name'][1];?>" onclick="form_submit('sort_field=au_name&sort_type=<?php echo $sort_array['au_name'][0]; ?>');">작성자</th>
            <th class="<?php echo $sort_array['bn_usestate'][1];?>" onclick="form_submit('sort_field=bn_usestate&sort_type=<?php echo $sort_array['bn_usestate'][0]; ?>');">상태</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($banner_list as $row) {
            ?>

            <tr role="row">
                <!--<td><input type="checkbox" name="bn_num[]" value="--><?php //echo $row->bn_num; ?><!--" /></td>-->
                <td><?php echo number_format($list_number); ?></td>
                <td>
                    <input type="text" class="form-control" style="width:50px;" data-num="<?php echo $row->bn_num; ?>" name="bn_order[]" value="<?php echo $row->bn_order; ?>" />
                </td>
                <td><?php echo get_config_item_text($row->bn_division, 'banner_division'); ?></td>
                <td><?php echo create_img_tag_from_json($row->bn_image, 1, 100); ?></td>
                <td><?php echo get_date_format($row->bn_regdatetime); ?></td>
                <td><?php echo $row->bn_subject; ?></td>
                <td><?php echo $row->bn_target_url; ?></td>
                <td><?php echo $row->au_name; ?></td>
                <td><?php echo get_config_item_text($row->bn_usestate, 'banner_usestate'); ?></td>
                <td>
                    <a href="#none" class="btn btn-primary btn-xs" onclick="banner_update_pop('<?php echo $row->bn_num; ?>')">수정</a>
                    <a href="#none" class="btn btn-danger btn-xs" onclick="banner_delete('<?php echo $row->bn_num; ?>')">삭제</a>
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