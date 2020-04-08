

<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">

<!--        <colgroup>-->
<!--            <col width="50px">-->
<!--            <col width="70px">-->
<!--            <col width="700px">-->
<!--            <col width="200px">-->
<!--            <col width="*">-->
<!--        </colgroup>-->

        <thead>
        <tr role="row" class="active">
            <th>No.</th>
            <th>적립금명</th>
            <th>발급기간</th>
            <th>알림톡</th>
            <th>수집</th>
            <th>알림톡 발송시간</th>
            <th>알림톡 발송수</th>
            <th>적용</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($coupon_info_list as $row) {
        ?>

            <tr role="row" data-seq="<?=$row['seq']?>">
                <td><?php echo number_format($list_number); ?></td>
                <td>
                    <?if($row['coupon_cnt'] < 2){?>
                        <?=$row['coupon_name']?>
                    <?}else{?>
                        <?=$row['coupon_name']?> 외 <?=$row['coupon_cnt']-1?>건
                    <?}?>
                </td>
                <td><?=view_date_format($row['start_date'])?> ~ <?=view_date_format($row['end_date'])?></td>
                <td>
                    <!--
                    W:  대기
                    P:  회원정보수집중 SELECT
                    I:  발송중  INSERT
                    RD: 발송요청완료
                    D:  발송완료
                    -->
                    <?if($row['alimtalk_flag'] == 'W'){?>
                    <button class="btn btn-success btn-xs send_alimtalk" data-flag="<?=$row['alimtalk_flag']?>" data-seq="<?=$row['seq']?>" data-code="<?=$row['coupon_code']?>">발송</button>
                    <?}else if($row['alimtalk_flag'] == 'P'){?>
                        <button class="btn btn-info btn-xs" data-flag="<?=$row['alimtalk_flag']?>" data-seq="<?=$row['seq']?>" data-code="<?=$row['coupon_code']?>">정보수집 중</button>
                        <br/><span>[발송요청 전]</span>
                    <?}else if($row['alimtalk_flag'] == 'I'){?>
                        <button class="btn btn-danger btn-xs" data-flag="<?=$row['alimtalk_flag']?>" data-seq="<?=$row['seq']?>" data-code="<?=$row['coupon_code']?>">발송 중</button>
                        <br/><span class="text-primary">[발송요청 중]</span>
                    <?}else if($row['alimtalk_flag'] == 'RD'){?>
                        <button class="btn btn-danger btn-xs" data-flag="<?=$row['alimtalk_flag']?>" data-seq="<?=$row['seq']?>" data-code="<?=$row['coupon_code']?>">발송 중</button>
                        <br/><span class="text-danger">[발송요청 완료]</span>
                    <?}else if($row['alimtalk_flag'] == 'D'){?>
                        <button class="btn btn-danger btn-xs" data-flag="<?=$row['alimtalk_flag']?>" data-seq="<?=$row['seq']?>" data-code="<?=$row['coupon_code']?>">완료</button>
                    <?}?>
                </td>
                <td><?=number_format($row['proc_cnt'])?></td>
                <td>
                    <?if(empty($row['alimtalk_send_date']) == false){?>
                        <?=view_date_format($row['alimtalk_send_date'],2)?> ~ <?=$row['alimtalk_end_date']?view_date_format($row['alimtalk_end_date'],2):'발송 중'?>

                        <?if(empty($row['alimtalk_end_date']) == true){?>
                            <?=$row['alimtalk_process_date']?'<br><p class="alert alert-danger" style="display: inline-block;margin: 0;padding: 3px 8px;">최종 발송시간 : '.view_date_format($row['alimtalk_process_date'],3).'</p>':''?>
                        <?}?>
                    <?}else{?>
                        발송 전
                    <?}?>
                </td>

                <td>
                    <?=number_format($row['alimtalk_count'])?>
                    <?if($row['scheduled_count'] > 0 ){?><br>[발송예정수 : <?=number_format($row['scheduled_count'])?>]<?}?>
                </td>

                <td>
                    <?if($row['use_flag'] == 'N'){?>
                        <button class="btn btn-danger btn-xs coupon_activate" data-flag="<?=$row['use_flag']?>" data-seq="<?=$row['seq']?>">비활성화</button>
                    <?}else{?>
                        <button class="btn btn-success btn-xs coupon_activate" data-flag="<?=$row['use_flag']?>" data-seq="<?=$row['seq']?>">활성화</button>
                    <?}?>
                </td>

                <td>
                    <button class="btn btn-primary btn-xs coupon_update" data-seq="<?=$row['seq']?>">수정</button>
                    <button class="btn btn-danger btn-xs coupon_delete" data-seq="<?=$row['seq']?>">삭제</button>
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
    $(function(){
        $('[data-toggle="tooltip"]').tooltip();
    })
</script>
