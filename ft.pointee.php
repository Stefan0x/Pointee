<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *  Pointee Fieldtype for ExpressionEngine 2
 *
 * @package		ExpressionEngine
 * @subpackage	Fieldtypes
 * @category	Fieldtypes
 * @author    	Iain Urquhart <shout@iain.co.nz>
 * @copyright 	Copyright (c) 2010 Iain Urquhart
 * @license   	http://creativecommons.org/licenses/MIT/  MIT License
*/

	class Pointee_ft extends EE_Fieldtype
	{
		var $info = array(
			'name'		=> 'Pointee',
			'version'	=> '0.31'
		);

		public function Pointee_ft()
		{
			parent::EE_Fieldtype();
			$this->EE->lang->loadfile('pointee');
		}	


		public function display_field($data)
		{
			
		  	// prep the variables
		  	$r = '';
		  	$asset_path = 'expressionengine/third_party/pointee/assets/';
		  	$image 	= '';
		  	$ee_image = '';
			$xc 	= '0';
			$yc 	= '0';
			$marker = '<div class="map_marker pointee-marker '.$this->settings['marker'].'"></div>';
			$image_selector = '';
			$js_hide = '';
			$directory_id = '';
			$filename = '';
			$type = '';
		  	
		  	// include css & js
		  	$r .= '<link rel="stylesheet" type="text/css" href="'.$asset_path.'pointee.css" />';
			$r .= '<script type="text/javascript" src="'.$asset_path.'pointee.js"></script>';

			// has the user predefined an upload image via field settings
			if(isset($this->settings['image_upload']))
			{
				// declare the type
				$type = 'user-defined';

				// field contains pointee data
				if($data != '')
				{
					// why the fark does this come back as an array sometimes?
					if(is_array($data))
					{
						$ee_image = $data['image'];
						$c 		= explode('|', $data['coordinates']);
						$xc 	= $c[0];
						$yc 	= $c[1];
					}
					else
					{
						$pointee_data = explode('|', $data);
						$ee_image 	= $pointee_data[0];
						$xc 	= $pointee_data[1];
						$yc 	= $pointee_data[2];
					}
					// parse the {filedir_x} and convert to path
					$upload_directories = $this->EE->tools_model->get_upload_preferences($this->EE->session->userdata('group_id'));
					foreach($upload_directories->result() as $row)
					{
						$upload_dirs[$row->id] = $row->name;
					}
					
					// find the matching dir
					if (preg_match('/{filedir_([0-9]+)}/', $ee_image, $matches))
					{
						$directory_id = $matches[1];
						$filename = str_replace($matches[0], '', $ee_image);
					}
	
					// Get dir info
					$directory_info = $this->EE->tools_model->get_upload_preferences($this->EE->session->userdata('group_id'), $directory_id);
					$directory_server_path = $directory_info->row('server_path');
					$directory_url = $directory_info->row('url');
					
					// define the image
					if($filename)
					{
						$image = '<img class="clickable-image" src="'.$directory_url.$filename.'" />';
					}
				}
				else
				{
				// no pointee data
				$js_hide = 'js_hide';
				
				}
				
				// file browser tool
				
				$image_selector .= '
					<table class="mainTable pointee_image_select" border="0" cellspacing="0" cellpadding="0" style="margin-top: 5px;">
							<tr>
								<th colspan="2">Image Select</th>
							</tr>
							<tr>
								<td style="width: 85%">'.
								form_input(array(
										'name'		=> $this->field_name.'[image]',
										'id'		=> $this->field_name,
										'value'		=> $ee_image,
										'class'		=> 'pointee_select_input fullfield',
										'dir'		=> $this->settings['field_text_direction'],
										'maxlength'	=> $this->settings['field_maxl']
									))
								.'</td>
								<td><button class="submit submit_alt file_manipulate js_show">Select&nbsp;a&nbsp;file</button></td>
							</tr>
					</table>
					<span class="pointee_notification notice"></span>';
			
			}
			
			// or do we have a defined image for the field
			elseif($this->settings['image_location'] != '')
			{
				// define the image
				$image = '<img class="clickable-image" src="'.$this->settings['image_location'].'">';
				
				// declare the type
				$type = 'predefined';
				
				// field contains pointee data
				// why the fark does this come back as an array sometimes?
				if(is_array($data))
				{
					$c 		= $data['coordinates'];
					$xc 	= $c[0];
					$yc 	= $c[1];
				}
				elseif($data != '')
				{
					$pointee_data = explode('|', $data);
					$xc 	= $pointee_data[0];
					$yc 	= $pointee_data[1];
				}
			}
			
			// we have neither :(
			else
			{
				return '<p class="notice">'.$this->EE->lang->line('please_check_field_config').'</p>';
			}

			// output the field
			$r .= '<div class="pointee-wrapper '.$js_hide.'">';
			$r .= '<div class="map-wrapper">';
			$r .= $image.$marker;
			$r .= '<p class="coordinates-holder"><strong>x</strong> <span class="x-coordinate">'.$xc.'</span>';
			$r .= '<strong>y</strong> <span class="y-coordinate">'.$yc.'</span></p>';
			$r .= '<input type="hidden" name="'.$this->field_name.'[coordinates]" value="'.$xc.'|'.$yc.'" class="coordinates" />';
			$r .= '<input type="hidden" name="'.$this->field_name.'[type]" value="'.$type.'" />';
			$r .= '</div>';
			$r .= '</div>';
			$r .= $image_selector;

			return $r;

		}
		
		function pre_process($data)
		{

			$pipe_count = substr_count($data, '|');
			
			// if its an image per entry as per settings, parse the {filedir_x} and swap out the path, 
			if($pipe_count == 2)
			{
				
				$pointee_data = explode("|", $data);
				$image 	= $pointee_data[0];
				
				if (preg_match('/^{filedir_(\d+)}/', $data, $matches))
				{
					// only replace it once
					$path = substr($data, 0, 10 + strlen($data[1]));

					$file_dirs = $this->EE->functions->fetch_file_paths();

					$file_info['path'] = str_replace($matches[0], $file_dirs[$matches[1]], $path);
					$data = str_replace($matches[0], '', $data);
				}
		
				$data = $file_info['path'].$data;

				return $data;

			}
			
			// otherwise just output the data
			return $data;
				
				
		}
		

		public function replace_tag($data, $params = FALSE, $tagdata = FALSE)
		{
			
			$image = '';
			$xc = '';
			$yc = '';
			
			// eh? can't access field settings here, gaaah.
			// count the pipes. hrmph.
			$pipe_count = substr_count($data, '|');
			
			if($pipe_count)
			{
			
				$pointee_data = explode("|", $data);
			
				// image and coordinates
				if($pipe_count == 2)
				{
					$image 	= $pointee_data[0];
					$xc 	= $pointee_data[1];
					$yc 	= $pointee_data[2];
				}
				
				// just coordinates
				elseif($pipe_count == 1)
				{
					$xc 	= $pointee_data[0];
					$yc 	= $pointee_data[1];
				}
				
				// somethings wrong
				else
				{
					return $data;
				}
			}	
			
			if ($data != '' && isset($params['show'])) 
			{
				switch($params['show'])
				{
					default:
						return $data;
						break;
		
					case "x":
						if(isset($params['offset']))
						{
							$xc = $xc + $params['offset'];
						}
						return $xc;
						break;
		
					case "y":
						if(isset($params['offset']))
						{
							$yc = $yc + $params['offset'];
						}
						return $yc;
						break;
					case "image":
						return $image;
						break;
				}
			}
			else
			{
			return $data;
			}
			
				
		}
		
		public function save($data)
		{
			// @todo Note to self: Get clevererer than this.
			
			// if we have an image per entry, merge with coordinates and store the lot
			if(isset($data['image']))
			{
				// {filedir_x}filename.jpg|x|y
				$newdata = $data['image'].'|'.$data['coordinates'];
			}
			
			// no image, its defined by the field settings and we're just storing coordinates
			else
			{
				$newdata = $data['coordinates'];
			}
			
			// stop a blank entry going into the db
			if($newdata == '|0|0' OR $newdata == '0|0')
			{
				return NULL;
			}

			return $newdata;
			
		}
		
		function post_save($data)
		{

		}
		
		public function validate($data)
		{
			return TRUE;
		}
		
		public function save_settings($data)
		{
			return array(
				'image_location'		=> $this->EE->input->post('image_location'),
				'marker'				=> $this->EE->input->post('marker'),
				'image_upload'			=> ($this->EE->input->post('image_upload') == 1 ? TRUE : NULL)
			);
		}

		public function display_settings($data)
		{
			
			if(!isset($data['image_location']))
			{
				$data['image_location'] = '/images/uploads/example.png';
			}
			
			// set an image as default for the field
 			$this->EE->table->add_row(
 				$this->EE->lang->line('image_location'),
				form_input(array(
					'name'	=> 'image_location',
					'id'	=> $this->field_id,
					'class'	=> 'fullfield',
					'value'	=> $data['image_location']
				))
 			);
 			
 			$marker_options = array(
								'pointee-black' => $this->EE->lang->line('black'), 
								'pointee-blue' 	=> $this->EE->lang->line('blue'),
								'pointee-pink' 	=> $this->EE->lang->line('pink'),
								'pointee-yellow' => $this->EE->lang->line('yellow')
								);
 			
 			// what type of marker
 			if(!isset($data['marker']))
			{
				$data['marker'] = '';
			}
						 			
 			$this->EE->table->add_row(
 				$this->EE->lang->line('marker_type'),
				form_dropdown('marker', $marker_options, $data['marker'])
 			);
 			
 			// what type of marker
 			if(!isset($data['image_upload']))
			{
				$data['image_upload'] = '';
			}
			
			// is it an image upload per publish?	 			
 			$this->EE->table->add_row(
 				$this->EE->lang->line('allow_user_upload'),
				form_checkbox('image_upload', '1', $data['image_upload'])
 			);

 		}			


		function install()
		{
			//nothing
		}

		function unsinstall()
		{
			//nothing
		}
	}
	//END CLASS
	
/* End of file ft.pointee.php */