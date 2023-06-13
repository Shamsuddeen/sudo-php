<?php

namespace SudoAfrica\Sudo\Exception;
class SudoException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}

class InvalidCredentials extends SudoException { 
    public $errors;
    public function __construct($message, array $errors = [])
    {
        parent::__construct($message);
        $this->errors = $errors;
    }
}
class RequiredValuesMissing extends SudoException {
    public $errors;
    public function __construct($message, array $errors = [])
    {
        parent::__construct($message);
        $this->errors = $errors;
    }
}
class IsNullOrInvalid extends SudoException { 
    public $errors;
    public function __construct($message, array $errors = [])
    {
        parent::__construct($message);
        $this->errors = $errors;
    }
}

?>