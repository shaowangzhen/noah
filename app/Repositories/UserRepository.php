<?php

namespace App\Repositories;

use App\Models\NoahMasterRoles;
use App\Models\NoahRole;
use App\Models\NoahResRole;
use App\Models\NoahMaster;
use App\Models\NoahRoleActions;
use App\Models\NoahAction;
use Mockery\CountValidator\Exception;
use App\Library\RedisCommon;
use Config;
use DB;

class UserRepository extends BaseRepository
{
    const REDIS_KEY_APPEND = ':op.dabanma.com:dealeruser:resetpassword';
    const REDIS_KEY_LOGIN_ERROR_TIMES = '_op.dabanma.com_login_error_times';   //登录密码错误次数 按天累计
    const REDIS_KEY_LOGIN_SMS_CODE = '_op.dabanma.com_login_sms_code';     //登录手机验证码

    static public function searchDBUserByName($mastername) {
        $resArr = [
            'code' => '1',
            'msg' => 'username_in_use'
        ];
        if(trim($mastername) != '') {
            $userExist = DB::table('noah_master')
                ->select(['masterid'])
                ->where('mastername', '=', sprintf("%s", $mastername))
                ->whereIn('status', [0, 1])
                ->get();

            if ($userExist) {
                $userExist = $userExist->toArray();
            }
            if(empty($userExist)) {
                $resArr = [
                    'code' => '0',
                    'msg' => 'username_can_use'
                ];
            }
            unset($userExist);
        }
        return $resArr;
    }

    static public function getDBUser($mastername, $pwd = '') {
        $selectCols = [
            'mastername',
            'fullname',
            'mobile',
            'email',
            'deptname',
            'password',
            'status'
        ];
        $dbUser = DB::table('noah_master')
            ->select($selectCols)
            ->where('mastername', '=', sprintf("%s", $mastername))
            ->where('mastername',  $mastername)
            ->orderBy('status','desc')
            ->orderBy('masterid','desc')
            ->first();
        if(is_null($dbUser)) {
            $dbUser = false;
        } else {
            $dbUser->password = base64_decode($dbUser->password);
            //验证密码是否正确
            if(password_verify(trim($pwd), $dbUser->password)) {
                switch ($dbUser->status) {
                    case -1:
                        $dbUser = ['code' => -1,'msg' => '该用户已被删除！'];
                        break;
                    case 0:
                        $dbUser = ['code' => -1,'msg' => '该用户已被禁用！'];
                        break;
                    case 1:
                        unset($dbUser->password);
                        unset($dbUser->status);
                        $dbUser = json_decode(json_encode($dbUser), true);
                        break;
                }
            } else {
                $dbUser = false;
            }
        }
        return $dbUser;
    }

    /**
     * 验证登录信息
     * @param string $mastername
     * @param string $pwd
     * @return true/false
     */
    static public function checkLogin($mastername, $pwd, $code = '', $checkCode = false, $errortimes = 10)
    {
        try {
            $result = self::getDBUser($mastername, $pwd);
            //禁止登录的用户
            if(isset($result['code'])) {
                return $result;
            }
            // 验证是否为系统的用户，构造基本信息和权限等信息，保存至session
            $data = [
                'users' => '',
                'roles' => '',
                'action_ids' => '',
                'powers' => ''
            ];
            $userInfo = NoahMaster::where('mastername', $mastername)->where('status', '1')->first();
            if (!$userInfo) {
                return ['code' => -1,'msg' => '抱歉，您不是系统用户！'];
            }else if ($result === false) {
                return ['code' => -1,'msg' => '用户名或密码错误！'];
            }else{
                $userInfo->lastlogintime = $userInfo->thislogintime;
                $userInfo->thislogintime = date('Y-m-d H:i:s');
                $userInfo->save();
            }
            $userInfo = $userInfo->toArray();
            // 构造用户基本信息
            $data['users'] = $userInfo;
            $masterid = $userInfo['masterid'];
            // 角色信息
            $data['roles'] = array_keys(self::getRoleList($masterid));
            $roleIds = $data['roles']?$data['roles']:[];
            // action列表
            $actionList = self::getActionList($roleIds);
            $data['action_ids'] = array_keys($actionList);
            foreach ($actionList as $k => $actionInfo)
            {
                $actionList[$k]['controller'] = strtolower($actionInfo['controller']);
                $actionList[$k]['actions'] = strtolower($actionInfo['actions']);
            }
            // 权限列表
            $data['powers'] = self::getPowerList($actionList);
            if(empty($data['powers'])){
                return ['code' => -1,'msg' => '抱歉，您没有系统权限！'];
            }
            session(['userInfo' => $data]);
            return ['code' => 1,'msg' => '成功'];
        } catch (\Exception $e) {
            throw $e;
        }
    }


