<?php

class index extends Controller {

    public function auto() {
        $p = new Page('post', 'pid,title,text,istop,stats,addtime,ptid,checked', 'addtime','1=1',10);

       $data=array(
            'newsAlls' => $p->sql(),
            'build_page' => $p->run()
        );        
        $this->setData($data);        
        $this->loadView('postlists');        
    }

}