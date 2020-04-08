<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <!--<th><input type="checkbox" id="all_list_check" /></th>-->
            <th>No.</th>
            <th>이미지</th>
            <th class="<?php echo $sort_array['p_name'][1];?>" onclick="form_submit('sort_field=p_name&sort_type=<?php echo $sort_array['p_name'][0]; ?>');">상품명</th>
            <th class="<?php echo $sort_array['ed_startdatetime'][1];?>" onclick="form_submit('sort_field=ed_startdatetime&sort_type=<?php echo $sort_array['ed_startdatetime'][0]; ?>');">이벤트시작일시</th>
            <th class="<?php echo $sort_array['ed_enddatetime'][1];?>" onclick="form_submit('sort_field=ed_enddatetime&sort_type=<?php echo $sort_array['ed_enddatetime'][0]; ?>');">이벤트종료일시</th>
            <th class="<?php echo $sort_array['ed_winner_count'][1];?>" onclick="form_submit('sort_field=ed_winner_count&sort_type=<?php echo $sort_array['ed_winner_count'][0]; ?>');">당첨인원</th>
            <th class="<?php echo $sort_array['ed_usestate'][1];?>" onclick="form_submit('sort_field=ed_usestate&sort_type=<?php echo $sort_array['ed_usestate'][0]; ?>');">진행상태</th>
            <th class="<?php echo $sort_array['ed_displaystate'][1];?>" onclick="form_submit('sort_field=ed_displaystate&sort_type=<?php echo $sort_array['ed_displaystate'][0]; ?>');">노출여부</th>
            <th class="<?php echo $sort_array['au_name'][1];?>" onclick="form_submit('sort_field=au_name&sort_type=<?php echo $sort_array['au_name'][0]; ?>');">작성자</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($everyday_list as $row) {
            ?>

            <tr role="row">
                <!--<td><input type="checkbox" name="num[]" value="--><?php //echo $row->pmd_division; ?><!--:--><?php //echo $row->pmd_product_num; ?><!--" /></td>-->
                <td><?php echo number_format($list_number); ?></td>
                <td><?php echo create_img_tag_from_json($row->p_rep_image, 1, 100);?></td>
                <td><a href="#none" onclick="product_detail_win('<?php echo $row->p_num; ?>');"><?php echo $row->p_name; ?></a></td>
                <td><?php echo get_datetime_format($row->ed_startdatetime); ?></td>
                <td><?php echo get_datetime_format($row->ed_enddatetime); ?></td>
                <td><?php echo number_format($row->ed_winner_count); ?></td>
                <td>
                    <?php if( $row->ed_usestate == 'Y' ) { ?>
                        <button type="button" class="btn btn-success btn-xs" onclick="everyday_update_toggle('<?php echo $row->ed_num; ?>', 'ed_usestate');"><?php echo get_config_item_text($row->ed_usestate, 'everyday_usestate', false); ?></button>
                    <?php } else { ?>
                        <button type="button" class="btn btn-warning btn-xs" onclick="everyday_update_toggle('<?php echo $row->ed_num; ?>', 'ed_usestate');"><?php echo get_config_item_text($row->ed_usestate, 'everyday_usestate', false); ?></button>
                    <?php } ?>
                </td>
                <td>
                    <?php if( $row->ed_displaystate == 'Y' ) { ?>
                        <button type="button" class="btn btn-success btn-xs" onclick="everyday_update_toggle('<?php echo $row->ed_num; ?>', 'ed_displaystate');"><?php echo get_config_item_text($row->ed_displaystate, 'everyday_displaystate', false); ?></button>
                    <?php } else { ?>
                        <button type="button" class="btn btn-warning btn-xs" onclick="everyday_update_toggle('<?php echo $row->ed_num; ?>', 'ed_displaystate');"><?php echo get_config_item_text($row->ed_displaystate, 'everyday_displaystate', false); ?></button>
                    <?php } ?>
                </td>
                <td><?php echo $row->au_name; ?></td>
                <td>
                    <a href="#none" class="btn btn-primary btn-xs" onclick="everyday_update_pop('<?php echo $row->ed_num; ?>')">수정</a>
                    <a href="#none" class="btn btn-danger btn-xs" onclick="everyday_delete('<?php echo $row->ed_num; ?>')">삭제</a>
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