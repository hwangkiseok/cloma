<table class="table table-bordered table-responsive table-hover">
    <tr>
        <th>No</th>
        <th>적립금코드</th>
        <th>적립금명</th>
        <th>최초적립금</th>
        <th>사용적립금</th>
        <th>남은적립금</th>
        <th>발급일시</th>
        <th>사용시작일</th>
        <th>사용만료일</th>
        <th>사용여부</th>
        <th>관리</th>
    </tr>

    <?php foreach($data as $key => $item): ?>
        <?php if($item['active_yn'] == 'C' || $item['use_yn'] == 'Y' || $item['expire_yn'] == 'Y'): ?>
        <?php $style = "style='text-decoration:line-through'"; ?>
        <?php else: ?>
        <?php $style = ''; ?>
        <?php endif; ?>
        <tr data-uid="<?php echo $item['uid']; ?>">
            <td><?php echo $cnt; ?></td>
            <td><?php echo $item['code'] . $item['pm_active_yn'] .$item['pm_expire_yn']; ?></td>
            <td><?php echo $item['name']; ?></td>
            <td <?php echo $style; ?>><?php echo $item['org_points']; ?></td>
            <td <?php echo $style; ?>><?php echo $item['used_points']; ?></td>
            <td <?php echo $style; ?>><?php echo $item['rest_points']; ?></td>
            <td><?php echo $item['reg_date']; ?></td>
            <td><?php echo $item['use_startdate']; ?></td>
            <td><?php echo $item['use_enddate']; ?></td>
            <td>
                <?php if($item['delete_able'] == 'N' || $data_item['part_used'] == 'Y'): ?>
                    <a href="#" onclick="point_used_history('<?php echo $item['uid']; ?>', '<?php echo $cnt; ?>')">
                <?php endif; ?>
                <?php echo $item['use_state_text']; ?>
                <?php if($item['delete_able'] == 'N'): ?>
                    </a>
                <?php endif; ?>
            </td>
            <td>
                <?php if($item['delete_able'] == "Y") { ?>
                    <a href="#none" class="btn btn-xs btn-danger" onclick="point_delete('<?php echo $item['uid']; ?>');">삭제</a>
                <?php } ?>
            </td>
        </tr>
        <?php $cnt = $cnt - 1; ?>
    <?php endforeach; ?>
</table>
<script>
    function point_used_history(uid, no) {
        var container = $('<div>');
        $(container).load('/point/used_history?uid=' + uid);

        modalPop.createPop('적립금사용내역' + no, container);
        modalPop.createCloseButton('닫기', 'btn btn-default btn-sm');
        modalPop.show({
            'backdrop' : 'static',
            'dialog_class' : 'modal-lg'
        });
    }
</script>