<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// ------------------------------------------------------------------------

/**
 * 扩展CI的CI_Model类
 *
 * @package		CodeIgniter
 * @subpackage	models
 * @category	MY_Model
 * @author		South
 */
class XT_Model extends CI_Model {
	
	protected $mTable;
	protected $mPkId = 'id';
	protected $mCache;
	protected $mCache_list;
	
	public function __construct(){
		$this->db = _get_db('default');
		
	}

	public function prefix(){
		return $this->db->dbprefix;
	}
	
	public function table($table='')
	{
		if (! $table)
		{
			$table = $this->mTable;
		}
		return $this->db->protect_identifiers($table, TRUE);
	}
	
	public function set_table($table)
	{
		$this->mTable = $table;
		return $this;
	}
	
	public function execute($sql)
	{
		return $this->db->query($sql);
	}
    
	/**
	 * 根据主键id获取数据使用时确保查询的表有主键
	 * @param unknown $id
	 * @param string $fields
	 */
	public function get_by_id($id, $fields='*')
	{
		$result = $this->db->select($fields)
					->from($this->mTable)
					->where($this->mPkId, $id)
					->get()
					->row_array();
		return $result;
	}

	/**
	*根据条件查询,支持多表
	*/
	public function get_by_where($where, $fields='*', $order_by='', $tb=''){
		if(empty($tb))
			$tb = $this->mTable;

		$this->db->select($fields)->from($tb);

		if(!is_array($where))
			$this->db->where($where);
		else
		{
			foreach($where as $key=>$val)
			{	
				if (is_array($val))
				{
					$this->db->where_in($key, $val);
				}
				else
				{
					$this->db->where($key, $val);
				}
			}
		}

		if ($order_by)
		{
			$this->db->order_by($order_by);
		}

		$result = $this->db->limit(1)->get()->row_array();
		return $result;
	}
    /**
     * 向表中添加数据
     * @param unknown $data
     */
	public function insert($data)
	{
		if(!empty($this->mCache))
			dkcache($this->mCache);
		if(!empty($this->mCache_list))
			dkcache($this->mCache_list);

		$sql = $this->db->insert_string($this->mTable, $data);
		$sql = 'INSERT IGNORE '.ltrim($sql,'INSERT');

		$update = array();
		foreach($data as $key=>$val)
		{
			$update[] = $key.'='.$this->db->escape($val);
		}
		$sql .= ' ON duplicate KEY UPDATE '.join(',', $update);

		return $this->db->query($sql);
	}
	/**
	 * 向表中添加一条数据，并且返回插入记录的主键id
	 * @param unknown $data
	 */
	public function insert_string($data)
	{
		if(!empty($this->mCache))
			dkcache($this->mCache);
		if(!empty($this->mCache_list))
			dkcache($this->mCache_list);

		$sql = $this->db->insert_string($this->mTable, $data);
		$this->db->query($sql);
		$id =  $this->db->insert_id();
		return $id;
	}

	public function insert_ignore($data)
	{
		if(!empty($this->mCache))
			dkcache($this->mCache);
		if(!empty($this->mCache_list))
			dkcache($this->mCache_list);

		$sql = $this->db->insert_string($this->mTable, $data);
		$sql = 'INSERT IGNORE '.ltrim($sql,'INSERT');
		$this->db->query($sql);
		
		return $this->db->insert_id();
	}

	public function insert_id()
	{
	    return $this->db->insert_id();
	}
	
	public function affected_rows(){
	    return $this->db->affected_rows();
	}

	public function get_count($where)
	{
		$this->db->select('COUNT(1) AS count', FALSE)
					->from($this->mTable);
		if(!is_array($where))
			$this->db->where($where);
		else
		{
			foreach($where as $key=>$val)
			{	
				if (is_array($val))
				{
					$this->db->where_in($key, $val);
				}
				else
				{
					$this->db->where($key, $val);
				}
			}
		}
		
		$result = $this->db->get()->row_array();
		return (int)$result['count'];
	}
	
	public function count($arrWhere)
	{
		$this->db->select('COUNT(1) AS count', FALSE)
					->from($this->mTable);
		foreach($arrWhere as $key=>$val)
		{	
			if (is_array($val))
			{
				$this->db->where_in($key, $val);
			}
			else
			{
				$this->db->where($key, $val);
			}
		}
		$result = $this->db->get()->row_array();
		return $result['count'];
	}


    public function sum($arrWhere,$field)
    {
        $this->db->select('SUM('.$field.') AS num', FALSE)
            ->from($this->mTable);
        foreach($arrWhere as $key=>$val)
        {
            if (is_array($val))
            {
                $this->db->where_in($key, $val);
            }
            else
            {
                $this->db->where($key, $val);
            }
        }
        $result = $this->db->get()->row_array();

        return $result['num']?$result['num']:0;
    }

	/**
	 * 根据id删除数据，如果表中有status字段，则用update_by_id
	 * @param unknown $id
	 */
	public function delete_by_id($id)
	{
		if(!empty($this->mCache))
			dkcache($this->mCache);
		if(!empty($this->mCache_list))
			dkcache($this->mCache_list);

		if (!is_array($id))
		{
			$id = array($id);
		}
		return $this->db->where_in($this->mPkId, $id)->limit(count($id))->delete($this->mTable);
	}
	
