<table class="table table-bordered">

    <tr>
        <th class="active">NO</th>
        <th class="active">출석일자</th>
    </tr>


    <? foreach ($aAttendLists as $key => $row) {?>

        <tr>
            <td><?=$key+1?></td>
            <td><?=view_date_format($row['ea_regdatetime'],2)?></td>
        </tr>

    <?}?>

</table>