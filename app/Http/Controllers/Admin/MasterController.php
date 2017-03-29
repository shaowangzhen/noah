<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\NoahMaster;
use App\Repositories\UserRepository;
use App\Repositories\MasterRepository;

class MasterController extends BaseController
{

    protected $masterRepo;

    public function __construct(MasterRepository $masterRepo)
    {
        parent::__construct();
        $this->masterRepo = $masterRepo;
    }

    /**
     * 用户列表
     * @param Request $request
     * @return type
     */
    public function master(Request $request)
    {
        $params = $request->all();

        $lists = $this->masterRepo->getMasterList($params);

        $data = ['lists' => $lists, 'params' => $params];
        return view('admin.master', $data);
    }

    /**
     * 用户添加
     * @param Request $request
     * @return type
     */
    public function masterAdd(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            $res = $this->masterRepo->createData($data);
            if ($res == '添加成功') {
                return $this->setCode(self::CODE_SUCCESS)->setMsg($res)->toJson();
            } else {
                return $this->setCode(self::CODE_ERROR)->setMsg($res)->toJson();
            }
        }
        $data_level = NoahMaster::$dataLevel;
        $status = NoahMaster::$status;
        $roles = $this->masterRepo->getRolesWithMaster();
        $data = [
            'data_level' => $data_level,
            'roles' => $roles,
            'status' => $status
        ];

        return view('admin.master_add', $data);
    }

    /**
     * 用户信息修改
     * @param Request $request
     * @return type
     */
    public function masterEdit(Request $request, $masterid)
    {
        $noahMaster = NoahMaster::where('masterid', $masterid)->first();
        if ($request->isMethod('post')) {
            $data = $request->all();
            $res = $this->masterRepo->updateData($data, $masterid);
            if ($res === true) {
                return $this->setCode(self::CODE_SUCCESS)->setMsg('编辑成功')->toJson();
            } else {
                if($res === false) {
                    return $this->setCode(self::CODE_ERROR)->setMsg('编辑失败')->toJson();
                } else {
                    return $this->setCode(self::CODE_ERROR)->setMsg($res)->toJson();
                }
            }
        }
        $data_level = NoahMaster::$dataLevel;
        $status = NoahMaster::$status;
        $roles = $this->masterRepo->getRolesWithMaster($masterid);
        $data = [
            'data_level' => $data_level,
            'master' => $noahMaster,
            'roles' => $roles,
            'status' => $status
        ];

        return view('admin.master_edit', $data);
    }

    public function ajaxDBUser(Request $request) {
        $mastername = $request->input('mastername');
        $data = UserRepository::searchDBUserByName($mastername);
        echo json_encode($data);
    }

}
