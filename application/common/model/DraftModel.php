<?php
/**
 * 文稿模型
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\common\model;
use think\Model;
use think\model\concern\SoftDelete;
use app\common\AppTool;
class DraftModel extends Model{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    protected $pk = 'id';
    protected $table = 'cbs_draft';
    protected $fields = array(
        'id','title','author','img_list','digest','content','show_time','recommend_time','count_view'
    );
    
    public function hideField(){
        return $this->hidden(self::hiddenFields());
    }
    public static function hiddenFields(){
        return ['delete_time','update_time'];
    }
    /**
     * 推荐查询条件
     * @return \app\common\model\BookModel
     */
    public function recommendQuery(){
        $this->where('recommend_time', '<>', 0);
        return $this;
    }
    public function getDigestTextAttr($value, $data){
        return 'test';
    }
    public function setContentAttr($value){
        return base64_encode(gzcompress($value, 6));
    }
    public function getContentAttr($value){
        return gzuncompress(base64_decode($value));
    }
}
?>