	/**
	 * 根据where条件删除数据，如果表中有status字段，则用update_by_where
	 * @param unknown $where
	 */
	public function delete_by_where($where)
	{
		if(!empty($this->mCache))
			dkcache($this->mCache);
		if(!empty($this->mCache_list))
			dkcache($this->mCache_list);
		
		return $this->db->where($where)->delete($this->mTable);
	}
	
	/**
	 * 根据id更新数据
	 * @param unknown $id
	 * @param unknown $data
	 */
	public function update_by_id($id, $data)
	{
		if(!empty($this->mCache))
			dkcache($this->mCache);
		if(!empty($this->mCache_list))
			dkcache($this->mCache_list);

		$where = array($this->mPkId=> $id);
		$sql = $this->db->update_string($this->mTable, $data, $where);
		return $this->db->query($sql);
	}
	
	
	/**
	 * 根据where条件更新表
	 * @param unknown $where
	 * @param unknown $data
	 */
	public function update_by_where($where, $data)
	{
		if(!empty($this->mCache))
			dkcache($this->mCache);
		if(!empty($this->mCache_list))
			dkcache($this->mCache_list);
		
		if (!$where)return false;
		if(!is_array($where))
			$this->db->where($where);
		else
		{
			foreach($where as $key=>$val)
			{	
				if (is_array($val))
				{
					$this->db->where_in($key, $val);
				}
				else
				{
					$this->db->where($key, $val);
				}
			}
		}
		return $this->db->update($this->mTable, $data);
	}
	
	/**
	 * a=a+1 操作
	 * @return unknown_type
	 */
	public function operate_by_id($id, $map)
	{
		$where = array($this->mPkId=> $id);
		$this->db->where($where);
		foreach($map as $key=>$val)
		{
			$this->db->set($key, $val, FALSE);
		}
		$this->db->update($this->mTable);
	}

	public function get_list_cache(){
		$list = rkcache($this->mCache_list);
		if(!$list){
			$list = $this->get_list();
			wkcache($this->mCache_list, $list);
		}

		return $list;
	}

	/**
	 * 根据where条件获取一组数据
	 * @param unknown $where  筛选条件
	 * @param string $fields  要取出的字段
	 * @param string $order_by 排序方式
	 * @param number $limit 取出的记录数
	 */
	public function get_list($where=array(), $fields='*', $order_by='', $limit = 0, $tb='')
	{
		return $this->fetch_rows($where, $fields, $order_by, $limit, $tb);
	}
	
	public function fetch_row($where, $fields='*', $order_by='')
	{
		$this->db->select($fields)
						->from($this->mTable)
						->where($where);
		if ($order_by)
		{
			$this->db->order_by($order_by);
		}
		return $this->db->limit(1)->get()->row_array();
	}
	
	public function fetch_field($where, $field='')
	{
		$arr =	$this->db->select($field)
						->from($this->mTable)
						->where($where)
						->get()
						->row_array();
		return $arr[$field];
	} 
	
	public function fetch_rows($where=array(), $fields='*', $order_by='', $limit=0, $tb='')
	{
		if(empty($tb))
			$tb = $this->mTable;
		$this->db->select($fields)->from($tb);
		if(!is_array($where))
			$this->db->where($where);
		else
		{
			foreach($where as $key=>$val)
			{	
				if (is_array($val))
				{
					$this->db->where_in($key, $val);
				}
				else
				{
					$this->db->where($key, $val, false);
				}
					
			}
		}

		if ($order_by)
		{
			$this->db->order_by($order_by);
		}
		if ($limit)
		{
			if (is_array($limit))
			{
				$this->db->limit($limit[0], $limit[1]);
			}
			else
			{
				$this->db->limit($limit);
			}
		}
		return $this->db->get()->result_array();
	}
	
	/**
	 * 根据where条件获取数据， 一般后面会有分页的设置
	 * @param number $page 获取第几页数据
	 * @param number $pagesize 每页记录数
	 * @param unknown $where 筛选数据的条件
	 * @param string $fields 要取出的字段
	 * @param string $order_by 排序方式
	 * @param string $tb 
	 */
	public function fetch_page($page=1, $pagesize=10, $where=array(), $fields='*', $order_by='', $tb = '')
	{
		if(!$tb) $tb = $this->mTable;
		$order_by = $order_by ? $order_by : $this->mPkId.' DESC';
		$fields_count = 'COUNT(1) AS count';
		$this->db->select($fields_count, FALSE)
					->from($tb);
	    foreach($where as $key=>$val)
		{	
		    if ($key{0} == '@' && is_array($val))
		    {// array('@where'=>array('a'=>1,'b'=>1))
		        $key = substr($key, 1);
		        foreach($val as $k=>$v)
		        {
		            $this->db->$key($k, $v);
		        }
		        continue;
		    }
			if (is_array($val))
			{
				$this->db->where_in($key, $val);
			}
			else
			{
				$bAuto = true;
				if($tb)
					$bAuto = false;
				$this->db->where($key, $val, $bAuto);
			}
		}
		$result = $this->db->get()->row_array();
		
		$num = $result['count'];
		$result['rows'] = array();
		if ($num > 0)
		{
		    $sql = $this->db->last_query();
			$sql =  str_replace($fields_count, $fields, $sql);
			$sql .= ' ORDER BY '.$order_by;
			$sql .= ' LIMIT '.(($page-1)*$pagesize).','.$pagesize;
			$result['rows'] = $this->db->query($sql)->result_array();
		}
		return $result;
	}
	
}
// END XT_Model Class
