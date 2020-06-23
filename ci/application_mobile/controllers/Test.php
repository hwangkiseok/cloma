<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 테스트용
 */
class Test extends M_Controller
{

    public $debug = true;
    public $curr_time;
    public $curr_time_i;
    public $curr_time_h;

    public function __construct()
    {
        parent::__construct();

        error_reporting(-1);
        ini_set('display_errors', 1);
        error_reporting(E_ALL & ~E_NOTICE);

        $allow_ip_array = array(
            "112.146.73.238" //사무실
        ,   "106.243.140.135"   //마포
        );

        if( array_search($this->input->ip_address(), $allow_ip_array) === false ) {
            redirect('/');
        }

    }//end of __construct()

    public function login()
    {

        $m_num = $this->uri->segment(3);

        if( empty($m_num) ) {
            exit;
        }

        $query = "select * from member_tb where m_num = '" . $this->db->escape_str($m_num) . "'";
        $member_row = $this->db->query($query)->row_array();

        if( empty($member_row) ) {
            exit;
        }

        set_login_session($member_row);

        alert("OK", "/");

    }//end of login()

    public function send(){

        $this->load->library('Snoopy');

        $url = "https://m.cloma.co.kr/test/login/55";

        $params = array();

        $this->snoopy->submit($url,$params);

        $this->snoopy->setcookies();

        $uri = 'https://m.cloma.co.kr/delivery';

        $this->snoopy->fetch($uri);
        zsView($this->snoopy->results);
        exit;
// https://api-console.adbrix.io/api/v1/User/Login
    }


