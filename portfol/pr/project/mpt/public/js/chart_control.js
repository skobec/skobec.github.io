/**
 * Created by mart on 17.06.15.
 */

var last_mouse_over_index = -1;
var last_mouse_out_index = -1;

var window_width = $(window).width();
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

/* Функция для клонирования объектов */
function clone(obj) {
    if (obj == null || typeof (obj) != 'object')
        return obj;
    var temp = new obj.constructor();
    for (var key in obj)
        temp[key] = clone(obj[key]);
    return temp;
}

/**
 * Конструктор класса
 * @param int id сценария, для которого отрисовывается график
 * @param object[] data данные виджета
 * @param object[] opts объект дополнительных опций виджета
 *
 * */
function ChartControl(id, data, opts) {
    //"#FF0F00"
    //this.colors = ["#FF6600","#FF9E01","#FCD202","#F8FF01","#B0DE09","#04D215","#0D8ECF","#0D52D1","#2A0CD0","#8A0CCF","#CD0D74","#754DEB","#DDDDDD","#999999","#333333","#000000","#57032A","#CA9726","#990000","#4B0C25"
    //];
    this.colors = colors;
            //this.colors =  ['rgb(65, 174, 118)', 'rgb(140, 107, 177)', 'rgb(78, 179, 211)', 'rgb(65, 171, 93)', 'rgb(115, 115, 115)',
            //    'rgb(241, 105, 19)', 'rgb(239, 101, 72)', 'rgb(54, 144, 192)', 'rgb(231, 43, 138)', 'rgb(106, 81, 163)',
            //    'rgb(203, 29, 29),rgb(65, 171, 93),rgb(28, 145, 192),rgb(236, 112, 19)'],
            //this.colors.reverse();
    this.id = (id == undefined) ? '' : id;
    this.chart_type;
    this.defaults = {
        chart: $('#container' + this.id),
        widget: $('#widget' + id),
        widget_width: 605,
        window_width: window_width,
        /* фильтры графиков по умолчанию*/
        filter_spending: 'plan',
        filter_chart_state: 'quarters',
        filter_years: [2015],
        filter_sort:'desc',

        filter_compare:false, //флаг, определяющий активно сравнение план/факт или нет( если true - то выбран пункт сравнения план/факт)
        filter_show_grouped_data:false // флаг, отвечаюзий за отображение прочих статей (пункт легенды "Остальные"), если стоит TRUE  - отображаются все данные без скрытия
    };
    this.history = [];
    this.hidden_items = [];
    this.opts = $.extend(true, this.defaults, opts); // объединение объекта значений по умолчанию с объектом переданных опций

    if (data !== undefined) {
        this.master_data_other = data.other_data;
        this.master_data_pie = data.chart_data;

        this.pie_fact = data.chart_data['fact'];// данные плана и факта для кругового графика
        this.pie_plan = data.chart_data['plan'];
        this.other_fact; // данные плана и факта для прочих графиков
        this.other_plan;

        this.chart_data = this.pie_plan; // свойства для хранения данных, используемых в текущий момент ( план или факт)
        this.other_data = this.other_plan;

        this.xAxis;
        this.total_sum = data.money[0].y;
        this.title_with_links = data.title; // тайтл с ключевыми словами обернутыми ссылками
        this.title = data.title; // тайтл без изменений
    }

    this.bindEventsHandlers();
}

ChartControl.prototype.convertToMonthsState = function () {
    var fact = [];
    var plan = [];
    for (var item = 0; item < this.master_data_other.length; item++) {
        var fact_data = [];
        var plan_data = [];
        var xAxis = [];
        for (var y = 0; y < this.opts.filter_years.length; y++) {
            var year = this.opts.filter_years[y];
            for (var i = 1; i < 13; i++) {
                /* Формирование оси X*/
                if (this.opts.filter_years.length > 1 && i == 1) {
                    xAxis.push(year);
                } else {
                    xAxis.push(i);
                }

                if (this.master_data_other[item]['plan'][year][i] == undefined) {
                    this.master_data_other[item]['plan'][year][i] = 0;
                }
                if (this.master_data_other[item]['fact'][year][i] == undefined) {
                    this.master_data_other[item]['fact'][year][i] = 0;
                }
                plan_data.push(this.master_data_other[item]['plan'][year][i]);
                fact_data.push(this.master_data_other[item]['fact'][year][i]);
            }
        }
        plan[item] = {
            data: plan_data,
            name: this.master_data_other[item]['name']
        };
        fact[item] = {
            data: fact_data,
            name: this.master_data_other[item]['name']
        };
    }
    this.xAxis = xAxis;
    this.other_plan = plan;
    this.other_fact = fact;
    this.pie_fact = this.master_data_pie.fact;
    this.pie_plan = this.master_data_pie.plan;
    this.other_data = this.opts.filter_spending == 'fact' ? this.other_fact : this.other_plan;
    this.chart_data = this.opts.filter_spending == 'fact' ? this.pie_fact : this.pie_plan;
}

/* Формирование серий графика с разбивкой по кварталам*/
ChartControl.prototype.convertToQuartersState = function () {
    var fact = [];
    var plan = [];
    var xAxis = [];
    for (var item = 0; item < this.master_data_other.length; item++) {
        var fact_data = [];
        var plan_data = [];
        for (var y = 0; y < this.opts.filter_years.length; y++) {
            var year = this.opts.filter_years[y];

            var quarter4_plan = 0, quarter4_fact = 0, quarter3_plan = 0, quarter3_fact = 0,
                    quarter2_plan = 0, quarter2_fact = 0, quarter1_plan = 0, quarter1_fact = 0;

            for (var i = 1; i < 13; i++) {
                if (this.master_data_other[item]['plan'][year][i] == undefined) {
                    this.master_data_other[item]['plan'][year][i] = 0;
                }
                if (this.master_data_other[item]['fact'][year][i] == undefined) {
                    this.master_data_other[item]['fact'][year][i] = 0;
                }

                if (i > 9) {
                    quarter4_plan += this.master_data_other[item]['plan'][year][i];
                    quarter4_fact += this.master_data_other[item]['fact'][year][i];
                } else if (i > 6) {
                    quarter3_plan += this.master_data_other[item]['plan'][year][i];
                    quarter3_fact += this.master_data_other[item]['fact'][year][i];
                } else if (i > 3) {
                    quarter2_plan += this.master_data_other[item]['plan'][year][i];
                    quarter2_fact += this.master_data_other[item]['fact'][year][i];
                } else {
                    quarter1_plan += this.master_data_other[item]['plan'][year][i];
                    quarter1_fact += this.master_data_other[item]['fact'][year][i];
                }
            }
            plan_data.push(quarter1_plan, quarter2_plan, quarter3_plan, quarter4_plan);
            fact_data.push(quarter1_fact, quarter2_fact, quarter3_fact, quarter4_fact);
        }
        plan[item] = {
            data: plan_data,
            name: this.master_data_other[item]['name']
        };
        fact[item] = {
            data: fact_data,
            name: this.master_data_other[item]['name']
        };
    }

    /* Формирование оси X*/
    for (var y = 0; y < this.opts.filter_years.length; y++) {
        var year = this.opts.filter_years[y];
        if (this.opts.filter_years.length > 1) {
            xAxis.push(year, 'II квартал', 'III квартал', 'IV квартал');
        } else {
            xAxis.push('I квартал', 'II квартал', 'III квартал', 'IV квартал');
        }
    }
    this.xAxis = xAxis;
    this.other_plan = plan;
    this.other_fact = fact;
    this.pie_fact = this.master_data_pie.fact;
    this.pie_plan = this.master_data_pie.plan;
    this.other_data = this.opts.filter_spending == 'fact' ? this.other_fact : this.other_plan;
    this.chart_data = this.opts.filter_spending == 'fact' ? this.pie_fact : this.pie_plan;
}

