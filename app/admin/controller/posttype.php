<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of posttype
 *
 * @author YunPeng
 */

class posttype extends Controller{
    function auto(){
        $ddd=$this->model->getPosttype();
        $data=array(
            'results' =>$ddd,
            'newDataSelect'=>$this->model->showPostSelect($ddd),
        );
        $this->setData($data);
        $this->loadView();
    }

    function save(){
        $id=Input::number('id');
        $name=Input::text('typename');
        $parentid=Input::number('posttype');
        if($id>0){
            DB::table('posttype')->where('ptid=?',array($id))->update(array(
                'cname'=>$name,
                'parentid'=>$parentid,
            ));
        }else{
            DB::table('posttype')->save(array(
                'cname'=>$name,
                'parentid'=>$parentid,
                'level'=>$this->model->getLevel($parentid),
            ));
        }
        Html::jump(URL::controller());
    }

    function edit(){
        $id=Get::number(0);
        if($id>0){
            $results=DB::table('posttype')->where('ptid=?',array($id))->find('cname,parentid');
            $parentid=$results['parentid'];
            $typename=$results['cname'];
        }else{
            $parentid=0;
            $typename='';
        }
        $ddd=$this->model->getPosttype();
        $data=array(
            'name'=>$typename,
            'id'=>$id,
            'newDataSelect'=>$this->model->showPostSelect($ddd,$parentid),
        );
        $this->setData($data);
        $this->loadPage('posttypenew');
    }

    function delete(){
        $id=Get::number(0);
        if($id>0){
            $del=DB::transaction(function() use ($id){
                DB::table('posttype')->where('parentid=?',array($id))->update(array(
                    'parentid'=>0,
                    'level'=>1,
                ));
                return DB::table('posttype')->where('ptid=?',array($id))->delete();
            });

            if($del>0){
                Html::jump(URL::controller());
            }
        }
    }
}
