<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <!--<th><input type="checkbox" id="all_list_check" /></th>-->
            <th>No.</th>
            <th class="<?php echo $sort_array['pmd_order'][1];?>" onclick="form_submit('sort_field=pmd_order&sort_type=<?php echo $sort_array['pmd_order'][0]; ?>');">순서</th>
            <th class="<?php echo $sort_array['pmd_division'][1];?>" onclick="form_submit('sort_field=pmd_division&sort_type=<?php echo $sort_array['pmd_division'][0]; ?>');">MD분류</th>
            <th class="<?php echo $sort_array['p_category'][1];?>" onclick="form_submit('sort_field=p_category&sort_type=<?php echo $sort_array['p_category'][0]; ?>');">카테고리</th>
            <th class="<?php echo $sort_array['p_display_state'][1];?>" onclick="form_submit('sort_field=p_display_state&sort_type=<?php echo $sort_array['p_display_state'][0]; ?>');">진열상태</th>
            <th class="<?php echo $sort_array['p_sale_state'][1];?>" onclick="form_submit('sort_field=p_sale_state&sort_type=<?php echo $sort_array['p_sale_state'][0]; ?>');">판매상태</th>
            <th class="<?php echo $sort_array['p_termlimit_yn'][1];?>" onclick="form_submit('sort_field=p_termlimit_yn&sort_type=<?php echo $sort_array['p_termlimit_yn'][0]; ?>');">기간한정</th>
            <th class="<?php echo $sort_array['p_name'][1];?>" onclick="form_submit('sort_field=p_name&sort_type=<?php echo $sort_array['p_name'][0]; ?>');">상품명</th>
            <th>이미지</th>
            <th class="<?php echo $sort_array['p_original_price'][1];?>" onclick="form_submit('sort_field=p_original_price&sort_type=<?php echo $sort_array['p_original_price'][0]; ?>');">기존가</th>
            <th class="<?php echo $sort_array['p_sale_price'][1];?>" onclick="form_submit('sort_field=p_sale_price&sort_type=<?php echo $sort_array['p_sale_price'][0]; ?>');">판매가</th>
            <th class="<?php echo $sort_array['p_discount_rate'][1];?>" onclick="form_submit('sort_field=p_discount_rate&sort_type=<?php echo $sort_array['p_discount_rate'][0]; ?>');">할인율</th>
            <th class="<?php echo $sort_array['p_wish_count'][1];?>" onclick="form_submit('sort_field=p_wish_count&sort_type=<?php echo $sort_array['p_wish_count'][0]; ?>');">관심</th>
            <th class="<?php echo $sort_array['p_share_count'][1];?>" onclick="form_submit('sort_field=p_share_count&sort_type=<?php echo $sort_array['p_share_count'][0]; ?>');">공유</th>
            <th class="<?php echo $sort_array['p_view_count'][1];?>" onclick="form_submit('sort_field=p_view_count&sort_type=<?php echo $sort_array['p_view_count'][0]; ?>');">진입</th>
            <!--<th class="<?php echo $sort_array['p_click_count'][1];?>" onclick="form_submit('sort_field=p_click_count&sort_type=<?php echo $sort_array['p_click_count'][0]; ?>');">구매클릭</th>-->
            <th>삭제</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($md_list as $row) {
        ?>

            <tr role="row">
                <!--<td><input type="checkbox" name="num[]" value="--><?php //echo $row->pmd_division; ?><!--:--><?php //echo $row->pmd_product_num; ?><!--" /></td>-->
                <td><?php echo number_format($list_number); ?></td>
                <td>
                    <div class="form-group form-group-sm">
                        <input type="text" class="form-control" style="width:50px;" data-num="<?php echo $row['pmd_division']; ?>:<?php echo $row['pmd_product_num']; ?>" name="pmd_order[]" value="<?php echo $row['pmd_order']; ?>" />
                    </div>
                </td>
                <td><?php echo get_config_item_text($row['pmd_division'], 'product_md_division'); ?></td>
                <td>
                    <!--<?php echo get_config_item_text($row['p_category'], 'product_category'); ?>-->
                    <?php echo implode(" &gt; ", array_filter(array($row['p_cate1'], $row['p_cate2'], $row['p_cate3']))); ?>
                </td>
                <td><?php echo get_config_item_text($row['p_display_state'], 'product_display_state'); ?></td>
                <td><?php echo get_config_item_text($row['p_sale_state'], 'product_sale_state'); ?></td>
                <td><?php echo get_config_item_text($row['p_termlimit_yn'], 'product_termlimit_yn'); ?></td>
                <td><a href="#none" onclick="product_detail_win('<?php echo $row['p_num']; ?>')"><?php echo $row['p_name']; ?></a></td>
                <td><?php echo create_img_tag_from_json($row['p_rep_image'], 1, 100);?></td>
                <td><?php echo number_format($row['p_original_price']); ?></td>
                <td><?php echo number_format($row['p_sale_price']); ?></td>
                <td><?php echo $row['p_discount_rate']; ?> %</td>
                <td><?php echo number_format($row['p_wish_count']); ?></td>
                <td><?php echo number_format($row['p_share_count']); ?></td>
                <td><?php echo number_format($row['p_view_count']); ?></td>
                <!--<td><?php echo number_format($row['p_click_count']); ?></td>-->
                <!--<td><a href="--><?php //echo $this->page_link->delete_proc; ?><!--/?p_num=--><?php //echo $row->p_num; ?><!--&--><?php //echo $GV; ?><!--" class="btn btn-danger btn-xs">삭제</a></td>-->
                <td><a href="#none" class="btn btn-danger btn-xs" onclick="md_delete('<?php echo $row['pmd_division']; ?>', '<?php echo $row['pmd_product_num']; ?>')">삭제</a></td>
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
<script>
    $(function(){
        //갯수 업데이트
        $('#md_count_total').text(number_format(<?php echo $md_count_array['total']['cnt']; ?>));
        <?php foreach( $this->config->item('product_md_division') as $key => $item ) { ?>
        $('#md_count_<?php echo $key; ?>').text(number_format(<?php echo $md_count_array[$key]['cnt']; ?>));
        <?php } ?>
    });
</script>