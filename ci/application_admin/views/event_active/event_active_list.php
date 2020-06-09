<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js" ></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css"/>
<link href="/plugins/datepicker/datepicker3.css?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/datepicker3.css"); ?>" rel="stylesheet" />

<style> .file{position: absolute;width: 1px;height: 1px;padding: 0;margin: -1px;overflow: hidden;clip: rect(0,0,0,0);border: 0;} </style>

<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">이벤트참여관리 > 목록</h4>
    </div>

    <form name="search_form" id="search_form" method="post" action="<?php echo $this->page_link->list_ajax; ?>" class="form-horizontal">
        <input type="hidden" name="list_per_page" value="<?php echo $list_per_page; ?>" />
        <input type="hidden" name="sort_field" value="" />
        <input type="hidden" name="sort_type" value="" />
        <div class="row">

            <div class="form-group form-group-sm form-inline">
                <label class="col-sm-2 control-label" for="div">이벤트종류</label>
                <div class="col-sm-10">
                    <div class="pull-left">
                        <select id="div" name="div" class="form-control">
                            <?php echo get_select_option("", $this->config->item("event_division"), ""); ?>
                            <option value="naver_search_20170922">옷쟁이들 검색 이벤트</option>
                            <option value="event_20171027">추석 이벤트</option>
                            <option value="event_20171205">안마의자 드립니다 이벤트</option>
                            <option value="event_20171206">안마의자 드립니다2 이벤트</option>
                            <option value="event_20171219">VIP 고객 이벤트</option>
                            <option value="event_20171221">VIP 고객2 이벤트</option>
                            <option value="event_20180109">선착순 이벤트</option>
                            <option value="event_20180117">구매고객 감사이벤트</option>
                            <option value="event_20180122">선착순 이벤트2</option>
                            <option value="event_20180201">선착순 이벤트3</option>
                            <option value="event_20180310">메가쇼 룰렛이벤트</option>
                            <option value="event_20180312">룰렛이벤트2</option>
                            <option value="event_20180328">룰렛이벤트3</option>
                            <option value="event_20180508">럭키박스 이벤트</option>
                            <option value="event_20180516">럭키박스 이벤트2</option>
                            <option value="event_20180620">럭키박스 이벤트3</option>
                            <option value="event_20180711">NEW 럭키박스 이벤트</option>
                            <option value="event_20180717">카카오 럭키박스 이벤트</option>
                            <option value="event_20180823">깜짝 이벤트</option>
                            <option value="event_20180911">매일룰렛이벤트</option>
                            <option value="event_20190128">럭키박스 복주머니 이벤트</option>
                            <option value="event_20190304">매일룰렛이벤트V2</option>

                            <option value="event_20180906">포토리뷰 당첨자안내</option>
                            <option value="event_20190527">쇼핑지원금,지금쏜다</option>

                            <option value="event_20190723">환급이벤트</option>



                        </select>
                    </div>
                    <div class="pull-left" style="margin-left:20px;">
                        <label class="checkbox">
                            <input type="checkbox" name="grp_yn" id="grp_yn" value="Y"> 회원별보기
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group form-group-sm form-inline">
                <label class="col-sm-2 control-label" for="div">날짜검색</label>
                <div class="col-sm-10">
                    <!--<div class="pull-left">-->
                    <!--    <select id="year" name="year" class="form-control">-->
                    <!--        --><?php //echo get_select_option_year("", date("Y", time()), 2016, date("Y", time())); ?>
                    <!--    </select>-->
                    <!--</div>-->
                    <!--<div class="pull-left" style="margin-left:20px;">-->
                    <!--    <select id="month" name="month" class="form-control">-->
                    <!--        --><?php //echo get_select_option_month("", date("m", time())); ?>
                    <!--    </select>-->
                    <!--</div>-->

                    <div class="pull-left">
                        <select id="dateType" name="dateType" class="form-control" style="border-bottom-left-radius:3px;border-top-left-radius:3px;">
                            <option value="ea_regdatetime">참여일</option>
                        </select>
                    </div>
                    <div class="pull-left mgl10">
                        <div class="input-group date" style="width:133px;">
                            <input type="text" class="form-control" style="width:100px;" name="date1" value="<?php echo $req['date1']; ?>" />
                            <span class="input-group-btn text-left">
                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                            </span>
                        </div>
                    </div>
                    <div class="pull-left text-center" style="width:20px;">~</div>
                    <div class="pull-left">
                        <div class="input-group date" style="width:133px;">
                            <input type="text" class="form-control" style="width:100px;" name="date2" value="<?php echo $req['date2']; ?>" />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                            </span>
                        </div>
                    </div>
                    <div class="pull-left">
                        <button type="button" class="btn btn-primary btn-outline btn-sm mgl5" onclick="set_date_term('-3');">3일전</button>
                        <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-2');">2일전</button>
                        <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-1');">어제</button>

                        <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term();">오늘</button>
                        <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-3d');">3일</button>
                        <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-7d');">7일</button>
                        <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-1m');">1개월</button>
                        <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-3m');">3개월</button>
                        <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="clear_date_term();">전체</button>
                    </div>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="div">달성검색</label>
                <div class="col-sm-10">
                    <input type="radio" name="ew_type" id="ew_type_0" value="" checked /><label for="ew_type_0">전체</label>&nbsp;
                    <input type="radio" name="ew_type" id="ew_type_all" value="all" /><label for="ew_type_all">달성모두</label>&nbsp;
                    <?php echo get_input_radio("ew_type", $ew_type_array, ""); ?>
                </div>
            </div>

            <div class="form-group form-group-sm select_gift_area" style="display: none">
                <label class="col-sm-2 control-label" for="div">당첨상품</label>
                <div class="col-sm-4">
                    <select name="select_gift" class="form-control"></select>
                </div>
            </div>


            <div class="form-group form-group-sm form-inline">
                <label class="col-sm-2 control-label" for="kwd">검색분류</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <span class="input-group-btn" style="width:auto">
                            <select id="kfd" name="kfd" class="form-control" style="border-bottom-left-radius:3px;border-top-left-radius:3px;">
                                <option value="m_loginid">회원아이디 / 연락처</option>
