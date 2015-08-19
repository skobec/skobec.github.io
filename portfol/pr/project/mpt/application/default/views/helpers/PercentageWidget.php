<?php

/**
 * Страница Л/С
 * @author sciner
 * @since 2014-11-27
 */
class Zend_View_Helper_PercentageWidget extends Zend_View_Helper_Abstract {

    public function percentageWidget($data, $nextLevel) {
        ?>
        <style>
            .chart-tooltip{
                padding: 5px;
                border-radius: 5px;
                box-shadow: 2px 2px 2px;
                color: white;
                font-size: 20px;
            }
        </style>
        <li class="cards-item is-expanded" data-item="card">
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
                    <div class="cards-chart-graph" id="container"></div>
                </div>
                <aside class="cards-sidebar" data-item="sidebar">
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

        <script>
            // История графиков.
            var chartHistory = new Array();
            $(function () {
                $('.icon-bars').on('click', function () {
                    $('.cards-chart').highcharts().series[0].update({type: "column"});
                });
                $('.icon-pie').on('click', function () {
                    $('.cards-chart').highcharts().series[0].update({type: "pie"});
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
                // Начальная инициализация графиков
                $.getJSON('/chart/getdata', {
                    type: '<?= $nextLevel ?>',
                    data: {'ministry_id': 1}
                },
                function (data) {
                    chartHistory.push(data);
                    $('#container').highcharts({
                        tooltip: {
                            positioner: function (labelWidth, labelHeight, point) {
                                return {x: point.plotX + labelWidth / 2, y: point.plotY - 200};
                            },
                            shape: 'square',
                            useHTML: true,
                            backgroundColor: null,
                            borderWidth: 0,
                            shadow: false,
                            formatter: function () {
                                return '<div class="cards-chart-tooltip">'
                                        + '<div class = "cards-chart-tooltip-inner">'
                                        + '<div class = "cards-chart-tooltip-title" > Ноябрь 2014 </div>'
                                        + '<div class = "cards-chart-tooltip-cols">'
                                        + '<div class = "cards-chart-tooltip-col">'
                                        + '<div class = "cards-chart-tooltip-subtitle"> План </div>'
                                        + '<div class = "cards-chart-tooltip-num"> 135 </div>'
                                        + '</div>'
                                        + '<div class = "cards-chart-tooltip-col">'
                                        + '<div class = "cards-chart-tooltip-subtitle"> Факт </div>'
                                        + '<div class = "cards-chart-tooltip-num"> 130 </div>'
                                        + '</div>'
                                        + '</div>'
                                        + '<div class = "cards-chart-tooltip-summary">'
                                        + '<div class = "cards-chart-tooltip-percent"> 50 % </div>'
                                        + '<div class = "cards-chart-tooltip-total">'
                                        + '<div class = "cards-chart-tooltip-arrow"> <span class = "icon icon-arrow-down"> </span></div>'
                                        + '<div class = "cards-chart-tooltip-value"> 5 </div>'
                                        + '</div>'
                                        + '</div>'
                                        + '</div>'
                                        + '</div>';
        //                                return '<div style="background-color:' + this.point.color + '" class="chart-tooltip"> ' +
        //                                        this.y + ' млрд. Р' +
        //                                        '</div>';
                            }
                        },
                        legend: {
                            enabled: false
                        },
                        chart: {
                            type: 'area',
                        },
                        title: {
                            style: {
                                display: 'none'
                            }
                        },
                        xAxis: {
                            categories: data.settings['x_axis'],
                            tickmarkPlacement: 'on',
                            title: {
                                enabled: false
                            },
                            labels: {
                                formatter: function () {
                                    return data.settings['x_axis'][this.value];
                                }
                            }

                        },
                        plotOptions: {
                            area: {
                                stacking: 'percent',
                                lineColor: '#ffffff',
                                lineWidth: 1,
                                marker: {
                                    lineWidth: 1,
                                    lineColor: '#ffffff'
                                },
                            },
                            series: {
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        //Подгрузка новых данных
                                        click: function () {
                                            var type = (this.type.length > 1) ? $('#chart-type').val() : this.type[0];
                                            $.getJSON(
                                                    '/chart/getdata', {
                                                        type: type,
                                                        data: this.data
                                                    }, function (data) {
                                                chartHistory.push(data);
                                                if (chartHistory.length == 2) {
                                                    $('#back-chart').removeProp('disabled');
                                                }
                                                renderChart(data);
                                            }

                                            );
                                        },
                                        // Эфект затемнения 
                                        mouseOver: function () {
                                            this.options.oldColor = this.color;
                                            this.graphic.attr("fill", "black");
                                            $('.govprogs-legend').children().css({'opacity': 0.4});
                                            $('.govprogs-legend').children().eq(this.x).css({'opacity': 1});
                                        },
                                        mouseOut: function () {
                                            this.graphic.attr("fill", this.options.oldColor);
                                            $('.govprogs-legend').children().css({'opacity': 1});
                                        }

                                    }
                                },
                                marker: {
                                    lineWidth: 1
                                }
                            }
                        },
                        series: data.series
                    });
                    // Заполняем легенду
                    chart_series = $('#container').highcharts().series;
                    renderLegend(chart_series);
                });
            });
            //Отрисовка легенды            
            function renderLegend(chart_series) {
                $('.cards-legend-list').empty();
                $.each(chart_series, function (key, val) {
                    row = $('<li>', {class: 'cards-legend-item', 'data-item': 'view'}).appendTo($('.cards-legend-list'));
                    link = $('<a>', {'href': '#', 'class': 'cards-legend-link'}).appendTo(row);
                    $('<span>', {class: 'cards-legend-marker', css: {'background': val.color}}).appendTo(link);
                    $('<span>', {class: 'cards-legend-text'}).text(val.name.substring(0, 60) + '...').appendTo(link);
                });
                return false;
            }

        </script>

        <?
    }

}
