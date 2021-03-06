<?php

/*
 * Class Page
 *
 * update : 2012-05-02
 * ----------------------------------------------------------------------*
 * @improve : ZYP            @time : 2012-01-11
 * ----------------------------------------------------------------------*
 * Notes :
 *
 * $p = new Page ($num_in_per_page , $table_name_from_sql);
 * $p -> run();		// get a string of the front face, you just need to echo it.
 * $p -> sql();		// get array of the content from the datebase
 * ----------------------------------------------------------------------*
 */

class Page {

    private $total; //数据表中总记录数
    private $listRows; //每页显示行数
    private $pageNum; //页数
    private $config = array('header' => '条记录', 'prev' => 'PRE', 'next' => 'NEXT', 'first' => '«', 'last' => "»", 'jump' => 'GO');
    private $uri;
    private $listNum = 4;
    private $tablename;
    private $page;
    private $sql;
    private $select;
    private $order;

    function __construct($tablename, $select, $order, $where=1,$listRows=10) {
        $pa = "";
        $this->sql = $where;
        $this->select= $select;
        $this->tablename = $tablename;
        $this->total = DB::table($this->tablename)->where($where)->count();
        $this->listRows = $listRows;
        $this->uri = $this->getUri($pa);
        $this->page = isset($_GET['p']) ? intval($_GET['p']) : 1;
        $this->pageNum = ceil($this->total / $this->listRows);
        $this->order=$order;
    }

    private function getUri($pa) {
        $url = $_SERVER["REQUEST_URI"] . (strpos($_SERVER["REQUEST_URI"], '?') ? '' : "?") . $pa;
        $parse = parse_url($url);
        if (isset($parse["query"])) {
            $params=array();
            parse_str($parse['query'], $params);
            unset($params["p"]);
            $url = $parse['path'] . '?' . http_build_query($params);
        }
        return $url;
    }

    private function htmlLink($href, $page, $name) {
        $and = substr($href, -1) == '?' ? '' : '&';
        return "<li><a href='" . $href . $and . "p=" . $page . "'>" . $name . "</a></li>";
    }

    private function htmlCurrent($num){
        return '<li  class="active current" ><a href="#">'  . $num . '</a></li>';
    }
    
    private function start() {
        return $this->total == 0?0:($this->page - 1) * $this->listRows + 1;
    }

    private function end() {
        return min($this->page * $this->listRows, $this->total);
    }

    private function first() {
       return $this->page == 1?'':$this->htmlLink($this->uri, 1, $this->config["first"]);
    }

    private function prev() {
        return $this->page == 1?'':$this->htmlLink($this->uri, $this->page - 1, $this->config["prev"]);
    }

    private function pageList() {
        $linkPage = "";
        $inum = floor($this->listNum);

        for ($i = $inum; $i >= 1; $i--) {
            $page = $this->page - $i;
            if ($page < 1)
                continue;
            $linkPage .=$this->htmlLink($this->uri, $page, $page); //每页数字两边显示空格
        }

        $linkPage.=$this->htmlCurrent($this->page);

        for ($i = 1; $i <= $inum; $i++) {
            $page = $this->page + $i;
            if ($page <= $this->pageNum)
                $linkPage .=$this->htmlLink($this->uri, $page, $page) ; //每页数字两边显示空格
            else
                break;
        }

        return $linkPage;
    }

    private function next() {
        return $this->page == $this->pageNum?'':$this->htmlLink($this->uri, $this->page + 1, $this->config["next"]);
    }

    private function last() {
        return $this->page == $this->pageNum?'':$this->htmlLink($this->uri, $this->pageNum, $this->config["last"]);
    }

    private function jump() {
        return '<input type="text" onkeydown="javascript:if(event.keyCode==13){var page=(this.value>' . $this->pageNum . ')?' . $this->pageNum . ':this.value;location=\'' . $this->uri . '&p=\'+page+\'\'}" value="' . $this->page . '" style="width:25px"><input type="button" value="' . $this->config['jump'] . '" onclick="javascript:var page=(this.previousSibling.value>' . $this->pageNum . ')?' . $this->pageNum . ':this.previousSibling.value;location=\'' . $this->uri . '&p=\'+page+\'\'">';
    }

    function info() {
        $run = '';
        $run.="共有" . $this->total . $this->config["header"];
        $run.="每页" . ($this->end() - $this->start() + 1) . "条，本页" . $this->start() . "~" . $this->end() . "条&nbsp;&nbsp;";
        $run.=$this->page . "/" . $this->pageNum . "页&nbsp;&nbsp;<br>";
        return $run;
    }

    function run() {
        if ($this->pageNum != 1) {
            return '<ul class="pagination">'
                    .$this->first()
                    .$this->prev()
                    .$this->pageList()
                    .$this->next()
                    .$this->last()
                    //.$this->jump()
                    .'</ul>';
        }
    }

    function sql() {
        $where = $this->sql;
        $order=$this->order;
        $per = $this->listRows;
        $ber = ($this->page - 1) * $per; //每页开始查询数字
        return DB::table($this->tablename)->where("$where order by $order desc limit $ber,$per")->findAll($this->select);
    }

}
