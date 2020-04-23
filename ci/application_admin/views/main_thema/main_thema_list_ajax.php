
<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">

        <colgroup>
            <col width="50px">
            <col width="70px">
            <col width="*">
            <col width="*">
        </colgroup>

        <thead>
        <tr role="row" class="active">
            <th>No.</th>
            <th class="<?php echo $sort_array['sort_num'][1];?>" onclick="form_submit('sort_field=sort_num&sort_type=<?php echo $sort_array['sort_num'][0]; ?>');">순서</th>
            <th>제목</th>
            <th>노출기간</th>
            <th class="<?php echo $sort_array['activate_flag'][1];?>" onclick="form_submit('sort_field=activate_flag&sort_type=<?php echo $sort_array['activate_flag'][0]; ?>');">활성여부</th>
            <th class="<?php echo $sort_array['reg_date'][1];?>" onclick="form_submit('sort_field=reg_date&sort_type=<?php echo $sort_array['reg_date'][0]; ?>');">등록일</th>
            <th class="<?php echo $sort_array['mod_date'][1];?>" onclick="form_submit('sort_field=mod_date&sort_type=<?php echo $sort_array['mod_date'][0]; ?>');">수정일</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($main_thema_list as $row) {
        ?>

            <tr role="row" data-seq="<?=$row['seq']?>">
                <td><?php echo number_format($list_number); ?></td>
                <td><?=$row['sort_num']?></td>
                <td><?=$row['thema_name']?></td>
                <td>
                    <?if($row['view_type'] == 'A'){?>
                        <?=view_date_format($row['start_date'])?> ~ <?=view_date_format($row['end_date'])?>
                    <?}else{?>
                    상시
                    <?}?>
                </td>
                <td>
                    <?if($row['activate_flag'] == 'Y'){?>
                        <button class="btn btn-success btn-xs special-offer-activate" data-flag="<?=$row['activate_flag']?>">활성화</button>
                    <?}else{?>
                        <button class="btn btn-warning btn-xs special-offer-activate" data-flag="<?=$row['activate_flag']?>">비활성화</button>
                    <?}?>
                </td>

                <td><?= view_date_format($row['reg_date'],2)?></td>
                <td><?=$row['mod_date']?view_date_format($row['mod_date'],2):'-'?></td>
                <td>
                    <button class="btn btn-primary btn-xs special-offer-update">수정</button>
                    <button class="btn btn-danger btn-xs special-offer-delete">삭제</button>
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
    $(document).ready(function(){
        $('.activate_num b').html('<?=$activate_num?>');
    });
</script>