    public function chk_json(){

exit;
        $this->load->model('member_model');

        $sql = "SELECT buyer_hhp,buyer_name,partner_buyer_id FROM snsform_order_tb WHERE trade_no <> '' AND partner_buyer_id <> ''; ";
        $aResult = $this->db->query($sql)->result_array();



        echo get_short_url(create_dynamic_url('https://m.cloma.co.kr','intro','intro'));

        exit;

        //SELECT * FROM `special_offer_tb`
        $aRet = $this->db->query('SELECT * FROM special_offer_tb ')->result_array();

        foreach ($aRet as $k => $r) {
            //zsView($r['thema_name'].' : '.create_dynamic_url('https://m.cloma.co.kr/exhibition/list?seq='.$r['seq'],'',''));
        }


//        $str = '[{"option_supply":3700,"option_count":50,"option_depth1":"드로잉 홑이불","option_price":8900,"option_depth2":"드로잉 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"0"},{"option_supply":3700,"option_count":50,"option_depth1":"드로잉 홑이불","option_price":8900,"option_depth2":"덴들라이 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"1"},{"option_supply":3700,"option_count":50,"option_depth1":"드로잉 홑이불","option_price":8900,"option_depth2":"레인보우 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"2"},{"option_supply":3700,"option_count":50,"option_depth1":"드로잉 홑이불","option_price":8900,"option_depth2":"라인 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"3"},{"option_supply":3700,"option_count":50,"option_depth1":"드로잉 홑이불","option_price":8900,"option_depth2":"라벤더 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"4"},{"option_supply":3700,"option_count":50,"option_depth1":"드로잉 홑이불","option_price":8900,"option_depth2":"브로썸 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"5"},{"option_supply":3700,"option_count":50,"option_depth1":"드로잉 홑이불","option_price":8900,"option_depth2":"블루링고 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"6"},{"option_supply":3700,"option_count":50,"option_depth1":"드로잉 홑이불","option_price":8900,"option_depth2":"올리아핑크 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"7"},{"option_supply":3700,"option_count":50,"option_depth1":"드로잉 홑이불","option_price":8900,"option_depth2":"아카리샤 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"8"},{"option_supply":3700,"option_count":50,"option_depth1":"드로잉 홑이불","option_price":8900,"option_depth2":"원터플라워 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"9"},{"option_supply":3700,"option_count":50,"option_depth1":"드로잉 홑이불","option_price":8900,"option_depth2":"코렐 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"10"},{"option_supply":3700,"option_count":50,"option_depth1":"드로잉 홑이불","option_price":8900,"option_depth2":"테스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"11"},{"option_supply":3700,"option_count":50,"option_depth1":"드로잉 홑이불","option_price":8900,"option_depth2":"피그먼트 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"12"},{"option_supply":3700,"option_count":50,"option_depth1":"드로잉 홑이불","option_price":8900,"option_depth2":"히아신스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"13"},{"option_supply":3700,"option_count":50,"option_depth1":"드로잉 홑이불","option_price":8900,"option_depth2":"해피트리 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"14"},{"option_supply":3700,"option_count":50,"option_depth1":"덴들라이 홑이불","option_price":8900,"option_depth2":"드로잉 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"15"},{"option_supply":3700,"option_count":50,"option_depth1":"덴들라이 홑이불","option_price":8900,"option_depth2":"덴들라이 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"16"},{"option_supply":3700,"option_count":50,"option_depth1":"덴들라이 홑이불","option_price":8900,"option_depth2":"레인보우 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"17"},{"option_supply":3700,"option_count":50,"option_depth1":"덴들라이 홑이불","option_price":8900,"option_depth2":"라인 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"18"},{"option_supply":3700,"option_count":50,"option_depth1":"덴들라이 홑이불","option_price":8900,"option_depth2":"라벤더 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"19"},{"option_supply":3700,"option_count":50,"option_depth1":"덴들라이 홑이불","option_price":8900,"option_depth2":"브로썸 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"20"},{"option_supply":3700,"option_count":50,"option_depth1":"덴들라이 홑이불","option_price":8900,"option_depth2":"블루링고 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"21"},{"option_supply":3700,"option_count":49,"option_depth1":"덴들라이 홑이불","option_price":8900,"option_depth2":"올리아핑크 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"22"},{"option_supply":3700,"option_count":50,"option_depth1":"덴들라이 홑이불","option_price":8900,"option_depth2":"아카리샤 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"23"},{"option_supply":3700,"option_count":50,"option_depth1":"덴들라이 홑이불","option_price":8900,"option_depth2":"원터플라워 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"24"},{"option_supply":3700,"option_count":50,"option_depth1":"덴들라이 홑이불","option_price":8900,"option_depth2":"코렐 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"25"},{"option_supply":3700,"option_count":50,"option_depth1":"덴들라이 홑이불","option_price":8900,"option_depth2":"테스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"26"},{"option_supply":3700,"option_count":50,"option_depth1":"덴들라이 홑이불","option_price":8900,"option_depth2":"피그먼트 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"27"},{"option_supply":3700,"option_count":50,"option_depth1":"덴들라이 홑이불","option_price":8900,"option_depth2":"히아신스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"28"},{"option_supply":3700,"option_count":50,"option_depth1":"덴들라이 홑이불","option_price":8900,"option_depth2":"해피트리 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"29"},{"option_supply":3700,"option_count":50,"option_depth1":"레인보우 홑이불","option_price":8900,"option_depth2":"드로잉 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"30"},{"option_supply":3700,"option_count":50,"option_depth1":"레인보우 홑이불","option_price":8900,"option_depth2":"덴들라이 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"31"},{"option_supply":3700,"option_count":50,"option_depth1":"레인보우 홑이불","option_price":8900,"option_depth2":"레인보우 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"32"},{"option_supply":3700,"option_count":50,"option_depth1":"레인보우 홑이불","option_price":8900,"option_depth2":"라인 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"33"},{"option_supply":3700,"option_count":50,"option_depth1":"레인보우 홑이불","option_price":8900,"option_depth2":"라벤더 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"34"},{"option_supply":3700,"option_count":50,"option_depth1":"레인보우 홑이불","option_price":8900,"option_depth2":"브로썸 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"35"},{"option_supply":3700,"option_count":50,"option_depth1":"레인보우 홑이불","option_price":8900,"option_depth2":"블루링고 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"36"},{"option_supply":3700,"option_count":50,"option_depth1":"레인보우 홑이불","option_price":8900,"option_depth2":"올리아핑크 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"37"},{"option_supply":3700,"option_count":50,"option_depth1":"레인보우 홑이불","option_price":8900,"option_depth2":"아카리샤 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"38"},{"option_supply":3700,"option_count":50,"option_depth1":"레인보우 홑이불","option_price":8900,"option_depth2":"원터플라워 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"39"},{"option_supply":3700,"option_count":50,"option_depth1":"레인보우 홑이불","option_price":8900,"option_depth2":"코렐 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"40"},{"option_supply":3700,"option_count":50,"option_depth1":"레인보우 홑이불","option_price":8900,"option_depth2":"테스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"41"},{"option_supply":3700,"option_count":50,"option_depth1":"레인보우 홑이불","option_price":8900,"option_depth2":"피그먼트 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"42"},{"option_supply":3700,"option_count":50,"option_depth1":"레인보우 홑이불","option_price":8900,"option_depth2":"히아신스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"43"},{"option_supply":3700,"option_count":50,"option_depth1":"레인보우 홑이불","option_price":8900,"option_depth2":"해피트리 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"44"},{"option_supply":3700,"option_count":50,"option_depth1":"라인 홑이불","option_price":8900,"option_depth2":"드로잉 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"45"},{"option_supply":3700,"option_count":50,"option_depth1":"라인 홑이불","option_price":8900,"option_depth2":"덴들라이 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"46"},{"option_supply":3700,"option_count":50,"option_depth1":"라인 홑이불","option_price":8900,"option_depth2":"레인보우 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"47"},{"option_supply":3700,"option_count":50,"option_depth1":"라인 홑이불","option_price":8900,"option_depth2":"라인 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"48"},{"option_supply":3700,"option_count":50,"option_depth1":"라인 홑이불","option_price":8900,"option_depth2":"라벤더 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"49"},{"option_supply":3700,"option_count":50,"option_depth1":"라인 홑이불","option_price":8900,"option_depth2":"브로썸 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"50"},{"option_supply":3700,"option_count":50,"option_depth1":"라인 홑이불","option_price":8900,"option_depth2":"블루링고 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"51"},{"option_supply":3700,"option_count":50,"option_depth1":"라인 홑이불","option_price":8900,"option_depth2":"올리아핑크 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"52"},{"option_supply":3700,"option_count":50,"option_depth1":"라인 홑이불","option_price":8900,"option_depth2":"아카리샤 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"53"},{"option_supply":3700,"option_count":50,"option_depth1":"라인 홑이불","option_price":8900,"option_depth2":"원터플라워 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"54"},{"option_supply":3700,"option_count":50,"option_depth1":"라인 홑이불","option_price":8900,"option_depth2":"코렐 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"55"},{"option_supply":3700,"option_count":50,"option_depth1":"라인 홑이불","option_price":8900,"option_depth2":"테스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"56"},{"option_supply":3700,"option_count":50,"option_depth1":"라인 홑이불","option_price":8900,"option_depth2":"피그먼트 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"57"},{"option_supply":3700,"option_count":50,"option_depth1":"라인 홑이불","option_price":8900,"option_depth2":"히아신스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"58"},{"option_supply":3700,"option_count":50,"option_depth1":"라인 홑이불","option_price":8900,"option_depth2":"해피트리 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"59"},{"option_supply":3700,"option_count":50,"option_depth1":"라벤더 홑이불","option_price":8900,"option_depth2":"드로잉 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"60"},{"option_supply":3700,"option_count":50,"option_depth1":"라벤더 홑이불","option_price":8900,"option_depth2":"덴들라이 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"61"},{"option_supply":3700,"option_count":50,"option_depth1":"라벤더 홑이불","option_price":8900,"option_depth2":"레인보우 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"62"},{"option_supply":3700,"option_count":50,"option_depth1":"라벤더 홑이불","option_price":8900,"option_depth2":"라인 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"63"},{"option_supply":3700,"option_count":50,"option_depth1":"라벤더 홑이불","option_price":8900,"option_depth2":"라벤더 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"64"},{"option_supply":3700,"option_count":50,"option_depth1":"라벤더 홑이불","option_price":8900,"option_depth2":"브로썸 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"65"},{"option_supply":3700,"option_count":50,"option_depth1":"라벤더 홑이불","option_price":8900,"option_depth2":"블루링고 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"66"},{"option_supply":3700,"option_count":50,"option_depth1":"라벤더 홑이불","option_price":8900,"option_depth2":"올리아핑크 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"67"},{"option_supply":3700,"option_count":50,"option_depth1":"라벤더 홑이불","option_price":8900,"option_depth2":"아카리샤 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"68"},{"option_supply":3700,"option_count":50,"option_depth1":"라벤더 홑이불","option_price":8900,"option_depth2":"원터플라워 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"69"},{"option_supply":3700,"option_count":50,"option_depth1":"라벤더 홑이불","option_price":8900,"option_depth2":"코렐 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"70"},{"option_supply":3700,"option_count":50,"option_depth1":"라벤더 홑이불","option_price":8900,"option_depth2":"테스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"71"},{"option_supply":3700,"option_count":50,"option_depth1":"라벤더 홑이불","option_price":8900,"option_depth2":"피그먼트 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"72"},{"option_supply":3700,"option_count":50,"option_depth1":"라벤더 홑이불","option_price":8900,"option_depth2":"히아신스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"73"},{"option_supply":3700,"option_count":50,"option_depth1":"라벤더 홑이불","option_price":8900,"option_depth2":"해피트리 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"74"},{"option_supply":3700,"option_count":50,"option_depth1":"브로썸 홑이불","option_price":8900,"option_depth2":"드로잉 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"75"},{"option_supply":3700,"option_count":50,"option_depth1":"브로썸 홑이불","option_price":8900,"option_depth2":"덴들라이 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"76"},{"option_supply":3700,"option_count":50,"option_depth1":"브로썸 홑이불","option_price":8900,"option_depth2":"레인보우 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"77"},{"option_supply":3700,"option_count":50,"option_depth1":"브로썸 홑이불","option_price":8900,"option_depth2":"라인 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"78"},{"option_supply":3700,"option_count":50,"option_depth1":"브로썸 홑이불","option_price":8900,"option_depth2":"라벤더 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"79"},{"option_supply":3700,"option_count":50,"option_depth1":"브로썸 홑이불","option_price":8900,"option_depth2":"브로썸 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"80"},{"option_supply":3700,"option_count":50,"option_depth1":"브로썸 홑이불","option_price":8900,"option_depth2":"블루링고 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"81"},{"option_supply":3700,"option_count":50,"option_depth1":"브로썸 홑이불","option_price":8900,"option_depth2":"올리아핑크 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"82"},{"option_supply":3700,"option_count":50,"option_depth1":"브로썸 홑이불","option_price":8900,"option_depth2":"아카리샤 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"83"},{"option_supply":3700,"option_count":50,"option_depth1":"브로썸 홑이불","option_price":8900,"option_depth2":"원터플라워 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"84"},{"option_supply":3700,"option_count":50,"option_depth1":"브로썸 홑이불","option_price":8900,"option_depth2":"코렐 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"85"},{"option_supply":3700,"option_count":50,"option_depth1":"브로썸 홑이불","option_price":8900,"option_depth2":"테스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"86"},{"option_supply":3700,"option_count":50,"option_depth1":"브로썸 홑이불","option_price":8900,"option_depth2":"피그먼트 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"87"},{"option_supply":3700,"option_count":50,"option_depth1":"브로썸 홑이불","option_price":8900,"option_depth2":"히아신스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"88"},{"option_supply":3700,"option_count":50,"option_depth1":"브로썸 홑이불","option_price":8900,"option_depth2":"해피트리 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"89"},{"option_supply":3700,"option_count":50,"option_depth1":"블루링고 홑이불","option_price":8900,"option_depth2":"드로잉 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"90"},{"option_supply":3700,"option_count":50,"option_depth1":"블루링고 홑이불","option_price":8900,"option_depth2":"덴들라이 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"91"},{"option_supply":3700,"option_count":50,"option_depth1":"블루링고 홑이불","option_price":8900,"option_depth2":"레인보우 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"92"},{"option_supply":3700,"option_count":50,"option_depth1":"블루링고 홑이불","option_price":8900,"option_depth2":"라인 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"93"},{"option_supply":3700,"option_count":50,"option_depth1":"블루링고 홑이불","option_price":8900,"option_depth2":"라벤더 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"94"},{"option_supply":3700,"option_count":50,"option_depth1":"블루링고 홑이불","option_price":8900,"option_depth2":"브로썸 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"95"},{"option_supply":3700,"option_count":50,"option_depth1":"블루링고 홑이불","option_price":8900,"option_depth2":"블루링고 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"96"},{"option_supply":3700,"option_count":50,"option_depth1":"블루링고 홑이불","option_price":8900,"option_depth2":"올리아핑크 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"97"},{"option_supply":3700,"option_count":50,"option_depth1":"블루링고 홑이불","option_price":8900,"option_depth2":"아카리샤 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"98"},{"option_supply":3700,"option_count":50,"option_depth1":"블루링고 홑이불","option_price":8900,"option_depth2":"원터플라워 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"99"},{"option_supply":3700,"option_count":50,"option_depth1":"블루링고 홑이불","option_price":8900,"option_depth2":"코렐 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"100"},{"option_supply":3700,"option_count":50,"option_depth1":"블루링고 홑이불","option_price":8900,"option_depth2":"테스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"101"},{"option_supply":3700,"option_count":50,"option_depth1":"블루링고 홑이불","option_price":8900,"option_depth2":"피그먼트 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"102"},{"option_supply":3700,"option_count":50,"option_depth1":"블루링고 홑이불","option_price":8900,"option_depth2":"히아신스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"103"},{"option_supply":3700,"option_count":50,"option_depth1":"블루링고 홑이불","option_price":8900,"option_depth2":"해피트리 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"104"},{"option_supply":3700,"option_count":50,"option_depth1":"올리아핑크 홑이불","option_price":8900,"option_depth2":"드로잉 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"105"},{"option_supply":3700,"option_count":50,"option_depth1":"올리아핑크 홑이불","option_price":8900,"option_depth2":"덴들라이 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"106"},{"option_supply":3700,"option_count":50,"option_depth1":"올리아핑크 홑이불","option_price":8900,"option_depth2":"레인보우 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"107"},{"option_supply":3700,"option_count":50,"option_depth1":"올리아핑크 홑이불","option_price":8900,"option_depth2":"라인 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"108"},{"option_supply":3700,"option_count":50,"option_depth1":"올리아핑크 홑이불","option_price":8900,"option_depth2":"라벤더 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"109"},{"option_supply":3700,"option_count":50,"option_depth1":"올리아핑크 홑이불","option_price":8900,"option_depth2":"브로썸 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"110"},{"option_supply":3700,"option_count":50,"option_depth1":"올리아핑크 홑이불","option_price":8900,"option_depth2":"블루링고 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"111"},{"option_supply":3700,"option_count":50,"option_depth1":"올리아핑크 홑이불","option_price":8900,"option_depth2":"올리아핑크 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"112"},{"option_supply":3700,"option_count":50,"option_depth1":"올리아핑크 홑이불","option_price":8900,"option_depth2":"아카리샤 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"113"},{"option_supply":3700,"option_count":50,"option_depth1":"올리아핑크 홑이불","option_price":8900,"option_depth2":"원터플라워 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"114"},{"option_supply":3700,"option_count":50,"option_depth1":"올리아핑크 홑이불","option_price":8900,"option_depth2":"코렐 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"115"},{"option_supply":3700,"option_count":50,"option_depth1":"올리아핑크 홑이불","option_price":8900,"option_depth2":"테스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"116"},{"option_supply":3700,"option_count":50,"option_depth1":"올리아핑크 홑이불","option_price":8900,"option_depth2":"피그먼트 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"117"},{"option_supply":3700,"option_count":50,"option_depth1":"올리아핑크 홑이불","option_price":8900,"option_depth2":"히아신스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"118"},{"option_supply":3700,"option_count":50,"option_depth1":"올리아핑크 홑이불","option_price":8900,"option_depth2":"해피트리 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"119"},{"option_supply":3700,"option_count":50,"option_depth1":"아카리샤 홑이불","option_price":8900,"option_depth2":"드로잉 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"120"},{"option_supply":3700,"option_count":50,"option_depth1":"아카리샤 홑이불","option_price":8900,"option_depth2":"덴들라이 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"121"},{"option_supply":3700,"option_count":50,"option_depth1":"아카리샤 홑이불","option_price":8900,"option_depth2":"레인보우 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"122"},{"option_supply":3700,"option_count":50,"option_depth1":"아카리샤 홑이불","option_price":8900,"option_depth2":"라인 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"123"},{"option_supply":3700,"option_count":50,"option_depth1":"아카리샤 홑이불","option_price":8900,"option_depth2":"라벤더 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"124"},{"option_supply":3700,"option_count":50,"option_depth1":"아카리샤 홑이불","option_price":8900,"option_depth2":"브로썸 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"125"},{"option_supply":3700,"option_count":50,"option_depth1":"아카리샤 홑이불","option_price":8900,"option_depth2":"블루링고 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"126"},{"option_supply":3700,"option_count":50,"option_depth1":"아카리샤 홑이불","option_price":8900,"option_depth2":"올리아핑크 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"127"},{"option_supply":3700,"option_count":50,"option_depth1":"아카리샤 홑이불","option_price":8900,"option_depth2":"아카리샤 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"128"},{"option_supply":3700,"option_count":50,"option_depth1":"아카리샤 홑이불","option_price":8900,"option_depth2":"원터플라워 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"129"},{"option_supply":3700,"option_count":50,"option_depth1":"아카리샤 홑이불","option_price":8900,"option_depth2":"코렐 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"130"},{"option_supply":3700,"option_count":50,"option_depth1":"아카리샤 홑이불","option_price":8900,"option_depth2":"테스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"131"},{"option_supply":3700,"option_count":50,"option_depth1":"아카리샤 홑이불","option_price":8900,"option_depth2":"피그먼트 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"132"},{"option_supply":3700,"option_count":50,"option_depth1":"아카리샤 홑이불","option_price":8900,"option_depth2":"히아신스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"133"},{"option_supply":3700,"option_count":50,"option_depth1":"아카리샤 홑이불","option_price":8900,"option_depth2":"해피트리 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"134"},{"option_supply":3700,"option_count":50,"option_depth1":"원터플라워 홑이불","option_price":8900,"option_depth2":"드로잉 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"135"},{"option_supply":3700,"option_count":50,"option_depth1":"원터플라워 홑이불","option_price":8900,"option_depth2":"덴들라이 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"136"},{"option_supply":3700,"option_count":50,"option_depth1":"원터플라워 홑이불","option_price":8900,"option_depth2":"레인보우 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"137"},{"option_supply":3700,"option_count":50,"option_depth1":"원터플라워 홑이불","option_price":8900,"option_depth2":"라인 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"138"},{"option_supply":3700,"option_count":50,"option_depth1":"원터플라워 홑이불","option_price":8900,"option_depth2":"라벤더 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"139"},{"option_supply":3700,"option_count":50,"option_depth1":"원터플라워 홑이불","option_price":8900,"option_depth2":"브로썸 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"140"},{"option_supply":3700,"option_count":50,"option_depth1":"원터플라워 홑이불","option_price":8900,"option_depth2":"블루링고 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"141"},{"option_supply":3700,"option_count":50,"option_depth1":"원터플라워 홑이불","option_price":8900,"option_depth2":"올리아핑크 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"142"},{"option_supply":3700,"option_count":50,"option_depth1":"원터플라워 홑이불","option_price":8900,"option_depth2":"아카리샤 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"143"},{"option_supply":3700,"option_count":50,"option_depth1":"원터플라워 홑이불","option_price":8900,"option_depth2":"원터플라워 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"144"},{"option_supply":3700,"option_count":50,"option_depth1":"원터플라워 홑이불","option_price":8900,"option_depth2":"코렐 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"145"},{"option_supply":3700,"option_count":50,"option_depth1":"원터플라워 홑이불","option_price":8900,"option_depth2":"테스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"146"},{"option_supply":3700,"option_count":50,"option_depth1":"원터플라워 홑이불","option_price":8900,"option_depth2":"피그먼트 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"147"},{"option_supply":3700,"option_count":50,"option_depth1":"원터플라워 홑이불","option_price":8900,"option_depth2":"히아신스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"148"},{"option_supply":3700,"option_count":50,"option_depth1":"원터플라워 홑이불","option_price":8900,"option_depth2":"해피트리 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"149"},{"option_supply":3700,"option_count":50,"option_depth1":"코렐 홑이불","option_price":8900,"option_depth2":"드로잉 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"150"},{"option_supply":3700,"option_count":50,"option_depth1":"코렐 홑이불","option_price":8900,"option_depth2":"덴들라이 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"151"},{"option_supply":3700,"option_count":50,"option_depth1":"코렐 홑이불","option_price":8900,"option_depth2":"레인보우 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"152"},{"option_supply":3700,"option_count":50,"option_depth1":"코렐 홑이불","option_price":8900,"option_depth2":"라인 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"153"},{"option_supply":3700,"option_count":50,"option_depth1":"코렐 홑이불","option_price":8900,"option_depth2":"라벤더 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"154"},{"option_supply":3700,"option_count":50,"option_depth1":"코렐 홑이불","option_price":8900,"option_depth2":"브로썸 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"155"},{"option_supply":3700,"option_count":50,"option_depth1":"코렐 홑이불","option_price":8900,"option_depth2":"블루링고 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"156"},{"option_supply":3700,"option_count":50,"option_depth1":"코렐 홑이불","option_price":8900,"option_depth2":"올리아핑크 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"157"},{"option_supply":3700,"option_count":50,"option_depth1":"코렐 홑이불","option_price":8900,"option_depth2":"아카리샤 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"158"},{"option_supply":3700,"option_count":50,"option_depth1":"코렐 홑이불","option_price":8900,"option_depth2":"원터플라워 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"159"},{"option_supply":3700,"option_count":50,"option_depth1":"코렐 홑이불","option_price":8900,"option_depth2":"코렐 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"160"},{"option_supply":3700,"option_count":50,"option_depth1":"코렐 홑이불","option_price":8900,"option_depth2":"테스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"161"},{"option_supply":3700,"option_count":50,"option_depth1":"코렐 홑이불","option_price":8900,"option_depth2":"피그먼트 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"162"},{"option_supply":3700,"option_count":50,"option_depth1":"코렐 홑이불","option_price":8900,"option_depth2":"히아신스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"163"},{"option_supply":3700,"option_count":50,"option_depth1":"코렐 홑이불","option_price":8900,"option_depth2":"해피트리 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"164"},{"option_supply":3700,"option_count":50,"option_depth1":"테스 홑이불","option_price":8900,"option_depth2":"드로잉 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"165"},{"option_supply":3700,"option_count":50,"option_depth1":"테스 홑이불","option_price":8900,"option_depth2":"덴들라이 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"166"},{"option_supply":3700,"option_count":50,"option_depth1":"테스 홑이불","option_price":8900,"option_depth2":"레인보우 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"167"},{"option_supply":3700,"option_count":50,"option_depth1":"테스 홑이불","option_price":8900,"option_depth2":"라인 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"168"},{"option_supply":3700,"option_count":50,"option_depth1":"테스 홑이불","option_price":8900,"option_depth2":"라벤더 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"169"},{"option_supply":3700,"option_count":50,"option_depth1":"테스 홑이불","option_price":8900,"option_depth2":"브로썸 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"170"},{"option_supply":3700,"option_count":50,"option_depth1":"테스 홑이불","option_price":8900,"option_depth2":"블루링고 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"171"},{"option_supply":3700,"option_count":50,"option_depth1":"테스 홑이불","option_price":8900,"option_depth2":"올리아핑크 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"172"},{"option_supply":3700,"option_count":50,"option_depth1":"테스 홑이불","option_price":8900,"option_depth2":"아카리샤 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"173"},{"option_supply":3700,"option_count":50,"option_depth1":"테스 홑이불","option_price":8900,"option_depth2":"원터플라워 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"174"},{"option_supply":3700,"option_count":50,"option_depth1":"테스 홑이불","option_price":8900,"option_depth2":"코렐 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"175"},{"option_supply":3700,"option_count":50,"option_depth1":"테스 홑이불","option_price":8900,"option_depth2":"테스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"176"},{"option_supply":3700,"option_count":50,"option_depth1":"테스 홑이불","option_price":8900,"option_depth2":"피그먼트 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"177"},{"option_supply":3700,"option_count":50,"option_depth1":"테스 홑이불","option_price":8900,"option_depth2":"히아신스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"178"},{"option_supply":3700,"option_count":50,"option_depth1":"테스 홑이불","option_price":8900,"option_depth2":"해피트리 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"179"},{"option_supply":3700,"option_count":50,"option_depth1":"피그먼트 홑이불","option_price":8900,"option_depth2":"드로잉 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"180"},{"option_supply":3700,"option_count":50,"option_depth1":"피그먼트 홑이불","option_price":8900,"option_depth2":"덴들라이 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"181"},{"option_supply":3700,"option_count":50,"option_depth1":"피그먼트 홑이불","option_price":8900,"option_depth2":"레인보우 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"182"},{"option_supply":3700,"option_count":50,"option_depth1":"피그먼트 홑이불","option_price":8900,"option_depth2":"라인 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"183"},{"option_supply":3700,"option_count":50,"option_depth1":"피그먼트 홑이불","option_price":8900,"option_depth2":"라벤더 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"184"},{"option_supply":3700,"option_count":50,"option_depth1":"피그먼트 홑이불","option_price":8900,"option_depth2":"브로썸 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"185"},{"option_supply":3700,"option_count":50,"option_depth1":"피그먼트 홑이불","option_price":8900,"option_depth2":"블루링고 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"186"},{"option_supply":3700,"option_count":50,"option_depth1":"피그먼트 홑이불","option_price":8900,"option_depth2":"올리아핑크 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"187"},{"option_supply":3700,"option_count":50,"option_depth1":"피그먼트 홑이불","option_price":8900,"option_depth2":"아카리샤 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"188"},{"option_supply":3700,"option_count":50,"option_depth1":"피그먼트 홑이불","option_price":8900,"option_depth2":"원터플라워 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"189"},{"option_supply":3700,"option_count":50,"option_depth1":"피그먼트 홑이불","option_price":8900,"option_depth2":"코렐 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"190"},{"option_supply":3700,"option_count":50,"option_depth1":"피그먼트 홑이불","option_price":8900,"option_depth2":"테스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"191"},{"option_supply":3700,"option_count":50,"option_depth1":"피그먼트 홑이불","option_price":8900,"option_depth2":"피그먼트 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"192"},{"option_supply":3700,"option_count":50,"option_depth1":"피그먼트 홑이불","option_price":8900,"option_depth2":"히아신스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"193"},{"option_supply":3700,"option_count":50,"option_depth1":"피그먼트 홑이불","option_price":8900,"option_depth2":"해피트리 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"194"},{"option_supply":3700,"option_count":50,"option_depth1":"히아신스 홑이불","option_price":8900,"option_depth2":"드로잉 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"195"},{"option_supply":3700,"option_count":50,"option_depth1":"히아신스 홑이불","option_price":8900,"option_depth2":"덴들라이 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"196"},{"option_supply":3700,"option_count":50,"option_depth1":"히아신스 홑이불","option_price":8900,"option_depth2":"레인보우 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"197"},{"option_supply":3700,"option_count":50,"option_depth1":"히아신스 홑이불","option_price":8900,"option_depth2":"라인 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"198"},{"option_supply":3700,"option_count":50,"option_depth1":"히아신스 홑이불","option_price":8900,"option_depth2":"라벤더 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"199"},{"option_supply":3700,"option_count":50,"option_depth1":"히아신스 홑이불","option_price":8900,"option_depth2":"브로썸 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"200"},{"option_supply":3700,"option_count":50,"option_depth1":"히아신스 홑이불","option_price":8900,"option_depth2":"블루링고 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"201"},{"option_supply":3700,"option_count":50,"option_depth1":"히아신스 홑이불","option_price":8900,"option_depth2":"올리아핑크 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"202"},{"option_supply":3700,"option_count":50,"option_depth1":"히아신스 홑이불","option_price":8900,"option_depth2":"아카리샤 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"203"},{"option_supply":3700,"option_count":50,"option_depth1":"히아신스 홑이불","option_price":8900,"option_depth2":"원터플라워 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"204"},{"option_supply":3700,"option_count":50,"option_depth1":"히아신스 홑이불","option_price":8900,"option_depth2":"코렐 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"205"},{"option_supply":3700,"option_count":50,"option_depth1":"히아신스 홑이불","option_price":8900,"option_depth2":"테스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"206"},{"option_supply":3700,"option_count":50,"option_depth1":"히아신스 홑이불","option_price":8900,"option_depth2":"피그먼트 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"207"},{"option_supply":3700,"option_count":50,"option_depth1":"히아신스 홑이불","option_price":8900,"option_depth2":"히아신스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"208"},{"option_supply":3700,"option_count":50,"option_depth1":"히아신스 홑이불","option_price":8900,"option_depth2":"해피트리 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"209"},{"option_supply":3700,"option_count":50,"option_depth1":"해피트리 홑이불","option_price":8900,"option_depth2":"드로잉 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"210"},{"option_supply":3700,"option_count":50,"option_depth1":"해피트리 홑이불","option_price":8900,"option_depth2":"덴들라이 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"211"},{"option_supply":3700,"option_count":50,"option_depth1":"해피트리 홑이불","option_price":8900,"option_depth2":"레인보우 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"212"},{"option_supply":3700,"option_count":50,"option_depth1":"해피트리 홑이불","option_price":8900,"option_depth2":"라인 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"213"},{"option_supply":3700,"option_count":50,"option_depth1":"해피트리 홑이불","option_price":8900,"option_depth2":"라벤더 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"214"},{"option_supply":3700,"option_count":50,"option_depth1":"해피트리 홑이불","option_price":8900,"option_depth2":"브로썸 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"215"},{"option_supply":3700,"option_count":50,"option_depth1":"해피트리 홑이불","option_price":8900,"option_depth2":"블루링고 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"216"},{"option_supply":3700,"option_count":50,"option_depth1":"해피트리 홑이불","option_price":8900,"option_depth2":"올리아핑크 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"217"},{"option_supply":3700,"option_count":50,"option_depth1":"해피트리 홑이불","option_price":8900,"option_depth2":"아카리샤 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"218"},{"option_supply":3700,"option_count":50,"option_depth1":"해피트리 홑이불","option_price":8900,"option_depth2":"원터플라워 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"219"},{"option_supply":3700,"option_count":50,"option_depth1":"해피트리 홑이불","option_price":8900,"option_depth2":"코렐 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"220"},{"option_supply":3700,"option_count":50,"option_depth1":"해피트리 홑이불","option_price":8900,"option_depth2":"테스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"221"},{"option_supply":3700,"option_count":50,"option_depth1":"해피트리 홑이불","option_price":8900,"option_depth2":"피그먼트 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"222"},{"option_supply":3700,"option_count":50,"option_depth1":"해피트리 홑이불","option_price":8900,"option_depth2":"히아신스 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"223"},{"option_supply":3700,"option_count":50,"option_depth1":"해피트리 홑이불","option_price":8900,"option_depth2":"해피트리 홑이불","use_yn":"Y","item_no":"2006450507","option_order":"224"}]';
//        $json = json_decode($str,true);
//
//        zsView($json);

    }

