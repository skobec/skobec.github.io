<?php

// отправка сообщений

/**
    SMTP - коды ответов сервера
    http://muff.kiev.ua/content/smtp-kody-otvetov-servera

    Как известно, каждый ответ SMTP-сервера клиенту сопровождается трехзначным кодом. Каждая цифра в коде ответа сервера имеет свое назначение:
    первая цифра обозначает успех, неудачу или незавершенность команды;
    вторая цифра уточняет тип ответа (например, ошибка почтовой системы или синтаксическая ошибка команды);
    третья цифра служит для лучшей градации информации.

    Первая цифра (существует 5 вариантов)

    1yz – положительный предварительный отклик.
    Сервер воспринял команду, но находится в ожидании подтверждения на продолжение или отказ от выполнения запрошенных действий.

    2yz – положительный отклик 
    Запрошенное действие было выполнено и сервер готов к принятию новой команды.

    3yz – положительный промежуточный отклик 
     Сервер воспринял команду, но для завершения действия ожидает дальней информации.

    4yz – временный негативный отклик 
     Команда не принята, и запрошенные действия не были исполнены. Однако ошибочное состояние временное, и действие может быть запрошено еще раз.

    5yz – негативный отклик 
     Команда не принята, и запрошенные действия не были исполнены.

    Вторая цифра (категория ошибки)

    x0z – Синтаксис 
     Отклик обозначает синтаксическую ошибку команды; команда может быть синтаксически правильная, но отклик нельзя отнести к другим категориям; нереализованная команда.

    x1z – Информация
     Отклик на запрос информации, например, статус или помощь.

    x2z – Соединение
     Отклики, относящиеся к каналу передачи данных.

    x3z – Не определены

    x4z – Не определены

    x5z – Почтовая система
     Отклики указывают состояние принимающей почтовой системы в отношении запрошенной передачи или другого действия почтовой системы.

    Третья цифра служит для лучшей градации значения в каждой категории, определяемой второй цифрой. Перечисление кодов откликов:

    211 Состояние системы или системная справка. 
    214 Информация о том, как работать с сервером, описание нестандартных команд и т.д. 
    220 Служба готова к работе. 
    221 Служба закрывает канал передачи данных. 
    235 Успешная аутентификация на сервере. 
    250 Выполнение почтовой команды успешно окончено. 
    251 Нелокальный пользователь. 
    252 Невозможно проверить наличие почтового ящика для пользователя, но сообщение принято, и сервер попытается его доставить.

    354 Начало приема сообщения. Сообщение должно заканчиваться точкой на новой строке и новой строкой.

    421 Работа с сервером невозможна. Произойдет закрытие канала связи (может быть ответом на любую команду, если серверу нужно закрыть соединение). 
    450 Запрошенная команда не принята – недоступен почтовый ящик (почтовый ящик временно занят) . 
    451 Запрошенная команда прервана – локальная ошибка при обработке команды. 
    452 Запрошенная команда невозможна – недостаточно дискового пространства. 
    454 Аутентификация невозможна по причине временного сбоя сервера.

    500 Синтаксическая ошибка, команда не распознана (также этот отклик может означать, что длина команды слишком большая). 
    501 Синтаксическая ошибка в команде или аргументе. 
    502 Команда распознана, но её реализация сервером не поддерживается. 
    503 Неверная последовательность команд. 
    504 Параметр команды сервером не поддерживается. 
    530 Сервер требует аутентификации для выполнения запрошенной команды. 
    534 Данный отклик означает, что выбранный механизм аутентификации для данного пользователя является не достаточно надежным. 
    535 Аутентификация отклонена сервером (например, ошибка в кодировании данных). 
    538 Выбранный метод аутентификации возможен только при зашифрованном канале связи. 
    550 Запрошенная операция невозможна – почтовый ящик недоступен (почтовый ящик не найден или нет доступа; команда отклонена локальной политикой безопасности). 
    551 Нелокальный пользователь. 
    552 Запрошенная почтовая команда прервана – превышено выделенное на сервере пространство. 
    553 Запрошенная почтовая команда прервана – недопустимое имя почтового ящика (возможно синтаксическая ошибка в имени). 
    554 Неудачная транзакция или отсутствие SMTP сервиса (при открытии сеанса передачи данных).
**/

class Delivery {

	private $socket = null;

    private $logs = array();

    /**
    * Логи последней отправки
    * @return array
    */
    public function getLog() {
        return $this->logs;
    }

