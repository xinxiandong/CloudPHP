<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Controller
 *
 * @author YunPeng
 */
class Controller {

    public $abc = 'hello';
    protected $get = array();
    protected $db;

    public function __construct() {
        $this->get = App::$get;
        $this->db = App::$db;
    }

    public function __destruct() {
        
    }

    public function __call($name, $value) {
        exit("<br>;Bad function name '$name' ");
    }

}