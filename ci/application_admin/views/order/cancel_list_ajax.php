<? //zsView($cancel_list); ?>

<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <th style="width:60px;">No.</th>
            <th style="width:120px;">주문번호<br>(장바구니번호)</th>
            <th style="width:120px;">회원정보</th>
            <th style="width:160px;">주문정보</th>
            <th style="">상품명</th>
            <th style="width:120px;">결제수단</th>
            <th style="width:100px;">주문상태</th>
            <th style="width:100px;">취소상태</th>
            <th style="width:100px;">취소사유</th>
            <th style="width:140px;">신청일</th>
            <th style="width:140px;">처리여부</th>
            <th style="width:100px;">상세</th>
            <th style="width:100px;">회수택배</th>
        </tr>
        </thead>

        <tbody>

        <?php $list_number = $list_count['cnt'] - ($list_per_page * ($page-1)); ?>
        <?php foreach($cancel_list as $key => $row) { //zsView($row); ?>

            <tr role="row">
                <td><?php echo number_format($list_number); ?></td>
                <td>
                    <?=$row['trade_no']?>
                    <?if(empty($row['m_trade_no']) == false){?><br>(<?=$row['m_trade_no']?>)<?}?>
                </td>
                <td><a href="#none" onclick="member_update_pop('<?php echo $row['m_num']; ?>');"><?php echo $row['m_nickname']; ?></a></td>

                <td>
                    <?=$row['buyer_name']?> / <?=ph_slice($row['buyer_hhp'])?><br>
                </td>

                <td><?=$row['item_name']?></td>
                <td><?=$this->config->item($row['payway_cd'],'form_payway_cd')?></td>
                <td><button class="btn btn-info btn-xs"><?=$this->config->item($row['status_cd'],'form_status_cd')?></button></td>
                <td><button class="btn btn-danger btn-xs"><?=$this->config->item($row['after_status_cd'],'after_form_status_cd')?></button></td>
                <td>
                    <?if($row['after_status_cd'] == '66' || $row['after_status_cd'] == '166'){
                        echo $this->config->item($row['cancel_gubun'],'order_cancel_gubun');
                    }else if($row['after_status_cd'] == '67' || $row['after_status_cd'] == '167'){
                        echo $this->config->item($row['cancel_gubun'],'order_exchange_gubun');
                    }else if($row['after_status_cd'] == '68' || $row['after_status_cd'] == '168'){
                        echo $this->config->item($row['cancel_gubun'],'order_refund_gubun');
                    }else{
                        echo $row['cancel_gubun'];
                    }?>
                </td>
                <td><?=view_date_format($row['reg_date'],3)?></td>
                <td>
                    <?if( $row['proc_flag'] == 'N'){?>
                        <button class="btn btn-warning btn-xs" onclick="proc_flag('<?=$row['seq']?>','Y');">처리 전</button>
                    <?}else{?>
                        <button class="btn btn-success btn-xs" onclick="proc_flag('<?=$row['seq']?>','N');">처리완료</button>
                        <br><em><?=view_date_format($row['proc_date'],3)?></em>
                    <?}?>
                </td>
                <td> <button class="btn btn-info btn-xs popDetail" data-seq="<?=$row['seq']?>">자세히보기</button> </td>

                <td>
                    <?if($row['after_status_cd'] == '67' || $row['after_status_cd'] == '68'){ // 교환 / 반품?>
                        <button class="btn btn-warning btn-xs popExchangInfo" data-seq="<?=$row['seq']?>">주소확인</button>
                    <?}else{?>
                        -
                    <?}?>
                </td>

            </tr>

            <?php $list_number--; ?>
        <?php } //end of foreach() ?>

        </tbody>
    </table>
</div>


<div class="row text-center">
    <?php echo $pagination; ?>
</div>
