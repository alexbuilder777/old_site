<?php
namespace Api\Exception;

class ExceptionApi extends \Exception {


    // Переопределим исключение так, что параметр message станет обязательным
    public function __construct($message, $code = 0, Exception $previous = null) {
		parent::__construct($message);
    }

    // Переопределим строковое представление объекта.
    public function __toString() {
        return __CLASS__ . "333: [{$this->code}]: {$this->message}\n";
    }

    public function setError() {

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'error' => [
                'msg' => $this->message,
                'code' => $this->code,
            ],
        ]);
        exit;

    }



}