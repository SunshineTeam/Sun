<?php

class ApplicationException extends Exception {
    
    /**
     * @param string $message
     * @param integer $code
     * @param Exception $previous
     */ 
    public function __construct ($message, $code = 0, Exception $previous = NULL) {
        parent::__construct ($message, $code, $previous);

    }

    public function __toString () {
        return __CLASS__ . ": [{$this -> code}]: {$this -> message}\n";

    }
}
