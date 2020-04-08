<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <th style="width:40px;">No.</th>
            <th style="width:120px;">회원정보</th>
            <th style="width:120px;">이름</th>
            <th style="width:120px;">연락처</th>
            <th style="width:120px;">이메일</th>
            <th>내용</th>
            <th style="width:150px;">등록일</th>
            <th style="width:120px;">처리여부</th>
            <th style="width:120px;">삭제</th>
        </tr>
        </thead>
        <tbody>

        <?php $list_number = $list_count['cnt'] - ($list_per_page * ($page-1)); ?>
        <?php foreach($offer_list as $key => $row) { //zsView($row); ?>

            <tr role="row" class="cmt-item">
                <td><?php echo number_format($list_number); ?></td>
                <td>
                    <a href="#none" onclick="member_update_pop('<?php echo $row['m_num']; ?>');"><?php echo $row['m_nickname']; ?></a>
                </td>
                <td><?=$row['user_name']?></td>
                <td><?=$row['user_hp']?></td>
                <td><?=$row['user_email']?></td>
                <td style="text-align: left;"><?=nl2br($row['content'])?></td>
                <td><?=view_date_format($row['reg_date'],3)?></td>

                <td>
                    <?if( $row['proc_flag'] == 'N'){?>
                        <button class="btn btn-warning btn-xs" onclick="proc_flag('<?=$row['seq']?>','Y');">처리 전</button>
                    <?}else{?>
                        <button class="btn btn-success btn-xs" onclick="proc_flag('<?=$row['seq']?>','N');">처리완료</button>
                        <br><em><?=view_date_format($row['proc_date'],3)?></em>
                    <?}?>
                </td>

                <td>
                    <button class="btn btn-danger btn-xs" onclick="offer_delete('<?=$row['seq']?>');">삭제</button>
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
