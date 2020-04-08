
<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">

        <colgroup>
            <col width="50px">
            <col width="50px">
            <col width="100px">
            <col width="100px">
            <col width="100px">
            <col width="*">
        </colgroup>

        <thead>
        <tr role="row" class="active">
            <th><input type="checkbox" id="all_list_check" /></th>
            <th>No.</th>
            <th class="<?php echo $sort_array['p_display_state'][1];?>" onclick="order_submit('p_display_state', '<?php echo $sort_array['p_display_state'][0]; ?>');">진열상태</th>
            <th class="<?php echo $sort_array['p_sale_state'][1];?>" onclick="order_submit('p_sale_state', '<?php echo $sort_array['p_sale_state'][0]; ?>');">판매상태</th>
            <th class="<?php echo $sort_array['p_stock_state'][1];?>" onclick="order_submit('p_stock_state', '<?php echo $sort_array['p_stock_state'][0]; ?>');">재고상태</th>
            <th class="<?php echo $sort_array['p_termlimit_yn'][1];?>" onclick="order_submit('p_termlimit_yn', '<?php echo $sort_array['p_termlimit_yn'][0]; ?>');">기간한정</th>
            <th class="<?php echo $sort_array['p_name'][1];?>" onclick="order_submit('p_name', '<?php echo $sort_array['p_name'][0]; ?>');">상품명</th>
            <th>이미지</th>
            <th class="<?php echo $sort_array['p_comment_count'][1];?>" onclick="order_submit('p_comment_count', '<?php echo $sort_array['p_comment_count'][0]; ?>');">댓글</th>
            <th class="<?php echo $sort_array['p_review_count'][1];?>" onclick="order_submit('p_review_count', '<?php echo $sort_array['p_review_count'][0]; ?>');">리뷰</th>
            <th class="<?php echo $sort_array['p_restock_cnt'][1];?>" onclick="order_submit('p_restock_cnt', '<?php echo $sort_array['p_restock_cnt'][0]; ?>');">재입고요청</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($restock_list as $row) {
        ?>

            <tr role="row" data-seq="<?=$row['p_num']?>">
                <td><input type="checkbox" name="p_num[]" value="<?php echo $row['p_num']; ?>" /></td>
                <td><?php echo number_format($list_number); ?></td>
                <td>
                    <?php if( $row['p_display_state'] == 'Y' ) { ?>
                        <button type="button" class="btn btn-success btn-xs"><?php echo get_config_item_text($row['p_display_state'], 'product_display_state', false); ?></button>
                    <?php } else { ?>
                        <button type="button" class="btn btn-warning btn-xs"><?php echo get_config_item_text($row['p_display_state'], 'product_display_state', false); ?></button>
                    <?php } ?>
                </td>
                <td>
                    <?php if( $row['p_sale_state'] == 'Y' ) { ?>
                        <button type="button" class="btn btn-success btn-xs"><?php echo get_config_item_text($row['p_sale_state'], 'product_sale_state', false); ?></button>
                    <?php } else { ?>
                        <button type="button" class="btn btn-warning btn-xs"><?php echo get_config_item_text($row['p_sale_state'], 'product_sale_state', false); ?></button>
                    <?php } ?>
                </td>
                <td>
                    <?php if( $row['p_stock_state'] == 'Y' ) { ?>
                        <button type="button" class="btn btn-success btn-xs"><?php echo get_config_item_text($row['p_stock_state'], 'product_stock_state', false); ?></button>
                    <?php } else { ?>
                        <button type="button" class="btn btn-warning btn-xs"><?php echo get_config_item_text($row['p_stock_state'], 'product_stock_state', false); ?></button>
                    <?php } ?>
                </td>
                <td>
                    <?php echo get_config_item_text($row['p_termlimit_yn'], 'product_termlimit_yn'); ?>
                    <?php
                    //판매시작일 ~ 판매종료일시
                    if( $row['p_termlimit_yn'] == "Y" ) {
                        echo "<br />" . substr(get_datetime_format($row['p_termlimit_datetime1']), 2, 14) . " <br />~ " . substr(get_datetime_format($row['p_termlimit_datetime2']), 2, 14);
                    }
                    ?>
                </td>
                <td>
                    <b><a href="#none" style="font-size:13px;" onclick="product_detail_win('<?php echo $row['p_num']; ?>')"><?php echo $row['p_name']; ?></a></b>
                </td>

                <td><?php echo create_img_tag_from_json($row['p_rep_image'], 1, 100);?></td>
                <td><a href="#none" onclick="new_win_open('/comment/list/?tb=product&tb_num=<?php echo $row['p_num']; ?>&view_type=simple&pop=1', 'comment_list_win', 1200, 800); "><?php echo number_format($row['p_comment_count']); ?></a></td>
                <td><a href="#none" onclick="new_win_open('/review/list/?tb=review&tb_num=<?php echo $row['p_num']; ?>&view_type=simple&pop=1', 'review_list_win', 1200, 800); "><?php echo number_format($row['p_review_count']); ?></a></td>
                <td>
                    <?php echo number_format($row['p_restock_cnt']); ?><br>
                    <button class="btn btn-info btn-xs sendRestockPush" data-cnt="<?=$row['p_restock_cnt']?>" data-seq="<?=$row['p_num']?>">재입고<br>푸시발송</button>
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