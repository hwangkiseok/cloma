

<div class="table-responsive">

    <p class="alert alert-warning" style="margin-bottom: 3px;display: inline-block;min-width: 700px;"><?=$stat_data['aPrevData']['set_date']?> 집계&nbsp;&nbsp;|&nbsp;&nbsp;전체 : <?=$stat_data['aPrevData']['tot_req_cnt']?number_format($stat_data['aPrevData']['tot_req_cnt']):'0'?>&nbsp;&nbsp;|&nbsp;&nbsp;신규 신청자 : <?=$stat_data['aPrevData']['new_req_cnt']?number_format($stat_data['aPrevData']['new_req_cnt']):'0'?>&nbsp;&nbsp;|&nbsp;&nbsp;재 신청자 : <?=$stat_data['aPrevData']['re_req_cnt']?number_format($stat_data['aPrevData']['re_req_cnt']):'0'?></p><br>
    <p class="alert alert-info" style="display: inline-block;min-width: 700px;"><?=$stat_data['aCurrData']['set_date']?> 집계&nbsp;&nbsp;|&nbsp;&nbsp;전체 : <?=$stat_data['aCurrData']['tot_req_cnt']?number_format($stat_data['aCurrData']['tot_req_cnt']):'0'?>&nbsp;&nbsp;|&nbsp;&nbsp;신규 신청자 : <?=$stat_data['aCurrData']['new_req_cnt']?number_format($stat_data['aCurrData']['new_req_cnt']):'0'?>&nbsp;&nbsp;|&nbsp;&nbsp;재 신청자 : <?=$stat_data['aCurrData']['re_req_cnt']?number_format($stat_data['aCurrData']['re_req_cnt']):'0'?>   </p>

    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <!--<th><input type="checkbox" id="all_list_check" /></th>-->
            <th>No.</th>
            <th>이름</th>
            <th>연락처</th>
            <th>출석 수</th>
            <th>신청여부</th>
            <th>등록일</th>
            <th>수정일</th>
            <th>관리</th>

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
                <td>
                    <a onclick="member_update_pop('<?php echo $row->m_num; ?>');" class="zs-cp">
                        <?if($row->m_nickname){?>
                            <?=$row->m_nickname?>
                        <?}else{?>
                            [닉네임없음]
                        <?}?>
                    </a>
                </td>
                <td><?=$row->m_authno?></td>
                <td><?=$row->attend_cnt?></td>
                <td>
                    <?if($row->use_flag == 'Y' ){?>
                        <button class="btn btn-success btn-xs">자동출석 중</button>
                    <?}else{?>
                        <button class="btn btn-danger btn-xs">자동출석 안함</button>
                        <br><span style="display: inline-block;margin-top: 3px;" class="text-danger">취소일 : <?=view_date_format($row->cancel_date,2)?></span>
                    <?}?>
                </td>

                <td><?=view_date_format($row->reg_date,2)?></td>
                <td><?=view_date_format($row->mod_date,2)?></td>
                <td><button class="btn btn-info btn-xs viewDetail" data-seq="<?=$row->seq?>">상세보기</button> </td>

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

    });//end of document.ready


    $(document).on('click','.viewDetail',function(){
        var seq = $(this).data('seq');
        var container = $('<div>');
        $(container).load('/event_active/auto_attend_view/?seq=' + seq);
        modalPop.createPop('자동출석 상세', container);
        modalPop.createCloseButton('취소', 'btn btn-default btn-sm');
        modalPop.show();
    });


</script>