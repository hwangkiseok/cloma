<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">CDN Purge > 실행결과</h4>
    </div>

    <div class="row">

        <table class="table table-hover table-bordered dataTable">
        <?php foreach($result_arr as $url => $result) { ?>

            <tr role="row">
                <td><?=$url;?></td>
                <td><?=$result;?></td>
            </tr>

        <?php }//endforeach; ?>

        </table>

        <button type="button" class="btn btn-success" onclick="location.href='/cdn_purge';">입력페이지로 이동</button>
    </div>
</div>

<script>

    //document.ready
    $(function () {

    });//en d of document.ready
</script>