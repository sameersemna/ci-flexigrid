<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Countries_Feed extends CI_Controller
{
    
    function __construct()
    {
        parent::__construct();
        $this->load->model('countries_model');
        $this->load->library('flexigrid');
    }
    
    function index()
    {
        //$filters = json_decode($this->input->post('filters'), true);
        // print_r($_POST['filters']);
        // $filters = json_decode($_POST['filters'], true);
        // if(isset($filters['rules'])) {
        
        // foreach($filters['rules'] as $v) {
        // switch($v['op']){
        // case 'eq' : $op = '='; break;
        // case 'bt' : $op = '>'; break;
        // case 'st' : $op = '<'; break;
        // case 'nq' : $op = '<>'; break;
        // }
        // echo $v['field'] . " " . $op . " '" . $v['data']. "'<br/>";
        // }
        // }
        //List of all fields that can be sortable. This is Optional.
        //This prevents that a user sorts by a column that we dont want him to access, or that doesnt exist, preventing errors.
        $valid_fields = array(
            'id',
            'iso',
            'name',
            'printable_name',
            'iso3',
            'numcode',
            'created_date'
        );
        
        $this->flexigrid->validate_post('id', 'asc', $valid_fields);
        
        $records = $this->countries_model->get_countries();
        
        $this->output->set_header($this->config->item('json_header'));
        
        /*
         * Json build WITH json_encode. If you do not have this function please read
         * http://flexigrid.eyeviewdesign.com/index.php/flexigrid/example#s3 to know how to use the alternative
         */
        $record_items = array();
        foreach ($records['records']->result() as $row) {
            $record_items[] = array(
                $row->id,
                $row->id,
                $row->iso,
                $row->name,
                '<span style=\'color:#ff4400\'>' . addslashes($row->printable_name) . '</span>',
                $row->iso3,
                $row->numcode,
                $row->created_date,
                '<a href=\'#\'><img border=\'0\' src=\'' . $this->config->item('base_url') . 'assets/flexigrid/images/close.png\'></a> '
            );
        }
        //Print please
        $this->output->set_output($this->flexigrid->json_build($records['record_count'], $record_items, $records['footmsg']));
    }
    
    
    //Delete Country
    function deletec()
    {
        //SHS split deprecated
        //$countries_ids_post_array = split(",",$this->input->post('items'));
        $countries_ids_post_array = explode(",", trim($this->input->post('items', true), ","));
        foreach ($countries_ids_post_array as $index => $country_id)
            if (is_numeric($country_id) && $country_id > 1)
                $this->countries_model->delete_country($country_id);
        
        
        $error = "Selected countries (id's: " . $this->input->post('items') . ") deleted with success";
        
        $this->output->set_header($this->config->item('ajax_header'));
        $this->output->set_output($error);
    }
    
    //Export data
    function export()
    {
		///Set filters to POST from GET
		$filters = $this->input->get('filters', true);
		$_POST['filters'] = $filters;
		
        $valid_fields = array(
            'id',
            'iso',
            'name',
            'printable_name',
            'iso3',
            'numcode',
            'created_date'
        );
        
        $this->flexigrid->validate_post('id', 'asc', $valid_fields);
        
        $records = $this->countries_model->get_all_countries();
        
        $record_items = array();
        foreach ($records['records']->result() as $row) {
            $record_items[] = array(
                $row->id,
                $row->id,
                $row->iso,
                $row->name,
                $row->iso3,
                $row->numcode,
                $row->created_date
            );
        }
        
        $format = $this->input->get('format', true);
        if ($format == 'CSV') {
            $file_name = 'countries_' . date('Ymd_His') . '.csv';
            $this->convert_to_csv($record_items, $file_name);
        } else if ($format == 'Excel') {
            $file_name = 'countries_' . date('Ymd_His') . '.xls';
            $this->convert_to_excel($record_items, $file_name);
        }
    }

    
    function convert_to_csv($input_array, $output_file_name, $delimiter = ',')
    {
        /** open raw memory as file, no need for temp files */
        $temp_memory = fopen('php://memory', 'w');
        /** loop through array  */
        foreach ($input_array as $line) {
            /** default php csv handler **/
            fputcsv($temp_memory, $line, $delimiter);
        }
        /** rewrind the "file" with the csv lines **/
        fseek($temp_memory, 0);
        /** modify header to be downloadable csv file **/
        header('Content-Type: application/csv');
        header('Content-Disposition: attachement; filename="' . $output_file_name . '";');
        /** Send file to browser for download */
        fpassthru($temp_memory);
    }
    
    
    protected function convert_to_excel($input_array, $output_file_name)
	{
		/**
		 * No need to use an external library here. The only bad thing without using external library is that Microsoft Excel is complaining
		 * that the file is in a different format than specified by the file extension. If you press "Yes" everything will be just fine.
		 * */
		$string_to_export = "";
		foreach ($input_array as $line) {
			foreach($line as $column){
				$string_to_export .= $this->_trim_export_string($column)."\t";
			}
			$string_to_export .= "\n";
		}
		// Convert to UTF-16LE and Prepend BOM
		$string_to_export = "\xFF\xFE" .mb_convert_encoding($string_to_export, 'UTF-16LE', 'UTF-8');
		header('Content-type: application/vnd.ms-excel;charset=UTF-16LE');
		header('Content-Disposition: attachment; filename='.$output_file_name);
		header("Cache-Control: no-cache");
		echo $string_to_export;
		die();
	}
	
	protected function _trim_export_string($value)
	{
		$value = str_replace(array("&nbsp;","&amp;","&gt;","&lt;"),array(" ","&",">","<"),$value);
		return  strip_tags(str_replace(array("\t","\n","\r"),"",$value));
	}
    
    
}


?>
