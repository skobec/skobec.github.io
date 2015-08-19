<?php
class Zend_View_Helper_BarWidget extends Zend_View_Helper_Abstract {

    public function barWidget($data, $nextLevel) {
        ?>
<!--        <script src="/js/chart_control.js"></script>-->
        <style>
            /*.cards-item.is-expanded {*/
            /*width: 95%;*/
            /*}*/
            /*.cards-item {*/
            /*height: 616px;*/
            /*}*/
            .chart-tooltip{
                padding: 5px;
                border-radius: 5px;
                box-shadow: 2px 2px 2px;
                color: white;
                font-size: 20px;
                z-index: 10;
            }
            .cards-chart {
                padding-top: 35px;
                background-color: white;
                overflow: visible;
            }
            .highcharts-button{
                display:none;
            }
            .cards-cover{
                border-right: 1px solid #c3c3c3;
            }
        </style>
        <input id="yaer_2015" name="years_interval" value="2015" type="hidden">

        <?php for($i =1;$i<5;$i++){?>
            <li class="cards-item is-expanded" data-item="card" id="widget<?=$i?>">
                <ul class="cards-opt">
                    <li class="cards-opt-item">
                        <a href="#" class="cards-opt-link">
                            <span class="icon icon-compare"></span>
                        </a>
                    </li>
                    <li class="cards-opt-item">
                        <a href="#" class="cards-opt-link">
                            <span class="icon icon-settings"></span>
                        </a>
                    </li>
                    <li class="cards-opt-item is-active">
                        <a href="#" class="cards-opt-link" data-action="expand">
                            <span class="icon icon-expand"></span>
                        </a>
                    </li>
                </ul>
                <div class="cards-cover">
                    <header class="cards-header">
                        <h2 class="cards-title">Расходы <a href="#" class="cards-title-link">бюджета</a> Минпромторга<br> по <a href="#" class="cards-title-link">госпрограммам</a><br> в 2015 году</h2>
                    </header>
                    <div class="cards-icon">
                        <span class="icon icon-govprog"></span>
                    </div>
                    <div class="cards-amount">
                        <span class="cards-amount-value"><?= $data[0]->y ?></span>
                        <span class="cards-amount-currency">млрд. <span class="rouble">Р</span></span>
                    </div>
                </div>
                <div class="cards-main">
                    <ul class="cards-filter">
                        <li class="cards-filter-item">
                            <a href="#" class="cards-filter-link">
                                <span class="cards-filter-text">План</span>
                            </a>
                        </li>
                        <li class="cards-filter-item">
                            <a href="#" class="cards-filter-link">
                                <span class="cards-filter-text">Годы</span>
                            </a>
                        </li>
                        <li class="cards-filter-item">
                            <a href="#" class="cards-filter-link">
                                <span class="cards-filter-text">2015</span>
                            </a>
                        </li>
                    </ul>
                    <div class="cards-chart">
                        <div class="cards-chart-graph" id="container<?=$i?>"></div>
                    </div>
                    <aside class="cards-sidebar is-collapsed" data-item="sidebar">
                        <div class="cards-view" data-container="view">
                            <ul class="cards-view-list">
                                <li class="cards-view-item is-active" data-item="view">
                                    <a href="#" class="cards-view-link" data-action="view">
                                        <span class="icon icon-percentage"></span>
                                    </a>
                                </li>
                                <li class="cards-view-item" data-item="view">
                                    <a href="#" class="cards-view-link" data-action="view">
                                        <span class="icon icon-stacked"></span>
                                    </a>
                                </li>
                                <li class="cards-view-item" data-item="view">
                                    <a href="#" class="cards-view-link" data-action="view">
                                        <span class="icon icon-line"></span>
                                    </a>
                                </li>
                                <li class="cards-view-item" data-item="view">
                                    <a href="#" class="cards-view-link" data-action="view">
                                        <span class="icon icon-pie"></span>
                                    </a>
                                </li>
                                <li class="cards-view-item" data-item="view">
                                    <a href="#" class="cards-view-link" data-action="view">
                                        <span class="icon icon-bars"></span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="cards-legend" data-container="legend">
                            <ul class="cards-legend-list"></ul>
                            <a href="#" class="cards-legend-more">Показать еще...</a>
                        </div>
                    </aside>
                </div>
            </li>
       <? }?>

        <script>
            $(function () {
                var chart1 = new ChartControl(1);
                var chart2 = new ChartControl(2);
                var chart3 = new ChartControl(3);
                var chart4 = new ChartControl(4);
                chart1.getDataAndRender('months');
                chart2.getDataAndRender('columns');
                chart3.getDataAndRender('stacked_area');
                chart4.getDataAndRender('basic');
            });
        </script>
    <?
    }
}
