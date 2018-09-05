<?php

/**
 * 权限管理
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Admin\NoahAction;
use Illuminate\Http\Request;
use App\Repositories\Admin\ActionRepository;

class ActionController extends BaseController
{
    protected $request;
    public function __construct(Request $request, NoahAction $noahAction){
        parent::__construct();
        $this->request = $request;
        $this->NoahAction = $noahAction;
    }
    /**
     * 角色列表
     */
    public function action()
    {
        $data ['type'] = config('auth.actionType');

        return view('admin.action', $data);
    }

    /**
     *  添加权限信息
     * @retun json
     */
    public function addInfo()
    {
        $actionId = isset($this->request['action_id']) && !empty($this->request['action_id']) ? $this->request['action_id']:0;
        $actionService = new ActionRepository();

        //更新操作
        $is_update = false;
        if ($actionId > 0) {
            $noahActionModel = $this->NoahAction->find($actionId);
            $is_update = true;
        }
        $data = $this->_checkParams($this->request->all());
        $noahActionModel->action_name            = $data['action_name'];
        $noahActionModel->controller            = $data['controller'];
        $noahActionModel->actions               = $data['actions'];
        $noahActionModel->order_id               = $data['order_id'];
        $noahActionModel->url                   = $data['url'];
        $noahActionModel->type                  = $data['type'];
        $noahActionModel->code                  = $data['code'];
        $noahActionModel->icon                  = $data['icon'];
        $noahActionModel->status                = $data['status'];
        $noahActionModel->creator_id             = $this->getUserId();

        //添加根节点
        if ($this->request['pid'] == -1 && $actionId == -1) {
            $noahActionModel->pid = 0;
        }

        //添加节点
        if ($this->request['pid'] != -1) {
            $noahActionModel->pid = $this->request['pid'];
            $is_child = 1;
        }

        if ($noahActionModel->save()) {
            if($is_update){
                return $this->setCode(self::CODE_UPDATE)
                    ->setMsg('更新成功')
                    ->setData( array('id' => $noahActionModel->id, 'pid' => $noahActionModel->pid))
                    ->toJson();
            }
            $actionService->update_action_code($noahActionModel->id);
            $data = array('id'=>$noahActionModel->id, 'pid'=>$noahActionModel->pid);
            return $this->setCode(self::CODE_SUCCESS)->setMsg('添加成功')->setData($data)->toJson();
        } else {
            return $this->setCode(self::CODE_ERROR)->setMsg('添加失败')->toJson();
        }
    }
    
    /**
     *  获取权限结构
     *  @retun json
     */
    public function getTree(){
        $id = $this->request['id'];
        if(empty($id)){
            $id = 0;
        }
        $item = $this->NoahAction->where(array('pid'=>$id))->orderBy('order_id','DESC')->get();
        $data = [];
        foreach( $item as $val){
            $type = 'item';
            $status = $this->_checkChildren($val['id']);
            if($status){
                $type = 'folder';
            }
            $node = array(
                'id' => $val['id'],
                'name' => $val['action_name'],
                'order' => $val['order_id'],
                'type' => $type,
                'pid' => $val['pid'],
                "additionalParameters" => array('id' => $val['id'], "children" => true, "itemSelected" => true)
            );
            $data[] = $node;

        }
        $res = ['code'=>1,'msg'=>'操作成功','data'=>$data];
        return json_encode($res);
    }
    
    
    /**
     * @param $actionid 节点id
     * @return bool
     *
     */
    private function _checkChildren($actionid)
    {
        $item = $this->NoahAction->where('pid',$actionid)->first();
        return isset($item->id) ? true : false;
    }

    /**
     * 获取节点信息
     * @return  json
     */
    public function getInfo()
    {
        $id = $this->request['id'];
        $item = $this->NoahAction->find($id);
        $res = ['code'=>1,'msg'=>'操作成功','data'=>$item];
        return json_encode($res);
    }


    /**
     * 删除节点
     * @return json
     */
    public function delInfo()
    {
        $actionId = $this->request['id'];
        $status = $this->NoahAction->where('id',$actionId)->delete();
        $item = $this->NoahAction->where('pid',$actionId)->get();
        if($item) {
            $this->NoahAction->where('pid',$actionId)->delete();
        }

        $res = ['code'=>1,'msg'=>'操作成功','data'=>array('id'=>$actionId)];
        if($status) {
            return json_encode($res);
        }
        return json_encode($res);
    }
    
    public function _checkParams($data)
    {
        $data['controller'] = isset($data['controller']) ? trim($data['controller']) : '';
        $data['actions'] = isset($data['actions']) ? trim($data['actions']) : '';
        $data['action_name'] = isset($data['actionname']) ? trim($data['actionname']) : '';
        $data['pid'] = isset($data['pid']) ? intval($data['pid']) : 0;
        $data['order_id'] = isset($data['order_id']) ? intval($data['order_id']) : 0;
        $data['status'] = isset($data['status']) ? intval($data['status']) : -1;
        $data['url'] = isset($data['url']) ? trim($data['url']) : '';
        $data['type'] = isset($data['type']) ? intval($data['type']) : 0;
        $data['code'] = isset($data['code']) ? trim($data['code']) : '';
        $data['icon'] = isset($data['icon']) ? trim($data['icon']) : '';
        return $data;
    }
    
    /**
     * 修复code使用
     */
    public function updateCode(Request $request)
    {
        (new ActionRepository)->updateCodes($request->all());
    }

}
