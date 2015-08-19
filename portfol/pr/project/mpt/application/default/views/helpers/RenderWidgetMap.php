<?php

/**
 * Страница Л/С
 * @author sciner
 * @since 2015-07-13
 */
class Zend_View_Helper_RenderWidgetMap extends Zend_View_Helper_Abstract {

    public function renderWidgetMap($widget) {
        ?>
        <li data-widget-id ="<?= $widget->id ?>" class="cards-item  <?= $widget->is_expanded ? 'is-expanded' : '' ?>" data-item="card" id="map_widget">
            <ul class="cards-opt">
                <li class="cards-opt-item">
                    <a href="#" class="cards-opt-link">
                        <span class="icon icon-remove remove-widget"></span>
                    </a>
                </li>
                <li class="cards-opt-item">
                    <a href="#" class="cards-opt-link">
                        <span class="icon icon-legend hide_legend"></span>
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
                    <a href="#" id="drillup-map" style="color:#362f2d;display:none">Назад</a>
                    <h2 class="cards-title"></h2>
                </header>
                <div class="cards-icon">
                    <span class="icon icon-star"></span>
                </div>
                <div class="cards-amount">
                    <span class="cards-amount-value"><?= $widget->money[0]->y ?></span>
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
                    <div class="cards-chart-graph map"  data-widget-id ="<?= $widget->id ?>" id="container"></div>
                </div>
                <aside style="" class="cards-sidebar" data-item="sidebar">
                    <div class="cards-legend" data-container="legend">
                        <ul data-widget-id="<?= $widget->id ?>" class="cards-legend-list"></ul>
                        <a href="#" class="cards-legend-more">Показать еще...</a>
                    </div>
                </aside>
            </div>
        </li>
        <style>
            #map_widget .highcharts-button{
                display: none;
            }
        </style>
        <script>
            $(function () {
//                var colors = ["#FF6600", "#FF9E01", "#FCD202", "#F8FF01", "#B0DE09", "#04D215", "#0D8ECF", "#0D52D1", "#2A0CD0", "#8A0CCF", "#CD0D74", "#754DEB", "#DDDDDD", "#999999", "#333333", "#000000", "#57032A", "#CA9726", "#990000", "#4B0C25"];
                var colors = [
                    "#619bb2",
                    "#61b2b0",
                    "#6f87b9",
                    "#7ab492",
                    "#a28cb0",
                    "#b47a9c",

                    "#c2e4fe",
                    "#dbfec2",
                    "#fedcc2",
                    "#def8f0",
                    "#f6cee2",
                    "#fdbba7",

                    "#fdc689",
                    "#f6989d",
                    "#82ca9c",
                    "#bd8cbf",
                    "#c4df9b",
                    "#c7b29a",

                    "#998675",
                    "#a3d49c",
                    "#8781be",
                    "#75bfba",
                    "#ddc4a7",
                    "#b7b7b7",
                ];




                $('.cards-item[data-widget-id="<?= $widget->id ?>"] .cards-title').text("<?= $widget->title ?>");
                try {
                    // Some responsiveness
//                    var small = $('.cards-item[data-widget-id="<?//= $widget->id ?>//"] .cards-chart-graph').width() < 400;
                    var init_data = Highcharts.geojson(Highcharts.maps['countries/ru/ru-all']);
                    // Set drilldown pointers
                    $.each(init_data, function (i) {
                        var id = this.properties['id'];
                        this.drilldown = this.properties['hc-key'];
                        var districts = $.parseJSON('<?= json_encode($widget->chart_data_districts) ?>');
                        var district = $.grep(districts, function (e) {
                            return e.id === id;
                        });
                        this.value = district[0]['value']; // Non-random bogus data
                        this.color = colors[i];
                    });
                    // Instanciate the map
                    $('.cards-item[data-widget-id="<?= $widget->id ?>"] .cards-chart-graph').highcharts('Map', {
                        chart: {
                            backgroundColor: '#efefef',
                            events: {
                                drilldown: function (e) {

                                    $('#drillup-map').show();
                                    $('.cards-item[data-widget-id="<?= $widget->id ?>"] .cards-amount-value').text(e.point.value);
                                    if (!e.seriesOptions) {
                                        var chart = this;
                                        var mapKey = 'countries/ru/' + e.point.drilldown + '-all';
                                        // Handle error, the timeout is cleared on success
                                        var fail = setTimeout(function () {
                                            if (!Highcharts.maps[mapKey]) {
                                                chart.showLoading('<i class="icon-frown"></i> Ошибка загрузки ' + e.point.name);
                                                fail = setTimeout(function () {
                                                    chart.hideLoading();
                                                }, 1000);
                                            }
                                        }, 3000);
                                        // Show the spinner
                                        chart.showLoading(CustomUI.wait_image);
                                        // Load the drilldown map
                                        // $.getScript('http://code.highcharts.com/mapdata/' + mapKey + '.js', function () {
                                        // mapKey = 'countries/ru/ru-ck-all';
                                        // alert(mapKey);
                                        $.getScript('/mapdata/?mapkey=' + mapKey, function () {
                                            data = Highcharts.geojson(Highcharts.maps[mapKey]);
                                            renderLegend(data);
                                            // Set a non-random bogus value
                                            $.each(data, function (i) {
                                                var id = this.properties['id'];
                                                var regions = $.parseJSON('<?= json_encode($widget->chart_data) ?>');
                                                var region = $.grep(regions, function (e) {
                                                    return e.id === id;
                                                });
                                                this.value = region[0]['value']; // Non-random bogus data

                                            });

                                            // Hide loading and add series
                                            chart.hideLoading();
                                            clearTimeout(fail);
                                            chart.addSeriesAsDrilldown(e.point, {
                                                name: e.point.name,
                                                data: data,
                                                dataLabels: {
                                                    enabled: true,
                                                    format: '{point.name}'
                                                }
                                            });
                                        });
                                    }
                                    this.setTitle(null, {text: e.point.name});
                                },
                                drillup: function () {
                                    $('.cards-item[data-widget-id="<?= $widget->id ?>"] .cards-amount-value').text('<?= $widget->money[0]->y ?>');
                                    renderLegend(init_data);
                                    this.setTitle(null, {text: 'Россия'});
                                }
                            }
                        },
                        title: {
                            text: '' // Россия
                        },
                        credits: {
                            enabled: false
                        },
                        subtitle: {
                            text: '', // Россия
                            floating: true,
                            align: 'right',
                            y: 50,
                            style: {
                                fontSize: '16px'
                            }
                        },
//                        legend: small ? {} : {
//                            layout: 'vertical',
//                            align: 'right',
//                            verticalAlign: 'middle'
//                        },
                        legend:{
                            enabled:false
                        },
//                        colorAxis: {
//                            min: 0,
//                            minColor: '#E6E7E8',
//                            maxColor: '#005645'
//                        },
                        mapNavigation: {
                            enabled: true,
                            buttonOptions: {
                                verticalAlign: 'bottom'
                            }
                        },
                        plotOptions: {
                            map: {
                                states: {
                                    hover: {
                                        color: 'white'
                                    }
                                }
                            }
                        },
                        series: [{
                                data: init_data,
                                name: 'Россия',
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.properties.postal-code}'
                                },
                                point: {
                                    events: {
                                        // Эфект затеменения 
                                        mouseOver: function () {
                                            $('.cards-legend-list[data-widget-id="<?= $widget->id ?>"]').children().css({'opacity': 0.4});
                                            $('.cards-legend-list[data-widget-id="<?= $widget->id ?>"]').children().eq(this.x).css({'opacity': 1});
                                        },
                                        mouseOut: function () {
                                            $('.cards-legend-list[data-widget-id="<?= $widget->id ?>"]').children().css({'opacity': 1});
                                        },
                                    },
                                }
                            }],
                        drilldown: {
                            series: {
                                point: {
                                    events: {
                                        // Эфект затеменения 
                                        mouseOver: function () {
                                            console.log(this);
                                            $('.cards-legend-list[data-widget-id="<?= $widget->id ?>"]').children().css({'opacity': 0.4});
                                            $('.cards-legend-list[data-widget-id="<?= $widget->id ?>"]').children().eq(this.x).css({'opacity': 1});
                                        },
                                        mouseOut: function () {
                                            $('.cards-legend-list[data-widget-id="<?= $widget->id ?>"]').children().css({'opacity': 1});
                                        },
                                    },
                                }
                            },
                            // series: drilldownSeries,
                            activeDataLabelStyle: {
                                color: '#FFFFFF',
                                textDecoration: 'none',
                                textShadow: '0 0 3px #000000'
                            },
                            drillUpButton: {
                                relativeTo: 'spacingBox',
                                position: {
                                    x: 0,
                                    y: 60
                                }
                            }
                        }
                    });
                    renderLegend(init_data);
                } catch (e) {
                    alert(e);
                    return false;
                }