    public static function loginStrategy($userInfo)
    {
        $mastername = $userInfo->mastername;
        $msg = self::getLoginMsg($mastername, 'times');
        $error = $msg ? ++$msg : 1;
        self::setLoginMsg($mastername, $error, 'times');
    }

    // 删除session中的登录内容
    public static function delLoginInfo()
    {
        try {
            session(['userInfo' => '']);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // 已有角色列表
    public static function getRoleList($masterid)
    {
        $roleIds = NoahMasterRoles::where('masterid',$masterid)->pluck('roleid')->all();
        $lists = NoahRole::whereIn('roleid', $roleIds)->where('status', 1)->get();
        $data = [];
        if (! empty($lists)) {
            foreach ($lists as $v) {
                $data[$v->roleid] = $v->name;
            }
        }

        return $data;
    }

    // 所有角色列表
    public static function getAllRoleList()
    {
        $lists = NoahRole::where('status', 1)->get();
        $data = [];
        if (! empty($lists)) {
            foreach ($lists as $v) {
                $data[$v->roleid] = $v->name;
            }
        }
        return $data;
    }

    // action列表
    private static function getActionList($roleIds)
    {
        try {
            $result = $actionList = array();
            if (empty($roleIds)) {
                return $result;
            }
            // 根据roleid，关联erp_role_actions和erp_action，获取roleid对应的actions
            $actionIds = NoahRoleActions::whereIn('roleid',$roleIds)->pluck('actionid')->all();
            $actionList = NoahAction::whereIn('actionid',$actionIds)->where('status',1)->orderBy('orderid','asc')->get()->toArray();
            foreach ($actionList as $v) {
                $result[$v['actionid']] = $v;
            }
            return $result;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    //  所有权限
    public static function getActionsLists()
    {
        return NoahAction::select('actionid')->get()->toarray();
    }

    // 权限列表(controller和action都小写)
    private static function getPowerList($actionList)
    {
        try {
            $powerList = [];
            if ($actionList) {
                foreach ($actionList as $actionInfo) {
                    // 根据controller和action，构造权限数据结构
                    if ($actionInfo['controller']) {
                        $actions = explode(',', $actionInfo['actions']);
                        foreach ((array) $actions as $_action) {
                            if (! empty($_action)) {
                                $_controller = strtolower(trim($actionInfo['controller']));
                                $_action = strtolower(trim($_action));
                                $powerList[$_controller][$_action] = $_action;
                            }
                        }
                    }
                }
                return $powerList;
            } else {
                return [];
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 构造admin后台左侧菜单列表（降序排列）
     *
     * @param array $roleIds（登录用户的权限ids）
     * @param array $powerList（登录用户的权限列表）
     * @return array
     */
    public static function getMenuList($roleIds, $actionIds)
    {
        try {
            $menuList = array();
            if (empty($roleIds)) {
                throw new \Exception('没有角色信息');
            }
            $Config = config('auth.system_id');
            $actionList = self::getActionList($roleIds);
            if (! empty($actionList)) {
                foreach ($actionList as $k => $v) {
                    // 根据type，构造menus数据结构（type=1是顶级菜单，type=2是点击可打开页面的菜单）
                    if ($v['type'] == 1 || $v['type'] == 2) {
                        // 仅使用登录用户信息中存在的action
                        if (in_array($v['actionid'], $actionIds)) {
                            // 构造菜单的基本信息
                            $menuList[$v['actionid']]['name'] = $v['actionname'];
                            $menuList[$v['actionid']]['actionid'] = $v['actionid'];
                            $menuList[$v['actionid']]['parent_actionid'] = $v['parent_actionid'];
                            // 构造页面url、showtype、icon
                            $menuList[$v['actionid']]['url'] = $v['url'];
                            $menuList[$v['actionid']]['icon'] = $v['icon'];
                        }
                    }
                }
            }
            $menuList = self::getMenuTree($menuList, $Config);
            return $menuList;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // 构造树形菜单
    public static function getMenuTree($data, $parent_actionid)
    {
        $menuTree = array();
        if ($data) {
            foreach ((array) $data as $k => $v) {
                // 父亲找到儿子
                if ($v['parent_actionid'] == $parent_actionid) {
                    $v['child'] = self::getMenuTree($data, $v['actionid']);
                    $menuTree[] = $v;
                }
            }
        }
        return $menuTree;
    }

    /**
     * 获取用户sesion信息
     */
    static public function getLoginInfo()
    {
        $userInfo = [];
        try {
            $userSessionData = session('userInfo');
            if ($userSessionData) {
                $userInfo = $userSessionData;
            }
            return $userInfo;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    /**
     * 验证权限，在免验证数组中的权限可过滤
     *
     * @param string $controllerName 小写
     * @param string $actionName 小写
     * @param array $powerList 权限数组
     * @return boolean true/false
     */
    public static function checkPower($controllerName, $actionName, $powerList)
    {
        try {
            // 为避免参数忽略大小写的情况，再次转化为小写
            $controllerName = strtolower($controllerName);
            $actionName = strtolower($actionName);
            $result = true;
            // 判断是否需要验证权限
            $checkPower = true;
            // 权限验证例外（不需做权限验证的controller和action）
            $exceptPower = config('auth.exceptPower');
            if (isset($exceptPower[$controllerName]) && (in_array("*", $exceptPower[$controllerName]) || in_array($actionName, $exceptPower[$controllerName]))) {
                $checkPower = false;
            }
            // 验证权限
            if ($checkPower) {
                if (! isset($powerList[$controllerName]) || ! isset($powerList[$controllerName][$actionName])) {
                    $result = false;
                }
            }
        } catch (\Exception $e) {
            $result = false;
        }

        return $result;
    }

    /*************************以下是功能函数****************************/

    /**
     * 根据用户id获取角色ids
     * @param int $masterid 用户id
     * @return array mixed
     * @author ZhaoZuoWu 2016-02-24
     */
    static public function getUserRoleIdsByMasterId($masterid)
    {
        $userRoleIdsArr= NoahMasterRoles::where('masterid',$masterid)->pluck('roleid')->all();
        return $userRoleIdsArr;


    }

    /**
     * @param string|array $roleids  角色id
     * @param array $columns  获取的字段
     * @return mixed
     */
    static public function getResRoleByRoleids($roleids='',$columns=['*'])
    {

        $roleids  = is_array($roleids) ? $roleids :explode(',',$roleids);

        $resRoleList = NoahResRole::select($columns)->whereIn('roleid',$roleids)->get();
        $resRoleList = $resRoleList ? $resRoleList->toArray():[];
        return $resRoleList;



    }

    /**
     * @param type $roleids
     * @param type $columns
     * @return type
     */
    static public function getRoleByRoleids($roleids='',$columns=['*'])
    {

        $roleids  = is_array($roleids) ? $roleids :explode(',',$roleids);

        $RoleList = NoahRole::select($columns)->whereIn('roleid',$roleids)->where('status','1')->get();
        $RoleList = $RoleList ? $RoleList->toArray():[];
        return $RoleList;



    }

    /**
     * @param string|array $roleids  角色id
     * @param string $columns
     * @return mixed
     */
    static public function listResRoleByRoleids($roleids = '', $columns = 'resid', $restype = 'city')
    {

        $roleIds = is_array($roleids) ? $roleids : explode(',', $roleids);

        $resRoleList = NoahResRole::whereIn('roleid', $roleIds)->where('restype',$restype)->pluck($columns)->all();

        return $resRoleList;
    }

    /**
     * 制作password
     * @param $password
     * @return string
     */
    static public function makePassword($password)
    {
        $passwordString = base64_encode(password_hash($password, PASSWORD_BCRYPT));
        return $passwordString;
    }

    /**
     * 验证密码通过用户id
     * @param $password
     * @param $masterId
     * @return bool
     */
    static public function verifyPasswordByMasterId($password, $masterId)
    {
        $masterPassword = DB::table('noah_master')
            ->select(['password'])
            ->where('masterid', '=', $masterId)
            ->first();
        $check = password_verify($password, base64_decode($masterPassword->password));
        return $check;
    }

    /**
     * 通过用户id更新密码
     * @param $password
     * @param $masterId
     * @return bool
     */
    static public function updatePasswordByMasterId($password, $masterId) {
        try {
            $master = NoahMaster::where('masterid', $masterId)
                ->first();
            if(is_null($master)) {
                throw new Exception('user_not_exist');
            }
            $master->password = SELF::makePassword($password);
            $master->save();

            if($master->isdealer == 1) {
                $dealerNewInfo = DealerNew::where('id', '=', $master->newdealerid)
                    ->first();
                if(!is_null($dealerNewInfo)) {
                    if(!self::updateDealerNewPasswordByDealerInfo($password, $dealerNewInfo)) {
                        throw new Exception("dealer_update_error");
                    }
                    unset($dealerNewInfo);
                }
            }
            unset($master);
        } catch(\Exception $e) {
            return false;
        }
        return true;
    }

    static public function updateDealerNewPasswordByDealerInfo($password, $dealerNewInfo) {
        try {
            if(!is_null($dealerNewInfo)) {
                $dealerHash = $dealerNewInfo->hash;
                $dealerPassword = md5(md5($password).$dealerHash);
                $dealerNewInfo->password = $dealerPassword;
                $dealerNewInfo->save();
            }
        } catch(Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * 通过用户名和手机号更新用户密码
     * @param $password
     * @param $mastername
     * @param $mobile
     * @return bool
     */
    static public function updatePasswordByMasterNameAndMobile($password, $mastername, $mobile) {
        try {
            $master = NoahMaster::where('mastername', $mastername)
                ->where('mobile', $mobile)
                ->where('status', '=', 1)
                ->where('isdealer', '=', 1)
                ->first();
            if(is_null($master)) {
                throw new Exception('user_not_exist');
            }
            $master->password = SELF::makePassword($password);
            $master->save();

            if($master->isdealer == 1) {
                $dealerNewInfo = DealerNew::where('id', '=', $master->newdealerid)
                    ->first();
                if(!is_null($dealerNewInfo)) {
                    if(!self::updateDealerNewPasswordByDealerInfo($password, $dealerNewInfo)) {
                        throw new Exception("dealer_update_error");
                    }
                    unset($dealerNewInfo);
                }
            }
            unset($master);
        } catch(\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * 通过用户名检查手机是否有效
     * @param $masterName
     * @param $mobile
     * @return bool
     */
    static public function checkMobileByMasterName($masterName, $mobile) {
        try {
            $master = NoahMaster::select(['masterid'])
                ->where('mastername', $masterName)
                ->where('mobile', $mobile)
                ->where('status', '=', 1)
                ->where('isdealer', '=', 1)
                ->first();
            if(!is_null($master)) {
                unset($master);
                return true;
            }
        } catch(\Exception $e) {
            return false;
        }
        return false;
    }

    /**
     * 制作验证码
     * @param $mobile
     * @return string
     */
    static public function makeSMSCode($mobile, $digit = 6, $key = self::REDIS_KEY_APPEND) {
        $smsCode = md5(microtime(true));
        $seed = rand(0, 10);
        $smsCode = substr($smsCode, $seed, $digit);
        $redis = new RedisCommon();
        $redis->setex($mobile . $key, $smsCode, 90);
        return $smsCode;
    }

    /**
     * 清除缓存内手机验证码
     * @param $mobile
     * @return bool
     */
    static public function clearSMSCode($mobile, $key = self::REDIS_KEY_APPEND) {
        $redis = new RedisCommon();
        $redis->delete($mobile . $key);
        return true;
    }

    /**
     * 检查验证码
     * @param $smsCode
     * @param $mobile
     * @param $password
     * @return string  1匹配 -1失效 -2不匹配
     */
    static public function checkSMSCode($smsCode, $mobile, $password, $key = self::REDIS_KEY_APPEND) {
        $redis = new RedisCommon();
        $smsOldCode = $redis->get($mobile . $key);
        if($smsOldCode === false) {
            return -1;
        }
        if($smsCode != $smsOldCode) {
            return -2;
        } else {
            return 1;
        }
        return -1;
    }

    public static function setLoginMsg($mastername, $msg, $type = 'code')
    {
        $redis = new RedisCommon();
        if($type == 'times'){
            $time = date('Ymd');
            $redis->setex($mastername . self::REDIS_KEY_LOGIN_ERROR_TIMES . $time, $msg, 86400);
        }else{
            $redis->setex($mastername . self::REDIS_KEY_LOGIN_SMS_CODE, $msg, 60);
        }

    }

    public static function getLoginMsg($mastername, $type = 'code')
    {
        $redis = new RedisCommon();
        if($type == 'times'){
            $time = date('Ymd');
            $msg = $redis->get($mastername . self::REDIS_KEY_LOGIN_ERROR_TIMES . $time);
        }else{
            $msg = $redis->get($mastername . self::REDIS_KEY_LOGIN_SMS_CODE);
        }
        return $msg;
    }
}