    public function srh_post(){
        $this->_header();
        $this->load->view('/test/srh_post' );
        $this->_footer();
    }

    public function send_v2(){

        $this->load->library('Snoopy');

        {

            zsView('https://console.adbrix.io/login -----------------------------------------------------------------------------------------------');

            $this->snoopy->_submit_type = 'text/html; charset=utf-8;';

            $url = "https://console.adbrix.io/login";
            $this->snoopy->fetch($url);
            $this->snoopy->setcookies();

        }


        { //login


            zsView('1 https://api-console.adbrix.io/api/v1/User/Login -----------------------------------------------------------------------------------------------');
            $url    = "https://api-console.adbrix.io/api/v1/User/Login";
            $this->snoopy->_submit_method = 'OPTION';
            $ret = $this->snoopy->submit($url,'');
            zsView($ret);

            exit;




            zsView('https://api-console.adbrix.io/api/v1/User/Login -----------------------------------------------------------------------------------------------');

            $url    = "https://api-console.adbrix.io/api/v1/User/Login";
            $params = "{'email' : 'ordersuvin@gmail.com' , 'password' : 'Fflw*84200'}";

            $this->snoopy->_adbrix = true;
            $this->snoopy->_httpmethod = 'POST';
            $this->snoopy->_submit_type = 'application/json; charset=utf-8;';

            $ret = $this->snoopy->submit($url,$params);
            $this->snoopy->setcookies();

            $log_ret_arr = json_decode($this->snoopy->results,true);
            $user_token = $log_ret_arr['data']['user_token'];

            zsView($this->snoopy->results);

        }

        if(empty($user_token) == true){//계정정보 가져오기

            zsView('https://api-console.adbrix.io/api/v1/Account/GetAccounts -----------------------------------------------------------------------------------------------');

            unset($params);

            $uri    = 'https://api-console.adbrix.io/api/v1/Account/GetAccounts';
            $params = "{'user_token' : '{$log_ret_arr['data']['user_token']}'}";

            $this->snoopy->accept = 'application/json, text/plain, */*';
            $ret = $this->snoopy->submit($uri,$params);
            $this->snoopy->setcookies();
            $account_ret_arr = json_decode($this->snoopy->results,true);

            $account_id = $account_ret_arr['data'][0]['account_id'];


            zsView($this->snoopy->results);
            zsView('-----------------------------------------------------------------------------------------------');

        }

        if(empty($account_id) == true){//auth_token 발급

            zsView('https://api-console.adbrix.io/api/v1/Account/AccountLogin -----------------------------------------------------------------------------------------------');

            $uri = 'https://api-console.adbrix.io/api/v1/Account/AccountLogin';
            $params = "{'account_id' : '{$account_id}' , 'user_token' : '{$user_token}'}";
            $ret = $this->snoopy->submit($uri,$params);
            $this->snoopy->setcookies();
            $account_log_ret_arr = json_decode($this->snoopy->results,true);

            $auth_token = $account_log_ret_arr['data']['auth_token'];

            zsView($ret);
            zsView($account_log_ret_arr);
            zsView('-----------------------------------------------------------------------------------------------');
            exit;
        }





//        $this->snoopy->referer = 'https://console.adbrix.io/8TdID8XlgEaZLi15gzVRbw/attributions/UDvd4XedQ0CrceXhCwoYbg/ad-tracking';

        $params = "{'appkey': 'UDvd4XedQ0CrceXhCwoYbg', 'ignore': false, 'first_index': 0, 'length': 2500}";
        $uri = 'https://api-console.adbrix.io/api/v1/AdCampaign/GetAdCampaigns';

        //$this->snoopy->_adbrix_auth = $log_ret_arr['data']['refresh_token'];
        $this->snoopy->_adbrix_auth = $log_ret_arr['data']['user_token'];

        $ret = $this->snoopy->submit($uri,$params);


        zsView($ret);

        exit;

//
    }


