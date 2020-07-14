<link href="/plugins/datepicker/datepicker3.css" rel="stylesheet" />

<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">회원관리 > 카카오채널 회원</h4>
    </div>

    <div class="row">
        <form name="search_form" id="search_form" method="post" action="<?php echo $this->page_link->list_ajax; ?>" class="form-horizontal">
            <input type="hidden" name="state" value="<?php echo $req['state']; ?>" />
            <input type="hidden" name="list_per_page" value="<?php echo $list_per_page; ?>" />
            <input type="hidden" name="page" value="<?php echo $req['page']; ?>" />
            <input type="hidden" name="excel" value="" />
            <input type="hidden" name="sort_field" value="" />
            <input type="hidden" name="sort_type" value="" />

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="kwd">검색분류</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <span class="input-group-btn" style="width:auto">
                            <select id="kfd" name="kfd" class="form-control" style="border-bottom-left-radius:3px;border-top-left-radius:3px;">
                                <option value="nickname">닉네임</option>
                                <option value="phone_number">연락처</option>
                            </select>
                        </span>
                        <input type="text" class="form-control" id="kwd" name="kwd" value="<?php echo $req['kwd']; ?>" style="width:auto;border-left:0;" />
                    </div>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="cate">상태</label>
                <div class="col-sm-10">
                    <label><input type="radio" name="state" value="" <?php echo (empty($req['state']) ? 'checked' : ''); ?> /> 전체</label>
                    <label><input type="radio" name="state" value="added" <?php echo $req['state'] == 'added' ? 'checked' : ''; ?> /> 친구추가</label>
                    <label><input type="radio" name="state" value="blocked" <?php echo $req['state'] == 'blocked' ? 'checked' : ''; ?> /> 차단</label>
                </div>
            </div>

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="date_type">기간검색</label>
                <div class="col-sm-10">
                    <div class="pull-left">
                        <select name="date_type" id="date_type" class="form-control" style="width:auto;">
                            <option value="reg_date">채널 추가일</option>
                            <option value="update_date">마지막 상태변경일</option>
                        </select>
                        <script>selected_check('#date_type', '<?php echo $req['date_type']; ?>');</script>
                    </div>
                    <div class="pull-left mgl10">
                        <div class="input-group date" style="width:133px;">
                            <input type="text" class="form-control" style="width:100px;" name="date1" value="<?php echo $req['date1']; ?>" />
                            <span class="input-group-btn text-left">
                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                            </span>
                        </div>
                    </div>
                    <div class="pull-left text-center middle" style="width:20px;">~</div>
                    <div class="pull-left">
                        <div class="input-group date" style="width:133px;">
                            <input type="text" class="form-control" style="width:100px;" name="date2" value="<?php echo $req['date2']; ?>" />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                            </span>
                        </div>
                    </div>
                    <div class="pull-left">
                        <button type="button" class="btn btn-primary btn-outline btn-sm mgl5" onclick="clear_date_term();">전체</button>
                        <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-3');">3일전</button>
                        <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-2');">2일전</button>
                        <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-1');">어제</button>
                        <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term();">오늘</button>
                        <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-3d');">3일</button>
                        <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-7d');">7일</button>
                        <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-1m');">1개월</button>
                        <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-3m');">3개월</button>
                    </div>
                </div>

            </div>

            <hr />

            <div class="form-group form-group-sm text-center">
                <button type="submit" class="btn btn-primary btn-sm btn_search_submit" style="width:100px;">검색</button>
                <button type="button" class="btn btn-success btn-sm btn_download" style="width:100px;">엑셀다운로드</button>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="row mgb10">
            <div class="col-md-2 pull-right">
                <select id="list_per_page" class="form-control input-sm">
                    <?php echo get_select_option("", $this->config->item('list_per_page'), $list_per_page); ?>
                </select>
            </div>
        </div>

        <div id="member_list" style="position:relative;"></div>
    </div>

</div>

<script src="/plugins/datepicker/bootstrap-datepicker.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/bootstrap-datepicker.js"); ?>" charset="utf-8"></script>
<script src="/plugins/datepicker/locales/bootstrap-datepicker.kr.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/locales/bootstrap-datepicker.kr.js"); ?>" charset="utf-8"></script>

<script>
    /**
     * form submit
     */
    function form_submit(str) {
        var arr1 = str.split('&');
        for(var i in arr1) {
            if( !empty(arr1[i]) ) {
                var arr2 = arr1[i].split('=');
                var name = arr2[0];
                var val = arr2[1];

                $('#search_form [name="' + name + '"]').val(val);
            }
        }//end of for()

        $('#search_form').submit();
    }//end of form_submit()

    //document.ready
    $(function () {

        $('.btn_download').on('click',function(e){
            e.preventDefault();
            location.href = '<?php echo $this->page_link->list_excel; ?>/?' + $('#search_form').serialize();
        });

        $('input[name="m_state"]').on('click',function(){
            form_submit('state='+$(this).val());
        });

        $('#md_count_buttons a.btn').on('click', function(){
            $('#md_count_buttons a.btn').removeClass('active');
            $(this).addClass('active');
        });

        //datepicker
        $('.input-group.date').datepicker({format: "yyyy-mm-dd", language: "kr", autoclose: true});

        //목록 갯수보기 select change
        $('#list_per_page').on('change', function(){
            $('#search_form [name="list_per_page"]').val($(this).val());
            $('#search_form').submit();
        });

        //Ajax Form
        if( $('#search_form').length > 0 ) {
            //검색 클릭시
            $('.btn_search_submit').on('click', function() {
                // get_member_tag_stat();
            });

            //ajaxForm
            $('#search_form').ajaxForm({
                type: 'post',
                dataType: 'html',
                beforeSubmit: function(formData, jqForm, options) {
                    loadingBar.show($('#member_list'));
                },
                success: function(result) {
                    $('#member_list').html(result);
                },
                complete: function() {
                    loadingBar.hide();
                }
            });//end of ajaxForm()


            //당일검색으로 변경
            set_date_term();
            //로딩시 데이터 출력
            $('#search_form').submit();

            //ajax page
            $(document).on('click', '#member_list .pagination.ajax a', function(e){
                e.preventDefault();

                if( $(this).attr('href') == '#none' ) {
                    return false;
                }

                //$('#search_form').attr('action', $(this).attr('href'));
                //$('#search_form').submit();
                form_submit($(this).attr('href'));
            });

        }//end of if()
    });//en d of document.ready
</script>