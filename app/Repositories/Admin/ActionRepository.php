<?php
namespace App\Repositories\Admin;

use App\Repositories\BaseRepository;
use App\Models\Admin\NoahAction;
use App\Models\Admin\NoahRoleAction;

class ActionRepository extends BaseRepository {
    //定义权限编号初始值
    const CODE_START = 100;
    public $action;
    public $roleActions;
    
    public function __construct(NoahRoleAction $noahRoleAction, NoahAction $noahAction) {
        $this->roleActions = $noahRoleAction;
        $this->action = $noahAction;
    }
    public function add_manager($input){
        NoahAction::create($input);
    }
    /**
     * 获取所有权限
     * @return $arr Array
     */
    public function getActionsList()
    {
        $list = $this->action->get()->toarray();
        $arr = [];
        $lists = $this->_son($list, 0, $arr);
        return $lists;
    }

    public function _son($list, $pid = 0, &$arr)
    {
        if (! empty($list)) {
            foreach ($list as $k => $v) {
                if ($v['pid'] == $pid) {
                    $arr[$v['actionid']] = $v;
                    $this->_son($list, $v['action_id'], $arr[$v['action_id']]['child']);
                }
            }
            return $arr;
        }
    }

    /**
     * 权限单独
     * 
     * @param $actions 当前用户权限id array(1,2,3)
     * @param $action 已有权限数组            
     * @return $arr Array
     */
    public function getRoleAction($actions,$action)
    {
        $list = $this->action->whereIn('id',$actions)->orderBy('order_id', 'desc')->get()->toarray();
        $arr = [];
        $lists = $this->r_son($list, 0, $arr, $i = 0, $this->array_keys($action, 'id'));
        return $lists;
    }

    public function r_son($list, $pid = 0, &$arr, $i, $action = '')
    {
        if (! empty($list)) {
            foreach ($list as $k => $v) {
                if ($v['pid'] == $pid) {
                    $arr[$i]['text'] = $v['action_name'];
                    $arr[$i]['tags'] = $v['id'];
                    if (! empty($action[$v['id']])) {
                        $arr[$i]['href'] = 1;
                        $arr[$i]['state'] = [
                            'checked' => true,
                            'expanded' => false
                        ];
                    } else {
                        $arr[$i]['href'] = 0;
                        $arr[$i]['state'] = [
                            'checked' => false,
                            'expanded' => false
                        ];
                    }
                    $this->r_son($list, $v['id'], $arr[$i]['nodes'], 0, $action);
                    $i = $i + 1;
                }
            }
            return $arr;
        }
    }
    
    // PHP stdClass Object转array
    public function object_array($object)
    {
        $json = json_encode($object);
        $arr = json_decode($json, true);
        return $arr;
    }
    
    // PHP key array
    public function array_keys($arr, $key = 'id')
    {
        $newArr = [];
        $arr = $this->object_array($arr);
        if (! empty($arr)) {
            foreach ($arr as $k => $v) {
                $newArr[$v[$key]] = $v;
            }
        }
        return $newArr;
    }

    /**
     * 计算action_code
     */
    public function updateActionCode($actionid)
    {
        //获取当前记录
        $item = $this->action->find($actionid);
        //如果是一级目录
        $res = $this->action->where('pid',$item->pid)->get();
        if ($res->count()) {
            $max = 0;
            //取得最大的
            foreach ($res as $v) {
                if (isset($v->code) && $v->code > $max) {
                    $max = $v->code;
                }
            }
            if($max>0){
                $code = $max + 1;
            }else{
                //计算子节点的code 说明还没有子节点 parent_code . code_start = 102.100=102100
                $parent = $this->action->find($item->pid);
                $code = $parent->code.self::CODE_START;
            }
        }else{
            $code = self::CODE_START;
        }

        $this->action->where('id',$actionid)->update(array('code'=>$code));

    }
    
    public function updateCodes($params)
    {
        $codes = $this->formatCodes();
        if (isset($params['update']) && $params['update'] == 1) {
            foreach ($codes as $id => $code) {
                $this->action->where('id', $id)->update(['code' => $code]);
            }
            die('ok');
        } else {
            echo "<pre>";
            print_r($codes);
            exit();
        }
    }

    public function formatCodes($pid = 1, $parent_code = 100)
    {
        $codes = [];
        $allActions = $this->action->where('pid',$pid)->get()->toArray();
        $code = $parent_code.'100';
        
        foreach($allActions as $action){
            $thisCode = $code++;
            $codes[$action['actionid']] = $thisCode;
            $chCodes = $this->formatCodes($action['actionid'],$thisCode);
            $chCodes && $codes = $codes+$chCodes;
        }
        return $codes;
    }
}