    public function take(){
        $var = json_encode($_POST);
        echo $var;
    }


//    public function excel(){
//        $this->_header();
//        $this->load->view('/test/excel' );
//        $this->_footer();
//    }
//
//    public function excel_proc(){
//
//        $arrayParams['filePath'] = $_FILES['excel']['tmp_name'];
//        $this->load->library("/MY_Excel",$arrayParams);
//        $aResult = $this->my_excel->getData();
//
//        foreach ($aResult as $k => $r) {
//
//
//            if($k > 1){
//
//                $sql = "UPDATE product_tb SET p_summary = '{$r['C']}' WHERE p_num = '{$r['A']}'; ";
//                $this->db->query($sql);
//
//            }
//
//        }
//
//        zsView('complete');
//
//        exit;
//
//    }

    public function update_detail(){



//        $sql = "
//SELECT *
//FROM product_tb
//WHERE p_detail REGEXP \"^[\\r\\n]\";
//
//        ";
//
//        zsView('a');
//        $oResult = $this->db->query($sql);
//        $aResult = $oResult->result_array();


        exit;
        foreach ($aResult as $r) {
            //$text = preg_replace('/\r\n|\r|\n/','',$text);


//            $patterns       = "/<center><center><br>/";
//            $replacements   = '<center><center>';
//            $r['p_detail'] =  preg_replace($patterns, $replacements, $r['p_detail'],1);

            //$r['p_detail'] = preg_replace('/\r\n|\r|\n/','','',$r['p_detail'],1);

            $sql = "UPDATE product_tb SET p_detail = '{$r['p_detail']}' WHERE p_num = '{$r['p_num']}' ; ";
//            $this->db->query($sql);
            zsView($sql);

        }

        zsView('c : '.count($aResult));

//        $dir = "/data/shop1/www/uploads/suvin_n";
//
//        // 핸들 획득
//        $handle  = opendir($dir);
//
//        $files = array();
//
//        // 디렉터리에 포함된 파일을 저장한다.
//        while (false !== ($filename = readdir($handle))) {
//            if($filename == "." || $filename == ".."){
//                continue;
//            }
//
//            // 파일인 경우만 목록에 추가한다.
//            if(is_file($dir . "/" . $filename)){
//                $files[] = str_replace('.jpg','',$filename);
//            }
//        }
//
//        // 핸들 해제
//        closedir($handle);
//
//        // 정렬, 역순으로 정렬하려면 rsort 사용
//        sort($files);
//
//
//        $aProductLists = array();
//        $aProductLists_F = array();
//
//        foreach ($files as $v) {
//            $sql = "SELECT * FROM product_tb WHERE p_easy_admin_code = '{$v}';  ";
//            $aResult = $this->db->query($sql)->row_array();
//
//            $rep_image = array();
//
//            if(empty($aResult) == true){
//                $aProductLists_F[] = $v;
//            }else{
//                $aProductLists[] = $v;
//
//
////                $rep_image[1] = $rep_image[0] ='/uploads/suvin_n/'.$v.'.jpg';
////                $rep_image_j    = json_encode_no_slashes($rep_image);
////
////                $sql = "UPDATE product_tb SET p_rep_image = '{$rep_image_j}' WHERE p_easy_admin_code = '{$v}';  ";
////                $this->db->query($sql);
//
//            }
//
//        }
//
//        zsView($aProductLists_F);
//        zsView('-------------------------------');
//        zsView($aProductLists);

//        $sql = "SELECT * FROM product_tb WHERE p_rep_image LIKE '%www.cloma%'; ";
//        $sql = "SELECT * FROM product_tb WHERE p_name IN ('줄무늬블리롱원피스','더끌려유넥가오리티셔츠','편하다후드롱원피스','코튼셔링롱원피스','남녀공용기본면티셔츠','매일편하게밴딩팬츠','레터링꼬임롱원피스','소매핀턱코튼티셔츠','잘록라인밴딩원피스','스타일자수밴딩팬츠','두줄레터링롱티셔츠','린넨브이롱원피스','더편하게상하의세트','플라밍고파우치3종세트','편하고예쁜체크팬츠1+1','누구나편하게밴딩팬츠','알록이원피스앞치마','예쁨만점나염원피스','봄처럼레이스블라우스','촉촉주름상하의세트','컬러풀밴딩팬츠','남성쿨링터치티셔츠','여성에어터치티셔츠','주름나염끈원피스','쉬폰러플롱원피스','시원하게입는나염팬츠','밴딩민소매점프수트','살랑와이드밴딩팬츠','두줄라인롱원피스','라이프투톤바람막이','언발포인트티셔츠','크로스언발스커트','골드시보리밴딩팬츠','플레어밴딩롱스커트','간편레이어드나시티셔츠','바삭코튼베이직남방','허리단시보리원피스','군살커버와이드팬츠','화사레이스티셔츠','반팔후드상하의세트');";
//        $oResult = $this->db->query($sql);
//        $aResult = $oResult->result_array();
//
//        foreach ($aResult as $r) {
//
//            $rep_image = array();
//            $rep_image[] = $rep_image[] = '/uploads/suvin_n/'.$r['p_easy_admin_code'].'.jpg';//str_replace('/suvin','/suvin_n',$rep_image[1]);
//
//            $rep_image_j = json_encode_no_slashes($rep_image);
//
//            $sql = "UPDATE product_tb SET p_rep_image = '{$rep_image_j}' WHERE p_num = '{$r['p_num']}'; ";
//            $this->db->query($sql);
//            zsView($sql);
//
//        }

    }


/*
    public function excel_proc(){


        exit;

//
//        $allow_id = array('41129','43226','47230','47725','51105','51561','56926','57567','57594','57604','57617','57655','57904','57919','57946','57953','57974','58030','58037','58125','58211','58263','58266','58281','58310','58315','58336','58343','58348','58417','58424','58470','58522','58525','58534','58546','58554','58566','58572','58594','58597','58606','58612','58620','58642','58653','58666','58671','58681','58692','58703','58721','58766','58770','58775','58780','58787','58806','58811','58822','58834','58849','58871','58875','58878','58882','58885','58903','58908','58912','58918','58958','58964','58968','58981','58988','59004','59037','59075','59081','59085','59102','59130','59136','59167');
        $arrayParams['filePath'] = $_FILES['excel']['tmp_name'];
        $this->load->library("/MY_Excel",$arrayParams);
        $aResult = $this->my_excel->getData();


        foreach ($aResult as $k => $r) {


            if($k > 1){

                $sql = "UPDATE product_tb SET p_summary = '{$r['F']}' , p_sale_price = '{$r['C']}' WHERE p_order_code = '{$r['A']}'; ";
                $this->db->query($sql);

            }

        }

zsView('complete');

        exit;


//        $aInput = array();
//        $i = 0;
//
//        $cate1_arr[42]='패션';
//        $cate1_arr[48]='패션잡화';
//        $cate1_arr[49]='아우터';
//        $cate1_arr[50]='상의';
//        $cate1_arr[51]='하의';
//        $cate1_arr[52]='원피스';
//        $cate1_arr[53]='홈웨어';
//
//        foreach ($aResult as $k => $r) {
//
//
//
//            $sub_data1 = array();
//            $item_cnt = 0;
//
//            if($k > 1 ){
//
//
//
//                $tmp_arr = explode('/',$r['D']) ;
//                $tmp_arr[0]; //순서
//                $tmp_arr[1]; //옵션1
//                $tmp_arr[2]; //옵션2
//                $tmp_arr[3]; //옵션3
//                $tmp_arr[4]; //옵션금액
//                $tmp_arr[5]; //재고량
//
//                $sub_data1['option_supply']    = 0;
//                $sub_data1['option_count']     = $tmp_arr[5];
//                $sub_data1['option_depth1']    = $tmp_arr[1];
//                if(empty($tmp_arr[2]) == false) $sub_data1['option_depth2']    = $tmp_arr[2];
//                if(empty($tmp_arr[3]) == false) $sub_data1['option_depth3']    = $tmp_arr[3];
//                $sub_data1['option_price']     = $tmp_arr[4];
//                $sub_data1['use_yn']           = 'Y';
//                $sub_data1['item_no']          = $r['A'];
//                $sub_data1['option_order']     = $tmp_arr[0];
//
//                $item_cnt   += $sub_data1['option_count'];
//                $sub_data[]  = $sub_data1; //json_encode_no_slashes($sub_data);
//
//                if($aResult[$k+1]['A'] != $r['A'] ){ // insert & init
//
//                    $aInput[$i] = array(
//                        'item_no'           => $r['A']
//                    ,   'local_no'          => $r['B']
//                    ,   'item_name'         => $r['C']
//                    ,   'start_date'        => "2020-02-03 17:00:00"
//                    ,   'end_date'          => "2021-02-02 17:00:00"
//                    ,   'sel_payway_cd'     => '13572'
//                    ,   'item_price'        => $r['E']
//                    ,   'supply_name'       => $r['I']
//                    ,   'org_area'          => $r['N']
//                    ,   'contents'          => $r['J']
//                    ,   'img_url'           => $r['L']
//                    ,   'ctgry_seq'         => $cate1_arr[$r['H']]
//                    ,   'item_count'        => $item_cnt
//                    ,   'option_yn'         => 'Y'
//                    ,   'option_info'       => json_encode($sub_data,JSON_UNESCAPED_UNICODE)
//                    );
//
//                    $i++;
//
//                    $item_cnt = 0;
//                    $sub_data = array();
//
//                }
//
//            }
//
//        }
//
//        $this->load->model('product_model');
//        $this->load->helper('string');
//
//        foreach ($aInput as $k => $r) {
//
//            $img_arr = array(
//                '1'=> 'http://www.cloma.co.kr/uploads/suvin/'.$r['local_no'].'.jpg'
//            ,  '0'=> 'http://www.cloma.co.kr/uploads/suvin/'.$r['local_no'].'.jpg'
//            );
//
//            $rep_img_json = json_encode($img_arr);
//
//            unset($r['local_no']);
//
//            $bResult = $this->product_model->publicInsert('snsform_product_tb',$r);
//            if($bResult) $r_str = 'Y';
//            else $r_str = 'N';
//
//            zsView('insert SNSFORM TABLE :: '.$r['item_no'].' :: '.$r_str);
//
//            if($bResult == true){
//
//                $aInput = array(
//                    'p_order_code'          => $r['item_no']
//                ,   'p_name'                => $r['item_name']
//                ,   'p_cate1'               => $r['ctgry_seq']
//                ,   'p_detail'              => strip_quotes($r['contents'])
//                ,   'p_display_info'        => '{"":"Y"}'
//                ,   'p_termlimit_datetime1' => '20200203170000'
//                ,   'p_termlimit_datetime2' => '20210203170000'
//                ,   'p_display_state'       => 'N'
//                ,   'p_sale_state'          => 'N'
//                ,   'p_taxation'            => 1
//                ,   'p_stock_state'         => $r['item_count'] > 0 ? 'Y':'N'
//                ,   'p_today_image'         => $r['img_url']
//                ,   'p_rep_image'           => $rep_img_json
//                ,   'p_sale_price'          => $r['item_price']
//
//                    // 필수 입력 정보 확인 및 추가
//
//                    //snsform 상품등록일자필드 ?
//
//                );
//
//                $bResult = $this->product_model->publicInsert('product_tb',$aInput);
//                if($bResult) $r_str = 'Y';
//                else $r_str = 'N';
//
//                zsView('insert LOCAL TABLE :: '.$r['item_no'].' :: '.$r_str);
//
//            }
//
//        }

        zsView('complete');

    }

*/

}//end of class Test