    /**
    * Отправляет команду серверу, получает ответ, сравнивает код ответа с ожидаемым, затем возвращает полный ответ от сервера
    * @author sciner
    * @since 30/07/2012 15:25
    * 
    * @param string $cmd Команда
    * @param string $data Параметры команды
    * @param int $success_code Код успешного ожидаемого ответа
    * 
    * @return string Полный ответ от сервера
    */
    private function put($cmd, $data, $success_code) {
        try {
            $server_response = null;
            if($data && $cmd) {
                    $data = "{$cmd} {$data}\r\n";
            } elseif($cmd) {
                $data = "{$cmd}\r\n";
            }
            $log = array('c' => $data);
            fputs($this->socket, $data);
            $server_response = '111111110';
            while (substr($server_response, 3, 1) != ' ') {
                if (!($server_response = fgets($this->socket, 256))) {
                    throw new Exception("Не удалось отправить {$cmd}, {$server_response} != {$success_code}");
                }
                if (!(substr($server_response, 0, 3) == $success_code)) {
                    throw new Exception("Не удалось отправить {$cmd}, {$server_response} != {$success_code}");
                }
            }
            $log['s'] = $server_response;
            $this->logs[] = $log;
            return $server_response;
        } catch(Exception $ex) {
            $log['s'] = $server_response;
            $this->logs[] = $log;
            fclose ($this->socket);
            throw $ex;
        }
    }

    /**
    * Установка соединения с сервером
    *     
    * @param string $server
    * @param string $port
    * 
    * @return pointer
    */
    private function connect($server, $port) {
        if (!$this->socket = fsockopen($server, $port, $errno, $errstr, 30)) {
            throw new Exception($errstr, $errno);
        }
        return $this->socket;
    }

    /**
    * Закрытие соединения с сервером, если оно было установлено ранее
    */
    private function close() {
        if ($this->socket) {
            @fclose($this->socket);
        }
    }

    private function mimeUtf8($string) {
        return '=?'.$this->config['smtp_charset'].'?B?'.base64_encode($string).'?=';
    }

    /**
    * Нативная отправка почты напрямую без атворизации определяя MX-сервер
    * @author sciner
    * @since 30/07/2012 15:18
    * 
    * @exception Exception
    * 
    * @param string $mail_to Адрес получателя
    * @param string $subject Тема письма
    * @param string $message Текст сообщения
    * @param array $headers Массив заголовков (Имя => значение)
    * 
    * @return bool
    */
	/*
    public function sendMail($mail_to, $subject, $message, $headers = array()) {
        // очистка логов
        $this->logs = array();
        $smtp_from = $this->config['smtp_from'];
        $mail_from = $this->config['smtp_username'];
        // если нет указания особых заголовков, то создаем массив стандартных заголовков по умолчанию
        if(!is_array($headers) || !count($headers)) {
            $charset = $this->config['smtp_charset'];
            $headers = array(
                'Reply-To' => $mail_from,
                'MIME-Version' => '1.0',
                'Content-Type' => "text/html; charset=\"{$charset}\"",
                'Content-Transfer-Encoding' => '8bit',
                'From' => "{$smtp_from} <{$mail_from}>",
                'To' => "{$mail_to} <{$mail_to}>",
                'X-Priority' => 3,
            );
        }
        // заголовки, которые нельзя заменить и без которых нельзя отправлять письмо
        $def_headers = array('Date' => date('r'), 'Subject' => $this->mimeUtf8($subject), 'X-Mailer' => 'one-ai mailer');
        // слеиваем заданные заголовки с заголовками, которые нельзя изменить или без которых невозможна отправка письма
        $headers = array_merge($headers, $def_headers);
        // склеиваем все заголовки в единый строковый буфер
        $headers_raw = null;
        foreach($headers as $name => $value) {
            $name = ucfirst(strtolower($name));
            // base64 кодирование отправителя и получателя
            if($name == 'From' || $name == 'To') {
                $ex = explode('<', $value, 2);
                if(count($ex) == 2) {
                    $ex[0] = $this->mimeUtf8(trim($ex[0]));
                    $value = implode(' <', $ex);
                }
            }
            $headers_raw .= "{$name}: {$value}\r\n";
        }
        $send = "{$headers_raw}\r\n\r\n{$message}\r\n.\r\n\r\n";
        // Получаем mx-записи домена получателя
        $rcp = explode('@', $mail_to);
        $rcp_user = $rcp[0];
        $rcp_server = $rcp[1];
        getmxrr($rcp_server, $mx_records, $mx_weight);
        // @todo отсортировать массив серверов на основе их весов
        if(!is_array($mx_records) || !count($mx_records)) {
            throw new Exception('Не удалось определить MX-запись получателя');
        }
        // выбираем сервер, через которого будем отправлять письмо
        $rcp_mx_host = array_shift($mx_records);
        // стандартный порт сервера
        $rcp_mx_port = 25;
        try {
            // процесс общения с сервером, здесь идёт непосредственно отправка письма на указанный адрес
            $this->connect($rcp_mx_host, $rcp_mx_port);            
            $this->put(null, null, 220);
            $this->put('HELO', 'one-ai.ru', 250);
            $this->put('MAIL FROM:', "<{$mail_from}>", 250);
            $this->put('RCPT TO:', "<{$mail_to}>", 250);
            $this->put('DATA', null, 354);
            $this->put(null, $send, 250);
            $this->close();
        } catch (Exception $ex) {
            throw $ex;
        }
        // признак успешности выполнения функции
        return true;
    }*/

