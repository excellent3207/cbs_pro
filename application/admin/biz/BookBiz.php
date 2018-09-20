<?php
/**
 * 图书业务层
 */
namespace app\admin\biz;

use app\common\exception\AppException;
use app\common\model\BookModel;
use app\common\model\BookCateModel;
use think\Db;
use think\Exception;
use app\common\model\BookVideoModel;
use app\common\AppExcel;

class BookBiz{
    public function getRole(){
        
    }
    /**
     * 获取书籍详情
     * @param unknown $id
     * @return \app\common\model\BookModel
     */
    public function get($id){
        $res = BookModel::get($id)->hideField();
        return $res;
    }
    /**
     * 书籍列表
     * @param unknown $cond
     * @param int $page
     * @param int $pageSize
     * @return unknown
     */
    public function list($cond, $cateid, int $page, int $pageSize){
        $books = BookModel::where($cond)->order('create_time desc')->hidden(BookModel::hiddenFields())->page($page, $pageSize)->select();
        if(!$books->isEmpty())
            $books->load('cate');
        return $books;
    }
    /**
     * 编辑
     * @param unknown $data
     * @throws AppException
     * @return boolean
     */
    public function save($data){
        $model = new BookModel();
        if(isset($data['id']) && $data['id']){
            $res = $model->allowField(TRUE)->save($data, ['id' => $data['id']]);
            if(!$res) throw new AppException('编辑书籍失败');
            return $res;
        }else{
            $res = $model->allowField(TRUE)->save($data);
            if(!$res) throw new AppException('创建书籍失败');
            return $model->id;
        }
    }
    /**
     * 删除
     * @param unknown $navid
     * @return unknown
     */
    public function del($ids){
        return BookModel::destroy(function($query) use ($ids){
            $query->where('id','in', $ids);
        });
    }
    /**
     * 前端展示图书
     * @param unknown $id
     * @return unknown
     */
    public function doShow($id){
        $model = new BookModel();
        return $model->save(['show_time' => $_SERVER['REQUEST_TIME']], ['id' => $id]);
    }
    /**
     * 取消前端展示图书
     * @param unknown $id
     * @return unknown
     */
    public function cancelShow($id){
        $model = new BookModel();
        return $model->save(['show_time' => 0], ['id' => $id]);
    }
    /**
     * 推荐图书
     * @param unknown $id
     * @return unknown
     */
    public function doRecomm($id){
        $model = new BookModel();
        return $model->save(['recommend_time' => $_SERVER['REQUEST_TIME']], ['id' => $id]);
    }
    /**
     * 取消推荐图书
     * @param unknown $id
     * @return unknown
     */
    public function cancelRecomm($id){
        $model = new BookModel();
        return $model->save(['recommend_time' => 0], ['id' => $id]);
    }
    /**
     * 图书添加分类
     * @param unknown $bookid
     * @param unknown $cateids
     */
    public function addCate($bookid, $cateid){
        $cateid = $cateid?$cateid:0;
        $this->save(['id' => $bookid, 'cateid' => $cateid]);
        return true;
    }
    /**
     * 书籍列表数量
     * @param unknown $cond
     * @return unknown
     */
    public function listCount($cond, $cateid){
        return BookModel::where($cond)->count('id');
    }
    /**
     * 视频列表
     * @param unknown $cond
     * @param int $page
     * @param int $pageSize
     * @return unknown
     */
    public function videos($bookid, $cond, int $page, int $pageSize){
        $model = new BookModel();
        $model->id = $bookid;
        $videos = $model->videos()->where($cond)->select();
        return $videos;
    }
    /**
     * 获取视频信息
     * @param unknown $id
     * @return \app\common\model\BookVideoModel|NULL
     */
    public function getVideo($id){
        return BookVideoModel::get($id);
    }
    /**
     * 视频列表数量
     * @param unknown $cond
     * @return unknown
     */
    public function videoCount($bookid, $cond){
        $model = new BookModel();
        $model->id = $bookid;
        return $model->videos()->where($cond)->count('id');
    }
    /**
     * 编辑
     * @param unknown $data
     * @throws AppException
     * @return boolean
     */
    public function saveVideo($data){
        $model = new BookVideoModel();
        if(isset($data['id']) && $data['id']){
            $res = $model->allowField(TRUE)->save($data, ['id' => $data['id']]);
            if(!$res) throw new AppException('编辑视频失败');
            return $res;
        }else{
            $res = $model->allowField(TRUE)->save($data);
            if(!$res) throw new AppException('创建视频失败');
            return $model->id;
        }
    }
    /**
     * 删除视频
     * @param unknown $navid
     * @return unknown
     */
    public function delVideo($ids){
        return BookVideoModel::destroy(function($query) use ($ids){
            $query->where('id','in', $ids);
        });
    }
    /**
     * 前端展示视频
     * @param unknown $id
     * @return unknown
     */
    public function doVideoShow($id){
        $model = new BookVideoModel();
        return $model->save(['show_time' => $_SERVER['REQUEST_TIME']], ['id' => $id]);
    }
    /**
     * 取消前端展示视频
     * @param unknown $id
     * @return unknown
     */
    public function cancelVideoShow($id){
        $model = new BookVideoModel();
        return $model->save(['show_time' => 0], ['id' => $id]);
    }
    public function import($filename){
        $datas = AppExcel::import($filename);
        $errorDatas = [];
        $insertDatas = [];
        $num = 0;
        foreach($datas as $data){
            try{
                if(count($data) > 8){
                    throw new \Exception('数据字段小于7个');
                }
                if(count($data) < 8){
                    throw new \Exception('数据字段大于7个');
                }
                if(trim($data[0]) == '书名') continue;
                $publishtime = strtotime(trim($data[5]));
                if(!$publishtime){
                    throw new \Exception('出版时间格式错误');
                }
                $cate = BookCateModel::where('catename', trim($data[7]))->find();
                if(empty($cate)) throw new \Exception('图书分类（'.$data[7].'）不存在');
                $count = BookModel::where('name', trim($data[0]))->count('id');
                if($count > 0) throw new \Exception('图书（'.$data[0].'）已经存在');
                switch(trim($data[6])){
                    case '高职高专':
                        $type = 2;
                        break;
                    case '本科精品':
                        $type = 1;
                        break;
                    default:
                        throw new \Exception('教材类型只有‘高职高专’和‘本科精品’，你输入的是‘'.$data[6].'’');
                }
                array_push($insertDatas, ['name' => $data[0], 'book_no' => $data[1], 'author' => $data[2],
                    'price' => $data[3], 'plotter' => $data[4], 'publishtime' => $data[5], 'type' => $type, 'cateid' => $cate['id']]);
                $num++;
            }catch(\Exception $e){
                array_push($errorDatas, ['data' => $data, 'error' => $e->getMessage()]);
                continue;
            }
        }
        if(!empty($insertDatas)){
            $bookModel = new BookModel();
            $bookModel->saveAll($insertDatas);
        }
        return ['count' => $num, 'error' => $errorDatas];
    }
}

