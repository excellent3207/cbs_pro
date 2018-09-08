<?php
/**
 * 用户模型
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\common\model;
use think\Model;
use think\model\concern\SoftDelete;
class UserModel extends Model{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    protected $pk = 'id';
    protected $table = 'cbs_user';
    protected $fields = array(
        'id','wx_openid','phone','alias','face','sex','birth','school','professional'
    );
    
    public function hideField(){
        return $this->hidden(self::hiddenFields());
    }
    public static function hiddenFields(){
        return ['delete_time','create_time','update_time'];
    }
    /**
     * 图书所属分类关联
     * @return \think\model\relation\BelongsToMany
     */
    public function books(){
        return $this->belongsToMany('BookModel', '\\app\\common\\model\\BookShelf', 'bookid', 'userid');
    }
    /**
     * 性别
     * @param unknown $value
     * @param unknown $data
     * @return string
     */
    public function getSexAttr($value,$data){
        $sex = [1=>'男',2=>'女',0=>''];
        return $sex[$data['sex']];
    }
    /**
     * 设置性别转换
     * @param unknown $value
     * @return mixed
     */
    public function setSexAttr($value){
        $sex = 0;
        switch($value){
            case '男':
                $sex = 1;
                break;
            case '女':
                $sex = 2;
        }
        return $sex;
    }
    /**
     * 生日
     * @param unknown $value
     * @param unknown $data
     * @return string
     */
    public function getBirthAttr($value,$data){
       return $value?date('Y-m-d'):'';
    }
    /**
     * 设置生日转换
     * @param unknown $value
     * @return mixed
     */
    public function setBirthAttr($value){
        $str = $value?strtotime($value):0;
        return $str;
    }
}
?>