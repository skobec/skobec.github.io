<?php

class Mikron_Paginator extends Type_Paginator {

	/**
	* Вид выбиратора количества элементов на страницу
	*/
	const PAGINATOR_IPP_NONE = 0; // Скрыт
	const PAGINATOR_IPP_SELECT = 1; // Выпадающий список
	const PAGINATOR_IPP_LINK = 2; // Ссылки

	const PAGINATOR_STYLE_SCROLL = 1;
	const PAGINATOR_STYLE_STATIC = 2;

	/**
	* @var string
	*/
    private $id = null;
    private $start_index = 0;

    /**
    * Список возможных количеств элементов на страницу
    * @var int[]
    */
    private $ipp_list;
    /**
    * Вид выбиратора количества элементов на страницу
    * @var int PAGINATOR_IPP_*
    */
    private $ipp_style = self::PAGINATOR_IPP_LINK;
    private $pagination_style = self::PAGINATOR_STYLE_SCROLL;
    /**
    * Включение навигации по страницам кнопками Ctrl+[влево/вправо]
    * @var bool
    */
    private $use_ctrl_shortcut = true;

    /**
    * Пагинатор
    * 
    * @param string $id
    * @param int $items_per_page Количество элементов на страницу
    * @param int $total_pages Общее число страниц
    * @param int $current_page Номер текущей страницы (отсчёт с 1)
    * @param int[] $ipp_list Список возможных количеств элементов на страницу
    * 
    * @return void
    */
    public function __construct($id, $items_per_page = Constant::DEF_ITEMS_PER_PAGE, $total_pages = null, $current_page = null, $ipp_list = null) {
        $this->id = $id;
        $this->items_per_page = max(min($items_per_page, 100000), 1);
        $this->total_pages = (int)$total_pages;
        // определение текущей страницы
        $this->current_page = (int)$current_page;
        $this->fixRange();
        // Настройка выбиратора количества элементов на страницу
        $this->ipp_list = is_array($ipp_list) ? $ipp_list : [Constant::DEF_ITEMS_PER_PAGE, 50, 100];
    }

    /**
    * Возвращает вид выбиратора количества элементов на страницу
    * 
    * @return int PAGINATOR_IPP_*
    */
    function getIppStyle() {
		return $this->ipp_style;
    }

    /**
    * Устанавливает вид выбиратора количества элементов на страницу
    * 
    * @param int $ipp_style_id PAGINATOR_IPP_*
    * 
    * @return Mikron_Paginator
    */
    function setIppStyle($ipp_style_id) {
		$this->ipp_style = $ipp_style_id;
		return $this;
    }
    
    /**
    * Возвращает общее количество элементов
    * 
    * @return int
    */
    function getRecordsCount() {
        return $this->records_count;
    }

    function setPaginationStyle($style_id) {
		$this->pagination_style = $style_id;
		return $this;
    }

    function getPaginationStyle() {
		return $this->pagination_style;
    }

    /**
    * @author sciner
    * @since 2014-07-17
    * 
    * @param bool $value
    */
    function setUseCtrlShortcut($value) {
		$this->use_ctrl_shortcut = $value;
		return $this;
    }

    /**
    * @author sciner
    * @since 2014-07-17
    * 
    * @return bool 
    */
    function getUseCtrlShortcut() {
		return $this->use_ctrl_shortcut;
    }

    private function fixRange() {
        if($this->current_page < 1) {
            if(array_key_exists($this->id, $_GET)) {
                $this->current_page = (int)$_GET[$this->id];
            }
            if(array_key_exists($this->id.'_count', $_GET)) {
                $this->items_per_page = (int)$_GET[$this->id.'_count'];
            }
        }
        if($this->current_page < 1) {
            $this->current_page = 1;
        }
        $items_per_page = $this->items_per_page;
        $start = ($this->current_page - 1) * $items_per_page;
        $this->start_index = max((int)$start, 0);
    }

    public function getStartIndex() {
        $this->fixRange();
        return $this->start_index;
    }

    public function getItemsPerPage() {
        return $this->items_per_page;
    }

