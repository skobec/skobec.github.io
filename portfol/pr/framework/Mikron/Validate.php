<?php

/**
* Разбор описания правил валидации из особого формата
* @author sciner
* @since 2015-02-18
* @usage Mikron_Validator::parseValidatorAnnotation('NotEmpty, minMax(min="10", max="20"), Inn, Email, FileExtension(list="jpg,png,gif")');
*/
class Mikron_Validate {

	/**
	* Кеш отпарсенных правил валидирования объектов
	* @var array()
	*/
	private static $annotations_cache = array();
	private static $resolve_validator_list = array();

	// состояния
	const AN_STATE_WAIT_FUNC_NAME 		= 1;
	const AN_STATE_IN_FUNC_NAME 		= 2;
	const AN_STATE_WAIT_START_BRACKET 	= 3;
	const AN_STATE_WAIT_PARAM_NAME 		= 4;
	const AN_STATE_IN_PARAM_NAME 		= 5;
	const AN_STATE_WAIT_PARAM_VALUE 	= 6;
	const AN_STATE_IN_PARAM_VALUE 		= 7;
	const AN_STATE_WAIT_COMMA 			= 8;
	
	// массив корректных кодов символов для названий функций и имён атрибутов
	private $valid_name_chars;

	// служебные переменные парсера
	private
		$state = 0,
		$resp = array(), // итоговый ответ
		$buf = null, // временный строковый буфер
		$func_name = null, // имя текущей функции
		$param_name = null, // имя текущего параметра
		$params = array() // список аргументов текущей функции
	;

	/**
	* Регистрация валидатора
	* 
	* @param string $func_name Имя функции
	* @param string $class_name Имя класса валидатора
	* 
	* @return void
	*/
	static function addValidator($func_name, $class_name) {
		self::$resolve_validator_list[$func_name] = $class_name;
	}

	/**
	* Возвращает имя класса валидатора по имени функции
	* @param string $func_name
	*/
	static function getValidatorClassName($func_name) {
		if(!self::$resolve_validator_list) {
			self::addValidator('length', 'Mikron_Validate_Length');
			self::addValidator('notEmpty', 'Mikron_Validate_NotEmpty');
			self::addValidator('inn', 'Mikron_Validate_Inn');
			self::addValidator('email', 'Mikron_Validate_Email');
			self::addValidator('regexp', 'Mikron_Validate_Regexp');
			self::addValidator('minMax', 'Mikron_Validate_Minmax');
		}
		if(array_key_exists($func_name, self::$resolve_validator_list)) {
			return self::$resolve_validator_list[$func_name];
		} else {
			throw new Exception("Не найден класс валидатора `{$func_name}`", 951);
		}
	}

	private function mb_str_split( $string ) { 
	    # Split at all position not after the start: ^ 
	    # and not before the end: $ 
	    return preg_split('/(?<!^)(?!$)/u', $string ); 
	} 

	function makeRule() {
		if($key = trim($this->func_name)) {
			// $key = trim($this->func_name);
			if(array_key_exists($key, $this->resp)) {
				throw new Exception('Дублирование названия функции');
			}
			$this->resp[$key] = Functions::cast(array('name' => $key, 'argument_list' => $this->params), 'Mikron_Validate_Rule');
		}
		$this->resetState();
	}

	private function resetState() {
		$this->func_name = null;
		$this->param_name = null;
		$this->buf = null;
		$this->params = array();
		$this->state = self::AN_STATE_WAIT_FUNC_NAME;
	}
	
