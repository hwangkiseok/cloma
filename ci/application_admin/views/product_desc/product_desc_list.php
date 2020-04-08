
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js" ></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css"/>

<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">상품관리 > 첨부이미지관리</h4>
    </div>

    <!--  이미지 업로드  -->
    <div class="row">
        <form name="img_form" id="img_form" method="post" enctype="multipart/form-data"  action="/Product_desc/ImgUpload/" class="form-horizontal" onsubmit="return ImgSave();">
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="kwd">이미지선택</label>
                <div class="col-sm-10">
                    <div class="form-group">
                        <div class="col-sm-5 col-xs-7">
                            <div class="input-group">
                                <div class="input-group-addon">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                                <input type="text" name="TEXT_FILE1" class="form-control" value="첨부파일 없음" readonly="readonly">
                            </div>
                        </div>

                        <div class="col-sm-5 col-xs-5">
                            <select name="gubun" style="width: 100px ">
                                <option value="">선택</option>
                                <option value="1">상단</option>
                                <option value="2">하단</option>
                            </select>
                            <label for="FILE1" class="btn btn-warning" style="padding: 4px 12px;">추가하기</label>
                            <input type="file" id="FILE1" name="FILE1" class="file">
                            <input type="submit"  class="btn btn-success" value="이미지 저장" style="padding: 4px 12px;">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!--  이미지 리스트  -->
    <p class="alert alert-info h3">상단</p>
    <div class="row ajaxLists-Top">

    </div>

    <p class="alert alert-success h3">하단</p>
    <div class="row ajaxLists-Btm">

    </div>
</div>

<style>
    .file{position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip:rect(0,0,0,0); border: 0;}
</style>

