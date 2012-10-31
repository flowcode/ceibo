<?php

namespace flowcode\demo\domain;

/**
 * Description of PollOption
 *
 * @author JMA <jaguero@flowcode.com.ar>
 */
class PollOption {

    private $id;
    private $name;
    private $idPoll;
    private $poll;

    function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getIdPoll() {
        return $this->idPoll;
    }

    public function setIdPoll($idPoll) {
        $this->idPoll = $idPoll;
    }

    public function getPoll() {
        return $this->poll;
    }

    public function setPoll($poll) {
        $this->poll = $poll;
    }

}

?>
