<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <th>No.</th>
            <th class="<?php echo $sort_array['co_name'][1];?>" onclick="form_submit('sort_field=co_name&sort_type=<?php echo $sort_array['co_name'][0]; ?>');">제휴사명</th>
            <th class="<?php echo $sort_array['co_loginid'][1];?>" onclick="form_submit('sort_field=co_loginid&sort_type=<?php echo $sort_array['co_loginid'][0]; ?>');">제휴사아이디</th>
            <th class="<?php echo $sort_array['co_passwd'][1];?>" onclick="form_submit('sort_field=co_passwd&sort_type=<?php echo $sort_array['co_passwd'][0]; ?>');">제휴사비밀번호</th>
            <th class="<?php echo $sort_array['co_url'][1];?>" onclick="form_submit('sort_field=co_url&sort_type=<?php echo $sort_array['co_url'][0]; ?>');">제휴사URL</th>
            <th class="<?php echo $sort_array['au_name'][1];?>" onclick="form_submit('sort_field=au_name&sort_type=<?php echo $sort_array['au_name'][0]; ?>');">작성자</th>
            <th class="<?php echo $sort_array['co_regdatetime'][1];?>" onclick="form_submit('sort_field=co_regdatetime&sort_type=<?php echo $sort_array['co_regdatetime'][0]; ?>');">등록일</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($company_list as $row) {
            ?>

            <tr role="row">
                <td><?php echo number_format($list_number); ?></td>
                <td><?php echo $row->co_name; ?></td>
                <td><?php echo $row->co_loginid; ?></td>
                <td><?php echo $row->co_passwd; ?></td>
                <td><?php echo $row->co_url; ?></td>
                <td><?php echo $row->au_name; ?></td>
                <td><?php echo get_date_format($row->co_regdatetime); ?></td>
                <td>
                    <a href="#none" class="btn btn-primary btn-xs" onclick="company_update_pop('<?php echo $row->co_num; ?>')">수정</a>
                    <a href="#none" class="btn btn-danger btn-xs" onclick="company_delete('<?php echo $row->co_num; ?>')">삭제</a>
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