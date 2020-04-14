<style>
    #join_form {padding: 10px}
    #join_form p{font-size: 20px;font-weight: bold;padding: 10px 0}
    #join_form label{font-size: 16px;padding: 20px 0 10px 0;display: inline-block}
    #join_form table tr{ border-bottom: 1px solid #ddd;}
    #join_form table tr>* {padding: 10px 0}
    #join_form table tr th{width: 20%}
    #join_form table tr td input{padding: 5px}
    #join_form table tr td select{padding: 5px }
</style>

<form name="join_form" id="join_form" method="post" action="/Auth/join_proc_2">
    <p>부가정보 확인</p>

    <p style="background-color: #fcf8e3;padding: 15px;color: #333;font-size: 12px;">개인정보가 변경된 경우 수정 후 저장해주세요.</p>

    <table>
        <tr>
            <th>연락처</th>
            <td>
                <input type="number" name="cell_tel" style="width: 100%">
            </td>
        </tr>

        <tr>
            <th>생일</th>
            <td>
                <select name="birth_m">
                    <? for ($i = 1; $i < 13 ; $i++) {?>
                        <option value="<?=sprintf("%02d", $i);?>"><?=sprintf("%02d", $i);?></option>
                    <? } ?>
                </select>&nbsp;월&nbsp;&nbsp;
                <select name="birth_d">
                    <? for ($i = 1; $i < 31 ; $i++) {?>
                        <option value="<?=sprintf("%02d", $i);?>"><?=sprintf("%02d", $i);?></option>
                    <? } ?>
                </select>&nbsp;일
            </td>
        </tr>
        <tr>
            <th>연령대</th>
            <td>
                <select name="age_range">
                    <? for ($i = 1; $i < 7 ; $i++) {?>
                        <option value="<?=$i?>0"><?=$i?>0대</option>
                    <? } ?>
                </select>
            </td>
        </tr>

        <tr>
            <th>성별</th>
            <td>
                <select name="gender">
                    <option value="F">여성</option>
                    <option value="M">남성</option>
                </select>
            </td>
        </tr>

        <tr>
            <th>배송지정보</th>
            <td>
                <input type="text" id="zip_code" name="zip_code" style="width: 20%;margin-bottom: 5px;" readonly>&nbsp;&nbsp;<button class="btn btn-border-blue" style="padding: 6px;" onclick="execDaumPostcode();" type="button">주소검색</button><br>
                <input type="text" id="addr1" name="addr1" style="width: 100%;margin-bottom: 5px;" readonly><br>
                <input type="text" id="addr2" name="addr2" style="width: 100%">
            </td>
        </tr>

    </table>

    <p style="margin: 0 -10px;text-align: center; ">
        <button class="btn btn-danger" type="submit" style="">수정완료</button>
    </p>


</form>

<div id="layer" style="display:none;position:fixed;overflow:hidden;z-index:1;-webkit-overflow-scrolling:touch;">
    <img src="//t1.daumcdn.net/postcode/resource/images/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px;z-index:1" onclick="closeDaumPostcode()" alt="닫기 버튼">
</div>
<script src="http://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>

<script type="text/javascript">

    $(function(){

        $('#join_form').on('submit', function(){

            // if($('input[name="cell_tel"]').val() == ''){
            //     alert('연락처를 입력해주세요');
            //     return false;
            // }
            //
            //
            // if($('input[name="addr2"]').val() == ''){
            //     alert('상세주소를 입력해주세요');
            //     return false;
            // }

        });

        $('#join_form').ajaxForm({
            type: 'post',
            dataType: 'json',
            success: function(result) {
                showToast('처리완료');
            }

        });

    });


    //-------------------------------------- daum post js

    // 우편번호 찾기 화면을 넣을 element
    var element_layer = document.getElementById('layer');

    function closeDaumPostcode() {
        // iframe을 넣은 element를 안보이게 한다.
        element_layer.style.display = 'none';
    }

    function execDaumPostcode() {
        new daum.Postcode({
            oncomplete: function(data) {
                // 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

                // 각 주소의 노출 규칙에 따라 주소를 조합한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                var addr = ''; // 주소 변수
                var extraAddr = ''; // 참고항목 변수

                //사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
                if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                    addr = data.roadAddress;
                } else { // 사용자가 지번 주소를 선택했을 경우(J)
                    addr = data.jibunAddress;
                }

                // 사용자가 선택한 주소가 도로명 타입일때 참고항목을 조합한다.
                if(data.userSelectedType === 'R'){
                    // 법정동명이 있을 경우 추가한다. (법정리는 제외)
                    // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
                    if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
                        extraAddr += data.bname;
                    }
                    // 건물명이 있고, 공동주택일 경우 추가한다.
                    if(data.buildingName !== '' && data.apartment === 'Y'){
                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }
                    // 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
                    if(extraAddr !== ''){
                        extraAddr = ' (' + extraAddr + ')';
                    }
                    // 조합된 참고항목을 해당 필드에 넣는다.
                    // document.getElementById("sample2_extraAddress").value = extraAddr;

                } else {
                    // document.getElementById("sample2_extraAddress").value = '';
                }

                // 우편번호와 주소 정보를 해당 필드에 넣는다.
                document.getElementById('zip_code').value = data.zonecode;
                document.getElementById("addr1").value = addr;
                // 커서를 상세주소 필드로 이동한다.
                document.getElementById("addr2").focus();

                // iframe을 넣은 element를 안보이게 한다.
                // (autoClose:false 기능을 이용한다면, 아래 코드를 제거해야 화면에서 사라지지 않는다.)
                element_layer.style.display = 'none';
            },
            width : '100%',
            height : '100%',
            maxSuggestItems : 5
        }).embed(element_layer);

        // iframe을 넣은 element를 보이게 한다.
        element_layer.style.display = 'block';

        // iframe을 넣은 element의 위치를 화면의 가운데로 이동시킨다.
        initLayerPosition();
    }

    // 브라우저의 크기 변경에 따라 레이어를 가운데로 이동시키고자 하실때에는
    // resize이벤트나, orientationchange이벤트를 이용하여 값이 변경될때마다 아래 함수를 실행 시켜 주시거나,
    // 직접 element_layer의 top,left값을 수정해 주시면 됩니다.
    function initLayerPosition(){
        var width = 300; //우편번호서비스가 들어갈 element의 width
        var height = 400; //우편번호서비스가 들어갈 element의 height
        var borderWidth = 5; //샘플에서 사용하는 border의 두께

        // 위에서 선언한 값들을 실제 element에 넣는다.
        element_layer.style.width = width + 'px';
        element_layer.style.height = height + 'px';
        element_layer.style.border = borderWidth + 'px solid';
        // 실행되는 순간의 화면 너비와 높이 값을 가져와서 중앙에 뜰 수 있도록 위치를 계산한다.
        element_layer.style.left = (((window.innerWidth || document.documentElement.clientWidth) - width)/2 - borderWidth) + 'px';
        element_layer.style.top = (((window.innerHeight || document.documentElement.clientHeight) - height)/2 - borderWidth) + 'px';
    }

    //-------------------------------------- daum post js END
</script>
