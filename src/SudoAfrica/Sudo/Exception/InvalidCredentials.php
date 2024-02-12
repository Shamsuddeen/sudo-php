<?php

namespace SudoAfrica\Sudo\Exception;

class InvalidCredentials extends \Exception { 
    public $errors;
    public function __construct($message, array $errors = [])
    {
        parent::__construct($message);
        $this->errors = $errors;
    }
}

?>