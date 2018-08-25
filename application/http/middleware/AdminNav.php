<?php
namespace app\http\middleware;

use app\common\exception\AppException;

class AdminNav {
    public function handle($request, \Closure $next, $tag){
        try{
            $this->initNav($tag);
        }catch(\Exception $e){
            echo $e->getMessage();exit;
            switch($e->getCode()){
                case AppException::BUSINESS:
                    throw $e;
                    break;
                case AppException::NO_LOGIN:
                    return redirect('useradmin/login');
                    break;
                case AppException::NO_AUTH:
                    //return redirect('useradmin/login');
                    return redirect('useradmin/noauth');
                    break;
                default:
                    return redirect('useradmin/login');
                    break;
            }
        }
       return $next($request);
    }
    private function topNav($user, $curid){
        $topNav = '';
        if(!empty($user['nav']['data'][1][0])){
            $topList = $user['nav']['data'][1][0];
            $listStr = '';
            foreach($topList as $v){
                $active = $v['navid'] == $curid ? 'class="active"' : '';
                $listStr .= "<li {$active} id=\"mod-{$v['tag']}\"><a href=\"{$v['uri']}\">{$v['title']}</a></li>";
            }
            $topNav = "<ul class=\"nav navbar-nav\">{$listStr}</ul>";
        }
        return $topNav;
    }
    private function leftNav($user, $pid, $curid){
        $leftNav = '';
        if(!empty($user['nav']['data'][2][$pid])){
            $leftList = $user['nav']['data'][2][$pid];
            $listStr = '';
            foreach($leftList as $v){
                $listStr .= "<li id=\"mod-{$v['tag']}\"><a href=\"{$v['uri']}\">{$v['title']}</a>";
                if(!empty($user['nav']['data'][3][$v['navid']])){
                    $listStr .= '<ul class="sub-menu">';
                    foreach($user['nav']['data'][3][$v['navid']] as $sv){
                        $active = $sv['navid'] == $curid ? 'class="active"' : '';
                        $listStr .= "<li {$active} id=\"{$sv['tag']}\"><a href=\"{$sv['uri']}\">{$sv['title']}</a></li>";
                    }
                    $listStr .= '</ul>';
                }
            }
            $leftNav = "<ul class=\"cl-vnavigation\">{$listStr}</ul>";
        }
        return $leftNav;
    }
    private function getRecursiveNav($navid, $level, $navList){
        static $list;
        if($level >= 1){
            $nav = [];
            foreach($navList[$level] as $v){
                $flag = false;
                foreach($v as $sv){
                    if($sv['navid'] == $navid){
                        $nav = $sv;
                        $flag = true;
                        break;
                    }
                }
                if($flag) break;
            }
            if(!empty($nav)){
                $list[$level] = $nav;
            }
            $level--;
            if(!empty($nav)){
                $this->getRecursiveNav($nav['parentid'], $level, $navList);
            }
        }
        return $list;
    }
    private function initNav($tag){
        $user = config('user');
        if(empty($user)) throw new AppException('未登录', AppException::NO_LOGIN);
        if(!$tag || empty($user['nav']['dataTag'][$tag])) throw new AppException('没有权限', AppException::NO_AUTH);
        $nav = $user['nav']['dataTag'][$tag];
        if($nav['level'] < 3) throw new AppException('地址层级错误', AppException::BUSINESS);
        $recursiveNav = $this->getRecursiveNav($nav['navid'], $nav['level'], $user['nav']['data']);
        $topNav = $recursiveNav[1];
        $leftNav = $recursiveNav[3];
        config('topNav', $this->topNav($user, $topNav['navid']));
        config('leftNav', $this->leftNav($user, $topNav['navid'], $leftNav['navid']));
    }
}

