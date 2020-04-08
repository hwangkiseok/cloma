<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">관리자계정관리</h4>
    </div>

    <div class="row well pd10">
        <div class="row">
            <div class="pull-left mgl15" style="line-height:29px;">
                <??>
                전체 <?php echo number_format($total_count); ?>건
            </div>
            <div class="pull-right text-right mgr15">
                <a href="/adminuser/insert/?<?echo $GV; ?>" class="btn btn-primary btn-sm">관리자 등록</a>
            </div>
        </div>
    </div>

    <div class="row mgb10">
        <form name="search_form" id="search_form" method="post" class="form-inline">
            <div class="form-group form-group-sm pull-right">
                <div class="input-group">
                    <span class="input-group-addon">검색분류</span>
                    <span class="input-group-btn" style="min-width:70px;">
                        <select id="kfd" name="kfd" class="form-control">
                            <?php echo get_select_option("", array("au_name"=>"이름", "au_loginid"=>"아이디"), $req['kfd']); ?>
                        </select>
                    </span>
                    <input type="text" class="form-control" id="kwd" name="kwd" value="<?php echo $req['kwd']; ?>" style="min-width:50px;border-left:0;" />
                    <span class="input-group-btn"><button type="submit" class="btn btn-primary btn-sm">검색</button></span>
                </div>
                <!--</div>-->
            </div>
        </form>
    </div>

    <div class="row">
        <div class="table-responsive">
            <table class="table table-hover table-bordered"">
            <thead>
            <tr class="active">
                <th>No.</th>
                <th>구분</th>
                <th>아이디</th>
                <th>이름</th>
                <th>이메일</th>
                <th>휴대폰</th>
                <th>로그인일시</th>
                <th>등록일시</th>
                <th>상태</th>
                <th>수정</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $list_number = $total_count - ($list_per_page * ($page-1));

            foreach($adminuser_list as $row) {
            ?>

            <tr>
                <td><?echo number_format($list_number); ?></td>
                <td><?echo get_config_item_text($row->au_level, "adminuser_level"); ?></td>
                <td><?echo $row->au_loginid; ?></td>
                <td><?echo $row->au_name; ?></td>
                <td><?echo $row->au_email; ?></td>
                <td><?echo $row->au_mobile; ?></td>
                <td><?echo get_datetime_format($row->au_logindatetime); ?></td>
                <td><?echo get_date_format($row->au_regdate); ?></td>
                <td><?echo get_config_item_text($row->au_usestate, "adminuser_usestate")?></td>
                <td><a href="/adminuser/update/?au_num=<?echo $row->au_num; ?>&<?echo $GV; ?>" class="btn btn-primary btn-xs">수정</a></td>
            </tr>

            <?php
                $list_number--;
            }//end of for()
            ?>

            </tbody>
            </table>
        </div>
    </div>

    <div class="row text-center">
        <?php echo $pagination; ?>
    </div>
</div>