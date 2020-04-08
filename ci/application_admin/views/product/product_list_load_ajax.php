<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <!--<th><input type="checkbox" id="all_list_check" /></th>-->
            <th>No.</th>
            <th class="<?php echo $sort_array['p_category'][1];?>" onclick="order_submit('p_category', '<?php echo $sort_array['p_category'][0]; ?>');">카테고리</th>
            <th class="<?php echo $sort_array['p_display_state'][1];?>" onclick="order_submit('p_display_state', '<?php echo $sort_array['p_display_state'][0]; ?>');">진열상태</th>
            <th class="<?php echo $sort_array['p_sale_state'][1];?>" onclick="order_submit('p_sale_state', '<?php echo $sort_array['p_sale_state'][0]; ?>');">판매상태</th>
            <th class="<?php echo $sort_array['p_termlimit_yn'][1];?>" onclick="order_submit('p_termlimit_yn', '<?php echo $sort_array['p_termlimit_yn'][0]; ?>');">기간한정</th>
            <th class="<?php echo $sort_array['p_name'][1];?>" onclick="order_submit('p_name', '<?php echo $sort_array['p_name'][0]; ?>');">상품명</th>
            <th>이미지</th>
            <th class="<?php echo $sort_array['p_original_price'][1];?>" onclick="order_submit('p_original_price', '<?php echo $sort_array['p_original_price'][0]; ?>');">기존가</th>
            <th class="<?php echo $sort_array['p_sale_price'][1];?>" onclick="order_submit('p_sale_price', '<?php echo $sort_array['p_sale_price'][0]; ?>');">판매가</th>
            <th class="<?php echo $sort_array['p_discount_rate'][1];?>" onclick="order_submit('p_discount_rate', '<?php echo $sort_array['p_discount_rate'][0]; ?>');">할인율</th>
            <th class="<?php echo $sort_array['p_wish_count'][1];?>" onclick="order_submit('p_wish_count', '<?php echo $sort_array['p_wish_count'][0]; ?>');">관심</th>
            <th class="<?php echo $sort_array['p_share_count'][1];?>" onclick="order_submit('p_share_count', '<?php echo $sort_array['p_share_count'][0]; ?>');">공유</th>
            <th class="<?php echo $sort_array['p_view_count'][1];?>" onclick="order_submit('p_view_count', '<?php echo $sort_array['p_view_count'][0]; ?>');">진입</th>
            <th class="<?php echo $sort_array['p_click_count'][1];?>" onclick="order_submit('p_click_count', '<?php echo $sort_array['p_click_count'][0]; ?>');">구매클릭</th>
            <th>선택</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($product_list as $row) {
        ?>

            <tr role="row">
                <!--<td><input type="checkbox" name="p_num[]" value="--><?php //echo $row->p_num; ?><!--" /></td>-->
                <td><?php echo number_format($list_number); ?></td>
                <td><?php echo get_config_item_text($row->p_category, 'product_category'); ?></td>
                <td><?php echo get_config_item_text($row->p_display_state, 'product_display_state'); ?></td>
                <td><?php echo get_config_item_text($row->p_sale_state, 'product_sale_state'); ?></td>
                <td><?php echo get_config_item_text($row->p_termlimit_yn, 'product_termlimit_yn'); ?></td>
                <td>
                    <b><a href="#none" onclick="product_detail_win('<?php echo $row->p_num; ?>')"><?php echo $row->p_name; ?></a></b>
                    <br />
                    <a href="<?php echo $row->p_short_url; ?>" target="_blank" style="color:#666;"><?php echo $row->p_short_url; ?></a>
                </td>
                <td><?php echo create_img_tag_from_json($row->p_rep_image, 1, 100, "", "", $ext_site_url);?></td>
                <td><?php echo number_format($row->p_original_price); ?></td>
                <td><?php echo number_format($row->p_sale_price); ?></td>
                <td><?php echo $row->p_discount_rate; ?> %</td>
                <td><?php echo number_format($row->p_wish_count); ?>(<?php echo number_format($row->p_wish_count_user); ?>)</td>
                <td><?php echo number_format($row->p_share_count); ?>(<?php echo number_format($row->p_share_count_user); ?>)</td>
                <td><?php echo number_format($row->p_view_count); ?></td>
                <td><?php echo number_format($row->p_click_count); ?></td>
                <td><a href="#none" class="btn btn-primary btn-xs" onclick="product_select('<?php echo $row->p_num; ?>');">선택</a></td>
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
    /**
     * 상품 선택
     * @param p_num
     */
    function product_select(p_num, ck_name) {
        if( empty(p_num) ) {
            alert('상품을 선택하세요.');
            return false;
        }
        if( empty(ck_name) ) {
            ck_name = "Y";
        }

        if( ck_name == "Y" ) {
            if( !confirm('선택하신 상품을 등록하시겠습니까?') ) {
                return false;
            }
        }

        loadingScreen.show();
        //loadingBar.show('#pop_product_list');
        //Pace.restart();

        $.ajax({
            url : '/product/load_insert',
            data : {
                p_num : p_num,
                db : '<?php echo $req['db']; ?>',
                ck_name : ck_name
            },
            type : 'post',
            dataType : 'json',
            success : function (result) {
                if( result.message_type == 'alert' && !empty(result.message) ) {
                    alert(result.message);
                }

                if( result.status == '<?php echo get_status_code("success"); ?>' ) {
                    location.reload();
                }
                else if( result.status == '<?php echo get_status_code("overlap"); ?>' ) {
                    if( confirm('동일한 상품명으로 이미 등록된 상품이 있습니다.\n그래도 등록하시겠습니까?') ) {
                        product_select(p_num, 'N');
                    }
                    else {
                        return false;
                    }
                }

            },
            error : function () {
                alert('<?php echo lang("site_error_request"); ?>');
            },
            complete : function () {
                //loadingBar.hide();
                loadingScreen.hide();
                //Pace.stop();
            }
        });
    }//end of product_select()

    //document.ready
    $(function () {
        $('#total_count').text('<?php echo number_format($list_count); ?>');
    });//end of document.ready()
</script>