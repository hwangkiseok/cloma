$(document).ready(function() {

    // ckeditor user config
    CKEDITOR.replace('p_detail', {
        filebrowserImageUploadUrl: window.location.protocol + "//" + window.location.host + '/ckeditor/uploads',
        height: 200,
        removeDialogTabs: 'image:advanced;link:advanced',
        extraPlugins: 'colorbutton,colordialog,justify,youtube',
    } );

    CKEDITOR.config.image_previewText = ' '; // file manager 이미지창 기본텍스트 비우기
    CKEDITOR.config.image_prefillDimensions = false; // file manager 이미지 너비, 높이 없애기(크기 픽스가 되면 모바일에서 깨짐)

    //CKEDITOR.config.enterMode = CKEDITOR.ENTER_P;

    CKEDITOR.on('instanceReady', function(event) {
        if (event.editor.status != 'ready') {
            alert('HTML 에디터가 로딩되지 않았습니다... \n확인 후, 페이지 새로고침을 하여 에디터를 로딩 주세요.');
            return false;
        }
    });
});