/**
 * Подсвтека активного типа графика
 */
ChartControl.prototype.setActiveType = function () {
    switch (this.chart_type) {
        case 'pie':
            this.opts.widget.find('.icon-pie').closest('li').addClass('is-active');
            break;
        case 'area':
            this.opts.widget.find('.icon-percentage').closest('li').addClass('is-active');
            break;
        case 'stacked_area':
            this.opts.widget.find('.icon-stacked').closest('li').addClass('is-active');
            break;
        case 'basic':
            this.opts.widget.find('.icon-line').closest('li').addClass('is-active');
            break;
        case 'columns':
            this.opts.widget.find('.icon-bars').closest('li').addClass('is-active');
            break;
    }
}


/* Подготовка структуры данных для графиков в зависимости от выбранного представления графика (кварталы, месяцы)*/
ChartControl.prototype.prepareData = function () {
    switch (this.opts.filter_chart_state) {
        case 'quarters':
            this.convertToQuartersState();
            break;
        case 'months':
            this.convertToMonthsState();
            break;
        default:
            this.convertToQuartersState();
            break;
    }
    if(this.opts.filter_show_grouped_data == false){ // если выставлен флаг, скрывающий второстепенные статьи
        this.chart_data = this.buildShortDataForPie(this.chart_data);
        this.other_data = this.buildShortDataForOthers(this.other_data);
    }
    this.setDataVisiblity();
    this.sortChart();
}

/*Проваливание в график*/
ChartControl.prototype.chartDrilldown = function (chart_type, index) {
    var self = this;
    var data = self.master_data_pie.plan !== undefined ? self.master_data_pie.plan[index].data : undefined;
    if (data !== undefined) {
        var type = self.master_data_pie.plan !== undefined && self.master_data_pie.plan[0].type ? self.master_data_pie.plan[0].type[0] : undefined;
        var total_sum = self.master_data_pie.plan[index].y;
        var new_title = self.master_data_pie.plan[index].name;
        if (type && data) {
            $.getJSON('/chart/getdata', {
                type: type,
                data: data
            }, function (data) {
                if (data && data.other_series[0].plan) {
                    self.history.push({
                        master_data_pie: self.master_data_pie,
                        master_data_other: self.master_data_other, //при проваливании - запись в историю предыдущего шага
                        title: self.title,
                        sum: self.total_sum
                    });
                    self.hidden_items = [];
                    self.title_with_links = self.replaceTitleKeywords(_HINTS,new_title);
                    self.title = new_title;
                    self.total_sum = total_sum;
                    self.master_data_other = data.other_series;
                    self.master_data_pie = data.series;
                    self.opts.filter_show_grouped_data = false;
                    self.renderChart(chart_type);

                    //замена тайтлов и сумм виджета при проваливании в развернутом и обычном режиме
                    var title_element = $('#widget_title' + self.id);
                    var title_element_fullscr = $('#widget_title_fullscr' + self.id);

                    if (new_title.length > 110) {
                        title_element.css({'font-size': '11px'});
                    } else {

                    }

                    title_element.html(self.title_with_links);
                    title_element_fullscr.html(self.title_with_links);

                    $('#widget_amount' + self.id).text(total_sum);
                    $('#widget_amount_fullscr' + self.id).text(total_sum);
                    $('#back_btn' + self.id).text('назад');
                }
            });
        }
    }
}

ChartControl.prototype.chartDrillup = function () {
    var self = this;
    self.opts.filter_show_grouped_data = false;
    var history = self.history.pop();
    self.hidden_items = [];
    self.master_data_other = history.master_data_other;
    self.master_data_pie = history.master_data_pie;
    self.total_sum = history.sum;
    self.title = history.title;

    self.title_with_links = self.replaceTitleKeywords(_HINTS,self.title);
    self.renderChart(self.chart_type);

    var title_element = $('#widget_title' + self.id);
    if (self.title.length > 110) {
        title_element.css({'font-size': '11px'});
    } else {
        title_element.css({'font-size': '13px'});
    }
    ;
    title_element.html(self.title_with_links);
    $('#widget_title_fullscr' + self.id).html(self.title_with_links);

    $('#widget_amount_fullscr' + self.id).text(self.total_sum);
    $('#widget_amount' + self.id).text(self.total_sum);
    if (!self.history.length) {
        $('#back_btn' + self.id).text('');
    }
}

/**
 * Орисовка графика на основании входных данных
 * */
ChartControl.prototype.renderChart = function (chart_type) {
    this.destroyChart();
    console.time('render Widget:' + this.id);
    this.prepareData();
    this.chart_type = chart_type;
    this.setActiveType();
    var options = {};
    switch (chart_type) {
        case 'columns':
            options = this.renderColumns();
            break;
        case 'pie':
        case 'programs':
            options = this.renderCustomPie();
            break;
        case 'area':
            options = this.renderArea()
            break;
        case 'stacked_area':
            options = this.renderStackedArea()
            break;
        case 'stacked_columns':
            options = this.renderStackedColumns();
            break;
        case 'basic':
            options = this.renderBasicLine();
            break;
    };
    this.opts.chart.highcharts(options);
    this.renderLegend(chart_type);
    console.timeEnd('render Widget:' + this.id);
}

/*Уничтожение графика*/
ChartControl.prototype.destroyChart = function () {
    if (this.opts.chart.highcharts()) {
        this.opts.chart.highcharts().destroy();
    }
}

/*Отрисовка различных типов графиков*/
ChartControl.prototype.renderStackedColumns = function () {
    var self = this;
    var tooltip_data = null;
    return {
        colors: self.colors,
        chart: {
            type: 'column'
        },
        legend: {
            enabled: false
        },
        credits: {
            enabled: false
        },
        tooltip: {
            positioner: function (labelWidth, labelHeight, point) {
                var coords = {x: point.plotX - 25, y: point.plotY - 200};
                return coords;
            },
            shape: 'square',
            useHTML: true,
            backgroundColor: null,
            borderWidth: 0,
            shadow: false,
            formatter: function () {
                return self.renderTooltip(this);
            }
        },
        title: {
            style: {
                display: 'none'
            }
        },
        yAxis: {
            gridLineWidth: 0.5,
            title: {
                enabled: false
            },
            offset: 0
        },
        xAxis: {
            categories: self.xAxis,
            crosshair: true,
            gridLineWidth: 1

        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    //enabled: true,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: {
                        //textShadow: '0 0 3px black',
                        fontSize: "10px"
                    }
                },
                states: {
                    select: {
                        lineWidth: 1,
                        radius: 4,
                        //lineColor:,
                        lineColor: 'white',
                        fillColor: 'none'
                    },
                    hover: {
                        enabled: true,
                        lineWidth: 1.5,
                        radius: 5
                    }
                }
            }
        },
        series: self.other_data
    };
}

