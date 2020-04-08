<style>
    .url_content_wrap { width:100%; padding:0; text-align:right; list-style:none; }
    .url_content_wrap li { margin-top:5px; }
    .url_content_wrap .btn { font-size:11px; }
</style>

<button id="dummyClipboard" data-clipboard-text="" style="display: none;"></button>

<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <th>No.</th>
            <th class="<?php echo $sort_array['p_name'][1];?>" onclick="order_submit('p_name', '<?php echo $sort_array['p_name'][0]; ?>');">상품명</th>
            <th>이미지</th>
            <th class="<?php echo $sort_array['ksp_title'][1];?>" onclick="order_submit('ksp_title', '<?php echo $sort_array['ksp_title'][0]; ?>');">제목</th>
            <th class="<?php echo $sort_array['ksp_content'][1];?>" onclick="order_submit('ksp_content', '<?php echo $sort_array['ksp_content'][0]; ?>');">내용</th>
            <th class="<?php echo $sort_array['ksp_button_name'][1];?>" onclick="order_submit('ksp_button_name', '<?php echo $sort_array['ksp_button_name'][0]; ?>');">버튼명</th>
            <th>URL</th>
            <th class="<?php echo $sort_array['ksp_display_state'][1];?>" onclick="order_submit('ksp_display_state', '<?php echo $sort_array['ksp_display_state'][0]; ?>');">노출</th>
            <th class="<?php echo $sort_array['ksp_click_count'][1];?>" onclick="order_submit('ksp_click_count', '<?php echo $sort_array['ksp_click_count'][0]; ?>');">클릭수</th>
            <th class="<?php echo $sort_array['ksp_regdatetime'][1];?>" onclick="order_submit('ksp_regdatetime', '<?php echo $sort_array['ksp_regdatetime'][0]; ?>');">등록일시</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($kstory_product_list as $row) {
        ?>

            <tr role="row">
                <td><?php echo number_format($list_number); ?><br />(<?php echo $row->ksp_num; ?>)</td>
                <td>
                    <?=$row->p_name;?><br />
                    <span class="badge badge-light"><?=get_config_item_text($row->p_display_state, "product_display_state");?></span>
                    <span class="badge badge-light"><?=get_config_item_text($row->p_sale_state, "product_sale_state");?></span>
                    <span class="badge badge-light"><?=get_config_item_text($row->p_stock_state, "product_stock_state");?></span>
                </td>
                <td><?=create_img_tag($row->p_banner_image, "", "100");?></td>
                <td><?=$row->ksp_title;?></td>
                <td style="text-align:left;"><?=nl2br($row->ksp_content);?></td>
                <td><?=$row->ksp_button_name;?></td>
                <td><a href="#none" class="btn btn-default btn-xs zclipCopy" onclick="get_kstory_product_shorten_url('<?=$row->ksp_num;?>');">URL복사</a></td>
                <td>
                    <?php if( $row->ksp_display_state == 'Y' ) { ?>
                        <button type="button" class="btn btn-success btn-xs" onclick="kstory_product_update_toggle('<?php echo $row->ksp_num; ?>', 'ksp_display_state');"><?php echo get_config_item_text($row->ksp_display_state, 'kstory_product_display_state', false); ?></button>
                    <?php } else { ?>
                        <button type="button" class="btn btn-warning btn-xs" onclick="kstory_product_update_toggle('<?php echo $row->ksp_num; ?>', 'ksp_display_state');"><?php echo get_config_item_text($row->ksp_display_state, 'kstory_product_display_state', false); ?></button>
                    <?php } ?>
                </td>
                <td>
                    <span class="badge badge-info" title="오늘 클릭수"><?=number_format($row->ksp_click_count);?></span><br>
                    <span class="badge badge-dark mgt3" title="어제 클릭수"><?=number_format($row->ksp_click_count_d1);?></span><br>
                    <span class="badge badge-dark mgt3" title="그저께 클릭수"><?=number_format($row->ksp_click_count_d2);?></span>
                </td>
                <td><?=get_datetime_format($row->ksp_regdatetime);?></td>
                <td>
                    <a href="#none" class="btn btn-primary btn-xs" onclick="kstory_product_update_pop('<?=$row->ksp_num;?>')">수정</a><br />
                    <a href="#none" class="btn btn-danger btn-xs mgt5" onclick="kstory_product_delete('<?=$row->ksp_num;?>');">삭제</a>
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