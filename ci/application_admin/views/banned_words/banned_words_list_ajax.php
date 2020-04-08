<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <th>No.</th>
            <th class="<?php echo $sort_array['bw_word'][1];?>" onclick="form_submit('sort_field=bw_word&sort_type=<?php echo $sort_array['bw_word'][0]; ?>');">금칙어</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($banned_words_list as $row) {
            ?>

            <tr role="row">
                <td><?php echo number_format($list_number); ?></td>
                <td><?php echo $row->bw_word; ?></td>
                <td><a href="#none" class="btn btn-danger btn-xs" onclick="banned_words_delete('<?php echo $row->bw_num; ?>')">삭제</a></td>
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