ChartControl.prototype.renderArea = function () {
    var self = this;
    return {
        colors: self.colors,
        chart: {
            type: 'area',
            backgroundColor: '#efefef',
            className: 'chartArea',
        },
        exporting: {enabled: false},
        legend: {
            enabled: false
        },
        credits: {
            enabled: false
        },
        tooltip: {
            positioner: function (labelWidth, labelHeight, point) {
                var coords = {x: point.plotX - (labelWidth / 2 + 13), y: point.plotY - 205};
                return coords;
            },
            shape: 'square',
            useHTML: true,
            backgroundColor: null,
            borderWidth: 0,
            shadow: false,
            formatter: function () {
                return self.renderTooltip(this);
            }
        },
        title: {
            style: {
                display: 'none'
            }
        },
        yAxis: {
            gridLineWidth: 0,
            title: {
                enabled: false
            },
            offset: -5
        },
        xAxis: {
            minTickInterval: 1,
            gridLineWidth: 1,
            lineColor: '#000000',
            lineWidth: 2,
            tickInterval: 1,
            title: {
                enabled: false
            },
            showEmpty: false,
            labels: {
                distance: 0.5,
                formatter: function () {
                    return self.xAxis[this.value];
                }
            }
        },
        plotOptions: {
            area: {
                allowPointSelect: true,
                stacking: 'percent',
                lineColor: 'inherite',
                lineWidth: 0.5,
                marker: {
                    lineWidth: 1,
                    lineColor: '#ffffff'
                },
                trackByArea: true,
                events: {
                    click: function () {
                        self.chartDrilldown('area', this.index);
                    },
                    mouseOver: function () {
                        var index = this.index;
                        this.update({fillOpacity: 0.65});
                        self.opts.widget.find('.cards-legend-list').children('li').eq(index).addClass('is-active').siblings().addClass('is-inactive');

                    },
                    mouseOut: function () {
                        var index = this.index;
                        this.update({fillOpacity: 0.75});
                        self.opts.widget.find('.cards-legend-list').children('li').eq(index).removeClass('is-active').siblings().removeClass('is-inactive');
                    }
                }
            },
            series: {
                animation: {
                    duration: 300
                },
                showEmpty: false,
                cursor: 'pointer',
                marker: {
                    enabled: true,
                    lineWidth: 1,
                    radius: 4,
                    symbol: 'circle',
                    states: {
                        select: {
                            lineWidth: 1.5,
                            radius: 5,
                            //lineColor:,
                            lineColor: 'white',
                            fillColor: 'none'
                        },
                        hover: {
                            enabled: true,
                            lineWidth: 1.5,
                            radius: 5
                        }
                    }
                }
            }
        },
        series: self.compareData()
    };
}

ChartControl.prototype.renderStackedArea = function () {
    var self = this;
    return {
        colors: self.colors,
        chart: {
            type: 'area',
            backgroundColor: '#efefef'
        },
        legend: {
            enabled: false
        },
        exporting: {enabled: false},
        credits: {
            enabled: false
        },
        tooltip: {
            //positioner: function (labelWidth, labelHeight, point) {
            //    var coords = {x:point.plotX - 15,y:point.plotY-200};
            //    return coords;
            //},
            positioner: function (labelWidth, labelHeight, point) {
                var coords = {x: point.plotX - (labelWidth / 2 + 20), y: point.plotY - 205};
                return coords;
            },
            shape: 'square',
            useHTML: true,
            backgroundColor: null,
            borderWidth: 0,
            shadow: false,
            formatter: function () {
                return self.renderTooltip(this);
            }
        },
        title: {
            style: {
                display: 'none'
            }
        },
        yAxis: {
            gridLineWidth: 0,
            title: {
                enabled: false
            },
            offset: -5
        },
        xAxis: {
            minTickInterval: 1,
            gridLineWidth: 1,
            lineColor: '#000000',
            lineWidth: 2,
            tickInterval: 1,
            title: {
                enabled: false
            },
            showEmpty: false,
            labels: {
                distance: 1,
                formatter: function () {
                    return self.xAxis[this.value];
                }
            }
        },
        plotOptions: {
            area: {
                stacking: 'normal',
                lineColor: 'inherite',
                lineWidth: 0.5,
                marker: {
                    lineWidth: 1,
                    lineColor: '#ffffff'
                },
                trackByArea: true,
                /*stickyTracking: false,*/
                events: {
                    click: function () {
                        self.chartDrilldown('stacked_area', this.index);
                    },
                    mouseOver: function () {
                        var index = this.index;
                        this.update({fillOpacity: 0.65});
                        self.opts.widget.find('.cards-legend-list').children('li').eq(index).addClass('is-active').siblings().addClass('is-inactive');

                    },
                    mouseOut: function () {
                        var index = this.index;
                        this.update({fillOpacity: 0.75});
                        self.opts.widget.find('.cards-legend-list').children('li').eq(index).removeClass('is-active').siblings().removeClass('is-inactive');
                    }
                }
            },
            series: {
                animation: {
                    duration: 300
                },
                cursor: 'pointer',
                marker: {
                    enabled: true,
                    lineWidth: 1,
                    radius: 4,
                    symbol: 'circle',
                    states: {
                        select: {
                            lineWidth: 1.5,
                            radius: 4.5,
                            //lineColor:,
                            lineColor: 'white'
                        },
                        hover: {
                            enabled: true,
                            lineWidth: 1.5,
                            radius: 4.5
                        }
                    }
                }
            }
        },
        series: self.compareData()
    };
}

ChartControl.prototype.renderBasicLine = function () {
    var self = this;
    return {
        colors: self.colors,
        chart: {
            backgroundColor: '#efefef'
        },
        legend: {
            enabled: false
        },
        exporting: {enabled: false},
        credits: {
            enabled: false
        },
        tooltip: {
            positioner: function (labelWidth, labelHeight, point) {
                var coords = {x: point.plotX - (labelWidth / 2 + 20), y: point.plotY - 205};
                return coords;
            },
            shape: 'square',
            useHTML: true,
            backgroundColor: null,
            borderWidth: 0,
            shadow: false,
            formatter: function () {
                return self.renderTooltip(this);
            }
        },
        title: {
            style: {
                display: 'none'
            }
        },
        yAxis: {
            gridLineWidth: 0,
            title: {
                enabled: false
            },
            offset: -5,
            min: 0,
            showEmpty: false
        },
        xAxis: {
            minTickInterval: 1,
            gridLineWidth: 1,
            lineColor: '#000000',
            lineWidth: 2,
            tickInterval: 1,
            title: {
                enabled: false
            },
            showEmpty: false,
            labels: {
                distance: 1,
                formatter: function () {
                    return self.xAxis[this.value];
                }
            }
        },
        //xAxis: {
        //    //categories: data.settings['x_axis'],
        //    categories: self.xAxis,
        //    crosshair: true,
        //    gridLineWidth: 1
        //
        //},
        plotOptions: {
            line: {
                events: {
                    click: function () {
                        self.chartDrilldown('basic', this.index);
                    },
                    mouseOver: function () {
                        var index = this.index;
                        self.opts.widget.find('.cards-legend-list').children('li').eq(index).addClass('is-active').siblings().addClass('is-inactive');

                    },
                    mouseOut: function () {
                        var index = this.index;
                        self.opts.widget.find('.cards-legend-list').children('li').eq(index).removeClass('is-active').siblings().removeClass('is-inactive');
                    }
                }
            },
            series: {
                animation: {
                    duration: 300
                },
                showEmpty: false,
                cursor: 'pointer',
                marker: {
                    enabled: true,
                    lineWidth: 1,
                    radius: 4,
                    symbol: 'circle',
                    states: {
                        select: {
                            lineWidth: 1.5,
                            radius: 5,
                            //lineColor:,
                            lineColor: 'white',
                            fillColor: 'none'
                        },
                        hover: {
                            enabled: true,
                            lineWidth: 1.5,
                            radius: 5
                        }
                    }
                }
            }

        },
        //series: data.series
        series: self.other_data
    };
}