                $('#drillup-map').click(function () {
                    if ($('.cards-item[data-widget-id="<?= $widget->id ?>"] .cards-chart-graph').highcharts().drilldownLevels.length > 0) {
                        $('.cards-item[data-widget-id="<?= $widget->id ?>"] .cards-chart-graph').highcharts().drillUp();
                        $(this).hide();
                    }
                });

                $('#map_widget').on('click', '.hide_legend', function () {
                    var self = $('#map_widget');
                    self.find('.cards-sidebar').toggleClass('is-collapsed');
                    $('#container').closest('.cards-chart').toggleClass('is-expanded');
                    if ($('#container').closest('.cards-chart').hasClass('is-expanded')) {
                        setTimeout(function () {
                            $('#container').highcharts().setSize(605, 375, false);
                        }, 100);
                    } else {
                        $('#container').highcharts().setSize(393.25, 375, false);
                    }
                    return false;
                });

                $('#map_widget').on('click', '.remove-widget', function () {
                    var self = $('#map_widget');
                    self.fadeOut(400, function () {
                        $(this).remove();
                    });
                });

                $('#map_widget').on('click', '.cards-legend-more', function () {
                    $(this).siblings('.cards-legend-list').css({'overflow-y': 'auto'});
                    $(this).hide();
                    return false;
                });
            });

            //Отрисовка легенды            
            function renderLegend(data) {
                $('.cards-legend-list[data-widget-id="<?= $widget->id ?>"]').empty();
                $.each(data, function (key, val) {
                    row = $('<li>', {class: 'cards-legend-item', 'data-item': 'view'}).appendTo($('.cards-legend-list[data-widget-id="<?= $widget->id ?>"]'));
                    link = $('<a>', {'href': '#', 'class': 'cards-legend-link'}).appendTo(row);
                    $('<span>', {class: 'cards-legend-marker', css: {'background': val.color}}).appendTo(link);
                    $('<span>', {class: 'cards-legend-text'}).text(val.name).appendTo(link);
                });
                return false;
            }

            // Дополнительные обработчики для событий.

            $('.cards-legend-list[data-widget-id="<?= $widget->id ?>"]')
                    .on('mouseover', '.cards-legend-item', function () {
                        $('.cards-legend-item').css({'opacity': '0.4'});
                        $(this).css({'opacity': 1});
                        $('.cards-item[data-widget-id="<?= $widget->id ?>"] .cards-chart-graph').highcharts().series[0].data[$(this).index()].setState('hover');
                    })
                    .on('mouseout', '.cards-legend-item', function () {
                        $('.cards-legend-item').css({'opacity': '1'});
                        $('.cards-item[data-widget-id="<?= $widget->id ?>"] .cards-chart-graph').highcharts().series[0].data[$(this).index()].setState();
                    })
                    .on('click', '.cards-legend-item', function () {
                        $('.cards-item[data-widget-id="<?= $widget->id ?>"] .cards-chart-graph').highcharts().series[0].data[$(this).index()].firePointEvent('click');
                    });
        </script>
        <?
    }

}
