<?php
/*
 * copy from osps
*/
use emberlabs\Barcode\BarcodeBase;
require '../libs/help/barcodes/BarcodeBase.php';
require '../libs/help/barcodes/Code39.php';
require '../libs/help/barcodes/Code128.php';
require '../libs/help/barcodes/Ean13.php';
require '../libs/help/barcodes/Ean8.php';

require '../libs/myplugin/function.currency.php';

class Barcode_lib
{
	private $supported_barcodes = array('Code39' => 'Code 39', 'Code128' => 'Code 128', 'Ean8' => 'EAN 8', 'Ean13' => 'EAN 13');
	
	function __construct()
	{}
	
	public function get_list_barcodes()
	{
		return $this->supported_barcodes;
	}

	public function validate_barcode($barcode, $type)
	{
		$barcode_instance = $this->get_barcode_instance($type);
		return $barcode_instance->validate($barcode);
	}

	public static function barcode_instance($item, $barcode_config)
	{
		$barcode_instance = Barcode_lib::get_barcode_instance($barcode_config['barcode_type']);
		$is_valid = empty($item['item_number']) && $barcode_config['barcode_generate_if_empty'] || $barcode_instance->validate($item['item_number']);

		// if barcode validation does not succeed,
		if (!$is_valid)
		{
			$barcode_instance = Barcode_lib::get_barcode_instance();
		}
		$seed = Barcode_lib::barcode_seed($item, $barcode_instance, $barcode_config);
		$barcode_instance->setData($seed);

		return $barcode_instance;
	}

	private static function get_barcode_instance($barcode_type='Code128')
	{
		switch($barcode_type)
		{
			case 'Code39':
				return new emberlabs\Barcode\Code39();
				break;
				
			case 'Code128':
			default:
				return new emberlabs\Barcode\Code128();
				break;
				
			case 'Ean8':
				return new emberlabs\Barcode\Ean8();
				break;
				
			case 'Ean13':
				return new emberlabs\Barcode\Ean13();
				break;
		}
	}

	private static function barcode_seed($item, $barcode_instance, $barcode_config)
	{
		$seed = $barcode_config['barcode_content'] !== "id" && isset($item['item_number']) ? $item['item_number'] : $item['item_id'];

		if( $barcode_config['barcode_content'] !== "id" && !empty($item['item_number']))
		{
			$seed = $item['item_number'];
		}
		else
		{
			if ($barcode_config['barcode_generate_if_empty'])
			{
				// generate barcode with the correct instance
				$seed = $barcode_instance->generate($seed);
			}
			else
			{
				$seed = $item['item_id'];
			}
		}
		return $seed;
	}

	private function generate_barcode($item, $barcode_config)
	{
		try
		{
			$barcode_instance = Barcode_lib::barcode_instance($item, $barcode_config);
			$barcode_instance->setQuality($barcode_config['barcode_quality']);
			$barcode_instance->setDimensions($barcode_config['barcode_width'], $barcode_config['barcode_height']);

			$barcode_instance->draw();
			
			return $barcode_instance->base64();
		} 
		catch(Exception $e)
		{
			echo 'Caught exception: ', $e->getMessage(), "\n";		
		}
	}

	public function generate_receipt_barcode($barcode_content)
	{
		try
		{
			// Code128 is the default and used in this case for the receipts
			$barcode = $this->get_barcode_instance();

			// set the receipt number to generate the barcode for
			$barcode->setData($barcode_content);
			
			// image quality 100
			$barcode->setQuality(100);
			
			// width: 200, height: 30
			$barcode->setDimensions(200, 30);

			// draw the image
			$barcode->draw();
			
			return $barcode->base64();
		} 
		catch(Exception $e)
		{
			echo 'Caught exception: ', $e->getMessage(), "\n";		
		}
	}
	
	public function display_barcode($item, $barcode_config, $lang)
	{
		$display_table = "<table>";
		$display_table .= "<tr><td align='center'>" . $this->manage_display_layout($barcode_config['barcode_first_row'], $item, $barcode_config, $lang) . "</td></tr>";
		$barcode = $this->generate_barcode($item, $barcode_config);
		$display_table .= "<tr><td align='center'><img src='data:image/png;base64,$barcode' /></td></tr>";
		$display_table .= "<tr><td align='center'>" . $this->manage_display_layout($barcode_config['barcode_second_row'], $item, $barcode_config, $lang) . "</td></tr>";
		$display_table .= "<tr><td align='center'>" . $this->manage_display_layout($barcode_config['barcode_third_row'], $item, $barcode_config, $lang) . "</td></tr>";
		$display_table .= "</table>";
		
		return $display_table;
	}
	
	private function manage_display_layout($layout_type, $item, $barcode_config, $lang)
	{
		$result = '';
		
		if($layout_type == 'name')
		{
			$result = $lang['items_name'] . " " . $item['name'];
		}
		else if($layout_type == 'category' && isset($item['category']))
		{
			$result = $lang['items_category'] . " " . $item['category'];
		}
		else if($layout_type == 'cost_price' && isset($item['cost_price']))
		{
			$result = $lang['items_cost_price'] . " " . smarty_function_currency(array('number'=>$item['cost_price'],'thousands_separator'=>$barcode_config['thousands_separator'],'decimal_point'=>$barcode_config['decimal_point'],'decimals'=>$barcode_config['currency_decimals']));
		}
		else if($layout_type == 'unit_price' && isset($item['unit_price']))
		{
			$result = $lang['items_unit_price'] . " " . smarty_function_currency(array('number'=>$item['unit_price'],'thousands_separator'=>$barcode_config['thousands_separator'],'decimal_point'=>$barcode_config['decimal_point'],'decimals'=>$barcode_config['currency_decimals']));
		}
		else if($layout_type == 'company_name')
		{
			$result = $barcode_config['company'];
		}
		else if($layout_type == 'item_code')
		{
			$result = $barcode_config['barcode_content'] !== "id" && isset($item['item_number']) ? $item['item_number'] : $item['item_id'];
		}

		return $result;
	}
	
	public function listfonts($folder) 
	{
		$array = array();

		if (($handle = opendir($folder)) !== false)
		{
			while (($file = readdir($handle)) !== false)
			{
				if(substr($file, -4, 4) === '.ttf')
				{
					$array[$file] = $file;
				}
			}
		}

		closedir($handle);

		array_unshift($array, 'No Label');

		return $array;
	}

	public function get_font_name($font_file_name)
	{
		return substr($font_file_name, 0, -4);
	}
}
?>