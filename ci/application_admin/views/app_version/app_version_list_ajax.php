<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <th>No.</th>
            <th class="<?php echo $sort_array['av_os_type'][1];?>" onclick="form_submit('sort_field=av_os_type&sort_type=<?php echo $sort_array['av_os_type'][0]; ?>');">OS타입</th>
            <th class="<?php echo $sort_array['av_version'][1];?>" onclick="form_submit('sort_field=av_version&sort_type=<?php echo $sort_array['av_version'][0]; ?>');">버전명</th>
            <th class="<?php echo $sort_array['av_version_code'][1];?>" onclick="form_submit('sort_field=av_version_code&sort_type=<?php echo $sort_array['av_version_code'][0]; ?>');">버전코드</th>
            <th class="<?php echo $sort_array['av_offer_type'][1];?>" onclick="form_submit('sort_field=av_offer_type&sort_type=<?php echo $sort_array['av_offer_type'][0]; ?>');">제공방식</th>
            <th class="<?php echo $sort_array['au_name'][1];?>" onclick="form_submit('sort_field=au_name&sort_type=<?php echo $sort_array['au_name'][0]; ?>');">작성자</th>
            <th class="<?php echo $sort_array['av_regdatetime'][1];?>" onclick="form_submit('sort_field=av_regdatetime&sort_type=<?php echo $sort_array['av_regdatetime'][0]; ?>');">등록일</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($app_version_list as $row) {
            ?>

            <tr role="row">
                <td><?php echo number_format($list_number); ?></td>
                <td><?php echo get_config_item_text($row->av_os_type, 'app_version_os_type'); ?></td>
                <td><?php echo $row->av_version; ?></td>
                <td><?php echo $row->av_version_code; ?></td>
                <td><?php echo get_config_item_text($row->av_offer_type, 'app_version_offer_type'); ?></td>
                <td><?php echo $row->au_name; ?></td>
                <td><?php echo get_date_format($row->av_regdatetime); ?></td>
                <td>
                    <a href="#none" class="btn btn-primary btn-xs" onclick="app_version_update_pop('<?php echo $row->av_num; ?>')">수정</a>
                    <a href="#none" class="btn btn-danger btn-xs" onclick="app_version_delete('<?php echo $row->av_num; ?>')">삭제</a>
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