ChartControl.prototype.renderColumns = function () {
    var self = this;
    return {
        colors: self.colors,
        chart: {
            type: 'column',
            backgroundColor: '#efefef'
        },
        legend: {
            enabled: false
        },
        exporting: {enabled: false},
        credits: {
            enabled: false
        },
        tooltip: {
            positioner: function (labelWidth, labelHeight, point) {
                var coords = {x: point.plotX - 20, y: point.plotY - 200};
                return coords;
            },
            shape: 'square',
            useHTML: true,
            backgroundColor: null,
            borderWidth: 0,
            shadow: false,
            formatter: function () {
                return self.renderTooltip(this);
            }
        },
        title: {
            style: {
                display: 'none'
            }
        },
        yAxis: {
            gridLineWidth: 0.5,
            title: {
                enabled: false
            },
            offset: 0
        },
        xAxis: {
            //categories: data.settings['x_axis'],
            categories: self.xAxis,
            crosshair: true,
            gridLineWidth: 1

        },
        plotOptions: {
            column: {
                pointPadding: 0.1,
                borderWidth: 0,
                //trackByArea: true,
                events: {
                    click: function () {
                        self.chartDrilldown('columns', this.index);
                    },
                    mouseOver: function () {
                        var index = this.index;
                        this.update({fillOpacity: 0.75});
                        self.opts.widget.find('.cards-legend-list').children('li').eq(index).addClass('is-active').siblings().addClass('is-inactive');

                    },
                    mouseOut: function () {
                        var index = this.index;
                        this.update({fillOpacity: 1});
                        self.opts.widget.find('.cards-legend-list').children('li').eq(index).removeClass('is-active').siblings().removeClass('is-inactive');
                    }
                }
            },
            series: {
                animation: {
                    duration: 300
                }
            }
        },
        //series: data.series
        series: self.compareData()
    };
}

