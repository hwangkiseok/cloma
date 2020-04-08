<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <!--<th><input type="checkbox" id="all_list_check" onclick="all_check(this);" /></th>-->
            <th>No.</th>
            <th class="<?php echo $sort_array['pf_name'][0];?>" onclick="form_submit('sort_field=pf_name&sort_type=<?php echo $sort_array['pf_name'][0]; ?>');">상품명</th>
            <th>경로</th>
            <th class="<?php echo $sort_array['reg_date'][1];?>" onclick="form_submit('sort_field=reg_date&sort_type=<?php echo $sort_array['reg_date'][0]; ?>');">등록일</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count['cnt'] - ($list_per_page * ($page-1));

        foreach($proposal_list as $row) {
            ?>

            <tr role="row">
                <!--<td><input type="checkbox" name="bn_num[]" value="--><?php //echo $row->bn_num; ?><!--" /></td>-->
                <td><?php echo number_format($list_number); ?></td>
                <td><?php echo $row['pf_name']; ?></td>
                <td style="text-align: left;">
                    <?php echo $row['pf_file_url']; ?>
                    <br>
                    <button class="btn btn-primary btn-xs pf_file_download" data-name="<?=$row['pf_name']?>"  data-path="<?='/www'.$row['pf_file_url']?>">다운로드</button>
                    <button class="btn btn-warning btn-xs pf_file_copy" data-name="<?=$row['pf_name']?>"  data-path="<?='/www'.$row['pf_file_url']?>">URL복사</button>
                </td>
                <td><?php echo view_date_format($row['reg_date']); ?></td>
                <td>
                    <a href="#none" class="btn btn-primary btn-xs" onclick="proposal_update_pop('<?php echo $row['pf_num']; ?>')">수정</a>
                    <a href="#none" class="btn btn-danger btn-xs" onclick="proposal_delete('<?php echo $row['pf_num']; ?>')">삭제</a>
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