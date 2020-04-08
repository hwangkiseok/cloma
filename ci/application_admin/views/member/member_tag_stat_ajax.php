<style>
    .chart_wrap { width:90%; padding:20px; margin:0 auto; }

    #chartdiv {
        width: 100%;
        min-height: 400px;
        font-size: 11px;
    }
    .amcharts-pie-slice {
        transform: scale(1);
        transform-origin: 50% 50%;
        transition-duration: 0.3s;
        transition: all .3s ease-out;
        -webkit-transition: all .3s ease-out;
        -moz-transition: all .3s ease-out;
        -o-transition: all .3s ease-out;
        cursor: pointer;
        box-shadow: 0 0 30px 0 #000;
    }
    .amcharts-pie-slice:hover {
        transform: scale(1.1);
        filter: url(#shadow);
    }
    /*Modal Init*/
    .blocker { z-index: 1000!important; }
    .modal {max-width: 100%!important;}
    /*Modal Init*/

    #chartDataList { position:relative; list-style:none; }
    #chartDataList li { float:left; border:1px solid #ccc; padding:5px; margin-right:10px; margin-bottom:5px; }
</style>

<!-- 차트 -->
<div class="chart_wrap">
    <!--<canvas id="chartdiv "></canvas>-->
    <div id="chartdiv"></div>
</div>
<!-- // 차트 -->

<script>
    var chart = AmCharts.makeChart("chartdiv", {
        "type": "pie",
        "startDuration": 0,
        "theme": "light",
        "addClassNames": true,
        "legend":{
            "position":"right",
            "marginRight":100,
            "autoMargins":false
        },
        "innerRadius": "30%",
        "defs": {
            "filter": [{
                "id": "shadow",
                "width": "200%",
                "height": "200%",
                "feOffset": {
                    "result": "offOut",
                    "in": "SourceAlpha",
                    "dx": 0,
                    "dy": 0
                },
                "feGaussianBlur": {
                    "result": "blurOut",
                    "in": "offOut",
                    "stdDeviation": 5
                },
                "feBlend": {
                    "in": "SourceGraphic",
                    "in2": "blurOut",
                    "mode": "normal"
                }
            }]
        },
        "dataProvider": <?=json_encode_no_slashes($data);?>,
        "valueField": "cnt",
        "titleField": "m_tag",
        "export": {
            "enabled": true
        }
    });

    chart.addListener("init", handleInit);

    chart.addListener("rollOverSlice", function(e) {
        handleRollOver(e);
    });

    function handleInit(){
        chart.legend.addListener("rollOverItem", handleRollOver);
    }

    function handleRollOver(e){
        var wedge = e.dataItem.wedge.node;
        wedge.parentNode.appendChild(wedge);
    }
</script>
<!-- // chart -->