ChartControl.prototype.renderCustomPie = function () {
    var self = this;
    return {
        colors: self.colors,
        chart: {
            type: 'pie',
            backgroundColor: '#efefef'
        },
        exporting: {enabled: false},
        credits: {
            enabled: false
        },
        title: {
            style: {
                display: 'none'
            }
        },
        tooltip: {
            positioner: function (labelWidth, labelHeight, point) {
                var coords = {x: point.plotX - 15, y: point.plotY - 200};
                return coords;
            },
            shape: 'square',
            useHTML: true,
            backgroundColor: null,
            borderWidth: 0,
            shadow: false,
            formatter: function () {
                return self.renderTooltip(this);
            }
        },
        plotOptions: {
            series: {
                animation: {
                    duration: 300
                },
                cursor: 'pointer',
                point: {
                    events: {
                        //Подгрузка новых данных
                        click: function () {
                            self.chartDrilldown('pie', this.index);
                        },
                        mouseOver: function () {
                            var index = this.index;
                            self.opts.widget.find('.cards-legend-list').children('li').eq(index).addClass('is-active').siblings().addClass('is-inactive');

                        },
                        mouseOut: function () {
                            var index = this.index;
                            self.opts.widget.find('.cards-legend-list').children('li').eq(index).removeClass('is-active').siblings().removeClass('is-inactive');
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
        series: function () {
            if (self.opts.filter_compare) {
                return [{
                        name: 'Факт',
                        data: self.pie_fact,
                        size: '100%'
                    },
                    {
                        name: 'План',
                        type: 'pie',
                        size: '100%',
                        innerSize: '60%',
                        data: self.pie_plan
                    }];
            } else {
                return [{
                        name: '',
                        data: self.chart_data
                    }]
            }

        }()

    }
};

/**
 * Метод преобразует данные графиков, если выбрпан фильтр "План/Факт",
 * склеивая данные плана и факта в один массив
 * */
ChartControl.prototype.compareData = function () {
    var self = this;
    if (self.opts.filter_compare) {
        var new_data = [];

        //если выбран фильтр план/факт объединяем данные плана и факта в один массив , факт данные будут представлены в виде линий
        for (var i = 0; i < self.other_plan.length; i++) {
            new_data.push(self.other_plan[i]);

            var item = clone(self.other_fact[i]);//клонирование объекта факт, иначе изменяется оригинал
            item.type = 'spline';
            item.color = colors[i];

            new_data.push(item);
        }
        self.other_data = new_data;
        return self.other_data;
    } else {
        return self.other_data;
    }
}
/*Отрисовка тултипа*/
ChartControl.prototype.renderTooltip = function (data, coordinates) {
    var coords = '';
    if (coordinates !== undefined) {
        coords = ' style="left:' + (coordinates.x - 15) + 'px;top:' + (coordinates.y - 100) + 'px;width:130px;"';
    }
    //var index = data.series._i;
    if (data.point !== undefined && data.point.index !== undefined) {
        var key = data.point.index;
    } else {
        var key = data.index;
    }
    var tooltip_numbers = '';
    /*здесь будет проверка на существование факт и плановых показателей */
    if (false) {
        tooltip_numbers = '<div class = "cards-chart-tooltip-col">'
                + '<div class = "cards-chart-tooltip-subtitle"> План </div>'
                + '<div class = "cards-chart-tooltip-num">' + data.y.toFixed(1) + '</div>'
                + '</div>'
                + '<div class = "cards-chart-tooltip-col">'
                + '<div class = "cards-chart-tooltip-subtitle"> Факт </div>'
                + '<div class = "cards-chart-tooltip-num">' + fact.toFixed(1) + '</div>';
    } else {
        var tooltip_data = data.y.toFixed(1) > this.total_sum ? this.total_sum : data.y.toFixed(1);
        tooltip_numbers = '<div class = "cards-chart-tooltip-col single">'
                + '<div class = "cards-chart-tooltip-num">' + tooltip_data + '<br><span class="cards-chart-tooltip-units">млрд</span></div>'
                + '</div>';
    }
    //var plan = this.other_data[index].plan_data[key]?this.other_data[index].plan_data[key]:0;
    //var fact = this.other_data[index].fact_data[key]?this.other_data[index].fact_data[key]:0;
    var month = this.chart_type !== 'pie' ? this.getQuarter(key) + ' ' : '';

    if (this.opts.filter_chart_state == 'quarters') {
        var tooltip_title = this.chart_type !== 'pie' ? this.getQuarter(key) + ' ' : '2015';
    } else {
        var tooltip_title = this.chart_type !== 'pie' ? this.getMonth(key) + ' ' : '2015';
    }

    var total_percent = data.y > 0 ? data.y * 100 / this.total_sum : 0;
    return '<div class="cards-chart-tooltip" ' + coords + '>'
            + '<div class = "cards-chart-tooltip-inner">'
            + '<div class = "cards-chart-tooltip-title" >' + tooltip_title + '</div>'
            + '<div class = "cards-chart-tooltip-cols">'
            + tooltip_numbers
            + '</div>'
            + '<div class = "cards-chart-tooltip-summary">'
            + '<div class = "cards-chart-tooltip-percent">' + total_percent.toFixed(1) + ' %</div>'
            //+ '<div class = "cards-chart-tooltip-total">'
            //+ '<div class = "cards-chart-tooltip-arrow">'
            //+ '<span class = "icon icon-arrow-down"> </span></div>'
            //+ '<div class = "cards-chart-tooltip-value"> 5 </div>'
            + '</div>'
            + '</div>'
            + '</div>'
            + '</div>';
    //return '<div class="cards-chart-tooltip" id="chart_tooltip">'
    //    + '<div class = "cards-chart-tooltip-inner">'
    //    + '<div class = "cards-chart-tooltip-title" > Ноябрь 2014 </div>'
    //    + '<div class = "cards-chart-tooltip-cols">'
    //    + '<div class = "cards-chart-tooltip-col">'
    //    + '<div class = "cards-chart-tooltip-subtitle"> План </div>'
    //    + '<div class = "cards-chart-tooltip-num">'+plan.toFixed(1)+'</div>'
    //    + '</div>'
    //    + '<div class = "cards-chart-tooltip-col">'
    //    + '<div class = "cards-chart-tooltip-subtitle"> Факт </div>'
    //    + '<div class = "cards-chart-tooltip-num">'+fact.toFixed(1)+'</div>'
    //    + '</div>'
    //    + '</div>'
    //    + '<div class = "cards-chart-tooltip-summary">'
    //    + '<div class = "cards-chart-tooltip-percent">'+total_percent.toFixed(1)+' % </div>'
    //    + '<div class = "cards-chart-tooltip-total">'
    //    + '<div class = "cards-chart-tooltip-arrow"> <span class = "icon icon-arrow-down"> </span></div>'
    //    + '<div class = "cards-chart-tooltip-value"> 5 </div>'
    //    + '</div>'
    //    + '</div>'
    //    + '</div>'
    //    + '</div>';
}

ChartControl.prototype.renderLegend = function (chart_type) {
    var series = (chart_type == 'pie') ? this.opts.chart.highcharts().series[0].data : this.opts.chart.highcharts().series;
    var legend_container = $('<ul>', {class: 'cards-legend-list'});
    var counter = 0;
    var waiter = null;
    var self = this;

    $.each(series, function (key, val) {
        var visible = val.visible == false?' inactive':'';
        // если у серии стоит флаг объединенных данных (серия является набором объединенных данных "остальные"), запрещаем проваливаться в данную серию
        if(val.options.grouped_data !== undefined && val.options.grouped_data){
            var row = $('<li>', {class: 'cards-legend-item'+visible, 'data-item': val.index, 'data-group':true}).appendTo(legend_container);
        } else{
            var row = $('<li>', {class: 'cards-legend-item'+visible, 'data-item': val.index}).appendTo(legend_container);
        }
        var link = $('<a>', {'href': '#', 'class': 'cards-legend-link'}).appendTo(row);
        $('<span>', {class: 'cards-legend-marker', css: {'background': val.visible == false?'grey' : val.color}})
                .on('click', function (e) {

                val.setVisible();
                //если скрыли элемент легенды - заносим его в массив скрытых элементов, для сохранения состояния при переключении графиков
                if(!val.visible){
                    $(this).addClass('inactive').css({'background': 'grey'});
                    if(val.options.grouped_data){
                        self.hidden_items.push('others');//элемент "Остальные" отсутствует в общем наборе серий, поэтому его помечаем отдельно
                    } else {
                        self.hidden_items.push(val.options.item_index);
                    }
                } else {
                    $(this).removeClass('inactive').css({'background':val.color});
                    self.hidden_items.splice( self.hidden_items.indexOf(val.options.item_index));
                }
                e.preventDefault();
                return false;

            })
                .appendTo(link);
        var item = $('<span>', {class: 'cards-legend-text'});
        item.text(val.name.length > 60 ? val.name.substring(0, 60) + '...' : val.name).appendTo(link).closest('li')
                .on({
                    mouseover: function () {
                        waiter = setTimeout(function () {
                            item.text(val.name);
                        }, 400);
                    },
                    mouseout: function () {
                        clearTimeout(waiter);
                        waiter = null;
                        item.text(val.name.length > 60 ? val.name.substring(0, 60) + '...' : val.name)
                    }
                });

        counter++;
    });
    var legend = this.opts.chart.parent('div').siblings('.cards-sidebar').find('.cards-legend');
    var select = $('#legend_select_'+this.id).clone();
    if (counter > 4) {
        legend.html(legend_container).prepend(select).append('<a href="#" class="cards-legend-more">Показать еще...</a>');
    } else {
        legend.html(legend_container).prepend(select);
    }
    if ($.fn.jelect) {
        $('.js-jelect').jelect();
    }
    return false;
}

ChartControl.prototype.bindEventsHandlers = function () {
    var waiter = null;
    var self = this;
    self.opts.widget.on('click', '.filter-plan', function () {
        var state = self.opts.widget.find('.plan-main').hasClass('active');
        self.opts.widget.find('.plan-main').toggleClass('active', !state).siblings('.plan').removeClass('active');
    });

    self.opts.widget.on('click', '.filter-year', function () {
        var state = $('.years').hasClass('active');
        self.opts.widget.find('.years').toggleClass('active', !state).siblings('.plan').removeClass('active');
    });

    self.opts.widget.on('click', '.filter-period', function () {
        var state = $('.period').hasClass('active');
        self.opts.widget.find('.period').toggleClass('active', !state).siblings('.plan').removeClass('active');
    });

    self.opts.widget.on('change', '#legend_select_'+self.id, function () {
        self.opts.filter_sort = $(this).val();
        self.renderChart(self.chart_type);
    });

    this.opts.widget.on('click', '.icon-percentage', function () {
        self.opts.widget.find('.icon-percentage').each(function () {
            var wrap = $(this).closest('li');
            if (!wrap.hasClass('is-active')) {
                self.renderChart('area');
                wrap.addClass('is-active');
            }
            wrap.siblings('li').removeClass('is-active');
        });
        return false;
    });

    this.opts.widget.on('click', '.icon-bars', function () {
        self.opts.widget.find('.icon-bars').each(function () {
            var wrap = $(this).closest('li');
            if (!wrap.hasClass('is-active')) {
                self.renderChart('columns');
                wrap.addClass('is-active');
            }
            wrap.siblings('li').removeClass('is-active');
        });
        return false;
    });

    this.opts.widget.on('click', '.icon-pie', function () {
        self.opts.widget.find('.icon-pie').each(function () {
            var wrap = $(this).closest('li');
            if (!wrap.hasClass('is-active')) {
                self.renderChart('pie');
                wrap.addClass('is-active');
                wrap.siblings('li').removeClass('is-active');
            }
            wrap.siblings('li').removeClass('is-active');
        });
        return false;
    });

    this.opts.widget.on('click', '.icon-line', function () {
        self.opts.widget.find('.icon-line').each(function () {
            var wrap = $(this).closest('li');
            if (!wrap.hasClass('is-active')) {
                self.renderChart('basic');
                wrap.addClass('is-active');
                wrap.siblings('li').removeClass('is-active');
            }
            wrap.siblings('li').removeClass('is-active');
        });
        return false;
    });

    this.opts.widget.on('click', '.icon-stacked', function () {
        self.opts.widget.find('.icon-stacked').each(function () {
            var wrap = $(this).closest('li');
            if (!wrap.hasClass('is-active')) {
                self.renderChart('stacked_area');
                wrap.addClass('is-active');
                wrap.siblings('li').removeClass('is-active');
            }
            wrap.siblings('li').removeClass('is-active');
        });
        return false;
    });

    this.opts.widget.on('click', '.hide_legend', function () {
        self.opts.widget.find('.cards-sidebar').toggleClass('is-collapsed');
        self.opts.chart.closest('.cards-chart').toggleClass('is-expanded');
        self.resizeChart();

    });

    /*Сворачивание/разворачивание виджета (средний режим)*/
    this.opts.widget.on('click', '.cards-icon', function () {
        //$(this).closest('.cards-opt-item ').toggleClass('is-active').siblings('li').removeClass('is-active');
        if (self.opts.widget.hasClass('cards-item_full')) {
            self.opts.widget.siblings('li').show();
            self.opts.widget.removeClass('cards-item_full');
        }
        self.opts.widget.toggleClass('is-expanded');
        self.resizeChart();
        return false;
    });

    this.opts.widget.on('click', '.remove-widget', function () {
        self.opts.widget.fadeOut(400, function () {
            $(this).remove();
        });
    });

    /*Кнопка легенды - "показать еще"*/
    this.opts.widget.on('click', '.cards-legend-more', function () {
        $(this).siblings('.cards-legend-list').css({'overflow-y': 'auto'});
        $(this).hide();
        return false;
    });

    /*События легенды: проваливание, наведение, всплытие тултипов легенды и.т.д*/
    this.opts.widget.on({
        click: function () {
            var index = $(this).data('item');
            var group = $(this).data('group');
            if(group == undefined){
                self.chartDrilldown(self.chart_type, index);
            } else {
                self.opts.filter_show_grouped_data = true;
                self.renderChart(self.chart_type);
            }
            return false;
        },
        mouseenter: function () {
            var index = $(this).data('item');
            waiter = setTimeout(function () {

                switch (self.chart_type) {
                    case 'pie':
                        self.opts.chart.highcharts().series[0].data[index].setState('hover');
                        break;
                    case 'columns':
                        var data = self.opts.chart.highcharts().series[index].data;
                        var new_tooltip = null;
                        var points = self.opts.chart.highcharts().series[index].data;
                        var tooltip_container = $('<div>', {class: 'tooltip_container'});
                        points.map(function (point) {
                            point.setState('hover');
                            var coords = {};
                            coords.x = point.barX;
                            coords.y = point.plotY;
                            new_tooltip = self.renderLegendTooltip(point, coords);
                            tooltip_container.append(new_tooltip).fadeIn(300);
                        })
                        tooltip_container.appendTo(self.opts.chart);
                        break;
                    case 'basic':
                        self.opts.chart.highcharts().series[index].setState('hover');
                        var new_tooltip = null;
                        var points = self.opts.chart.highcharts().series[index].data;
                        var tooltip_container = $('<div>', {class: 'tooltip_container'});
                        points.map(function (point) {
                            point.pointAttr.select.fill = point.color;
                            point.pointAttr.select.stroke = '#ffffff';
                            point.select(true, true);
                            var coords = {};
                            coords.x = point.plotX;
                            coords.y = point.plotY;
                            new_tooltip = self.renderLegendTooltip(point, coords);
                            tooltip_container.append(new_tooltip).fadeIn(300);
                        })
                        tooltip_container.appendTo(self.opts.chart);

                        break;
                    default:
                        self.opts.chart.highcharts().series[index].update({fillOpacity: 0.65});
                        var new_tooltip = null;
                        var points = self.opts.chart.highcharts().series[index].data;
                        //var color = self.opts.chart.highcharts().series[index].data[0].color;
                        var tooltip_container = $('<div>', {class: 'tooltip_container'});

                        points.map(function (point) {
                            //point.update({fillColor:point.color});
                            point.pointAttr.select.fill = point.color;
                            //point.pointAttr.select.stroke = '#ffffff';
                            point.select(true, true);
                            var coords = {};
                            coords.x = point.plotX;
                            coords.y = point.plotY;
                            new_tooltip = self.renderLegendTooltip(point, coords);
                            tooltip_container.append(new_tooltip).fadeIn(300);
                        })
                        tooltip_container.appendTo(self.opts.chart);
                        break;
                }



            }, 400);

            $(this).addClass('is-active')
                    .siblings('.cards-legend-item').addClass('is-inactive');
            return false;

        },
        mouseleave: function () {
            clearTimeout(waiter);
            waiter = null;
            var index = $(this).data('item');
            switch (self.chart_type) {
                case 'pie':
                    self.opts.chart.highcharts().series[0].data[index].setState();
                    break;
                case 'columns':
                    var data = self.opts.chart.highcharts().series[index].data;
                    for (var i = 0; i < data.length; i++) {
                        self.opts.chart.highcharts().series[index].data[i].setState();
                    }
                    self.opts.chart.find('.tooltip_container').remove();
                    break;
                case 'basic':
                    var points = self.opts.chart.highcharts().series[index].data;
                    points.map(function (point) {
                        point.select(false);
                    })
                    self.opts.chart.highcharts().series[index].setState();
                    self.opts.chart.find('.tooltip_container').remove();
                    break;
                default:
                    self.opts.chart.find('.tooltip_container').remove();
                    self.opts.chart.highcharts().series[index].update({fillOpacity: 0.75});
                    var points = self.opts.chart.highcharts().series[index].data;
                    points.map(function (point) {
                        point.select(false);
                    })

                    break;
            }
            $(this).removeClass('is-active')
                    .siblings('.cards-legend-item').removeClass('is-inactive');
            return false;
        }
    }, '.cards-legend-item');

    /* Кнопка назад (шаг назад в проваливании)*/
    this.opts.widget.on('click', '#back_btn' + self.id, function () {
        if (self.history.length > 0) {
            self.chartDrillup();
        }
        return false;
    });

    /* Кнопка назад развернутый виджет (шаг назад в проваливании)*/
    this.opts.widget.on('click', '#back_btn_fullscr' + self.id, function () {
        if (self.history.length > 0) {
            self.chartDrillup();
        }
        return false;
    });

    this.opts.widget.on('click', '.icon-expand-full', function () {
        //Сохранение состояния виджетов
        //self.charts_state = {};
        //$('#widgets_container').children('.cards-item').not('.cards-item-add').each(function(){
        //    var widget_id = $(this).data('widgetId');
        //    self.charts_state[widget_id] = $(this).hasClass('is-expanded');
        //});

        var $page = $('.js-page');
        var container = $(this).closest('.cards-opt-item');

        self.opts.widget.siblings('li').toggle();
        var state = self.opts.widget.hasClass('cards-item_full');
        if (!state && self.opts.widget.hasClass('is-expanded')) {
            self.opts.widget.addClass('was-expanded');
            self.opts.widget.removeClass('is-expanded');
        }

        $(this).closest('.cards-item').toggleClass('cards-item_full', !state);
        if (state && self.opts.widget.hasClass('was-expanded')) {
            self.opts.widget.addClass('is-expanded');
        }
        self.resizeChart();


        var fullScreenState = $page.hasClass('page_full-screen');
        $page.toggleClass('page_full-screen', !state);

        //var stateBody = $('body').hasClass('hidden');

        //$('body').toggleClass('hidden', !state);

        var stateDashboard = $('.dashboard').hasClass('dashboard_hidden');
        $('.dashboard').toggleClass('dashboard_hidden', !stateDashboard);
    });


    /* переключение фильтра План/факт */
    this.opts.widget.on('change', '.spending', function () {
        switch ($(this).prop('id')) {
            case 'fact_' + self.id:
                self.opts.filter_spending = 'fact';
                self.chart_data = self.pie_fact;
                self.opts.widget.find('.filter-plan').text('Факт');
                self.opts.filter_compare = false;
                break;
            case 'plan_' + self.id:
                self.opts.filter_spending = 'plan';
                self.chart_data = self.pie_plan;
                self.opts.widget.find('.filter-plan').text('План');
                self.opts.filter_compare = false;
                break;
            case 'plan_and_fact_' + self.id:
                self.opts.filter_spending = 'plan/fact';
                self.chart_data = self.pie_fact;
                self.opts.widget.find('.filter-plan').text('План/Факт');
                self.opts.filter_compare = true;
                break;
        }
        self.renderChart(self.chart_type);
    });


    /*переключение фильтра Кварталы Месяцы Годы и перерисовка графика*/
    this.opts.widget.on('change', '.chart_state', function () {
        switch ($(this).prop('id')) {
            case 'years_state_' + self.id:
                self.opts.widget.find('.filter-year').text('Годы');
                break;
            case 'quarters_state_' + self.id:
                self.opts.filter_chart_state = 'quarters';
                self.opts.widget.find('.filter-year').text('Кварталы');
                break;
            case 'months_state_' + self.id:
                self.opts.filter_chart_state = 'months';
                self.opts.widget.find('.filter-year').text('Месяцы');
                break;
        }
        self.renderChart(self.chart_type);
    });

    this.opts.widget.on('click','.cards-title-link', function () {
        var id = $(this).data('hint-id');
        var hint = $.grep(_HINTS, function (element) {
            return element.id == id;
        })[0];
        $('.cards-popup-title').text(hint.title);
        $('.cards-popup-text').text(hint.description);

    });
}


ChartControl.prototype.getMonth = function (data) {
    var months = [
        'январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь'
    ]
    return months[data];
}

ChartControl.prototype.getQuarter = function (data) {
    var quarter = ['I квартал', 'II квартал', 'III квартал', 'IV квартал'];
    return quarter[data];
}

/*Отрисовка тултипов легенды*/
ChartControl.prototype.renderLegendTooltip = function (data, coordinates) {
    var coords = '';
    if (coordinates !== undefined) {
        coords = ' style="left:' + (coordinates.x) + 'px;top:' + (coordinates.y - 90) + 'px;"';
    }
//var index = data.series._i;
    if (data.point !== undefined && data.point.index !== undefined) {
        var key = data.point.index;
    } else {
        var key = data.index;
    }
    var tooltip_numbers = '';
    /*здесь будет проверка на существование факт и плановых показателей */
    var tooltip_data = data.y.toFixed(1) > this.total_sum ? this.total_sum : data.y.toFixed(1);
    tooltip_numbers = '<div class = "cards-chart-tooltip-col single">'
            + '<div class = "cards-chart-tooltip-num">' + tooltip_data + '<br><span class="cards-chart-tooltip-units">млрд</span></div>'
            + '</div>';

    if (this.opts.filter_chart_state == 'quarters') {
        var tooltip_title = this.chart_type !== 'pie' ? this.getQuarter(key) + ' ' : '2015';
    } else {
        var tooltip_title = this.chart_type !== 'pie' ? this.getMonth(key) + ' ' : '2015';
    }

// var total_percent = data.y > 0? data.y * 100/this.total_sum:0;
    return '<div class="cards-chart-tooltip" ' + coords + '>'
            + '<div class = "cards-chart-tooltip-inner">'
            + '<div class = "cards-chart-tooltip-title" >' + tooltip_title + '</div>'
            + '<div class = "cards-chart-tooltip-cols">'
            + tooltip_numbers
            + '</div>'
// + '<div class = "cards-chart-tooltip-summary">'
// + '<div class = "cards-chart-tooltip-percent">'+total_percent.toFixed(1)+' %</div>'
// + '</div>'
            + '</div>'
            + '</div>'
            + '</div>'
};

/*Изменение размера графика*/
ChartControl.prototype.resizeChart = function () {
    var self = this;
    if (this.opts.widget.hasClass('cards-item_full')) {
        if (this.opts.chart.closest('.cards-chart').hasClass('is-expanded')) {
            var target_width = this.opts.window_width;
            var target_height = this.opts.widget.height();
            setTimeout(function () {
                self.opts.chart.highcharts().setSize(target_width, target_height - 90, false);
            }, 100);
        } else {
            var target_width = Math.ceil(this.opts.window_width * 80 / 100);
            var target_height = this.opts.widget.height();
            this.opts.chart.highcharts().setSize(target_width, target_height - 90, false);
        }
    } else {
        if (this.opts.chart.closest('.cards-chart').hasClass('is-expanded')) {
            var target_width = this.opts.widget_width
            setTimeout(function () {
                self.opts.chart.highcharts().setSize(target_width - 1, 375, false);
            }, 150);
        } else {
            var target_width = Math.ceil(this.opts.widget_width * 65 / 100);
            setTimeout(function () {
                self.opts.chart.highcharts().setSize(target_width, 375, false);
            }, 100);
        }
    }


}

/**
 * Функция формирующая в серии новый элемент "Остальные" для кругового графика
 * http://redmine.etton.ru/issues/1705
 */
ChartControl.prototype.buildShortDataForPie = function (data, other_percent, min_percent) {
    var data = clone(data);
    other_percent = typeof other_percent !== 'undefined' ? other_percent : 20;
    min_percent = typeof min_percent !== 'undefined' ? min_percent : 3;
    var total = 0;
    var minimal_value = 0;
    var id = this.id;

    $.each(data, function (index, value) {
        if (index == 0) {
            minimal_value = value.y;
        } else {
            minimal_value = value.y < minimal_value ? value.y : minimal_value;
        }

        total += value.y;
    });
    //1.2.Если значение минимальной статьи > 3%, работа алгоритма прекращается.
    if (minimal_value / total > min_percent / 100) {
        return data;
    }

    var significantValues = [];
    var otherValues = [];


//Отcортируем массив с данными по возрастанию
    var new_data = data.sort(function (o1, o2) {
        return o1.y - o2.y;
    });
    $.each(new_data, function (index, value) {
        //2. Добавление статьи в категорию "Прочие".
        //2.1. Если при добавлении статьи категория "Прочие" > 20% от всей суммы, то статья не не добавляется, а отображается как самостоятельная область
        //

        //1.3.Если значение <=3%, то переходим к шагу 2.
        if (value.y / total < min_percent / 100) {
            otherValues.push(value);
            //
            var otherValues_total = 0;
            var otherValues_max = 0;
            var otherValues_max_index = 0;
            $.each(otherValues, function (index, value) {
                otherValues_total += value.y;
                otherValues_max = value.y > otherValues_max ? value.y : otherValues_max;
                otherValues_max_index = index;
            });
            if (otherValues_total / total > other_percent / 100) {
                otherValues.pop();
                significantValues.unshift(value);
            }
        } else {
            significantValues.unshift(value);
        }
    });

    var total_other = 0;
    $.each(otherValues, function (key, val) {
        total_other += val.y;
    });
    significantValues.push({
        name: 'Остальные',
        y: total_other,
        grouped_data: true
    });
    return significantValues;
}

/**
 * Функция формирующая в серии новый элемент "Остальные" для остальных типов графиков
 * http://redmine.etton.ru/issues/1705
 */
ChartControl.prototype.buildShortDataForOthers = function (data, other_percent, min_percent) {
    var data = clone(data);
    other_percent = typeof other_percent !== 'undefined' ? other_percent : 20;
    min_percent = typeof min_percent !== 'undefined' ? min_percent : 3;
    var total = 0;
    var minimal_value = 0;
    var id = this.id;

    $.each(data, function (index, value) {
        var item = value;
        var sum = 0;
        $.each(item.data, function (index, value) {
            sum += value;
        });
        sum = +sum.toFixed(2);
        if (index == 0) {
            minimal_value = sum;
        } else {
            minimal_value = sum < minimal_value ? sum : minimal_value;
        }
        total += sum;
    });
    //1.2.Если значение минимальной статьи > 3%, работа алгоритма прекращается.
    if (minimal_value / total > min_percent / 100) {
        return data;
    }

    var significantValues = [];
    var otherValues = [];

    var reverse_data = data;
    reverse_data.reverse();
    $.each(reverse_data, function (index, value) {
        var sum = 0;
        //2. Добавление статьи в категорию "Прочие".
        //2.1. Если при добавлении статьи категория "Прочие" > 20% от всей суммы, то статья не не добавляется, а отображается как самостоятельная область
        //
        $.each(value.data, function (index, value) {
            sum += value;
        });

        // Если значение <=3%, то добавляем статью в остальные.
        if (sum / total <= min_percent / 100) {
            otherValues.push(value);
            var otherValues_total = 0;
            var otherValues_max = 0;


            $.each(otherValues, function (index, value) {
                var item = value;
                var new_value = 0;
                $.each(item.data, function (index, value) {
                    new_value += value;
                });
                otherValues_total += new_value;
                otherValues_max = new_value > otherValues_max ? new_value : otherValues_max;
            });
            if (otherValues_total / total > other_percent / 100) {
                otherValues.pop();
                significantValues.unshift(value);
            }
        } else {
            significantValues.unshift(value);
        }
    });
    var total_other = 0;
    var inner_items = [];
    $.each(otherValues, function (key, value) {
        var item = value;
        var new_value = 0;

        $.each(item.data, function (index, value) {
            inner_items[index] = inner_items[index] == undefined ? 0 : inner_items[index];
            inner_items[index] += value;
        });
    });
    significantValues.push({
        data: inner_items,
        name: 'Остальные',
        grouped_data: true
    });
    return significantValues;
}

ChartControl.prototype.sortChart = function () {
    var other_data = this.other_data;
    var new_colors = [];

    switch (this.opts.filter_sort) {
        case 'asc':
            this.other_data.sort(function (item1, item2) {
                var sum1 = 0, sum2 = 0;
                $.each(item1.data, function (index, value) {
                    sum1 += value;
                });
                $.each(item2.data, function (index, value) {
                    sum2 += value;
                });
                return sum1 - sum2;
            });
            this.chart_data.sort(function (item1, item2) {
                return item1.y - item2.y;
            })
            break;
        case 'abc':
            this.other_data.sort(function (item1, item2) {
                if (item1.name > item2.name) {
                    return 1;
                }
                if (item1.name < item2.name) {
                    return -1;
                }
                return 0;
            });
            this.chart_data.sort(function (item1, item2) {
                if (item1.name > item2.name) {
                    return 1;
                }
                if (item1.name < item2.name) {
                    return -1;
                }
                return 0;
            })
            break;
        default:
        case 'desc':

            this.other_data.sort(function (item1, item2) {
                var sum1 = 0, sum2 = 0;
                $.each(item1.data, function (index, value) {
                    sum1 += value;
                });
                $.each(item2.data, function (index, value) {
                    sum2 += value;
                });
                return sum2 - sum1;
            });
            this.chart_data.sort(function (item1, item2) {
                return item2.y - item1.y;
            })
            break;
    }
    for(var i = 0;i<other_data.length;i++){
        //other_data[i]._colorIndex = other_data[i].item_index;
        new_colors.push(colors[other_data[i].item_index]);
    }
    this.colors =new_colors;
}
ChartControl.prototype.setDataVisiblity = function () {

    for(var i = 0;i<this.other_data.length;i++){
        if(this.other_data[i].grouped_data){
            var visible = this.hidden_items.indexOf('others') == -1?true:false;
        } else {
            var visible = this.hidden_items.indexOf(i) == -1?true:false;
        }
        this.chart_data[i].item_index = i;
        this.chart_data[i].visible = visible;

        this.other_data[i].item_index = i;
        this.other_data[i].visible = visible;
    }
}

/*
* Метод ищет ключевые слова в подстроке ( тайтле виджета) и оборачивает их в ссылку
* @param object{} hints - справочник ключевых слов
* @param string new_title - строка в которой будут производиться замены
* */
ChartControl.prototype.replaceTitleKeywords = function (hints,new_title) {
    var patterns = [];
    var replacements = [];
    var title = new_title;
    $.each(hints,function(index,hint){
        patterns.push(hint.title);
        replacements.push('<a href="#" data-hint-id="' + hint.id +'" class="cards-title-link js-title-link">'+hint.title+'</a>');
    })
    for (var i = 0; i < patterns.length; i++) {
        title = title.replace(patterns[i], replacements[i]);

    }
    return title;
}