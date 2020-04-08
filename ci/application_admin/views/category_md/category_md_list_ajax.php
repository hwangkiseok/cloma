<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <colgroup>
            <col style="width:80px;" />
            <col style="width:80px;" />
            <col span="6" />
            <col style="width:150px;" />
            <col style="width:100px;" />
        </colgroup>
        <thead>
        <tr role="row" class="active">
            <!--<th><input type="checkbox" id="all_list_check" /></th>-->
            <th>No.</th>
            <th class="<?php echo $sort_array['cmd_order'][1];?>" onclick="form_submit('sort_field=cmd_order&sort_type=<?php echo $sort_array['cmd_order'][0]; ?>');">순서</th>
            <!--
            <th class="<?php echo $sort_array['cmd_division'][1];?>" onclick="form_submit('sort_field=cmd_division&sort_type=<?php echo $sort_array['cmd_division'][0]; ?>');">구분</th>
            <th>리스트 이미지</th>
            <th>아이콘 이미지<br>(메인상단)</th>
            <th>아이콘 이미지<br>(메인하단/맞춤쇼핑)</th>
            -->
            <th class="<?php echo $sort_array['cmd_name'][1];?>" onclick="form_submit('sort_field=cmd_name&sort_type=<?php echo $sort_array['cmd_name'][0]; ?>');">카테고리명</th>
            <th class="<?php echo $sort_array['cmd_product_cate'][1];?>" onclick="form_submit('sort_field=cmd_product_cate&sort_type=<?php echo $sort_array['cmd_product_cate'][0]; ?>');">상품카테고리</th>
            <th class="<?php echo $sort_array['cmd_state'][1];?>" onclick="form_submit('sort_field=cmd_state&sort_type=<?php echo $sort_array['cmd_state'][0]; ?>');">활성</th>
            <th class="<?php echo $sort_array['cmd_regdatetime'][1];?>" onclick="form_submit('sort_field=p_name&cmd_regdatetime=<?php echo $sort_array['cmd_regdatetime'][0]; ?>');">등록일시</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($md_list as $row) {
        ?>

            <tr role="row">
                <!--<td><input type="checkbox" name="num[]" value="--><?php //echo $row->cmd_division; ?><!--:--><?php //echo $row->cmd_product_num; ?><!--" /></td>-->
                <td><?php echo number_format($list_number); ?><br />(<?=$row->cmd_num;?>)</td>
                <td>
                    <div class="form-group form-group-sm mg0">
                        <input type="text" class="form-control" data-num="<?php echo $row->cmd_num; ?>" name="cmd_order[]" value="<?php echo $row->cmd_order; ?>" />
                    </div>
                </td>
                <!--
                <td><?php echo get_config_item_text($row->cmd_division, 'category_md_division'); ?></td>
                <td><?php echo create_img_tag($row->cmd_image, 0, 100); ?></td>
                <td><?php echo create_img_tag($row->cmd_icon, 0, 50); ?></td>
                <td><?php echo create_img_tag($row->cmd_icon2, 0, 50); ?></td>
                -->
                <td><?php echo $row->cmd_name; ?></td>
                <td><?php echo $row->cmd_product_cate; ?></td>
                <td>
                    <?php if( $row->cmd_state == 'Y' ) { ?>
                        <button type="button" class="btn btn-success btn-xs" onclick="category_md_update_toggle('<?php echo $row->cmd_num; ?>', 'cmd_state');"><?php echo get_config_item_text($row->cmd_state, 'category_md_state', false); ?></button>
                    <?php } else { ?>
                        <button type="button" class="btn btn-warning btn-xs" onclick="category_md_update_toggle('<?php echo $row->cmd_num; ?>', 'cmd_state');"><?php echo get_config_item_text($row->cmd_state, 'category_md_state', false); ?></button>
                    <?php } ?>

                </td>
                <td><?php echo get_datetime_format($row->cmd_regdatetime); ?></td>
                <td>
                    <a href="#none" class="btn btn-primary btn-xs" onclick="category_md_update_pop('<?php echo $row->cmd_num; ?>')">수정</a>
                    <a href="#none" class="btn btn-danger btn-xs" onclick="category_md_delete('<?php echo $row->cmd_num; ?>')">삭제</a>
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