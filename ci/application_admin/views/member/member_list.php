<link href="/plugins/datepicker/datepicker3.css" rel="stylesheet" />

<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">회원관리 > 회원조회</h4>
    </div>

    <div class="row well pd10">
        <div class="row">
            <div id="md_count_buttons" class="col-sm-10 col-xs-9" style="line-height:29px;">
                <span><a href="#none" class="btn btn-info btn-sm active" onclick="form_submit('state=&date1=&date2=');">전체 <b id="member_count_total">0</b> 건</a></span>
                &nbsp;&nbsp;
                <span><a href="#none" class="btn btn-info btn-sm" onclick="form_submit('state=99&date1=&date2=');">임시 <b id="member_count_99">0</b> 건 (<span id="member_per_99">0</span>%)</a></span>
                <?php foreach($this->config->item('member_state') as $key => $item ) { ?>
                    <span><a href="#none" class="btn btn-info btn-sm" onclick="form_submit('state=<?php echo $key; ?>&date1=&date2=');"><?php echo $item; ?> <b id="member_count_<?=$key;?>">0</b> 건 (<span id="member_per_<?=$key;?>">0</span>%)</a></span>
                <?php } ?>

            </div>
        </div>
    </div>

    <div class="row">
        <form name="search_form" id="search_form" method="post" action="<?php echo $this->page_link->list_ajax; ?>" class="form-horizontal">
            <input type="hidden" name="state" value="<?php echo $req['state']; ?>" />
            <input type="hidden" name="list_per_page" value="<?php echo $list_per_page; ?>" />
            <input type="hidden" name="page" value="<?php echo $req['page']; ?>" />
            <input type="hidden" name="sort_field" value="" />
            <input type="hidden" name="sort_type" value="" />

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="kwd">검색분류</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <span class="input-group-btn" style="width:auto">
                            <select id="kfd" name="kfd" class="form-control" style="border-bottom-left-radius:3px;border-top-left-radius:3px;">
                                <option value="loginid">아이디/이메일/닉네임</option>
                                <option value="m_key">회원 KEY</option>
                                <option value="m_regid">FCM ID</option>
                                <option value="m_authno">휴대폰번호</option>
                                <option value="m_tag">회원태그</option>
                            </select>
                        </span>
                        <input type="text" class="form-control" id="kwd" name="kwd" value="<?php echo $req['kwd']; ?>" style="width:auto;border-left:0;" />
                    </div>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="cate">회원상태</label>
                <div class="col-sm-10">
                    <label><input type="radio" name="m_state" value="" <?php echo (empty($req['state']) ? 'checked' : ''); ?> /> 전체</label>
                    <?php echo get_input_radio('m_state', $this->config->item('member_state'), $req['state']); ?>

                    <span>
                         &nbsp;|&nbsp;<b style="display:inline-block;margin-left:10px;">재가입 : </b>
                        <label><input type="radio" id="rejoin_yn_" name="rejoin_yn" value="" <?=empty($req['rejoin_yn']) ? "checked" : "";?> /> 전체</label>
                        <label><input type="radio" id="rejoin_yn_Y" name="rejoin_yn" value="Y" <?=($req['rejoin_yn'] == "Y") ? "checked" : "";?> /> 재가입</label>
                        <label><input type="radio" id="rejoin_yn_N" name="rejoin_yn" value="N" <?=($req['rejoin_yn'] == "N") ? "checked" : "";?> /> 신규가입</label>
                    </span>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="cate">가입경로</label>
                <div class="col-sm-10">
                    <label><input type="radio" name="j_path" value="" <?php echo (empty($req['j_path']) ? 'checked' : ''); ?> /> 전체</label>
                    <?php echo get_input_radio('j_path', $this->config->item('member_join_path'), $req['j_path']); ?>
                </div>
            </div>

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="cate">휴대폰번호 유무</label>
                <div class="col-sm-10">
                    <label><input type="radio" name="ph_yn" value="" <?php echo (empty($req['ph_yn']) ? 'checked' : ''); ?> /> 전체</label>

                    <label><input type="radio" name="ph_yn" value="Y" <?php echo $req['ph_yn'] == 'Y' ? 'checked' : ''; ?> /> 있음</label>
                    <label><input type="radio" name="ph_yn" value="N" <?php echo $req['ph_yn'] == 'N' ? 'checked' : ''; ?> /> 없음</label>

                </div>
            </div>


            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="date_type">기간검색</label>
                <div class="col-sm-10">
                    <div class="pull-left">
                        <select name="date_type" id="date_type" class="form-control" style="width:auto;">
                            <option value="m_regdatetime">가입일</option>
                            <option value="m_logindatetime">접속일</option>
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
                    <div class="help-inline-sm mgl10">* 기간검색시 하단에 회원 태그별 통계가 출력됩니다.</div>
                </div>

            </div>

            <hr />

            <div class="form-group form-group-sm text-center">
                <button type="submit" class="btn btn-primary btn-sm btn_search_submit" style="width:100px;">검색</button>
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

    <!-- 회원태그 통계 -->
    <hr />

    <div class="member_tag_stat_wrap" style="display:none;">
        <h3>회원 태그별 통계 <span class="date_term" style="font-size:16px;">&nbsp;</span></h3>
        <div id="member_tag_stat"></div>
    </div>
    <!-- /회원태그 통계 -->
</div>


<script src="/plugins/datepicker/bootstrap-datepicker.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/bootstrap-datepicker.js"); ?>" charset="utf-8"></script>
<script src="/plugins/datepicker/locales/bootstrap-datepicker.kr.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/locales/bootstrap-datepicker.kr.js"); ?>" charset="utf-8"></script>

<!-- chart
<link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
<script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
<script src="https://www.amcharts.com/lib/3/pie.js"></script>
<script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
<script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
//chart -->

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

    /**
     * 회원태그별 통계 출력
     */
    function get_member_tag_stat() {
        $('.member_tag_stat_wrap').hide();

        // console.log('search_form submit');

        //기간검색시 회원태그 통계 출력
        // console.log($('#search_form [name="date_type"]').val(), $('#search_form [name="date1"]').val(), $('#search_form [name="date2"]').val());
        var date_type = $('#search_form [name="date_type"]').val();
        var date1 = $('#search_form [name="date1"]').val();
        var date2 = $('#search_form [name="date2"]').val();

        if( date_type && date1 && date2 ) {
            // console.log('회원태그 통계 출력!!');
            $.ajax({
                url : '/member/tag_stat_ajax',
                data : $('#search_form').serialize(),
                type : 'post',
                dataType : 'html',
                success : function(html) {
                    $('#member_tag_stat').html(html);
                    $('.member_tag_stat_wrap').show();

                    $('.date_term').text('(' + date1 + ' ~ ' + date2 + ')');
                }
            });
        }
    }//end of get_member_tag_stat()


    //document.ready
    $(function () {
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

            //회원태그별 통계 출력 (로딩시)
            //get_member_tag_stat();
        }//end of if()
    });//en d of document.ready
</script>