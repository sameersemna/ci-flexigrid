<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Eye View Design CMS module Ajax Model
 *
 * PHP version 5
 *
 * @category  CodeIgniter
 * @package   EVD CMS
 * @author    Frederico Carvalho
 * @copyright 2008 Mentes 100Limites
 * @version   0.1
*/

class Countries_model extends CI_Model 
{
	public $CI;
	public $table_name = 'countries';
	
	public function __construct()
	{
	    parent::__construct();
	    $this->CI =& get_instance();
	}
	
	public function get_select_countries()
	{
		//Select table name
		$table_name = $this->table_name;
		
		//Build contents query
		$separator = (string) ',';
		//$this->db->select('concat(iso3, concat('. addcslashes($separator) .', iso3))')->from($table_name);
		
		$query = $this->db->query("select concat(iso3, concat(':', iso3)) as name from $table_name where iso3 is not null");
		//Get contents
		return $query->result_array();
	}
	
	public function get_countries() 
	{
		//Select table name
		$table_name = $this->table_name;
		
		//Build contents query
		$this->db->select('id,iso,name,printable_name,iso3,numcode,created_date')->from($table_name);
		$this->CI->flexigrid->build_query();
		
		//Get contents
		$return['records'] = $this->db->get();
		//echo $this->db->last_query();
		//Build count query
		$this->db->select('count(id) as record_count')->from($table_name);
		$this->CI->flexigrid->build_query(FALSE);
		$record_count = $this->db->get();
		$row = $record_count->row();
		
		//Get Record Count
		$return['record_count'] = $row->record_count;
		
		
		//Build sum query for footer message
		$this->db->select('SUM(numcode) as sum_code')->from($table_name);
		$this->CI->flexigrid->build_query(FALSE);
		$record_count = $this->db->get();
		$row = $record_count->row();
		
		//Get Footer Message
		$return['footmsg'] = $row->sum_code;
	
		//Return all
		return $return;
	}
	
	///Get all countries without limit
	public function get_all_countries() 
	{
		//Select table name
		$table_name = $this->table_name;
		
		//Build contents query
		$this->db->select('id,iso,name,printable_name,iso3,numcode,created_date')->from($table_name);
		$this->CI->flexigrid->build_query(FALSE);
		
		//Get contents
		$return['records'] = $this->db->get();
		//echo $this->db->last_query();
		//Build count query
		$this->db->select('count(id) as record_count')->from($table_name);
		$this->CI->flexigrid->build_query(FALSE);
		$record_count = $this->db->get();
		$row = $record_count->row();
		
		//Get Record Count
		$return['record_count'] = $row->record_count;
	
		//Return all
		return $return;
	}
	
	/**
	* Remove country
	* @param int country id
	* @return boolean
	*/
	public function delete_country($country_id) 
	{
		$delete_country = $this->db->query('DELETE FROM '.$this->table_name.' WHERE id='.$country_id);
		
		return TRUE;
	}
}
?>