    /**
    * Нативная отправка почты напрямую без атворизации определяя MX-сервер
    * @author notfoolen
    * @since 23.04.0213
    * 
    * @exception Exception
    * 
    * @param string $mail_to Адрес получателя
    * @param string $subject Тема письма
    * @param string $message Текст сообщения
    * @param array $headers Массив заголовков (Имя => значение)
    * 
    * @return bool
    */
    public function sendEmailNow($mail_to, $subject, $message, $headers = array()) {

        require_once dirname(__FILE__).'/Mail/class.phpmailer.php';

        $mail = new PHPMailer;
        $mail->CharSet = 'utf-8';
        $mail->IsSMTP();                            // Set mailer to use SMTP
        $mail->SMTPAuth = true;                     // Enable SMTP authentication
        $mail->SMTPSecure = 'tls';                  // Enable encryption, 'ssl' also accepted
        $mail->Host = Constant::DELIVERY_HOST;      // server
        $mail->Port = Constant::DELIVERY_PORT;      // port
        $mail->Username = Constant::DELIVERY_USER;  // SMTP username
        $mail->Password = Constant::DELIVERY_PASS;  // SMTP password
        $mail->From = Constant::DELIVERY_FROM;
        $mail->FromName = Constant::DELIVERY_FROM_NAME;

        if(defined('Constant::REGION_DELIVERY_HOST')) {
	        $mail->Host = Constant::REGION_DELIVERY_HOST;      // Specify server
	        $mail->Port = Constant::REGION_DELIVERY_PORT;      // Specify port
	        $mail->Username = Constant::REGION_DELIVERY_USER;  // Specify SMTP username
	        $mail->Password = Constant::REGION_DELIVERY_PASS;  // Specify SMTP password
	        $mail->From = Constant::REGION_DELIVERY_FROM;
        }

        $mail->AddAddress($mail_to);                // Add a recipient

        $mail->WordWrap = 50;                       // Set word wrap to 50 characters
        $mail->IsHTML(true);                        // Set email format to HTML

        $mail->Subject = $subject;
        $mail->Body    = $message;

        if($mail->Send()) {
            return true;
        } else {
            throw new Exception($mail->ErrorInfo, 951);
        }
    }

    /**
     * Нативная отправка почты напрямую без атворизации определяя MX-сервер
     * @author notfoolen
     * @since 23.04.0213
     *
     * @exception Exception
     *
     * @param string $mail_to Адрес получателя
     * @param string $subject Тема письма
     * @param string $message Текст сообщения
     * @param array $headers Массив заголовков (Имя => значение)
     *
     * @return bool
     */
    public function sendSmtpEmailNow($mail_to, $subject, $message, $headers = array()) {

        require_once dirname(__FILE__).'/Mail/class.phpmailer.php';

        $mail = new PHPMailer;
        $mail->CharSet = 'utf-8';
        $mail->IsSMTP();                            // Set mailer to use SMTP
        $mail->SMTPAuth = true;                     // Enable SMTP authentication
        $mail->SMTPSecure = 'tls';                  // Enable encryption, 'ssl' also accepted
        $mail->Host = Constant::DELIVERY_HOST;      // server
        $mail->Port = Constant::DELIVERY_PORT;      // port
        $mail->Username = Constant::DELIVERY_USER;  // SMTP username
        $mail->Password = Constant::DELIVERY_PASS;  // SMTP password
        $mail->From = Constant::DELIVERY_FROM;
        $mail->FromName = Constant::DELIVERY_FROM_NAME;

        if(defined('Constant::REGION_DELIVERY_HOST')) {
            $mail->Host = Constant::REGION_DELIVERY_HOST;      // Specify server
            $mail->Port = Constant::REGION_DELIVERY_PORT;      // Specify port
            $mail->Username = Constant::REGION_DELIVERY_USER;  // Specify SMTP username
            $mail->Password = Constant::REGION_DELIVERY_PASS;  // Specify SMTP password
            $mail->From = Constant::REGION_DELIVERY_FROM;
        }

        if (!empty($_FILES['file']['tmp_name'])) {
            $mail->AddAttachment($_FILES['file']['tmp_name'], $_FILES['file']['name']);
        }

        $mail->AddAddress($mail_to);                // Add a recipient

        $mail->WordWrap = 50;                       // Set word wrap to 50 characters
        $mail->IsHTML(true);                        // Set email format to HTML

        $mail->Subject = $subject;
        $mail->Body    = $message;

        if($mail->Send()) {
            return true;
        } else {
            throw new Exception($mail->ErrorInfo, 951);
        }
    }