	private function changeStateTo($pos, $new_state_id) {
		// echo "changeStateTo({$this->state} &rarr; {$new_state_id}) ... buf = {$this->buf}&larr;<br>";
		if($this->state == self::AN_STATE_IN_FUNC_NAME) {
			if($new_state_id == self::AN_STATE_WAIT_FUNC_NAME) {
				$this->func_name = $this->buf;
				$this->makeRule();

			} elseif($new_state_id == self::AN_STATE_WAIT_START_BRACKET) {
				$this->func_name = $this->buf;
				$this->buf = null;
				$this->params = array();
			} elseif($new_state_id == self::AN_STATE_WAIT_PARAM_NAME) {
				$this->func_name = $this->buf;
				$this->buf = null;
				$this->params = array();
			} else {
				throw new Exception("Error changeStateTo({$this->state} &rarr; {$new_state_id}) ... buf = {$this->buf}&larr;<br>");
			}
		} elseif ($new_state_id == self::AN_STATE_WAIT_FUNC_NAME) {
			$this->makeRule();
		} elseif ($this->state == self::AN_STATE_IN_PARAM_NAME) {
			if($new_state_id == self::AN_STATE_WAIT_PARAM_VALUE) {
				$this->param_name = $this->buf;
				$this->buf = null;
			}
		} elseif ($this->state == self::AN_STATE_IN_PARAM_VALUE) {
			if($new_state_id == self::AN_STATE_WAIT_PARAM_NAME) {
				$key = trim($this->param_name);
				if(array_key_exists($key, $this->params)) {
					throw new Exception("Дублирование названия параметра `{$key}`");
				}
				if(!strlen($key)) {
					throw new Exception('Не указано название параметра');
				}
				$this->params[$key] = trim($this->buf);
				$this->buf = null;
			}
		}
		$this->state = $new_state_id;
	}

