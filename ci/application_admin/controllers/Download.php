<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Download extends A_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $req['f'] = trim($this->input->post_get("f", true));    //파일경로 (HOMEPATH를 제외한 나머지 경로)
        $req['m'] = trim($this->input->post_get("m", true));    //mode(view=보기(이미지), down=다운로드)
        $req['n'] = trim($this->input->post_get("n", true));    //파일명

        $filepath = HOMEPATH . $req['f'];
        $filesize = filesize($filepath);

        if($req['n']){
            $filename =  mb_basename($filepath);
            $ext = explode('.',$filename)[1];
            $filename = $req['n'].'.'.$ext;
        }else{
            $filename =  mb_basename($filepath);
        }

        if( is_ie() ) {
            $filename = utf2euc($filename);
        }

        header("Pragma: public");
        header("Expires: 0");
        header("Content-Length: $filesize");
        //이미지파일이고 보기일때
        if( is_image($filepath) && $req['m'] == "view" ) {
            $img_info = getimagesize($filepath);
            header("Content-Type: " . $img_info['mime']);
        }
        else {
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Content-Type: application/octet-stream");
            header("Content-Transfer-Encoding: binary");
        }

        readfile($filepath);
    }//end of index()

    /**
     * 이미지로 출력
     */
    public function download_img() {
        $req['f'] = trim($this->input->post_get("f", true));    //파일경로 (HOMEPATH를 제외한 나머지 경로)

        $filepath = HOMEPATH . $req['f'];
        $img_data = file_get_contents($filepath);
        echo '<img src="data:image/jpeg;base64,' . base64_encode($img_data) . '" alt="" />';
    }//end of downlaod_img()

}//end of class Download