<?php

namespace SudoAfrica\Sudo\Exception;

class IsNullOrInvalid extends \Exception { 
    public $errors;
    public function __construct($message, array $errors = [])
    {
        parent::__construct($message);
        $this->errors = $errors;
    }
}

?>