	/**
	* @param string $annotate
	* 
	* @return Mikron_Validate_Rule[] Список правил
	*/
	function parseValidateAnnotation($annotate) {
		if(!$this->valid_name_chars) {
			$this->valid_name_chars = array_merge(
				range(ord('a'), ord('z')),
				range(ord('A'), ord('Z')),
				range(ord('0'), ord('9')),
				array(ord('_'))
			);
		}
		$annotate_key = md5($annotate);
		if($resp = $this->getAnnotationsFromCache($annotate_key)) {
			return $resp;
		}
		$annotate = str_replace("\n", ' ', $annotate);
		$annotate = str_replace("\t", ' ', $annotate);
		$annotate = str_replace("\r", ' ', $annotate);
		$this->resetState();
		$this->resp = array();
		$this->state = self::AN_STATE_WAIT_FUNC_NAME;
		$annotate = $this->mb_str_split($annotate, '');
		try {
			$prev_code = -1;
			foreach($annotate as $pos => $l) {
				$code = ord($l);
				switch($code) {
					case 32: { // {space} 
						if($this->state == self::AN_STATE_IN_FUNC_NAME) {
							$this->changeStateTo($pos, self::AN_STATE_WAIT_START_BRACKET);

						} elseif($this->state == self::AN_STATE_IN_PARAM_VALUE) {
							$this->buf .= $l;

						}
						break;
					}
					case 34: { // "
						if($this->state == self::AN_STATE_IN_PARAM_VALUE) {
							$this->changeStateTo($pos, self::AN_STATE_WAIT_PARAM_NAME);

						} elseif($this->state == self::AN_STATE_IN_PARAM_NAME) {
							throw new Exception('Знак `"` в неожиданном месте');

						} else {
							$this->changeStateTo($pos, self::AN_STATE_IN_PARAM_VALUE);
						}
						break;
					}
					case 61: { // =
						if($this->state == self::AN_STATE_IN_PARAM_NAME) {
							$this->changeStateTo($pos, self::AN_STATE_WAIT_PARAM_VALUE);

						} elseif($this->state == self::AN_STATE_WAIT_PARAM_NAME) {
							throw new Exception('Знак `=` в неожиданном месте');
							
						} elseif($this->state == self::AN_STATE_IN_PARAM_VALUE) {
							$this->buf .= $l;
						}
						break;
					}
					case 40: { // (
						if($this->state == self::AN_STATE_IN_FUNC_NAME || $this->state == self::AN_STATE_WAIT_START_BRACKET) {
							$this->changeStateTo($pos, self::AN_STATE_WAIT_PARAM_NAME);

						} elseif($this->state == self::AN_STATE_IN_PARAM_NAME) {
							throw new Exception('Знак `(` в неожиданном месте');

						} elseif($this->state == self::AN_STATE_IN_PARAM_VALUE) {
							$this->buf .= $l;
						}
						break;
					}
					case 41: { // )
						if($this->state == self::AN_STATE_IN_PARAM_NAME) {
							throw new Exception('Ожидалось значение параметра');

						} elseif($this->state == self::AN_STATE_WAIT_PARAM_NAME) {
							$this->changeStateTo($pos, self::AN_STATE_WAIT_COMMA);

						} elseif($this->state == self::AN_STATE_WAIT_PARAM_VALUE) {
							throw new Exception('Знак `)` в неожиданном месте');

						} elseif($this->state == self::AN_STATE_WAIT_COMMA) {
							throw new Exception('Знак `)` в неожиданном месте');

						} elseif($this->state == self::AN_STATE_WAIT_FUNC_NAME) {
							throw new Exception('Знак `)` в неожиданном месте');

						} elseif($this->state == self::AN_STATE_IN_PARAM_VALUE) {
							$this->buf .= $l;

						}
						break;
					}
					case 44: { // ,
						if($this->state == self::AN_STATE_IN_FUNC_NAME || $this->state == self::AN_STATE_WAIT_START_BRACKET || $this->state == self::AN_STATE_WAIT_COMMA) {
							$this->changeStateTo($pos, self::AN_STATE_WAIT_FUNC_NAME);

						} elseif($this->state == self::AN_STATE_WAIT_PARAM_NAME) {
							// do nothing

						} elseif($this->state == self::AN_STATE_IN_PARAM_VALUE) {
							$this->buf .= $l;

						} else {
							throw new Exception('Запятая в неожиданном месте');

						}
						break;
					}
					default: {
						if($this->state == self::AN_STATE_WAIT_PARAM_VALUE) {
							throw new Exception('Ожидалась открывающая кавычка');
							
						} elseif($this->state == self::AN_STATE_WAIT_FUNC_NAME) {
							if(!in_array($code, $this->valid_name_chars)) {
								throw new Exception('Недопустимый символ в имени функции');
							}
							$this->changeStateTo($pos, self::AN_STATE_IN_FUNC_NAME);

						} elseif ($this->state == self::AN_STATE_WAIT_PARAM_NAME) {
							if(!in_array($code, $this->valid_name_chars)) {
								throw new Exception('Недопустимый символ в имени параметра');
							}
							$this->changeStateTo($pos, self::AN_STATE_IN_PARAM_NAME);
							
						} elseif($this->state == self::AN_STATE_WAIT_START_BRACKET) {
							throw new Exception('Ожидалась скобка');

						} elseif ($this->state == self::AN_STATE_IN_PARAM_NAME) {
							if(!in_array($code, $this->valid_name_chars)) {
								throw new Exception('Недопустимый символ в имени параметра');
							}
							if($prev_code == 32) { // 32 == {space}
								throw new Exception('Ожидалось значение параметра');
							}

						} elseif ($this->state == self::AN_STATE_IN_FUNC_NAME) {
							if(!in_array($code, $this->valid_name_chars)) {
								throw new Exception('Недопустимый символ в имени функции');
							}

						} elseif ($this->state == self::AN_STATE_WAIT_COMMA) {
							throw new Exception('Ожидалась запятая');
							
						}
						$this->buf .= $l;
						break;
					}
				}
				$prev_code = $code;
			}
			if($this->state == self::AN_STATE_IN_FUNC_NAME) {
				$this->changeStateTo($pos, self::AN_STATE_WAIT_START_BRACKET);
			}
			if(!in_array($this->state, array(self::AN_STATE_WAIT_COMMA, self::AN_STATE_WAIT_START_BRACKET))) {
				throw new Exception('Неоконченное выражение');
			}
			$this->makeRule();
		} catch(Exception $ex) {
			$annotation_before_error = array_slice($annotate, 0, $pos);
			$annotation_in_error = array_slice($annotate, $pos, 1);
			$annotation_after_error = array_slice($annotate, $pos+1, 30);
			$annotation_before_error = implode('', $annotation_before_error);
			$annotation_in_error = implode('', $annotation_in_error);
			$annotation_after_error = implode('', $annotation_after_error);
			$exmes = $ex->getMessage();
			throw new Exception("Ошибка правил валидации<br>позиция: {$pos},<br>символ: `{$l}`,<br>состояние: {$this->state},<br>{$exmes}: <span style=\"color: green;\">{$annotation_before_error}</span><span style=\"color: red;\">{$annotation_in_error}</span><span style=\"color: #a00;\">{$annotation_after_error}...</span>", 952);
		}
		$this->saveAnnotationsInCache($annotate_key, $this->resp);
		return $this->resp;
	}

	private function getAnnotationsFromCache($annotate_key) {
		if(array_key_exists($annotate_key, self::$annotations_cache)) {
			return self::$annotations_cache[$annotate_key];
		}
	}

	private function saveAnnotationsInCache($annotate_key, $annotation) {
		self::$annotations_cache[$annotate_key] = $annotation;
	}

}
