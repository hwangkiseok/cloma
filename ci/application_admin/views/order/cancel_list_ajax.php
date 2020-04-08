<? //zsView($cancel_list); ?>

<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <th style="width:40px;">No.</th>
            <th style="width:120px;">주문번호</th>
            <th style="width:120px;">회원정보</th>
            <th style="width:120px;">상품명</th>
            <th style="width:120px;">주문상태</th>
            <th style="width:120px;">취소상태</th>
            <th style="width:120px;">취소사유</th>
            <th style="width:120px;">신청일</th>
            <th style="width:120px;">처리여부</th>
            <th style="width:120px;">상세</th>
        </tr>
        </thead>

        <tbody>

        <?php $list_number = $list_count['cnt'] - ($list_per_page * ($page-1)); ?>
        <?php foreach($cancel_list as $key => $row) { //zsView($row); ?>

            <tr role="row">
                <td><?php echo number_format($list_number); ?></td>
                <td><?=$row['trade_no']?></td>
                <td><a href="#none" onclick="member_update_pop('<?php echo $row['m_num']; ?>');"><?php echo $row['m_nickname']; ?></a></td>
                <td><?=$row['item_name']?></td>
                <td><button class="btn btn-info btn-xs"><?=$this->config->item($row['status_cd'],'form_status_cd')?></button></td>
                <td><button class="btn btn-danger btn-xs"><?=$this->config->item($row['after_status_cd'],'form_status_cd')?></button></td>
                <td><?=$this->config->item($row['cancel_gubun'],'order_cancel_gubun')?></td>
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
            </tr>

            <?php $list_number--; ?>
        <?php } //end of foreach() ?>

        </tbody>
    </table>
</div>


<div class="row text-center">
    <?php echo $pagination; ?>
</div>