    /**
    * Генерация пагинатора для api-методов
    * 
    * @param object $select
    * 
    * @return Type_Paginator
    */
    public function getCalculated($select) {
        switch($driver_name = Mikron_Entity_Model::getConnectionAttribute(PDO::ATTR_DRIVER_NAME)) {
			case 'mysql': {
		        $resp = clone $select;
		        $this->records_count = $resp->count("*");
				break;
			}
			default: {
				// Psql
				$first_row = $select->fetch();
		        $this->records_count = $first_row ? $first_row['full_count'] : 0;
				break;
			}
        }
		$this->total_pages = ceil($this->records_count / $this->items_per_page);
		$this->current_page = max($this->current_page, 1);
		if(($this->current_page > $this->total_pages) && ($this->total_pages >= 0)) {
			$this->current_page = max($this->total_pages, 1);
			$select->limit($this->getItemsPerPage(), $this->getItemsPerPage() * ($this->current_page - 1));
		}
        return Functions::cast($this, 'Type_Paginator');
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setState($state) {
        foreach($state as $key => $value) {
            $this->$key = $value;
        }
        $this->total_pages = ceil($this->records_count / $this->items_per_page);
        return $this;
    }

    public function setTotalPages($value) {
        $this->total_pages = $value;
        if($this->current_page > $this->total_pages)  {
            $this->current_page = $this->total_pages;
        }
        return $this;
    }

    /**
    * Ссылка на предыдущую страницу
    */
    public function getPrevPageLink() {
        if($this->current_page > 1) {
            $query = $_SERVER['QUERY_STRING'];
            $pindex = $this->current_page - 1;
            if(strlen($query) == 0) {
                return $_SERVER['REQUEST_URI']."?{$this->id}={$pindex}";
            } else {
                parse_str($query, $output);
                $output[$this->id] = $pindex;
                return str_replace($_SERVER['REQUEST_URI'], $query, '?'.http_build_query($output));
            }
        }
        return null;
    }

    /**
    * Ссылка на следующую страницу
    */
    public function getNextPageLink() {
        if(($this->total_pages > 1) && ($this->current_page < $this->total_pages)) {
            $query = $_SERVER['QUERY_STRING'];
            $pindex = $this->current_page + 1;
            if(strlen($query) == 0) {
                return $_SERVER['REQUEST_URI']."?{$this->id}={$pindex}";
            } else {
                parse_str($query, $output);
                $output[$this->id] = $pindex;
                return str_replace($_SERVER['REQUEST_URI'], $query, '?'.http_build_query($output));
            }
        }
        return null;
    }

	public function getTotalPages() {
    	return $this->total_pages;
	}
    /**
    * @return string 
    */
    public function __toString() {
        ob_start();
            try {
                if($this->total_pages > 1) {
                    if($this->current_page > $this->total_pages) {
                        $this->current_page = $this->total_pages;
                    }
                    if($this->getPaginationStyle() == self::PAGINATOR_STYLE_SCROLL) {
	                    ?>
	                    <div class="paginator" id="paginator_<?=$this->id?>"></div>
						<script type="text/javascript">
	                		pag1 = new Paginator('paginator_<?=$this->id?>', <?= $this->total_pages?>, 10, <?= $this->current_page?>, '<?= $this->baseUrl().$this->getQueryString() ?>');
		                </script>
	                    <?php
					} elseif ($this->getPaginationStyle() == self::PAGINATOR_STYLE_STATIC) {
						$visible_count = 8;
						$url = $this->baseUrl().$this->getQueryString();
						$prev_enabled = $this->current_page > 1;
						$next_enabled = $this->current_page < $this->total_pages;
						$start_page = ($this->current_page < $visible_count) ? 1 : ($this->current_page - $visible_count / 2);
						$end_page = min(array($start_page + $visible_count, $this->total_pages));
						$first_visible = $start_page > 1;
						$last_visible = $end_page < $this->total_pages;
						?><div class="mikron-paginator" id="paginator_<?=$this->id?>">
						<?
						if ($prev_enabled) {
							?><span class="mikron-paginator-prev" id="mikron-paginator-prev-<?=$this->id?>">&larr; <?if($this->getUseCtrlShortcut()){?>Ctrl<?}?> <a href="<?=$url.($this->current_page - 1)?>">предыдущая</a></span><?
					 	}
						if ($first_visible) {
							?><a href="<?=$url.'1'?>">1</a> ... <?
						}
						for ($p = $start_page; $p <= $end_page; $p++) {
							?><a class="<?if($this->current_page == $p){?>active<?}?>" href="<?=$url.$p?>"><?=$p?></a><?
						}
						if ($last_visible) {
							?> ... <a href="<?=$url.$this->total_pages?>"><?=$this->total_pages?></a><?
						}
						if($next_enabled) {
							?><span class="mikron-paginator-next" id="mikron-paginator-next-<?=$this->id?>"><a href="<?=$url.($this->current_page + 1)?>">следующая</a> <?if($this->getUseCtrlShortcut()){?>Ctrl<?}?> &rarr;</span><?
						} ?>
						</div>
						<? if($this->getUseCtrlShortcut()) { ?>
							<script>
								$(document).keydown(function (e) {
									if(e.which == 37 && e.ctrlKey) {
										<? if($prev_enabled) { ?>
											// CTRL+Left arrow (Prev)
											var link = $('#mikron-paginator-prev-<?=$this->id?> a').attr('href');
											location.href = link;
											return false;
										<? } ?>
									}
									if(e.which == 39 && e.ctrlKey) {
										<? if($next_enabled) { ?>
											// CTRL+Right arrow (Next)
											var link = $('#mikron-paginator-next-<?=$this->id?> a').attr('href');
											location.href = link;
											return false;
										<? } ?>
									}
								});
							</script>
						<? }
					}
                }
                if(($this->records_count > Constant::DEF_ITEMS_PER_PAGE) && $this->ipp_style) {
                    if($this->getIppStyle() == self::PAGINATOR_IPP_SELECT) {
		                $option_list = '';
		                foreach($this->ipp_list as $ipp) {
		                    $selected = $ipp == $this->items_per_page ? 'selected="selected"' : '';
		                    $option_list .= '<option '.$selected.' value="'.$this->getUrlWithIpp($ipp).'" >'.$ipp.'</option>';
		                }
		                ?><div class="paginator_count" id="paginator_<?=$this->id?>_count">На странице: <select><?=$option_list?></select></div><?
					} else {
						?><div class="paginator_count" id="paginator_<?=$this->id?>_count">
						<style>
						/* Mikron-Paginator */
						.mikron-paginator {
							margin-top: 20px !important;
							display: block;
							text-align: center;
						}
						.mikron-paginator a {
							display: inline-block;
							font-size: 18px;
							font-weight: bold;
							padding: 3px;
							margin-right: 2px;
							color: #2584c0;
							border: none;
							text-decoration: underline;
						}
						.mikron-paginator a.active {
							color: #000;
							border: none;
							text-decoration: none;
						}
						.mikron-paginator .mikron-paginator-prev, .mikron-paginator .mikron-paginator-next {
							font-weight: normal;
							font-size: 11pt;
						}
						.mikron-paginator .mikron-paginator-prev a, .mikron-paginator .mikron-paginator-next a {
							font-size: 11pt;
							font-weight: normal;
						}
						.mikron-paginator .mikron-paginator-prev span, .mikron-paginator .mikron-paginator-next span {
							color: #000;
						}
						.paginator_count {
							font-size: 14px;
							margin: 10px 0;
							text-align: center;
							width: 100%;
						}
						.paginator_count a {
							margin-right: 10px;
						}
						.paginator_count .selected {
							color: #000;
							text-decoration: none;
							border: none;
						}
						</style>
						<?
		                foreach($this->ipp_list as $ipp) {
		                    ?><a class="<?if($ipp == $this->items_per_page){?>selected<?}?>" href="<?=$this->getUrlWithIpp($ipp)?>"><?=$ipp?></a><?
		                }
		                ?> элементов на странице</div><?
					}
                } ?>
                <script type="text/javascript">
	                <? if($this->ipp_style == self::PAGINATOR_IPP_SELECT) { ?>
		                // by Notfoolen
		                $(function() {
		                    $('.paginator_count select').change(function() {
		                       location.href = $(this).val();
		                    });
		                });
	                <? } ?>
                </script>
                <?
            } catch(Exception $ex) {
                echo $ex->getMessage();
            }
        return ob_get_clean();
    }

    private function getUrlWithIpp($count) {
        $getParams = $this->getQuery();
        $key = $this->id.'_count';
        $getParams[$key] = $count;
        $base_url = $this->baseUrl();
        return $base_url.$this->getQueryString($getParams).'1';
    }

    private function getQueryString($getParams = null) {
        if(!$getParams) {
            $getParams = $_GET;
        }
        $result = '/?';
        $i = 0;
        foreach($getParams as $key => $value) {
			if(is_array($value)) {
				foreach($value as $k => $v) {
					$result .= ($i==0?null:'&').$key.'['.$k.']='.$v;
					$i++;
				}
			} else {
				if($key == $this->id) {
					continue;
				}
				$result .= ($i==0?null:'&').$key.'='.$value;
				$i++;
			}
        }
        if($i == 0) {
            $result .= $this->id.'=';
        } else {
            $result .= '&'.$this->id.'=';
        }
        return $result;
    }
    
    /**
    * Возвращает список GET параметров
    */
    private function getQuery() {
        if(false) {
			return Zend_Controller_Front::getInstance()->getRequest()->getQuery();
        } else {
			return $_GET;
        }
    }

    /**
    * Возвращает адрес без GET параметров
    */
	private function baseUrl() {
        $url = strtok($_SERVER['REQUEST_URI'], '?');
        if(false) {
			return Zend_Controller_Front::getInstance()->getRequest()->getPathInfo();
        }
		return rtrim($url, '/');
	}

}
