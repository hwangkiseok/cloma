<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <colgroup>
            <col width="10%" />
            <col width="10%" />
            <col width="10%" />
            <col width="*" />
            <col width="10%" />
            <col width="10%" />
        </colgroup>
        <thead>
        <tr role="row" class="active">
            <th>No.</th>
            <th>사용처</th>
            <th>제목</th>
            <th>문구</th>
            <th>노출</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($word_use_list as $row) {
            ?>

            <tr role="row">
                <td><?php echo number_format($list_number); ?></td>
                <td><?php echo $this->config->item('wd_use')[$row->wd_use]; ?></td>
                <td><?php echo $row->wd_subject; ?></td>
                <td><?php echo nl2br($row->wd_content); ?></td>
                <td><?php if($row->wd_view == 'Y'){ echo '노출'; }else{ echo '비노출'; } ?></td>
                <td>
                    <a href="#none" class="btn btn-primary btn-xs" onclick="word_use_update_pop('<?php echo $row->wd_num; ?>')">수정</a>
                    <a href="#none" class="btn btn-danger btn-xs" onclick="word_use_delete('<?php echo $row->wd_num; ?>')">삭제</a>
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