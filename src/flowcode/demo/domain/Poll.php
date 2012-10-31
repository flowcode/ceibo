<?php

namespace flowcode\demo\domain;

/**
 * Description of Poll
 *
 * @author JMA <jaguero@flowcode.com.ar>
 */
class Poll {

    private $id;
    private $question;
    private $options;

    function __construct($id, $question) {
        $this->id = $id;
        $this->question = $question;
        $this->options = array();
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getQuestion() {
        return $this->question;
    }

    public function setQuestion($question) {
        $this->question = $question;
    }

    public function getOptions() {
        return $this->options;
    }

    public function setOptions($options) {
        $this->options = $options;
    }
    
    public function addOption(PollOption $option){
        $this->options[] = $option;
    }

}

?>
