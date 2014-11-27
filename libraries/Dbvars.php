<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dbvars Library
 *
 * Simplify storing variables in the database.
 *
 * @package		Dbvars
 * @version		1.0
 * @author 		Marc Nolte <contact@marcnolte.nl>
 * @copyright 	Copyright (c) 2014, Marc Nolte
 * @link		https://github.com/mnolte/codeigniter-dbvars
 */
class Dbvars
{
	// Param which holds all variables
	private $data;

	// Param which holds configuration settings
    private $config;

	// Param which holds Codeigniter instance
    private $CI;
    
	/**
	 * Constructor
	 */
	public function __construct( array $params = array() )
	{
		// Get instance
		$this->CI =& get_instance();
		$this->CI->load->add_package_path(APPPATH.'third_party/Dbvars/');

		// Load config
		$this->CI->load->config('dbvars', TRUE);
		$cfg = $this->CI->config->item('dbvars');
	
		// Setup configuration
		$this->config = (object) array_merge($cfg, $params);

		// Load vars
		$this->CI->db->select($this->config->table.".".$this->config->prefix."_key AS var_key, IFNULL(".$this->config->table_translations.".".$this->config->prefix."_value, ".$this->config->table.".".$this->config->prefix."_value) AS var_value", FALSE);
		$this->CI->db->from($this->config->table);
		$this->CI->db->join($this->config->table_translations, $this->config->table.'.'.$this->config->prefix.'_key = '.$this->config->table_translations.'.'.$this->config->prefix.'_key AND '.$this->config->table_translations.'.language = '.$this->CI->db->escape($this->config->lang), 'left');
		$q = $this->CI->db->get();
        foreach( $q->result() as $row )
		{
			$this->data[$row->var_key] = unserialize($row->var_value);
		}
		$q->free_result();

		// Check compatibility
		if( ! is_php('5.1.0') )
		{
			log_message('error', "PHP Version must be at least 5.1.0.");
		}

		// Debug
		log_message('debug', "Dbvars Class Initialized");
	}

	/**
	 * Get all values
	 *
	 * Get all values: $this->dbvars->get_all()
	 *
	 * @access	public
	 * @return	string
	 */
	function get_all()
	{
        return $this->data;
    }
    
	/**
	 * Get Value
	 *
	 * Get a value: $this->dbvars->key
	 *
	 * @access	public
	 * @param	string	$key	Key
	 * @return	string
	 */
	function __get( $key )
	{
        return $this->data[$key];
    }

	/**
	 * Set Value
	 *
	 * Set a value: $this->dbvars->key = 'value';
	 *
	 * @access	public
	 * @param	string	$key	Key
	 * @param	string	$value	Value
	 * @return	string
	 */
    function __set( $key, $value )
    {
        if( isset($this->data[$key]) )
        {
            $this->CI->db->where($this->config->prefix.'_key', $key);
            $this->CI->db->update($this->config->table, array($this->config->prefix.'_value' => serialize($value)));
        }
        else {
            $this->CI->db->insert($this->config->table, array($this->config->prefix.'_key' => $key, $this->config->prefix.'_value' => serialize($value)));
        }
        $this->data[$key] = $value;
    }
    
	/**
	 * Isset
	 *
	 * Check if a variable isset: $this->dbvars->__isset($key);
	 *
	 * @NOTE	PHP >= 5.1.0 needed
	 *
	 * @access	public
	 * @param	string	$key	Key
	 * @param	string	$value	Value
	 * @return	string
	 */
    function __isset( $key )
    {
        return isset($this->data[$key]);
    }
    
	/**
	 * Unset
	 *
	 * Unset a variable: $this->dbvars->__unset($key);
	 *
	 * @NOTE	PHP >= 5.1.0 needed
	 *
	 * @access	public
	 * @param	string	$key	Key
	 * @param	string	$value	Value
	 * @return	string
	 */
    function __unset( $key )
    {
        $this->CI->db->delete($this->config->table, array($this->config->prefix.'_key' => $key));    
        unset($this->data[$key]);
    }
}

/* End of file Dbvars.php */
/* Location: ./application/libraries/Dbvars.php */
