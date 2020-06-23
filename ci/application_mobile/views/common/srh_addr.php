<div class="overlay"></div>

<div class="cont">

    <header> 주소검색 </header>

    <article class="result_arti">
        <div class="pop-block">
            <label>이름</label>
            <input type="text" name="pop_name" placeholder="이름을 입력해 주세요." value="" />
        </div>

        <div class="pop-block addr" style="position: relative;">
            <label>주소</label>
            <input type="hidden" name="zipcd" value="" />
            <input type="hidden" name="pop_jibun" value="" />
            <input type="text" name="pop_road" placeholder="도로명, 건물명, 번지 입력 검색" value="" readonly style="color: #999;"  />
            <input type="text" name="pop_road_detail" placeholder="상세주소를 입력하세요" value="" style="margin-top: 4px;display: none;"  />
            <i class="go_srh_view"></i>
        </div>

        <div class="pop-block">
            <label>전화번호</label>
            <input type="number" name="pop_ph" placeholder="전화번호를 입력해주세요." value="" />
        </div>

        <div class="btn_area">
            <button class="deactive submit_btn">적용</button>
            <button class="close_pop cancel_btn">취소</button>
            <div class="clear"></div>
        </div>
    </article>

    <article class="srh_arti">

        <div class="pop-block addr" style="position: relative;">
            <input type="text" name="srh_addr" placeholder="도로명, 건물명, 번지 입력 검색" value=""/>
            <i class="go_srh"></i>
        </div>

        <div class="pop-block srh_noti">
            <label>우편번호 검색 방법</label>
            <p>도로명+건물번호 (예:강남대로 221)</p>
            <p>동/읍/명/리 + 번지 (예:역삼동 15-10)</p>
            <p>건물명 또는 아파트명 (예:반포 자이아파트)</p>
        </div>

        <div class="pop-block srh_result">
            <ul class="addr_list"></ul>
            <div class="srch_addr_pagination"></div>
        </div>

        <div class="btn_area">
            <button class="cancel_btn">취소</button>
            <div class="clear"></div>
        </div>

    </article>

</div>

<script> var juso_key = '<?=$this->config->item('juso_key')?>'; </script>
<script src="/js/page/search_addr.js" type="text/javascript"></script>