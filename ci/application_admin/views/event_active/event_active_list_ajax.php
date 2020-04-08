<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <!--<th><input type="checkbox" id="all_list_check" /></th>-->
            <th>No.</th>
            <th class="<?php echo $sort_array['e_division'][1];?>" onclick="form_submit('sort_field=e_division&sort_type=<?php echo $sort_array['e_division'][0]; ?>');">이벤트종류</th>

            <?php if( $req['div'] != 1 ) { ?>
            <th class="<?php echo $sort_array['e_subject'][1];?>" onclick="form_submit('sort_field=e_subject&sort_type=<?php echo $sort_array['e_subject'][0]; ?>');">이벤트제목</th>
            <th class="<?php echo $sort_array['e_termlimit_datetime1'][1];?>" onclick="form_submit('sort_field=e_termlimit_datetime1&sort_type=<?php echo $sort_array['e_termlimit_datetime1'][0]; ?>');">기간</th>
            <?php } ?>

            <th class="<?php echo $sort_array['m_loginid'][1];?>" onclick="form_submit('sort_field=m_loginid&sort_type=<?php echo $sort_array['m_loginid'][0]; ?>');">참여회원(ID)</th>
            <th class="<?php echo $sort_array['ea_month_count'][1];?>" onclick="form_submit('sort_field=ea_month_count&sort_type=<?php echo $sort_array['ea_month_count'][0]; ?>');">월총출석수</th>
            <th class="<?php echo $sort_array['ea_accrue_count'][1];?>" onclick="form_submit('sort_field=ea_accrue_count&sort_type=<?php echo $sort_array['ea_accrue_count'][0]; ?>');">월연속출석수</th>
            <th class="<?php echo $sort_array['ea_regdatetime'][1];?>" onclick="form_submit('sort_field=ea_regdatetime&sort_type=<?php echo $sort_array['ea_regdatetime'][0]; ?>');">참여일시</th>
            <?php if( !empty($req['ew_type']) ) { ?>
            <th class="<?php echo $sort_array['ew_type'][1];?>" onclick="form_submit('sort_field=ew_type&sort_type=<?php echo $sort_array['ew_type'][0]; ?>');">달성종류</th>
            <th class="<?php echo $sort_array['ew_contact'][1];?>" onclick="form_submit('sort_field=ew_contact&sort_type=<?php echo $sort_array['ew_contact'][0]; ?>');">연락처</th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($event_active_list as $row) {
        ?>

            <tr role="row">
                <!--<td><input type="checkbox" name="num[]" value="--><?php //echo $row->pmd_division; ?><!--:--><?php //echo $row->pmd_product_num; ?><!--" /></td>-->
                <td><?php echo number_format($list_number); ?></td>
                <td><?php echo get_config_item_text($row->e_division, 'event_division'); ?></td>

                <?php if( $req['div'] != 1 ) { ?>
                <td><?php echo $row->e_subject; ?></td>
                <td>
                    <?php
                    if($row->e_termlimit_yn == 'Y') {
                        echo get_date_format($row->e_termlimit_datetime1) . " ~ " . get_date_format($row->e_termlimit_datetime2);
                    }
                    else {
                        echo get_config_item_text($row->e_termlimit_yn, 'event_termlimit_yn');
                    }
                    ?>
                </td>
                <?php } ?>

                <td><a href="#none" onclick="member_update_pop('<?php echo $row->m_num; ?>');"><?php echo $row->m_loginid; ?></a></td>
                <td><?php echo number_format($row->ea_month_count); ?></td>
                <td><?php echo number_format($row->ea_accrue_count); ?></td>
                <td><?php echo get_datetime_format($row->ea_regdatetime); ?></td>
                <?php if( !empty($req['ew_type']) ) { ?>
                <td><?php echo get_event_winner_type_name($row->ew_type); ?></td>
                <td><?php echo $row->ew_contact; ?></td>
                <?php } ?>
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
    //document.ready
    $(function(){
        $.each($('#kfd option'), function(index, item){
            if( $(item).val() == "ew_contact" ) {
                $(item).hide();
            }
        });

        <?php if( !empty($req['ew_type']) ) { ?>
        $('#kfd option[value="ew_contact"]').show();
        <?php } ?>
    });//end of document.ready
</script>