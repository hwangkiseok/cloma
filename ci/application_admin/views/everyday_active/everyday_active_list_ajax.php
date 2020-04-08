<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <!--<th><input type="checkbox" id="all_list_check" /></th>-->
            <th>No.</th>
            <th>이미지</th>
            <th class="<?php echo $sort_array['p_name'][1];?>" onclick="form_submit('sort_field=p_name&sort_type=<?php echo $sort_array['p_name'][0]; ?>');">상품명</th>
            <th class="<?php echo $sort_array['ed_winner_count'][1];?>" onclick="form_submit('sort_field=ed_winner_count&sort_type=<?php echo $sort_array['ed_winner_count'][0]; ?>');">당첨인원</th>
            <th class="<?php echo $sort_array['ed_startdatetime'][1];?>" onclick="form_submit('sort_field=ed_startdatetime&sort_type=<?php echo $sort_array['ed_startdatetime'][0]; ?>');">이벤트시작일시</th>
            <th class="<?php echo $sort_array['ed_enddatetime'][1];?>" onclick="form_submit('sort_field=ed_enddatetime&sort_type=<?php echo $sort_array['ed_enddatetime'][0]; ?>');">이벤트종료일시</th>
            <th class="<?php echo $sort_array['m_loginid'][1];?>" onclick="form_submit('sort_field=m_loginid&sort_type=<?php echo $sort_array['m_loginid'][0]; ?>');">참여회원(ID)</th>
            <th class="<?php echo $sort_array['eda_winner_yn'][1];?>" onclick="form_submit('sort_field=eda_winner_yn&sort_type=<?php echo $sort_array['eda_winner_yn'][0]; ?>');">당첨여부</th>
            <th class="<?php echo $sort_array['eda_regdatetime'][1];?>" onclick="form_submit('sort_field=eda_regdatetime&sort_type=<?php echo $sort_array['eda_regdatetime'][0]; ?>');">참여일시</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($everyday_active_list as $row) {
        ?>

            <tr role="row">
                <!--<td><input type="checkbox" name="num[]" value="--><?php //echo $row->pmd_division; ?><!--:--><?php //echo $row->pmd_product_num; ?><!--" /></td>-->
                <td><?php echo number_format($list_number); ?></td>
                <td><?php echo create_img_tag_from_json($row->p_rep_image, 1, 50);?></td>
                <td><a href="#none" onclick="product_detail_win('<?php echo $row->p_num; ?>')"><?php echo $row->p_name; ?></a></td>
                <td><?php echo $row->ed_winner_count; ?></td>
                <td><?php echo get_datetime_format($row->ed_startdatetime); ?></td>
                <td><?php echo get_datetime_format($row->ed_enddatetime); ?></td>
                <td><a href="#none" onclick="member_update_pop('<?php echo $row->m_num; ?>');"><?php echo $row->m_loginid; ?></a></td>
                <td><?php echo get_config_item_text($row->eda_winner_yn, "everyday_active_winner_yn") ?></td>
                <td><?php echo get_datetime_format($row->eda_regdatetime); ?></td>
                <td>
                    <?php if( $row->eda_winner_reg_yn == "Y" ) { ?>
                        <button type="button" class="btn btn-primary btn-xs" onclick="winner_detail_pop('<?php echo $row->eda_everyday_num; ?>', '<?php echo $row->eda_member_num; ?>');">배송정보</button>
                    <?php } ?>
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