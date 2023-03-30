<?php

namespace Users\Exception;

use Exception;

class UserException extends Exception
{
    public function customMessage(): string
    {
        //error message
        return 'Error on line '.$this->getLine().' in '.$this->getFile()
            .': <b>'.$this->getMessage().'</b>';
    }
}
