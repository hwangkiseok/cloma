
<p class="alert alert-warning" style="font-size: 20px;font-weight: bold;"> 당첨자 : <?=number_format($nWinnerCnt)?> 명 / 당첨확인자수 : <?=number_format($nViewerCnt)?> 명 </p>
<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <!--<th><input type="checkbox" id="all_list_check" /></th>-->
            <th>No.</th>
            <th>이벤트제목</th>
            <th class="<?php echo $sort_array['ens_ph'][1];?>" onclick="form_submit('sort_field=ens_ph&sort_type=<?php echo $sort_array['ens_ph'][0]; ?>');">휴대폰번호</th>
            <th class="<?php echo $sort_array['ens_reg_ip'][1];?>" onclick="form_submit('sort_field=ens_ph&sort_type=<?php echo $sort_array['ens_reg_ip'][0]; ?>');">IP</th>
            <th class="<?php echo $sort_array['ens_reg_info'][1];?>" onclick="form_submit('sort_field=ens_reg_info&sort_type=<?php echo $sort_array['ens_reg_info'][0]; ?>');">브라우저</th>
            <th class="<?php echo $sort_array['ens_regdatetime'][1];?>" onclick="form_submit('sort_field=ens_regdatetime&sort_type=<?php echo $sort_array['ens_regdatetime'][0]; ?>');">참여일시</th>
            <!--<th class="<?php echo $sort_array['win_overlap'][1];?>" onclick="form_submit('sort_field=win_overlap&sort_type=<?php echo $sort_array['win_overlap'][0]; ?>');">당첨여부</th>-->
            <th>당첨여부</th>
            <th>당첨확인여부</th>

        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($event_active_list as $row) { //zsView($row);
        ?>

            <tr role="row">
                <td><?php echo number_format($list_number); ?></td>
                <td><?php echo $row->e_subject; ?></td>
                <td>
                    <?php
                    if($row->ens_member_num){
                        echo "<a class='zs-cp' onclick='member_update_pop(\"".$row->ens_member_num."\")'>".($row->ens_ph?$row->ens_ph:'[ 연락처없음 ]')."</a>";
                    }else{
                        echo $row->ens_ph;
                    }
                    ?>
                </td>
                <td><?php echo ($row->ens_reg_ip)?$row->ens_reg_ip : '-'; ?></td>
                <td><?php echo ($row->ens_reg_info)?mb_strcut($row->ens_reg_info, 0, 50, "utf-8").'...' : '-' ; ?> </td>
                <td><?php echo ($row->ens_regdatetime)?get_datetime_format($row->ens_regdatetime) : '-'; ?></td>
                <td>
                    <?if($row->win_overlap== '당첨'){?>
                        <a role="button" class="btn btn-primary btn-xs">당첨</a>
                    <?}else{?>
                        <a role="button" class="btn btn-danger btn-xs">X</a>
                    <?} ?>
                </td>
                <td>
                    <?if($row->gift_name){?>

                        <? if(strpos($row->gift_name ,'스타벅스 커피') !== false){?>
                            <a role="button" class="btn btn-danger btn-xs"><?=$row->gift_name?></a>
                        <?}else if(strpos($row->gift_name ,'육개장 사발면') !== false){?>
                            <a role="button" class="btn btn-primary btn-xs"><?=$row->gift_name?></a>
                        <?}else{?>
                            <a role="button" class="btn btn-default btn-xs"><?=$row->gift_name?></a>
                        <?} ?>

                    <?}else{?>
                        <?if($row->view_chk == '당첨확인'){?>
                            <a role="button" class="btn btn-primary btn-xs">당첨확인</a>
                        <?}else{?>
                            <a role="button" class="btn btn-danger btn-xs">당첨미확인</a>
                        <?} ?>
                    <?}?>
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