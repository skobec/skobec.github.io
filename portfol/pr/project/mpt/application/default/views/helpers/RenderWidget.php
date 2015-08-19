<?php

/**
 * Страница Л/С
 * @author sciner
 * @since 2014-11-27
 */
class Zend_View_Helper_RenderWidget extends Zend_View_Helper_Abstract
{

    public function renderWidget($widget, $parent_widget = null)
    {
        $draw_data = $widget->other_data ? true : false;

        ?>
        <li data-widget-id ="<?= $widget->id ?>" class="cards-item <?= $widget->is_expanded ? 'is-expanded' : '' ?>" data-item="card" id="widget<?= $widget->id ?>">
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
                <!--                <li class="cards-opt-item is-active">-->
                <!--                    <a href="#" class="cards-opt-link" data-action="expand">-->
                <!--                        <span class="icon icon-expand"></span>-->
                <!--                    </a>-->
                <!--                </li>-->
                <li class="cards-opt-item">
                    <a href="#" class="cards-opt-link" data-action="full-screen">
                        <span class="icon icon-expand-full"></span>
                    </a>
                </li>
            </ul>
            <div class="cards-cover">
                <header class="cards-header">
                    <!-- нумерация виджетов-->
                    <? if (IS_DEVELOPER_HOST) { ?>
                        <div class="previous_chart" id="back_btn<?= $widget->id ?>">ID <?= $widget->id ?> <?= (null !== $parent_widget) ? 'Назад' : '' ?></div>
                    <? } else { ?>
                        <div class="previous_chart" id="back_btn<?= $widget->id ?>"><?= (null !== $parent_widget) ? 'Назад' : '' ?></div>
                        <?
                    }

                    ?>
                    <h2 class="cards-title" id="widget_title<?= $widget->id ?>"><?= App_Mpt::wrapHints($widget->title) ?></h2>
                </header>
                <div class="cards-icon">
                    <span class="icon <?= $widget->icon_class ?>"></span>
                </div>
                <div class="cards-amount">
                    <span class="cards-amount-value" id="widget_amount<?= $widget->id ?>"><?= $draw_data ? $widget->money[0]->y : null ?></span>
                    <span class="cards-amount-currency">млрд. <span class="rouble">Р</span></span>
                </div>
            </div>
            <div class="cards-main">
                <div class="cards-main__top-menu">
                    <button class="cards-main__button" id="back_btn_fullscr<?= $widget->id ?>">←</button>
                    <button class="cards-main__button cards-main__button_detail"><sup>...</sup></button>
                    <div class="cards-main__top-menu-title" id="widget_title_fullscr<?= $widget->id ?>"><?= $widget->title ?></div>
                    <div class="cards-amount cards-amount_top-menu">
                        <span class="cards-amount-value" id="widget_amount_fullscr<?= $widget->id ?>">231,51</span>
                        <span class="cards-amount-currency">млрд. <span class="rouble">Р</span></span>
                    </div>
                    <div class="cards-view cards-view_top-menu" data-container="view">
                        <ul class="cards-view-list">
                            <li class="cards-view-item" data-item="view">
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
                </div>
                <ul class="cards-filter">
                    <li class="cards-filter-item">
                        <a href="#" class="cards-filter-link">
                            <span class="cards-filter-text filter-plan">План</span>
                        </a>
                    </li>
                    <li class="cards-filter-item">
                        <a href="#" class="cards-filter-link">
                            <span class="cards-filter-text filter-year">Кварталы</span>
                        </a>
                    </li>
                    <li class="cards-filter-item">
                        <a href="#" class="cards-filter-link">
                            <span class="cards-filter-text filter-period">2015</span>
                        </a>
                    </li>
                </ul>
                <div class="cards-chart">
                    <div class="plan plan-main">
                        <div class="plan-input">
                            <input type="radio" class="radio spending fact_data" id="fact_<?= $widget->id ?>" name="radio-spending<?= $widget->id ?>">
                            <label for="fact_<?= $widget->id ?>">Фактические расходы</label>
                        </div>
                        <div class="plan-input">
                            <input type="radio" class="radio spending fact_data" id="plan_<?= $widget->id ?>" name="radio-spending<?= $widget->id ?>" checked>
                            <label for="plan_<?= $widget->id ?>">Плановые расходы</label>
                        </div>
                        <div class="plan-input">
                            <input type="radio" class="radio spending plan_and_data" id="plan_and_fact_<?= $widget->id ?>" name="radio-spending<?= $widget->id ?>">
                            <label for="plan_and_fact_<?= $widget->id ?>">План и факт</label>
                        </div>
                    </div>
                    <div class="plan years">
                        <div class="plan-input">
                            <input type="radio"  class="radio chart_state" name="radio-chart_state<?= $widget->id ?>" checked id="years_state_<?= $widget->id ?>">
                            <label for="years_state_<?= $widget->id ?>">Годы</label>
                        </div>
                        <div class="plan-input">
                            <input type="radio"  class="radio chart_state" name="radio-chart_state<?= $widget->id ?>" id="quarters_state_<?= $widget->id ?>">
                            <label for="quarters_state_<?= $widget->id ?>">Кварталы</label>
                        </div>
                        <div class="plan-input">
                            <input type="radio"  class="radio chart_state" name="radio-chart_state<?= $widget->id ?>" id="months_state_<?= $widget->id ?>">
                            <label for="months_state_<?= $widget->id ?>">Месяцы</label>
                        </div>
                    </div>
                    <div class="plan period">
                        <div class="plan-input plan-period">
                            <input type="checkbox"  class="checkbox" id="year_2013_<?= $widget->id ?>">
                            <label for="year_2013_<?= $widget->id ?>">2013</label>
                        </div>
                        <div class="plan-input plan-period">
                            <input type="checkbox"  class="checkbox" id="year_2014_<?= $widget->id ?>">
                            <label for="year_2014_<?= $widget->id ?>">2014</label>
                        </div>
                        <div class="plan-input plan-period">
                            <input type="checkbox"  class="checkbox" checked="true" id="year_2015_<?= $widget->id ?>">
                            <label for="year_2015_<?= $widget->id ?>">2015</label>
                        </div>
                    </div>
                    <div class="cards-chart-graph" data-widget-id ="<?= $widget->id ?>" id="container<?= $widget->id ?>"></div>
                </div>
                <aside class="cards-sidebar" data-item="sidebar">
                    <div class="cards-view" data-container="view">
                        <ul class="cards-view-list">
                            <li class="cards-view-item" data-item="view">
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
                        <div id="legend_select_<?= $widget->id ?>" role="combobox" class="jelect jelect_top-menu js-jelect">
                            <input value="3" data-text="Сортировать по ..." type="text" class="jelect-input js-jelect-input">
                            <div data-val="desc" tabindex="1" role="button" class="jelect-current js-jelect-current">Сортировать по ...</div>
                            <div role="list-box" class="jelect-options js-jelect-options">
                                <div data-val="desc" tabindex="3" role="option" class="jelect-option js-jelect-option jelect-option_state_active">
                                    <div class="jelect-option__value js-jelect-option-value">сумме ↓</div>
                                </div>

                                <div data-val="asc" tabindex="2" role="option" class="jelect-option js-jelect-option">
                                    <div class="jelect-option__value js-jelect-option-value">сумме ↑</div>
                                </div>

                                <div data-val="abc" tabindex="1" role="option" class="jelect-option js-jelect-option ">
                                    <div class="jelect-option__value js-jelect-option-value">алфавиту</div>
                                </div>
                            </div>
                        </div>
                        <ul data-widget-id="<?= $widget->id ?>" class="cards-legend-list"></ul>
                        <a href="#" class="cards-legend-more">Показать еще...</a>
                    </div>
                </aside>
            </div>
        </li>
        <script>
            $(function () {
//                $('.cards-title-link').off().on('click', function () {
//                    var id = $(this).data('hint-id');
//                    var hint = $.grep(hints,function(element){
//                        return element.id == id;
//                    })[0];
//                    console.log(hint);
//                });
                
                // $('.cards-item[data-widget-id="<? //= $widget->id          ?>//"] .cards-title').text("<? //= $widget->title          ?>//");
                var chart_data = <?= json_encode($widget, JSON_UNESCAPED_UNICODE) ?>;
                var chart = new ChartControl(<?= $widget->id ?>, chart_data);
        <?php if (null !== $parent_widget) { ?>
                    var parent_widget = <?= json_encode($parent_widget[0], JSON_UNESCAPED_UNICODE) ?>;
                    chart.history.push({
                        chart_data: parent_widget.chart_data, //при проваливании - запись в историю предыдущего шага
                        other_data: parent_widget.other_data,
                        title: parent_widget.title,
                        sum: parent_widget.money[0].y
                    });
        <? } ?>

                //console.log(chart.history);
                chart.renderChart('<?= $widget->type ?>');
        <? /* ?>
          // Заполняем легенду
          //     chart_data = $('div[data-widget-id="<? //= $widget->id    ?>//"]').highcharts().series[0].data;
          //     renderLegend(chart_data);
          // Дополнительные обработчики для событий.
          //     $('.cards-item[data-widget-id="<?= $widget->id ?>"] .cards-legend-list').on('mouseover', '.cards-legend-item', function () {
          //         $('.cards-item[data-widget-id="<?= $widget->id ?>"] .cards-legend-item').css({'opacity': '0.4'});
          //         $(this).css({'opacity': 1});
          //         $('.cards-item[data-widget-id="<?= $widget->id ?>"] .cards-chart-graph').highcharts().series[0].data[$(this).index()].setState('hover');
          //     }).on('mouseout', '.cards-legend-item', function () {
          //         $('.cards-legend-item').css({'opacity': '1'});
          //         $('.cards-item[data-widget-id="<?= $widget->id ?>"] .cards-chart-graph').highcharts().series[0].data[$(this).index()].setState();
          //     }).on('click', '.cards-legend-item', function () {
          //         $('.cards-item[data-widget-id="<?= $widget->id ?>"] .cards-chart-graph').highcharts().series[0].data[$(this).index()].firePointEvent('click');
          //     });
          <? */ ?>
            });
        </script>
        <?
    }
}
