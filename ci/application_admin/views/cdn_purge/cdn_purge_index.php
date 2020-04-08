<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">CDN Purge</h4>
    </div>

    <div class="row">
        <form name="insert_form" id="insert_form" method="post" action="/cdn_purge/proc" class="form-horizontal">
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="kwd">사이트</label>
                <div class="col-sm-10">
                    <select name="tid" class="form-control input-sm" style="width:300px;">
                        <?php foreach($tid_array as $tid => $domain) { ?>
                            <option value="<?=$tid;?>"><?=$domain;?></option>
                        <?php }//endforeach; ?>
                    </select>
                </div>


            </div>

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label">이미지 경로(s)</label>
                <div class="col-sm-10">
                    <textarea name="urls" class="form-control" style="width:90%; height:500px;"></textarea>

                    <p class="help-block">* 입력예 : /uploads/product/2018/0501/이미지명.jpg</p>
                    <p class="help-block">* 한줄에 이미지경로 1개씩 입력할 것.</p>
                </div>
            </div>

            <hr />

            <div class="form-group form-group-sm text-center">
                <button type="submit" class="btn btn-info btn-sm mgl10" style="width:100px;">적용</button>
            </div>
        </form>
    </div>
</div>

<script>
    //document.ready
    $(function () {

    });//en d of document.ready
</script>