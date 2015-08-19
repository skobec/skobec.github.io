<?php

/**
 * Страница Л/С
 * @author sciner
 * @since 2014-11-27
 */
class Zend_View_Helper_DrawWidget extends Zend_View_Helper_Abstract {

    public function drawWidget($data,$nextLevel) {
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
        <div class="wg-govprog-farma-lg" data-ix="farma-show">
            <div class="govprogs-nav-bar-lg">
                <div class="w-clearfix govprogs-nav-bar-lg">
                    <div class="govprogs-set-btn-lg" data-ix="govprogs-settings-show"></div>
                    <div class="govprogs-compare-btn-lg" data-ix="cm-farma-show"></div>
                </div>
            </div>
            <h4><?= $data[0]->name ?></h4>
            <h3 class="one-year center"><span class="big"><?= $data[0]->y ?></span><br>млрд. Р</h3>
            <div class="govprogs-more-btn-lg"><a class="govprogs-more-link-lg black" href="#" data-ix="govprog-farma-show-detail">Из чего состоит сумма</a>
            </div>
        </div>

        <div class="w-clearfix wg-govprog-farma-expand-lg">
            <div class="w-clearfix govprogs-nav-bar-lg">
                <div class="govprogs-set-btn-lg" data-ix="govprogs-settings-show"></div>
                <div class="govprogs-compare-btn-lg"></div>
            </div>
            <div class="expand-left-collumn">
                <h4><?= $data[0]->name ?></h4>
                <h3 class="one-year"><?= $data[0]->y ?> млрд. Р</h3>
                <div class="govprogs-years-compare"></div>
                <div class="govprogs-year-dot-2013" data-ix="show-dym-2013"></div>
                <div class="govprogs-year-dot-2014" data-ix="show-dym-2014"></div>
                <div class="govsprogs-year-dot-2015" data-ix="show-dym-2015"></div>
                <div class="govprog-farma-hide-more-lg"><a class="govprogs-more-link-lg black" href="#" data-ix="govprog-farma-hide-detail">Свернуть подробности</a>
                </div>
            </div>
            <div class="w-clearfix expand-right-collumn">
                <div class="govprogs-2015-unit-select-num"><a class="govprogs-2015-unit-link-num" href="#">млрд. руб.&nbsp;<span class="triangle-sign">▼</span></a>
                </div>
                <h4 class="black-h4">TITLE</h4>
                <div class="w-clearfix govprog-farma-expand-v-bar"></div>
                <div class="govprogs-legend"></div>
                <button id="back-chart" class="btn btn-success">Back</button>
                <div class="farma-diag"></div>
                <div class="govprogs-sugblock">
                    <h5>Рекомендуем:</h5><a class="sugblock-link" href="#">Как устроен<br>бюджет</a><a class="sugblock-link" href="#">Ссылка на реле-&nbsp;<br>вантный виджет</a><a class="sugblock-link" href="#">Еще одна ссылка<br>на похожий запрос</a>
                </div>
            </div>
        </div>

        <script>
            // История графиков.
            var chartHistory = new Array();

            $(function () {
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
                    type: '<?=$nextLevel?>',
                    data: {'ministry_id': 1}
                },
                function (data) {
                    chartHistory.push(data);
                    $('.farma-diag').highcharts({
                        tooltip: {
                            enabled: false
                        },
                        chart: {
                            type: 'pie',
                        },
                        title: {
                            style: {
                                display: 'none'
                            }
                        },
                        plotOptions: {
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
                                        // Эфект затеменения 
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
                            },
                            pie: {
                                innerSize: 75,
                                depth: 0,
                                dataLabels: {
                                    enabled: false,
                                    format: '<b>{point.y}</b>',
                                    style: {
                                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                    }
                                }
                            }
                        },
                        series: [{
                                name: '',
                                data: data.series
                            }]
                    });
                    $('.govprog-farma-expand-v-bar').highcharts({
                        title: {text: data['title']},
                        tooltip: {
                            useHTML: true,
                            backgroundColor: null,
                            borderWidth: 0,
                            shadow: false,
                            formatter: function () {

                                return '<div style="background-color:' + this.point.color + '" class="chart-tooltip"> ' +
                                        this.y + ' млрд. <span class="rouble"> Р</span>' +
                                        '</div>';
                            }
                        },
                        chart: {
                            events: {
                                load: function (event) {
                                    var theData = this.series[0].data;
                                    var newCats = [];

                                    for (var i = 0; i < theData.length; i++) {
                                        newCats.push(theData[i].y)
                                    }

                                    this.xAxis[0].setCategories(newCats);
                                }
                            },
                            type: 'column'
                        },
                        xAxis: {
                            categories: [],
                            tickLength: 0,
                            crosshair: true,
                            labels:
                                    {
                                        enabled: true,
                                        style: {
                                            color: 'black',
                                            fontSize: '14px'
                                        }
                                    }
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'млрд. <span class="rouble"> Р</span>'
                            }
                        },
                        plotOptions: {
                            cursor: 'pointer',
                            series: {
                                minPointLength: 2,
                                pointPadding: 0,
                                groupPadding: 0,
                                borderWidth: 0,
                                shadow: true,
                                colorByPoint: true

                            },
                            column: {
                                borderWidth: 1
                            }
                        },
                        series: [{
                                data: data.series

                            }]
                    });

                    // Заполняем легенду
                    chart_data = $('.farma-diag').highcharts().series[0].data;
                    renderLegend(chart_data);

                });
                // Дополнительные обработчики для событий.
                $('.govprogs-legend').on('mouseover', '.legend', function () {
                    $('.legend').css({'opacity': '0.4'});
                    $(this).css({'opacity': 1});
                    $('.farma-diag').highcharts().series[0].data[$(this).index()].setState('hover');
                }).on('mouseout', '.legend', function () {
                    $('.legend').css({'opacity': '1'});
                    $('.farma-diag').highcharts().series[0].data[$(this).index()].setState();
                }).on('click', '.legend', function () {
                    $('.farma-diag').highcharts().series[0].data[$(this).index()].firePointEvent('click');
                });

            });


            //Отрисовка графика
            function renderChart(data) {
                var chart = $('.farma-diag').highcharts();
                //Generate fake data for pretty rendering
                fake_series = jQuery.extend(true, [], data['series']);
                for (i = 0; i < fake_series.length; i++) {
                    fake_series[i].y = i ? 1 : 1000;
                }
                chart.series[0].setData(fake_series);
                chart.series[0].setData(data['series']);
                chart.setTitle({text: data['title']});
                //Заполянем легенду
                chart_data = $('.farma-diag').highcharts().series[0].data;
                renderLegend(chart_data);
                $('.govprog-farma-expand-v-bar').empty();
                var chart2 = $('.govprog-farma-expand-v-bar').highcharts();


                chart2.series[0].setData(fake_series);
                chart2.series[0].setData(data['series']);
                chart2.setTitle({text: data['title']});

                var newCats = [];

                for (var i = 0; i < data['series'].length; i++) {
                    newCats.push(data['series'][i].y)
                }

                chart2.xAxis[0].setCategories(newCats);


                return false;
            }

            //Отрисовка легенды            
            function renderLegend(data) {
                $('.govprogs-legend').empty();
                $.each(data, function (key, val) {
                    row =
                            $('<div>', {class: 'w-row'}).appendTo(
                            $('<div>', {class: key % 2 ? 'legend' : 'legend light-silver',
                            }
                            )
                            .appendTo($('.govprogs-legend')));
                    $('<div>', {class: 'govprogs-l-1', css: {'background': val.color}}).appendTo($('<div>', {'class': 'w-col w-col-1 marker-cell'}).appendTo(row));
                    $('<a>', {class: 'govprogs-l-1-link', href: '#'}).text(val.name.substring(0, 60) + '...').appendTo($('<div>', {'class': 'w-col w-col-9 legend-collumn'}).appendTo(row));
                    $('<a>', {class: 'govprogs-l-1-link', href: '#'}).text(val.y).appendTo($('<div>', {'class': 'w-col w-col-2 num-cell'}).appendTo(row));

                });
                return false;
            }

        </script>

        <?
    }

}
