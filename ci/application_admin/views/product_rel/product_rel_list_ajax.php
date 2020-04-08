

<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <th>No.</th>
            <th>상품명</th>
            <th>이미지</th>
            <th>연관상품</th>
            <th class="<?php echo $sort_array['able_cnt'][1];?>" onclick="form_submit('sort_field=able_cnt&sort_type=<?php echo $sort_array['able_cnt'][0]; ?>');">연관상품정보</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>
        <? $list_number = $list_count - ($list_per_page * ($page-1)); ?>
        <? foreach ($product_rel_list as $row) {?>
            <tr>
                <td><?php echo number_format($list_number); ?></td>
                <td>
                    <?=$row->p_name?>
                    <br>
                    <span class="badge badge-light"><?=get_config_item_text($row->p_display_state, "product_display_state");?></span>
                    <span class="badge badge-light"><?=get_config_item_text($row->p_sale_state, "product_sale_state");?></span>
                    <span class="badge badge-light"><?=get_config_item_text($row->p_stock_state, "product_stock_state");?></span>
                </td>
                <td><?php echo create_img_tag_from_json($row->p_rep_image, 1, 100);?></td>
                <td>
                    <?if($row->able_cnt > 0){?>
                        <button class="btn btn-primary btn-xs">있음</button>
                    <?}else{?>
                        <button class="btn btn-gray btn-xs">없음</button>
                    <?}?>
                </td>

                <td>
                    <?if($row->able_cnt > 0){?>
                        <button class="btn btn-gray btn-xs viewRelProductDetail" data-seq="<?=$row->p_num?>">판매:<?=$row->rel_list['able_cnt']?> / 품절:<?=$row->rel_list['disable_cnt']?></button>
                    <?}else{?>
                        -
                    <?}?>
                </td>
                <td><a role="button" href="/Product_rel/update/?p_num=<?=$row->p_num?>&<?=$GV?>" class="btn btn-primary btn-xs">관리</a></td>


            </tr>
        <? $list_number--; } ?>

        </tbody>
    </table>
</div>

<div class="row text-center">
    <?php echo $pagination; ?>
</div>