    /**
    * Постановка писем в очередь
    * @author sciner
    * @since 30/07/2012 17:23
    * 
    * @param string $queueName Имя очереди
    * @param array $recipients массив получаталей
    * @param string $subject Тема письма
    * @param string $text Текст письма
    * @param array $attachments Файлы вложенные в письмо
    * 
    * @return int Количество успешно поставленных в очередь писем
    */
    public function sendEmail($queueName = 'purchase_order', $recipients, $subject, $text, $attachments) {
        // create a boundary string. It must be unique 
        // so we use the MD5 algorithm to generate a random hash 
        $random_hash = md5(date('r', time()));
        $attachmentsBody = null;
        
        /*if($text) {
            // $text = chunk_split(base64_encode($text));
$attachmentsBody .= <<<raw
--PHP-mixed-{$random_hash}
Content-Transfer-Encoding: 8bit
Content-Type: text/html; charset=utf-8

{$text}

raw;
        }*/
        
        if(count($attachments)) {
            foreach($attachments as $attach) {
                // define the body of the message.
                $fileBody = chunk_split(base64_encode($attach['file']));
                $fileName = $attach['file_name'];
                $fileType = $attach['file_mime_type'];
                $attachmentsBody .= <<<raw
--PHP-mixed-{$random_hash}
Content-Type: {$fileType}; name="{$fileName}"
Content-Transfer-Encoding: base64
Content-Disposition: attachment

{$fileBody}

raw;
            }
            $attachmentsBody = "\r\n" .$attachmentsBody. "\r\n--PHP-mixed-{$random_hash}--";
        }
        $cnt = 0;
        // каждому получателю отправляем письмо
        foreach($recipients as $mail_to) {
            // define the headers we want passed. Note that they are separated with \r\n
            $headers = array(
                'MIME-Version' => '1.0',
                'Reply-To' => Constant::DELIVERY_FROM,
                'To' => "{$mail_to} <{$mail_to}>",
                'Content-Transfer-Encoding' => '8bit',
                'From' => Constant::VAR_SITE_NAME . ' <' . Constant::DELIVERY_FROM . '>',
                //add boundary string and mime type specification 
                'Content-Type' => "multipart/mixed; boundary=\"PHP-mixed-{$random_hash}\"",
                'X-Priority' => 3,
            );
            // Service::Email()->createQueue($queueName);
            $added = Service::Email()->addToQueue($queueName, $mail_to, $subject, $attachmentsBody, $headers);
            if($added) {$cnt += 1;}
        }
        return $cnt;
    }

    /**
    * Отправка SMS
    * 
    * @param string $number Номер абонента
    * @param string $text Текст ообщения
    * 
    * @return bool
    */
    public function sms($number, $text) {
        if (!$number or !$text) {
            return false;
        }
        $message = mb_convert_encoding ($text, "UCS2", "UTF-8");
        $sms = "";
        $sms .= "To: {$number}\n";
        $sms .= "Alphabet: UCS-2\n";
        //$sms .= "Alphabet: Unicode\n";
        $sms .= "\n{$message}";
        //$date = date('d.m.Y_H:i:s');
        $file = '/var/spool/sms/outgoing/sendfromphp_'.rand(0, 2000000);
        $f = @fopen ($file, 'w+');
        if (!$f) {
            return false;
        }
        fwrite ($f, $sms);
        fclose ($f);
        return true;
    }

}