<script>

    function ImgSave(){

        if($('#FILE1').val() == ''){
            alert('이미지를 선택 후 저장해주세요 !');
            return false;
        }

        obj = $('#FILE1');
        size_mb = (obj[0]['files'][0]['size'] / 1024) / 1024;
        if(size_mb >= 10){
            alert('첩부파일은 10메가 미만 파일만 업로드가 가능합니다.');
            obj.val('');
            return false;
        }

        if($('select[name="gubun"]').val() == ''){
            alert('이미지 위치를 선택해주세요 !');
            return false;
        }

    }

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

    function listCall(){

        Pace.restart();

        $.ajax({
            url : '/Product_desc/product_desc_list_ajax/',
            type : 'post',
            dataType : 'json',
            success : function (result) {

                var html_no_img = '';
                var html_top = '';
                var html_btm = '';


                html_no_img += '<div class="col-md-offset-1 col-md-10">';
                html_no_img += '   <div class="alert alert-danger" role="alert">';
                html_no_img += '       등록된 이미지가 없습니다.';
                html_no_img += '   </div>';
                html_no_img += '</div>';


                var top = 0;
                var btm = 0;

                for(var i = 0 ; i < result.data.length ; i++){

                    if(result.data[i].gubun == '1'){

                        html_top += '<div class="col-md-2">';
                        html_top += '   <div class="thumbnail">';
                        html_top += '       <img src="'+result.data[i].url+'">';
                        html_top += '       <div class="caption text-center">';
                        html_top += '           <h5>'+result.data[i].org_name+'</h5>';
                        html_top += '           <p>';


                        if(result.data[i].default_flag == 'Y'){
                        html_top += '               <a href="#" class="btn btn-success default-Img" data="'+result.data[i].p_desc+'" role="button"><i class="glyphicon glyphicon-ok"></i> 기본</a>';
                        }else{
                        html_top += '               <a href="#" class="btn btn-warning default-Img" data="'+result.data[i].p_desc+'" role="button">기본아님</a>';
                        }
                        html_top += '               <a href="#" class="btn btn-danger del-Img" data="'+result.data[i].p_desc+'" path="'+result.data[i].url+'" role="button">삭제</a>';
                        html_top += '           </p>';
                        html_top += '       </div>';
                        html_top += '   </div>';
                        html_top += '</div>';
                        top++;

                    }else{

                        html_btm += '<div class="col-md-2">';
                        html_btm += '   <div class="thumbnail">';
                        html_btm += '       <img src="'+result.data[i].url+'">';
                        html_btm += '       <div class="caption text-center">';
                        html_btm += '           <h5>'+result.data[i].org_name+'</h5>';
                        html_btm += '           <p>';
                        if(result.data[i].open_flag == 'Y'){
                            html_btm += '               <a href="#" class="btn btn-success chk-Img" data="'+result.data[i].p_desc+'" role="button">공개</a>';
                        }else{
                            html_btm += '               <a href="#" class="btn btn-warning chk-Img" data="'+result.data[i].p_desc+'" role="button">비공개</a>';
                        }
                        html_btm += '               <a href="#" class="btn btn-danger del-Img" data="'+result.data[i].p_desc+'" path="'+result.data[i].url+'" role="button">삭제</a>';
                        html_btm += '           </p>';
                        html_btm += '       </div>';
                        html_btm += '   </div>';
                        html_btm += '</div>';
                        btm++;

                    }

                }

                if(top > 0){
                    $('.ajaxLists-Top').html(html_top);
                }else{
                    $('.ajaxLists-Top').html(html_no_img);
                }

                if(btm > 0){
                    $('.ajaxLists-Btm').html(html_btm);
                }else{

                    $('.ajaxLists-Btm').html(html_no_img);
                }

            },
            complete : function() {
                Pace.stop();
            }
        });

    }

    $(document).on('click','.chk-Img',function(){

        var open_flag = 'Y'
        if($(this).hasClass('btn-success')){
            open_flag = 'N';
        }

        var cf = confirm('해당 이미지 상태를 변경 하시겠습니까 ?');
        if(!cf) return false;

        obj = {p_desc : $(this).attr('data') , open_flag : open_flag};

        $.ajax({
            url : '/Product_desc/img_check/',
            type : 'post',
            data : obj ,
            dataType : 'json',
            success : function (result) {
                if(result.status == status_code['success']){ //성공
                    listCall();
                }else{ //실패
                    alert(result.message);
                    return false;
                }
            }
        });


    });


    $(document).on('click','.del-Img',function(){

        var cf = confirm('해당 이미지를 삭제하시겠습니까 ?');
        if(!cf) return false;

        obj = {p_desc : $(this).attr('data') , path : $(this).attr('path')};

        $.ajax({
            url : '/Product_desc/img_delete/',
            type : 'post',
            data : obj ,
            dataType : 'json',
            success : function (result) {
                if(result.status == status_code['success']){ //성공
                    listCall();
                }else{ //실패
                    alert(result.message);
                    return false;
                }
            }
        });

    });

    $(document).on('click','.default-Img',function(){

        var default_flag = 'Y'
        if($(this).hasClass('btn-success')){
            default_flag = 'N';
        }

        var cf = confirm('해당 이미지를 기본 이미지로 설정하시겠습니까 ?');
        if(!cf) return false;

        obj = {p_desc : $(this).attr('data') , default_flag : default_flag };

        $.ajax({
            url : '/Product_desc/img_default/',
            type : 'post',
            data : obj ,
            dataType : 'json',
            success : function (result) {
                if(result.status == status_code['success']){ //성공
                    listCall();
                }else{ //실패
                    alert(result.message);
                    return false;
                }
            }
        });

    });


    $(document).ready(function(){

        listCall();

        $('select[name="gubun"]').select2({ minimumResultsForSearch: Infinity });

        $('#img_form').ajaxForm({
            type : 'post',
            dataType : 'json',
            success: function(result){
                if(result.status == status_code['success']){ //성공
                    listCall();
                    $("form").each(function() { //폼셋 초기화
                        if(this.id == "img_form") this.reset();
                    });
                }else{ //실패
                    alert(result.message);
                    return false;
                }
            }
        });

        $('input[type="file"]').on('change',function(){

            var id		= $(this).attr('id');
            var bRst 	= FileChk($(this));

            if(!bRst){
                return false;
            }

            //-- 아이콘 변경
            var obj = $(this);
            var mime_type = obj[0]['files'][0]['type'];
            var type = mime_type.split('/');

            if(type[0] == 'image'){
                $(this).parent().parent().find('div .input-group .input-group-addon').html('<i class="fa fa-file-image-o" style="font-size:16px;"></i>');
            }else if(type[0] == 'application'){
                //$(this).parent().parent().find('div .input-group .input-group-addon').html('<i class="fa fa-file-excel-o" style="font-size:16px;"></i>');
                alert('지원하지 않는 파일 형식입니다.');
                return false;
            }else if(type[0] == 'text'){
                //$(this).parent().parent().find('div .input-group .input-group-addon').html('<i class="fa fa-file-text-o" style="font-size:16px;"></i>');
                alert('지원하지 않는 파일 형식입니다.');
                return false;
            }else{
                alert('지원하지 않는 파일 형식입니다.');
                return false;
            }
            //-- 텍스트 변경
            var fileValue = $(this).val().split("\\");
            var fileName = fileValue[fileValue.length-1]; // 파일명
            $('input[name="TEXT_'+id+'"]').val(fileName);
        });

    });

</script>