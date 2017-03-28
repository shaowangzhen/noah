<?php

namespace App\Models;

use App\Library\Common;
use Illuminate\Database\Eloquent\Model;

/**
 * 通用Model类
 * Class XinModel
 * @package App\Models
 */
class NoahModel extends Model
{

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    /**
     * 通用分页查询列表方法
     * @param array $fields
     * @param array $where
     * @param array $orderBy
     * @param array $groupBy
     * @param int $pagesize
     */
    public function getList($fields = [], $where = [], $orderBy = [], $groupBy = [], $pagesize = 15)
    {
        if(!is_array($fields)) {
            $fields = explode(',', $fields);
            $fields = array_map('trim', $fields);
        }

        $query = $this->select($fields);
        $query = $this->createWhere($query, $where, $orderBy, $groupBy);

        $result = $query->paginate($pagesize);

        return $result;
    }

    /**
     * 通用查询列表方法,查询全部
     * @param array $fields
     * @param array $where
     * @param array $orderBy
     * @param array $groupBy
     * @param int $limit
     * @param boolean $isArray 类型,默认是数组,对象可以传入obj
     * @return mixed 返回数组
     */
    public function getAll($fields = [], $where = [], $orderBy = [], $groupBy = [], $limit = 0, $isArray = true)
    {
        if(!is_array($fields)) {
            $fields = explode(',', $fields);
            $fields = array_map('trim', $fields);
        }

        $query = $this->select($fields);
        $query = $this->createWhere($query, $where, $orderBy, $groupBy);

        if($limit){
            $result = $query->take($limit)->get();
        }else{
            $result = $query->get();
        }

        if ($isArray) {
            $result = $result->toArray();
        }

        return $result;
    }

    /**
     * 通用获取单条记录方法
     * @param array $fields
     * @param array $where
     * @param array $orderBy
     * @return array
     */
    public function getOne($fields = [], $where = [], $orderBy = [])
    {
        if(!is_array($fields)) {
            $fields = explode(',', $fields);
            $fields = array_map('trim', $fields);
        }

        $query = $this->select($fields);
        $query = $this->createWhere($query, $where, $orderBy);

        $result = $query->first();

        $result = $result ? $result->toArray() : [];

        return $result;
    }


    /**
     * 根据条件统计总数
     * @param array $where
     */
    public function countBy($where = [])
    {
        $query = $this->createWhere($this, $where);

        $result = $query->count();

        return $result;
    }

    /**
     * 根据条件更新数据
     * @param $data
     * @param array $where
     */
    public function updateBy($data, $where)
    {
        $query = $this->createWhere($this, $where);
        return $query->update($data);
    }

    /**
     * 根据条件删除数据
     * @param $data
     * @param array $where
     */
    public function deleteBy($where)
    {
        $query = $this->createWhere($this, $where);
        return $query->delete();
    }

    /**
     * 设置where条件
     * @param $query
     * @param array $where
     * @param array $orderBy
     * @param array $groupBy
     * @return mixed
     */
    public function createWhere($query, $where =[], $orderBy = [], $groupBy = [])
    {
        if(isset($where['in'])) {
            foreach($where['in'] as $k => $v) {
                $query = $query->whereIn($k, $v);
            }
            unset($where['in']);
        }
        if(isset($where['not_in'])) {
            foreach($where['not_in'] as $k => $v) {
                $query = $query->whereNotIn($k, $v);
            }
            unset($where['not_in']);
        }
        if(isset($where['raw'])) {
            foreach($where['raw'] as $k => $v) {
                $query = $query->whereRaw($v);
            }
            unset($where['raw']);
        }

        if($where){
            foreach ($where as $k => $v) {
                $operator = '=';
                if (substr($k, -2) == ' <') {
                    $k = trim(str_replace(' <', '', $k));
                    $operator = '<';
                } elseif (substr($k, -3) == ' <=') {
                    $k = trim(str_replace(' <=', '', $k));
                    $operator = '<=';
                } elseif (substr($k, -2) == ' >') {
                    $k = trim(str_replace(' >', '', $k));
                    $operator = '>';
                } elseif (substr($k, -3) == ' >=') {
                    $k = trim(str_replace(' >=', '', $k));
                    $operator = '>=';
                } elseif (substr($k, -3) == ' !=') {
                    $k = trim(str_replace(' !=', '', $k));
                    $operator = '!=';
                } elseif (substr($k, -3) == ' <>') {
                    $k = trim(str_replace(' <>', '', $k));
                    $operator = '<>';
                } elseif (substr($k, -5) == ' like') {
                    $k = trim(str_replace(' like', '', $k));
                    $operator = 'like';
                    $v = '%' . $v . '%';
                }
                $query = $query->where($k, $operator, $v);
            }
        }

        if($orderBy) {
            foreach($orderBy as $k => $v) {
                $query = $query->orderBy($k, $v);
            }
        }

        if($groupBy) {
            $query = $query->groupBy($groupBy);
        }

        return $query;
    }

    /**
     * 写库通用获取单条记录方法
     * @param array $fields
     * @param array $where
     * @param array $orderBy
     * @return array
     */
    public function getOneOnWrite($fields = [ ], $where = [ ], $orderBy = [ ])
    {
        if(empty($fields)){
            $fields = '*';
        }else {
            if (!is_array($fields)) {
                $fields = explode(',', $fields);
                $fields = array_map('trim', $fields);
            }
        }

        $query = $this::onWriteConnection()->select($fields);
        $query = $this->createWhere($query, $where, $orderBy);

        $result = $query->first();

        $result = $result ? $result->toArray() : [ ];

        return $result;
    }

    /**
     * 写库通用查询列表方法,查询全部
     * @param array $fields
     * @param array $where
     * @param array $orderBy
     * @param array $groupBy
     * @param int $limit
     * @param boolean $isArray 类型,默认是数组,对象可以传入obj
     * @return mixed 返回数组
     */
    public function getAllOnWrite($fields = [], $where = [], $orderBy = [], $groupBy = [], $limit = 0, $isArray = true)
    {
        if(empty($fields)){
            $fields = '*';
        }else {
            if (!is_array($fields)) {
                $fields = explode(',', $fields);
                $fields = array_map('trim', $fields);
            }
        }

        $query = $this::onWriteConnection()->select($fields);
        $query = $this->createWhere($query, $where, $orderBy, $groupBy);

        if($limit){
            $result = $query->take($limit)->get();
        }else{
            $result = $query->get();
        }

        if ($isArray) {
            $result = $result->toArray();
        }

        return $result;
    }

    /**
     * 写库根据条件统计总数
     * @param array $where
     * @return int
     */
    public function countByOnWrite($where = [ ])
    {
        $query = $this->createWhere($this::onWriteConnection(), $where);

        $result = $query->count();

        return $result;
    }

    /*
     * 处理图片地址
     * @param $pic绝对或相对
     * @return 绝对地址
     * */
    public function dealPic($pic){
        $sub_str = '';
        if (preg_match('/^http:[\d\D]*?.com/', $pic)) {
            $sub_str = preg_replace('/^http:[\d\D]*?.com/', '', $pic);
        } else {
            $sub_str = $pic;
        }
        return Common::getImgHost().$sub_str;
    }

}
