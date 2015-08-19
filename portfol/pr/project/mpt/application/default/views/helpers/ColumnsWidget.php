<?php
class Zend_View_Helper_ColumnsWidget extends Zend_View_Helper_Abstract {

    public function columnsWidget($data, $nextLevel) {
        ?>
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
                /*padding-right:3%;*/
            }
            /*.cards-sidebar{*/
            /*display:none;*/
            /*}*/
            .highcharts-button{
                display:none;
            }
            /*#container,.highcharts-container,.cards-item,.cards-main,.cards-chart{*/
            /*overflow: visible!important;*/
            /*}*/
            .cards-cover{
                border-right: 1px solid #c3c3c3;
            }
        </style>
        <input id="yaer_2015" name="years_interval" value="2015" type="hidden">
        <li class="cards-item is-expanded" data-item="card" id="widget1">
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
                            <span class="cards-filter-text">2013—2015</span>
                        </a>
                    </li>
                </ul>
                <div class="cards-chart">
                    <div class="cards-chart-graph" id="container1"></div>
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
                    <div class="cards-legend" data-container="legend" id="container_legend">
                        <ul class="cards-legend-list"></ul>
                        <a href="#" class="cards-legend-more">Показать еще...</a>
                    </div>
                </aside>
            </div>
        </li>
<!--        <script src="/js/chart_control.js"></script>-->
        <script>
            // История графиков.
            var chartHistory = new Array();
//            var chart = $('#container');
            var chart_type = '<?= $nextLevel ?>';
            $(function () {
                var chart = new ChartControl(1);
                chart.getDataAndRender('months');

                $('.icon-percentage').on('click', function () {
                    if(!$(this).closest('.cards-view-item').hasClass('is-active')){
                        chart.remove();
                        chart.getDataAndRender('months');
                        $(this).closest('.cards-view-item').addClass('is-active')
                            .siblings().removeClass('is-active');
                    }
                    return false;
                });

                $('.icon-bars').on('click', function () {
                    if(!$(this).closest('.cards-view-item').hasClass('is-active')){
                        chart.remove();
                        chart.getDataAndRender('columns');
                        $(this).closest('.cards-view-item').addClass('is-active')
                            .siblings().removeClass('is-active');
                    }
                    return false;
                });

                $('.icon-pie').on('click', function () {
                    if(!$(this).closest('.cards-view-item').hasClass('is-active')){
                        chart.remove();
                        chart.getDataAndRender('programs');
                        $(this).closest('.cards-view-item').addClass('is-active')
                            .siblings().removeClass('is-active');
                    }
                    return false;
                });

                // По умолчанию сделаем кнопку Back неактивной
                $('#back-chart').prop('disabled', 'disabled');

                // При клике на кнопку Back будем брать данные из истории и перерисовывать графики и легенду
                $('#back-chart').on('click', function () {
                    historyData = jQuery.extend(true, {}, chartHistory[chartHistory.length - 2]);
                    chartHistory.pop();
                    if (chartHistory.length < 2) {
                        $(this).prop('disabled', 'disabled');
                    }
                    return renderChart(historyData);
                });

//                $('.icon-bars').on('click', function () {
//                    $('.cards-chart').highcharts().series[0].update({type: "column"});
//                });


//                chart.renderChart();
//                chart.renderColumns();
//                chart.renderMonths();
//                chart.renderPercentage();

            });

            //Отрисовка легенды
//            function renderLegend(chart_series) {
//                $('.cards-legend-list').empty();
//                $.each(chart_series, function (key, val) {
//                    row = $('<li>', {class: 'cards-legend-item', 'data-item': 'view'}).appendTo($('.cards-legend-list'));
//                    link = $('<a>', {'href': '#', 'class': 'cards-legend-link'}).appendTo(row);
//                    $('<span>', {class: 'cards-legend-marker', css: {'background': val.color}}).appendTo(link);
//                    $('<span>', {class: 'cards-legend-text'}).text(val.name.substring(0, 60) + '...').appendTo(link);
//                });
//                return false;
//            }

        </script>

    <?
    }

}