<!--                                <option value="ea_month_count">월총출석횟수</option>-->
<!--                                <option value="ea_accrue_count">연속출석횟수</option>-->
                                <option value="ew_contact">연락처</option>
                            </select>
                        </span>
                        <input type="text" class="form-control" id="kwd" name="kwd" value="<?php echo $req['kwd']; ?>" style="width:auto;border-left:0;" />
                    </div>
                </div>
            </div>

            <hr />

            <div class="form-group form-group-sm text-center">
                <button type="submit" class="btn btn-primary btn-sm" style="width:100px;">검색</button>
                <button type="button" class="btn btn-info btn-sm mgl15 mgr10" onclick="excel_download();">엑셀 다운로드</button>
                &nbsp;|&nbsp;
                <button type="button" class="btn btn-warning btn-sm mgl15" onclick="setWinner();">당첨자 등록</button>
            </div>
        </div>

        <div class="row">
            <div class="row mgb10">
                <div class="col-md-2 pull-left form-inline form-group-sm">
                    <select name="ym" class="form-control btn-success">
                        <?php echo get_select_option_ym("", date("Ym"), date("Ym"), 201602, "desc"); ?>
                    </select>
                </div>
                <div class="col-md-2 pull-right">
                    <select id="list_per_page" class="form-control input-sm">
                        <?php echo get_select_option("", $this->config->item('list_per_page'), $list_per_page); ?>
                    </select>
                </div>
            </div>

            <div id="event_list" style="position:relative;"></div>
        </div>
    </form>
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

    /**
     * 엑셀다운로드
     */
    function excel_download() {
        location.href = '<?php echo $this->page_link->list_excel; ?>/?' + $('#search_form').serialize();
    }//end of excel_download()


    //document.ready
    $(function () {
        //datepicker
        $('.input-group.date').datepicker({format: "yyyy-mm-dd", language: "kr", autoclose: true});

        $('#div,[name="ew_type"],[name="year"],[name="month"],[name="ym"],[name="grp_yn"]').on('change', function(){
            $('#search_form input[name="sort_field"]').val('');
            $('#search_form input[name="sort_type"]').val('');
            $('#search_form').submit();

            $.ajax({
                url: '/event_gift/getEventInfo',
                data: { e_code : $(this).val() , call_type : 'event_active_ajax' },
                type: 'post',
                dataType: 'json',
                success: function (result) {

                    if(result.status == '000'){

                        var data = JSON.parse(result.data);

                        var options = '';
                        $.each(data,function(val,name){
                            options += '<option value="'+val+'">'+name+'</option>';
                        })

                        if(options != ''){
                            $('.select_gift_area').show();
                            options = '<option value="">- 전체선택 -</option>' + options;

                            $('.select_gift_area select').html(options)
                        }else{
                            $('.select_gift_area').hide();
                            $('.select_gift_area select').html('')
                        }

                    }

                }
            });

        });

        $('[name="ew_type"]').on('click', function() {
            if ( !$(this).val() ) {
                $('#kfd option').eq(0).prop('selected', true);
                $('#kfd option[value="ew_contact"]').hide();
            }
        });

        //목록 갯수보기 select change
        $('#list_per_page').on('change', function(){
            $('#search_form [name="list_per_page"]').val($(this).val());
            $('#search_form').submit();
        });

        //Ajax Form
        $('#search_form').ajaxForm({
            //url : '<?php //echo $this->page_link->list_ajax; ?>//',
            type: 'post',
            dataType: 'html',
            beforeSubmit: function(formData, jqForm, options) {
                loadingBar.show($('#event_list'));
            },
            success: function(result) {
                $('#event_list').html(result);
            },
            complete: function() {
                loadingBar.hide();
            }
        });//end of ajax_form()

        $('#search_form').submit();

        //ajax page
        $(document).on('click', '#event_list .pagination.ajax a', function(e){
            e.preventDefault();

            if( $(this).attr('href') == '#none' ) {
                return false;
            }

            $('#search_form').attr('action', $(this).attr('href'));
            $('#search_form').submit();
        });
    });//en d of document.ready



    <?/**
     * @date 171016
     * @author 황기석
     * @desc 당첨자 등록
     */ ?>

    $(document).on('change','input[type="file"]',function(){

        var bRst = FileChk($(this));

        if(!bRst){
            $(this).parent().find('input[type="text"]').val('');
            return false;
        }
        var fileValue = $(this).val().split("\\");
        var fileName = fileValue[fileValue.length-1]; // 파일명

        $(this).parent().find('input[type="text"]').val(fileName);

    });

    function FileChk(obj){

        if(obj.val() != ''){
            var file_size = obj[0]['files'][0]['size'];
            var size_mb = (file_size / 1024) / 1024;
            if(size_mb >= 10){
                alert('첩부파일은 10메가 미만 파일만 업로드가 가능합니다.');
                obj.val('');
                return false;
            }else{
                return true;
            }
        }else{
            return true;
        }

    }

    function setWinnerFormChk(){

        var regist_winner_item  = $('select[name="regist_winner_item"]').val();
        var e_code              = $('.regist_winner input[name="e_code"]').val();

        if(e_code == '' || e_code == null || e_code == undefined){
            alert( '필수 입력정보 누락 [e_code]');
            return false;
        }

        if(regist_winner_item == '' || regist_winner_item == null || regist_winner_item == undefined){
            alert('당첨경품을 선택해주세요 !');
            return false;
        }

        if($('input[name="csvDataText"]').val() == '' || $('input[name="csvDataText"]').val() == undefined || $('input[name="csvDataText"]').val() == null){
            alert('당첨자 엑셀파일을 선택해주세요 !');
            return false;
        }

    }

    function setWinner(){

        var div_val     = $('form[name="search_form"] select[name="div"]').val();
        var div_text    = $('form[name="search_form"] select[name="div"]').find('option:selected').text();

        if(div_val == 1 || div_val == 2 ){
            alert('고정/일반 이벤트는 당첨자 등록이 불가능합니다.');
            return false;
        }

        var html  = '';
            html += '<form name="regist_winner" class="regist_winner" id="regist_winner" onsubmit="return setWinnerFormChk();" method="post" enctype="multipart/form-data">';
            html += '<input name="e_code" type="hidden" value="'+div_val+'" />';
            html += '   <div class="table-reponsive">';
            html += '       <table class="table table-bordered">';
            html += '       <col width="300px" />';
            html += '       <col width="" />';

            html += '       <tbody>';
            html += '       <tr role="row">';
            html += '           <th class="active">이벤트명</th>';
            html += '           <td style="text-align: left;">'+div_text+'</td>';
            html += '       </tr>';

            html += '       <tr role="row">';
            html += '           <th class="active">당첨경품</th>';
            html += '           <td style="text-align: left;"><select name="regist_winner_item" style="width: 300px;"></select></td>';
            html += '       </tr>';

            html += '       <tr role="row">';
            html += '           <th class="active">중복당첨 가능여부</th>';
            html += '           <td style="text-align: left;">';
            html += '               <label><input type="radio" name="overlap_chk" value="N" checked> 중복불가</label>&nbsp;&nbsp;&nbsp;';
            html += '               <label><input type="radio" name="overlap_chk" value="Y"> 중복가능</label>';
            html += '           </td>';
            html += '       </tr>';


            html += '       <tr role="row">';
            html += '           <th class="active">참여정보 여부</th>';
            html += '           <td style="text-align: left;">';
            html += '               <label><input type="radio" name="join_info_chk" value="N" checked> 참여정보 없음</label> <span class="text-danger">(포토리뷰 당첨자 형태)</span><br>';
            html += '               <label><input type="radio" name="join_info_chk" value="Y"> 참여정보 있음</label> <span class="text-danger">(출석이벤트 형태)</span>';
            html += '           </td>';
            html += '       </tr>';

            html += '       <tr role="row">';
            html += '           <th class="active">당첨자</th>';
            html += '           <td style="text-align: left;">';
            html += '               <input type="text" name="csvDataText" class="form-control" readonly="readonly" value="" style="width: 300px;display: inline-block">';
            html += '               <label for="csvData" style="display: inline-block"><a class="btn btn-info" style="padding: 4px 12px;">찾아보기</a></label>';

            html += '           <a role="button" class="btn btn-success join_info_chk_frm" style="padding: 4px 12px;">엑셀샘플</a>';

            html += '               <input type="file" id="csvData" name="csvData" class="file">';
            html += '               <p class="alert alert-danger list_download_noti" style="margin: 0;padding-top: 8px;padding-bottom: 5px;display:none;">리스트페이지에서 다운받은 엑셀파일로 업로드해주세요 !</p>';
            html += '           </td>';
            html += '       </tr>';

            html += '       </tbody>';
            html += '       </table>';
            html += '       <p style="text-align: center;"><input type="submit" value="저장" class="btn btn-primary" style="width: 300px;margin: 0 auto;" ></p> ';
            html += '   </div>';
            html += '   </div>';
            html += '</form>';

        var container = $('<div>');

        $(container).html(html);
        modalPop.createPop(div_text + ' 당첨자 등록', container);
        modalPop.createCloseButton('닫기', 'btn btn-default btn-sm');
        modalPop.show({'dialog_class':'modal-lg', 'backdrop':'static'});

    }
    $(document).on('click','.join_info_chk_frm',function(){
        file_download('<?=base64_encode(urlencode('/Archive/참여자정보없음_당첨자_샘플.xls'))?>');
    });

    $(document).on('change','input[name="join_info_chk"]',function(){
        if($('.join_info_chk_frm').length > 0){

            if( $(this).val() == 'Y' ){
                $('.join_info_chk_frm').hide();
                $('.list_download_noti').show();
            }else{
                $('.join_info_chk_frm').show();
                $('.list_download_noti').hide();
            }
        }
    });

    /* 팝업창 오픈후 처리*/
    $(document).on('shown.bs.modal', function(){

        $('#regist_winner').ajaxForm({
            url : '/event_active/setWinner',
            type: 'post',
            dataType : 'json',
            success: function(result) {
                alert(result.msg + '[ 중복 : '+result.overlap_cnt+'건 ]');
                if(result.success == true){
                    modalPop.hide();
                    $('#search_form').submit();
                }
            }
        });//end of ajax_form()

        $('select[name="regist_winner_item"]').select2({
            placeholder: "선택해주세요",
            minimumResultsForSearch : -1,
            ajax: {
                url: '/event_active/getWinnerItem/',
                data: {div_val: $('form[name="search_form"] select[name="div"]').val()} ,
                dataType: 'json',
                processResults: function (jResult) {

                    if(jResult.status == 200){
                        alert(jResult.message);
                        return { results: [] };
                    }else{
                        var aData   = jResult.data;
                        var data    = '';
                        for (var i in aData) {
                            data  += '{id:"'+i+'" , text:"'+aData[i]+'"},'  ;
                        }
                        data = eval('['+data+']');
                        return { results: data };
                    }

                }
            }
        });
    });

    /*당첨자 등록 Script End */

</script>