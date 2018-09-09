<?php
/**
 * 地址模型
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\common\model;
use think\Model;

class LocationModel extends Model{
    protected $pk = 'id';
    protected $table = 'cbs_location';
    protected $fields = array(
        'id','name','ename','serial','sort','parentid','namecode'
    );
}
?>