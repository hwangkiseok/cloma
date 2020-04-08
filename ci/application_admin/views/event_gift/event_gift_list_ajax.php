<div class="table-responsive">
    <table class="table table-hover table-bordered dataTable">
        <thead>
        <tr role="row" class="active">
            <th>No.</th>
            <th class="<?php echo $sort_array['e_subject'][1];?>" onclick="form_submit('sort_field=e_subject&sort_type=<?php echo $sort_array['e_subject'][0]; ?>');">이벤트명</th>
            <th class="<?php echo $sort_array['eg_event_ym'][1];?>" onclick="form_submit('sort_field=eg_event_ym&sort_type=<?php echo $sort_array['eg_event_ym'][0]; ?>');">이벤트 년월</th>
            <th class="<?php echo $sort_array['eg_gift'][1];?>" onclick="form_submit('sort_field=eg_gift&sort_type=<?php echo $sort_array['eg_gift'][0]; ?>');">기프티콘핀번호</th>
            <th class="<?php echo $sort_array['m_loginid'][1];?>" onclick="form_submit('sort_field=m_loginid&sort_type=<?php echo $sort_array['m_loginid'][0]; ?>');">발급회원</th>
            <th class="<?php echo $sort_array['eg_regdatetime'][1];?>" onclick="form_submit('sort_field=eg_regdatetime&sort_type=<?php echo $sort_array['eg_regdatetime'][0]; ?>');">등록일시</th>
            <th class="<?php echo $sort_array['eg_issuedatetime'][1];?>" onclick="form_submit('sort_field=eg_issuedatetime&sort_type=<?php echo $sort_array['eg_issuedatetime'][0]; ?>');">발급일시</th>
            <th class="<?php echo $sort_array['eg_state'][1];?>" onclick="form_submit('sort_field=eg_state&sort_type=<?php echo $sort_array['eg_state'][0]; ?>');">상태</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $list_number = $list_count - ($list_per_page * ($page-1));

        foreach($event_gift_list as $row) {
            ?>

            <tr role="row">
                <td><?php echo number_format($list_number); ?></td>
                <td><?php echo $row->e_subject; ?></td>
                <td><?php echo substr(get_date_format($row->eg_event_ym, "-"), 0, -1); ?></td>
                <td>
                    <?php echo $row->eg_gift; ?>
                    <a href="#none" onclick="new_win_open('/download/?f=<?php echo urlencode($row->eg_gift_file); ?>&t=view', 'download_win', 800, 600);"><i class="fa fa-file-image-o"></i></a>
                    <?if($row->eg_event_gift){?>
                    <br>( <?php
                        if($row->gift_name){
                            echo $row->gift_name;
                        }else{
                            echo $this->config->item($row->eg_event_gift,$row->e_code);
                        }
                        ?> )
                    <?}?>
                </td>
                <td>
                    <?if($row->m_loginid == ''){?>
                        <a href="#none" onclick="member_update_pop('<?php echo $row->eg_member_num; ?>')"><?php echo $row->eg_event_ph; ?></a>
                        <?//php echo $row->eg_event_ph; ?>
                    <?}else{?>
                    <a href="#none" onclick="member_update_pop('<?php echo $row->eg_member_num; ?>')"><?php echo $row->m_loginid; ?></a>
                    <?}?>
                </td>
                <td><?php echo get_datetime_format($row->eg_regdatetime); ?></td>
                <td><?php echo get_datetime_format($row->eg_issuedatetime); ?></td>
                <td><?php echo get_config_item_text($row->eg_state, "event_gift_state"); ?></td>
                <td>
                    <a href="#none" class="btn btn-primary btn-xs" onclick="event_gift_update_pop('<?php echo $row->eg_num; ?>')">수정</a>
                    <?php if( $row->eg_state == "2" ) { ?>
                    <a href="#none" class="btn btn-danger btn-xs" onclick="alert('발급된 기프티콘을 삭제할 수 없습니다.');">삭제</a>
                    <?php } else { ?>
                    <a href="#none" class="btn btn-danger btn-xs" onclick="event_gift_delete('<?php echo $row->eg_num; ?>')">삭제</a>
                    <?php }//endif; ?>
                </td>
            </tr>

            <?php
            $list_number--;
        }//end of foreach()
        ?>
        </tbody>
    </table>

    </form>
</div>

<div class="row text-center">
    <?php echo $pagination; ?>
</div>