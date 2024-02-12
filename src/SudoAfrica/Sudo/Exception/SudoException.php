<?php

namespace SudoAfrica\Sudo\Exception;

class SudoException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}

?>