<?php
/**
 * Created by PhpStorm.
 * User: 3093058@qq.com
 * Date: 2019-12-14 18:56
 * Dsecript: xxxxx
 */

class LRUCache {
    private $limit;
    private $list;

    /**
     * LRUCache constructor.
     * @param $limit
     */
    function __construct($limit) {
        $this->limit=$limit;
        $this->list=new HashList();
    }

    /**
     * @param $key
     * @return int
     */
    function get($key) {
        if($key<0) return -1;
        return $this->list->get($key);
    }

    /**
     * @param $key
     * @param $value
     */
    function put($key, $value) {
        $size=$this->list->size;
        $isHas=$this->list->checkIndex($key);
        if($isHas || $size+1 > $this->limit){
            $this->list->removeNode($key);
        }
        $this->list->addAsHead($key,$value);
    }
}

//链表的操作类
class HashList{
    public $head;
    public $tail;
    public $size;
    public $buckets=[];
    public function __construct(Node $head=null,Node $tail=null){
        $this->head=$head;
        $this->tail=$tail;
        $this->size=0;
    }

    /**
     * @param $key
     * @return bool
     * 检查键是否存在
     */
    public function checkIndex($key){
        return  array_key_exists($key ,$this->buckets) ? true : false ;
    }

    /**
     * @param $key
     * @return int
     * 获取key的值
     */
    public function get($key){
        if(!array_key_exists($key ,$this->buckets)) return -1;
        $res=$this->buckets[$key];
        $this->moveToHead($res);
        return $res->val;
    }

    /**
     * @param $key
     * @param $val
     * 新加入的节点
     */
    public function addAsHead($key,$val)
    {
        $node=new Node($val);
        if($this->tail==null && $this->head !=null){
            $this->tail=$this->head;
            $this->tail->next=null;
            $this->tail->pre=$node;
        }
        $node->pre=null;
        $node->next=$this->head;
        $this->head->pre=$node;
        $this->head=$node;
        $node->key=$key;
        $this->buckets[$key]=$node;
        $this->size++;
    }


    /**
     * @param $key
     * 移除指针(删除最近最少使用原则)
     */
    public function removeNode($key)
    {
        $current=$this->head;
        for($i=1;$i<$this->size;$i++){
            if($current->key==$key) break;
            $current=$current->next;
        }
        unset($this->buckets[$current->key]);
        //调整指针
        if($current->pre==null){
            $current->next->pre=null;
            $this->head=$current->next;
        }else if($current->next ==null){
            $current->pre->next=null;
            $current=$current->pre;
            $this->tail=$current;
        }else{
            $current->pre->next=$current->next;
            $current->next->pre=$current->pre;
            $current=null;
        }
        $this->size--;
    }

    /**
     * @param Node $node
     * 把对应的节点应到链表头部(最近get或者刚刚put进去的node节点)
     */
    public function moveToHead(Node $node)
    {
        if($node==$this->head) return ;
        //调整前后指针指向
        $node->pre->next=$node->next;
        $node->next->pre=$node->pre;
        $node->next=$this->head;
        $this->head->pre=$node;
        $this->head=$node;
        $node->pre=null;
    }

}


class Node{
    public $key;
    public $val;
    public $next;
    public $pre;
    public function __construct($val)
    {
        $this->val=$val;
    }
}


//测试
$cache = new LRUCache(3);
$cache->put("1", "1");
$cache->put("2", "2");
$cache->put("3", "3");
$cache->put("4", "4");
var_dump($cache->get('4'));  //输出 4   可以获取当前key为 2 3 4
var_dump($cache->get('1'));  //输出 -1  些时key=1的已经被删除
exit;
