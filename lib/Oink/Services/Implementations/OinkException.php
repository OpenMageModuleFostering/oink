<?php
class OinkException extends Exception{
    
    public function __toString() {
        return $this->getMessage();
    }
    
}
?>
