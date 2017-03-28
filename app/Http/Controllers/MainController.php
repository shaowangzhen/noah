<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use App\Models\NoahMaster;
use App\Library\Common;
use Illuminate\Http\Request;
use DB;

class MainController extends BaseController {


    //首页默认展示信息
    public function main()
    {
        $masterid = $this->getUserId();
        $user = NoahMaster::where('masterid',$masterid)->first()->toArray();
        $roleids = UserRepository::getUserRoleIdsByMasterId($masterid);
        $roles = UserRepository::getRoleByRoleids($roleids, ['name']);
        $data = [
            'user' => $user,
            'roles' => $roles
        ];
        $data['dealerInfo'] = [];
        return view('main',$data);
    }

    //用户编辑手机号
    public function editMobile(Request $request)
    {
        $masterid = $this->getUserId();
        $mobile = $request->input('mobile');
        if(Common::isMobile($mobile)){
            $master = NoahMaster::where('masterid',$masterid)->first();
            $master->mobile = $mobile;
            $master->save();
            return $this->setCode(self::CODE_SUCCESS)->setMsg('修改成功')->toJson();
        }else{
            return $this->setCode(self::CODE_ERROR)->setMsg('手机号格式错误！')->toJson();
        }
    }

    /**
     * 用户修改密码
     * 错误代码备忘
     * -1 : 默认
     *     empty_input  输入为空
     *     new_password_not_same  新密码两次输入不一致
     *     update_error  更新失败（数据库异常）
     *     old_password_error  原密码验证失败
     * 1 : 密码修改成功
     *
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editPassword(Request $request)
    {
        if($request->getMethod() == 'POST') {
            $post = $request->all();
            //去除两边特殊字符
            foreach($post as $k => $v) {
                $post[$k] = trim($v);
            }
            $returnArray = [
                'code' => '-1',
                'msg' => 'error'
            ];
            $masterid = $this->getUserId();
            if(empty($post['old_password']) ||
                empty($post['new_password']) ||
                empty($post['confirm_password'])) {
                $returnArray = [
                    'code' => '-1',
                    'msg' => 'empty_input'
                ];
            }elseif(trim($post['new_password']) != trim($post['confirm_password'])) {
                $returnArray = [
                    'code' => '-1',
                    'msg' => 'new_password_not_same'
                ];
            }elseif(UserRepository::verifyPasswordByMasterId(trim($post['old_password']), $masterid)) {
                if(UserRepository::updatePasswordByMasterId(trim($post['new_password']), $masterid)) {
                    $returnArray = [
                        'code' => '1',
                        'msg' => 'password_changed'
                    ];
                } else {
                    $returnArray = [
                        'code' => '-1',
                        'msg' => 'update_error'
                    ];
                }
            } else {
                $returnArray = [
                    'code' => '-1',
                    'msg' => 'old_password_error'
                ];
            }
            return $returnArray;
        }
        return view('editpassword', []);
    }

    /**
     * 重置用户密码
     * 错误代码备忘
     * 0 : 默认，（输入内容为空）
     * 1 : 密码修改成功
     * -1 :
     * -2 : 验证码验证失败
     * -3 : 验证码过期
     * -4 : 账号名称和手机号不匹配
     * -5 : 新密码输入为空
     * -6 : 更新操作失败（数据库异常）
     *
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function resetPassword(Request $request)
    {
        if($request->getMethod() == 'POST') {
            $post = $request->all();
            //去除两边特殊字符
            foreach($post as $k => $v) {
                $post[$k] = trim($v);
            }
            $checked = UserRepository::checkSMSCode(
                $post['sms_code'],
                $post['mobile'],
                $post['new_password']
            );
            $returnArray = [
                'code' => '0',
                'msg' => 'empty_input'
            ];

            switch($checked) {
                case 1:
                    $returnArray['code'] = '1';
                    $returnArray['msg'] = 'password_changed';
                    break;
                case -1:
                    $returnArray['code'] = '-3';
                    $returnArray['msg'] = 'code_expired';
                    break;
                case -2:
                    $returnArray['code'] = '-2';
                    $returnArray['msg'] = 'code_not_same';
                    break;
                default:
                    break;
            }
            if($returnArray['code'] == '1') {
                $checked = UserRepository::checkMobileByMasterName(
                    $post['master_name'],
                    $post['mobile']
                );
                if($checked) {
                    $newPassword = trim($post['new_password']);
                    if(trim($newPassword) == '') {
                        $returnArray['code'] = '-5';
                        $returnArray['msg'] = 'password_empty';
                    } else {
                        if(UserRepository::updatePasswordByMasterNameAndMobile(
                            $newPassword,
                            $post['master_name'],
                            $post['mobile'])
                        ) {
                            UserRepository::clearSMSCode($post['mobile']);
                            $returnArray['code'] = '1';
                            $returnArray['msg'] = 'password_changed';
                        } else {
                            $returnArray['code'] = '-6';
                            $returnArray['msg'] = 'update_error';
                        }
                    }
                } else {
                    $returnArray['code'] = '-4';
                    $returnArray['msg'] = 'account_mobile_error';
                }
            }
            return $returnArray;
        }
        return view('resetpassword', []);
    }
}
