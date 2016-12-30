<?php
require_once '../libs/secure.php';
require_once '../libs/help/common.php';
require_once '../libs/config.php';
require_once '../libs/supplier.php';
require_once '../libs/item_tax.php';
require_once '../libs/item_kits.php';
require_once '../libs/help/upload.php';
require_once '../libs/help/Barcode_lib.php';

class items extends secure {
private $flt = array('name' => FILTER_SANITIZE_SPECIAL_CHARS,
					'category' => FILTER_SANITIZE_SPECIAL_CHARS,
					'supplier' => FILTER_SANITIZE_SPECIAL_CHARS,
					'item_number' => FILTER_SANITIZE_SPECIAL_CHARS,
					'description' => FILTER_SANITIZE_SPECIAL_CHARS,
					'pic' => FILTER_SANITIZE_SPECIAL_CHARS,
					'cost_price' => FILTER_VALIDATE_FLOAT,
					'unit_price' => FILTER_VALIDATE_FLOAT,
					'reorder_level' => FILTER_VALIDATE_INT);
private $flt_more = array('offset' => FILTER_VALIDATE_INT, 
						'limit' => FILTER_VALIDATE_INT, 
						'type' => FILTER_SANITIZE_SPECIAL_CHARS, 
						'label' => FILTER_SANITIZE_SPECIAL_CHARS, 
						'value' => FILTER_SANITIZE_SPECIAL_CHARS);
public $flt_number = array('id'=>FILTER_VALIDATE_INT,
						'item_number'=>FILTER_SANITIZE_SPECIAL_CHARS);
private $flt_thumb = array('src'=>FILTER_SANITIZE_SPECIAL_CHARS, 'w'=>FILTER_VALIDATE_INT, 'h'=>FILTER_VALIDATE_INT);
private $flt_search = array('limit' => FILTER_VALIDATE_INT,
							'label' => FILTER_SANITIZE_SPECIAL_CHARS,
							'value' => FILTER_SANITIZE_SPECIAL_CHARS);
private $flt_suggest = array('term' => FILTER_SANITIZE_SPECIAL_CHARS,
							'label' => FILTER_SANITIZE_SPECIAL_CHARS,
							'value' => FILTER_SANITIZE_SPECIAL_CHARS);

private $table_struct = array('number'=>array('item_number'),
							'string'=>array('category', 'tax_name', 'company_name', 'name'));
private $conv = array('c'=>'category','company'=>'company_name','number'=>'item_number','n'=>'item_number','tax'=>'tax_name');
private $sconv = array();
private $recv_con = array('name'=>'it.name', 'item_number'=>'it.item_number');
private $recv_struct = array('number'=>array('item_number'), 'string'=>array('name'));

public function __construct($db, $grant, $permission) {
	$this->func_permission = array('view'=>'items',
									'more'=>'items',
									'create'=>'items_insert',
									'save'=>'items_insert',
									'delete'=>'items_delete',
									'update'=>'items_update',
									'get'=>'items',
									'suggest_search'=>'items',
									'search'=>'items',
									'suggest_category'=>'items',
									'suggest_supplier'=>'items',
									'suggest_tax'=>'items',
									'check_item_number'=>'items_update',
									'remove_logo'=>'items_insert',
									'upload'=>'items_insert',
									'excel'=>'items_insert',
									'excel_import'=>'items_insert',
									'do_excel_import'=>'items_insert',
									'bulk_edit'=>'items_update',
									'bulk_update'=>'items_update',
									'generate_barcodes'=>'items_update',
									'suggest_order'=>'receivings_insert',
									'suggest_return'=>'receivings_delete',
									'suggest_kit'=>'item_kits_update',
									'suggest_sale'=>'sales');
	parent::__construct($db, $grant, $permission);
}

public function view(&$ipos) {
	$app = require '../config/app_con.php';
	$ipos->language(array('suppliers'));
	
	$ipos->assign('item_pic_dir', $app['item_pic_dir']);
	$ipos->assign('controller_name', 'items');
	$ipos->assign('offset', 100);
	$ipos->assign('subgrant', $this->subgrant);
	$ipos->assign('items', $this->get_all());
	$ipos->display('items/manage.tpl');
}

public function more(&$ipos) {
	$var = filter_var_array($_REQUEST, $this->flt_more);
	$var['offset'] = empty($var['offset']) ? 0 : $var['offset'];
	$var['limit'] = empty($var['limit']) ? 100 : $var['limit'];
	
	$data = false;
	if ('more' == $var['type']) {
		$data = $this->get_all($var['offset'], $var['limit']);
	} else if ('search' == $var['type']) {
		$data = $this->search_data($var, $var['offset'], $var['limit']);
	}
	
	if (!empty($data)) {
		$app = require '../config/app_con.php';
		$ipos->language(array('suppliers'));
		
		$ipos->assign('item_pic_dir', $app['item_pic_dir']);
		$ipos->assign('controller_name', 'items');
		$ipos->assign('items', $data);
		$ipos->assign('subgrant', $this->subgrant);
		echo json_encode(array('success'=>true, 'msg'=>'', 'data'=>$ipos->fetch('items/table_row.tpl'), 'offset'=>($var['offset'] + $var['limit'])));
	} else if ($data === false) {
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['items_err_get']));
	} else {
		echo json_encode(array('success'=>true, 'msg'=>$ipos->lang['common_no_more_data']));
	}
}

public function create(&$ipos) {
	$ipos->assign('supplier', '');
	$ipos->assign('image_path', '');
	echo $ipos->fetch('items/form.tpl');
}

public function save(&$ipos) {
	$result = $this->filter();
	if ($result === false) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang[$this->err ? $this->err : 'items_err_save']));
		return;
	}
	
	$id = $this->maxid();
	if ($id === false) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['items_err_save']));
		return;
	}
	++$id;
	
	$this->db->beginTransaction();
	if (isset($result['tax'])) {
		$item_tax = new item_tax($this->db);
		if ($item_tax->save($result['tax']) === false) {
			$this->db->rollBack();
			echo json_encode(array("success" => false, "msg" => $ipos->lang['items_err_save']));
			return;
		}
	}
	
	if ($this->save_table('items', $result['item'], $id) === false
		|| $this->save_table('item_quantities', array('quantity'=>0), $id) === false) {
		$this->db->rollBack();
		echo json_encode(array("success" => false, "msg" => $ipos->lang['items_err_save']));
		return;
	}
	
	$this->db->commit();
	
	$result['item']['item_id'] = $id;
	$result['item']['quantity'] = 0;
	$result['item']['company_name'] = $result['supplier'];
	
	$app = require '../config/app_con.php';
	$ipos->assign('item_pic_dir', $app['item_pic_dir']);
	$ipos->assign('items', array($result['item']));
	$ipos->assign('subgrant', $this->subgrant);
	echo json_encode(array("success" => true, "msg" => $ipos->lang['items_msg_save'], "id"=>$id, "row"=>$ipos->fetch('items/table_row.tpl')));
}

public function update(&$ipos) {
	if (!isset($_REQUEST['id'])
		|| ($result = $this->filter()) === false) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang[$this->err ? $this->err : 'items_err_update']));
		return;
	}
	
	$id = intval($_REQUEST['id']);
	unset($result['item']['item_number']);
	
	$this->db->beginTransaction();
	if (isset($result['tax'])) {
		$item_tax = new item_tax($this->db);
		if ($item_tax->save($result['tax']) === false) {
			$this->db->rollBack();
			echo json_encode(array("success" => false, "msg" => $ipos->lang['items_err_update']));
			return;
		}
	}
	
	if ($this->update_table('items', $result['item'], $id) === false) {
		$this->db->rollBack();
		echo json_encode(array("success" => false, "msg" => $ipos->lang['items_err_update']));
		return;
	}
	
	$this->db->commit();
	
	$item = $this->get_info(array($id));
	
	$result['item']['item_id'] = $id;
	$result['item']['quantity'] = $item[0]['quantity'];
	$result['item']['item_number'] = $item[0]['item_number'];
	$result['item']['company_name'] = $result['supplier'];
	
	$app = require '../config/app_con.php';
	$ipos->assign('item_pic_dir', $app['item_pic_dir']);
	$ipos->assign('items', array($result['item']));
	$ipos->assign('subgrant', $this->subgrant);
	echo json_encode(array("success" => true, "msg" => $ipos->lang['items_msg_update'], "id"=>$id, "row"=>$ipos->fetch('items/table_row.tpl')));
}

public function delete(&$ipos) {
	if (!isset($_REQUEST['ids']) || !is_array($_REQUEST['ids'])) {
		$ipos->assign('err', $ipos->lang['items_err_delete']);
		echo $ipos->fetch('err_msg.tpl');
		return;
	}
	
	$ids = array();
	foreach ($_REQUEST['ids'] as $v) {
		$ids[] = array(intval($v));
	}
	
	$this->db->beginTransaction();
	$this->db->query('UPDATE items SET deleted=1 WHERE item_id=?');
	if ($this->db->update($ids)) {
		$this->db->commit();
		echo json_encode(array("success" => true, "msg" => $ipos->lang['items_msg_delete']));
	} else {
		$this->db->rollBack();
		echo json_encode(array("success" => false, "msg" => $ipos->lang['items_err_delete']));
	}
}

public function get(&$ipos) {
	if (empty($_REQUEST['id'])
		|| empty($items = $this->get_info(array(intval($_REQUEST['id']))))) {
		$ipos->assign('err', $ipos->lang['items_err_get']);
		echo $ipos->fetch('err_msg.tpl');
		return;
	}
	
	$tax = null;
	$item = $items[0];
	if (!empty($item['tax_name'])) {
		$item_tax = new item_tax($this->db);
		$data = array();
		$tmp = explode(item_tax::needle, $item['tax_name']);
		foreach ($tmp as $v) {
			$data[] = array($v);
		}
		$tax = $item_tax->get($data);
	}
	
	$supplier = '';
	if (!empty($item['supplier_id'])) {
		$result = supplier::get_info($this->db, 'person_id', $item['supplier_id']);
		$supplier = isset($result[0]['company_name']) ? $result[0]['company_name'] : '';
	}
	
	$image_path = '';
	if (!empty($item['pic'])) {
		$app = require '../config/app_con.php';
		$image_path = $app['item_pic_dir'] . $item['pic'];
	}
	
	$ipos->assign('language', $ipos->session->usrdata('lang'));
	$ipos->assign('image_path', $image_path);
	$ipos->assign('supplier', $supplier);
	$ipos->assign('item', $item);
	if ($tax !== null) $ipos->assign('item_tax', $tax);
	
	echo $ipos->fetch('items/form.tpl');
}

public function suggest_search() {
	if (empty($_REQUEST['term'])) return;
	
	$var = filter_var($_REQUEST['term'], FILTER_SANITIZE_SPECIAL_CHARS);
	$this->db->query('SELECT * FROM items as i 
			LEFT JOIN suppliers as s ON i.supplier_id=s.person_id 
			JOIN item_quantities as iq ON iq.item_id=i.item_id
			WHERE i.deleted=0
			AND (');
	$this->db->order('ORDER BY i.item_id ASC');
	if (!empty($result = $this->db->search_suggestions($var, $this->table_struct, $this->sconv, array($this, 'sugg_conv'))))
		echo json_encode($result);
}

public function search(&$ipos) {
	$var = filter_var_array($_REQUEST, $this->flt_search);
	$limit = empty($var['limit']) ? 100 : $var['limit'];
	$result = $this->search_data($var, 0, $limit);
	$result = $result === false ? array() : $result;
	
	$app = require '../config/app_con.php';
	$ipos->assign('item_pic_dir', $app['item_pic_dir']);
	$ipos->assign('items', $result);
	$ipos->assign('subgrant', $this->subgrant);
	echo json_encode(array('rows' => $ipos->fetch('items/table_row.tpl'), 'total_rows' => count($result), 'offset' => $limit));
}

public function suggest_category() {
	if (empty($_REQUEST['term'])) return;
	
	$var = filter_var($_REQUEST['term'], FILTER_SANITIZE_SPECIAL_CHARS);
	$this->db->query('SELECT distinct(category) FROM items WHERE deleted=0 AND category LIKE ? LIMIT 0,25');
	if (!empty($sel = $this->db->select(array(array('%'. $var .'%'))))) {
		$result = array();
		foreach ($sel as $v) {
			$result[] = array('label'=>$v['category']);
		}
		
		echo json_encode($result);
	}
}

public function suggest_supplier() {
	if (empty($_REQUEST['term'])) return;
	
	$var = filter_var($_REQUEST['term'], FILTER_SANITIZE_SPECIAL_CHARS);
	$this->db->query('SELECT distinct(company_name) FROM suppliers WHERE deleted=0 AND company_name LIKE ? LIMIT 0,25');
	if (!empty($sel = $this->db->select(array(array('%'. $var .'%'))))) {
		$result = array();
		foreach ($sel as $v) {
			$result[] = array('label'=>$v['company_name']);
		}
		
		echo json_encode($result);
	}
}

public function suggest_tax(&$ipos) {
	if (empty($_REQUEST['term'])) return;
	
	$var = filter_var($_REQUEST['term'], FILTER_SANITIZE_SPECIAL_CHARS);
	$this->db->query('SELECT * FROM items_taxes WHERE name LIKE ? LIMIT 0,25');
	if (!empty($sel = $this->db->select(array(array('%'. $var .'%'))))) {
		$result = array();
		foreach ($sel as $v) {
			$result[] = array('label'=>$v['name'], 'value'=>$v['percent']);
		}
		
		echo json_encode($result);
	}
}

public function suggest_order(&$ipos) {
	if (empty($_REQUEST['term'])) return;
	
	$result = array();
	$var = filter_var($_REQUEST['term'], FILTER_SANITIZE_SPECIAL_CHARS);
	$this->db->query('SELECT * FROM items as i
			LEFT JOIN suppliers as s ON i.supplier_id=s.person_id 
			WHERE i.deleted=0
			AND (');
	$this->db->order('ORDER BY i.item_id ASC');
	if (!empty($sel = $this->db->search_suggestions($var, $this->recv_struct, $this->conv, array($this, 'conversion'), false))) {
		foreach ($sel as $v) {
			$ipos->assign('item', $v);
			$result[] = array('label'=>$v['name'], 'value'=>$v['item_id'] .','. $ipos->fetch('receivings/order_row.tpl'));
		}
	}
	
	$this->db->query('SELECT it.*, s.company_name as company_name FROM item_kits as it
			JOIN item_kit_items as iti ON iti.item_kit_id=it.item_kit_id
			JOIN items as i ON i.item_id=iti.item_id
			LEFT JOIN suppliers as s ON i.supplier_id=s.person_id 
			WHERE (');
	$this->db->order('ORDER BY it.item_kit_id ASC');
	if (!empty($sel = $this->db->search_suggestions($var, $this->recv_struct, $this->recv_con, array($this, 'recv_conv'), false))) {
		foreach ($sel as $v) {
			$v['item_id'] = $v['item_kit_id'];
			$ipos->assign('item', $v);
			$result[] = array('label'=>$v['name'], 'value'=>$v['item_id'] .','. $ipos->fetch('receivings/order_row.tpl'));
		}
	}
	
	echo json_encode($result);
}

public function suggest_return() {
	if (empty($_REQUEST['term'])) return;
	
	$result = array();
	$var = filter_var($_REQUEST['term'], FILTER_SANITIZE_SPECIAL_CHARS);
	$this->db->query('SELECT * FROM items as i WHERE i.deleted=0 AND (');
	$this->db->order('ORDER BY i.item_id ASC');
	if (!empty($sel = $this->db->search_suggestions($var, $this->recv_struct, $this->conv, array($this, 'conversion'), false))) {
		foreach ($sel as $v) {
			$result[] =array('label'=>$v['name'], 'value'=>$v['item_id'] .',');
		}
	}
	
	
	$this->db->query('SELECT * FROM item_kits as it WHERE (');
	$this->db->order('ORDER BY it.item_kit_id ASC');
	if (!empty($sel = $this->db->search_suggestions($var, $this->recv_struct, $this->recv_con, array($this, 'recv_conv'), false))) {
		foreach ($sel as $v) {
			$result[] =array('label'=>$v['name'], 'value'=>$v['item_kit_id'].',kit');
		}
	}
		
	echo json_encode($result);
}

public function suggest_kit(&$ipos) {
	if (empty($_REQUEST['term'])) return;
	
	$var = filter_var_array($_REQUEST, $this->flt_suggest);
	$label = array('label'=>'name='. $var['term']);
	$this->db->query('SELECT i.*, s.company_name FROM items as i 
			JOIN item_quantities as iq ON iq.item_id=i.item_id 
			LEFT JOIN suppliers as s ON i.supplier_id=s.person_id 
			WHERE i.deleted=0 AND ');
	$this->db->order('ORDER BY i.item_id ASC');
	$offset = empty($var['limit']) ? 0 : $var['limit'];
	if (!empty($result = $this->db->search($label, $this->conv, array($this, 'conversion'), $offset))) {
		$suggestions = array();
		foreach ($result as $v) {
			$ipos->assign('item', $v);
			$suggestions[] = array('label'=>$v['name'] .' '. $v['company_name'] , 'value'=>$v['item_id'] .','. $ipos->fetch('item_kits/form_row.tpl'));
		}
		
		echo json_encode($suggestions);
	}
}

public function suggest_sale(&$ipos) {
	if (empty($_REQUEST['term'])) return;
	
	$config = $ipos->session->usrdata('config');
	$item = $ipos->session->usrdata('sale_item');
	$item_kit = $ipos->session->usrdata('sale_item_kit');
	$suggestions = array();
	
	$barcode = filter_var($_REQUEST['term'], FILTER_SANITIZE_SPECIAL_CHARS);
	$var = $barcode;
	$kg = 1;
	$barnum = strncmp($barcode, '22', 2) === 0 ? 5:(strncmp($barcode, '23', 2) === 0 ? 4:0);
	if ($barnum !== 0) {
		$var = ltrim(substr($barcode, 2, $barnum), '0');
		$kg = floatval(substr($barcode, 2+$barnum, -3) + '.' + substr($barcode, -3));
	}
	
	$label = is_numeric($var) ? array('label'=>'item_number='. $var) : array('label'=>'name='. $var);
	$this->db->query('SELECT i.*,iq.quantity as quantity FROM items as i
			JOIN item_quantities as iq ON iq.item_id=i.item_id 
			WHERE i.deleted=0 AND quantity>0 AND ');
	$this->db->order('ORDER BY i.item_id ASC');
	if (!empty($result = $this->db->search($label, $this->conv, array($this, 'conversion')))) {
		foreach ($result as $v) {
			if ($barnum !== 0 && strlen($v['item_number']) > $config['kg_barcode']) continue;
				
			$unit = $v['unit_price'] * (1 - $v['sale_discount'] / 100);
			$cost = $v['cost_price'] * (1 - $v['cost_discount'] / 100);
			$v['unit_price'] = $unit;
			$v['cost_price'] = $cost;
			$v['is_kg'] = ($barnum !== 0);
			$v['sale_quantity'] = $kg;
			$item[$v['item_id']] = $v;
			$ipos->assign('items', array($v));
			$suggestions[] = array('label'=>$v['name'] .' '. $v['item_number'] , 'value'=>json_encode(array('id'=>$v['item_id'],  'data'=>$ipos->fetch('sale/table_row.tpl'), 'kg'=>$kg)));
		}
		
		$ipos->session->param(array('sale_item'=>$item));
	}
	
	$this->db->query('SELECT item_kit_id FROM item_kits WHERE ');
	$this->db->order('ORDER BY item_kit_id ASC');
	if ($barnum === 0 && !empty($result = $this->db->search($label, null, null))) {
		if ($kit = item_kits::get_info($this->db, $result)) {
			$sale_item_kit_info = $ipos->session->usrdata('sale_item_kit_info');
			foreach ($kit as $v) {
				$im = $v['item'];
				$sale_item_kit_info[$im['item_kit_id']] = $v['kit_items'];
				$im['item_id'] = $im['item_kit_id'];
				$im['is_kg'] = false;	// item_kit is not kg.
				$im['sale_quantity'] = 1;
				$item_kit[$im['item_kit_id']] = $im;
				$ipos->assign('items', array($im));
				$suggestions[] = array('label'=>$im['name'] .' '. $im['item_number'] , 'value'=>json_encode(array('id'=>$im['item_id'],  'data'=>$ipos->fetch('sale/table_row.tpl'), 'kg'=>1)));
			}
			
			$ipos->session->param(array('sale_item_kit'=>$item_kit, 'sale_item_kit_info'=>$sale_item_kit_info));
		}
	}
	
	if (!empty($suggestions)) {
		echo json_encode($suggestions);
	}
}

public function check_item_number() {
	$var = filter_var_array($_REQUEST, $this->flt_number);
	if (empty($var['item_number'])) {
		echo 'true';
		return;
	}
	
	$result = $this->check_number($var['item_number'], $var['id']) ? 'true' : 'false';
	echo $result;
}

public function remove_logo(&$ipos) {
	echo json_encode(array("success" => true, "msg" => $ipos->lang['items_msg_remove_logo']));
}

public function upload(&$ipos) {
	if (!($app = include '../config/app_con.php')) {
		error_log('read app connfig err.');
		echo json_encode(array("success" => false, "msg" => $ipos->lang['con_err']));
		return;
	}
	
	$upload = new upload;
	$filename = $upload->load($app['cofig_upload_size'], $app['config_allowed_type'], 'item_image', $app['item_pic_dir'], true);
	if (!empty($filename[0])) {
		$id = empty($_REQUEST['id']) ? false : intval($_REQUEST['id']);
		if ($id === false 
			|| $this->update_table('items', array('pic' => $filename[0]), $id)) {			
			echo json_encode(array("success" => true, "msg" => $ipos->lang['items_msg_upload'], "pic"=>$filename[0]));
			return;
		}
	}
		
	echo json_encode(array("success" => false, "msg" => $ipos->lang['items_err']));
}

public function excel() {
	$app = include '../config/app_con.php';
	$name = 'import_items.csv';
	$file = $app['download_dir'] . $name;
	$data = file_get_contents($file);
	
	include_once '../libs/help/common.php';
	force_download($name, $data);
}

public function excel_import($ipos) {
	$ipos->assign('controller_name', 'items');
	echo $ipos->fetch('partial/form_excel_import.tpl');
}

public function do_excel_import(&$ipos) {
	$failCodes = array();
	if ($_FILES['file_path']['error'] != UPLOAD_ERR_OK) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['import_err']));
		return;
	} else {
		if (($handle = fopen($_FILES['file_path']['tmp_name'], "r")) !== false
			&& ($maxid = $this->maxid()) !== false) {
			$this->db->beginTransaction();
		
			//Skip first row
			fgetcsv($handle);
			
			$app = require '../config/app_con.php';
			$i = 0;
			while (($data = fgetcsv($handle)) !== false) {
				++$i;
				++$maxid;
				$item_data = array(
					'name'			=>	$data[1],
					'category'		=>	$data[2],
					'cost_price'	=>	$data[4],
					'unit_price'	=>	$data[5],
					'reorder_level'	=>	$data[10],
					'description'	=>	$data[11],
					'sale_discount'=>	$data[12],
					'cost_discount'=>	$data[13],
					'pic'			=> ''
				);
				$item_number = $data[0];
				if ($item_number != ''
					&& $this->check_number($item_number)) {
					$item_data['item_number'] = $item_number;
				} else {
					$failCodes[] = $i;
					continue;
				}
				
				$supplier = $data[3];
				if (!empty($supplier)) {
					if (supplier::get_info($this->db, 'person_id', $supplier)) {
						$item_data['supplier_id'] = $supplier;
					} else {
						$failCodes[] = $i;
						continue;
					}
				}
				
				if (!empty($item_data['pic']) && !file_exists($app['item_pic_dir'] . $item_data['pic'])) {
					$failCodes[] = $i;
					continue;
				}
				
				$quantity = 0;
				$tax_name = null;
				//tax 1
				if(is_numeric($data[7]) && $data[6] != '') {
					$tmp = str_replace(item_tax::needle, '', $data[6]);
					$tax_name = $tmp .item_tax::needle;
					$tax[$tmp] = $data[7];
				}
				//tax 2
				if(is_numeric($data[9]) && $data[8]!='') {
					$tmp = str_replace(item_tax::needle, '', $data[8]);
					$tax_name .= $tmp;
					$tax[$tmp] = $data[9];
				}
				
				if ($tax_name !== null) {
					$item_tax = new item_tax($this->db);
					if ($item_tax->save($tax) === false) {
						$failCodes[] = $i;
						continue;
					}
					
					$item_data['tax_name'] = rtrim($tax_name, item_tax::needle);
				}
				
				if ($this->save_table('items', $item_data, $maxid) === false
					|| $this->save_table('item_quantities', array('quantity'=>$quantity), $maxid) === false) {
						$failCodes[] = $i;
				}
			}
		} else {
			echo json_encode(array("success" => false, "msg" => $ipos->lang['import_err']));
			return;
		}
	}

	$cnt = count($failCodes);
	if($cnt > 0) {
		$this->db->rollBack();
		$msg = $ipos->lang['import_check'] . "(" .$cnt . "): " .implode(", ", $failCodes);
		echo json_encode(array('success'=>false, 'msg'=>$msg));
	} else {
		$this->db->commit();
		echo json_encode(array('success'=>true, 'msg'=>$ipos->lang['import_msg']));
	}
}

public function bulk_edit(&$ipos) {
	echo $ipos->fetch('items/bulk.tpl');
}

public function bulk_update(&$ipos) {
	if (!isset($_REQUEST['ids'])
		|| (array)$_REQUEST['ids'] !== $_REQUEST['ids']
		|| ($result = $this->filter()) === false) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang[$this->err ? $this->err : 'items_err_update']));
		return;
	}
	
	unset($result['item']['name'],$result['item']['pic'],$result['item']['item_number']);
	if ($_REQUEST['category'] == null) unset($result['item']['category']);
	if ($_REQUEST['cost_price'] == null) unset($result['item']['cost_price']);
	if ($_REQUEST['description'] == null) unset($result['item']['description']);
	if ($_REQUEST['reorder_level'] == null) unset($result['item']['reorder_level']);
	if ($_REQUEST['unit_price'] == null) unset($result['item']['unit_price']);
	if ($result['item']['tax_name'] == '') unset($result['item']['tax_name']);
	
	if (empty($result)) return;
	
	$this->db->beginTransaction();
	if (isset($result['tax'])) {
		$item_tax = new item_tax($this->db);
		if ($item_tax->save($result['tax']) === false) {
			$this->db->rollBack();
			echo json_encode(array("success" => false, "msg" => $ipos->lang['items_err_update']));
			return;
		}
	}
	
	$ids = array();
	foreach ($_REQUEST['ids'] as $v) {
		$id = intval($v);
		$ids[] = $id;
		if ($this->update_table('items', $result['item'], $id) === false) {
			$this->db->rollBack();
			echo json_encode(array("success" => false, "msg" => $ipos->lang['items_err_update']));
			return;
		}
	}
	$this->db->commit();
	
	$app = require '../config/app_con.php';
	$ipos->assign('item_pic_dir', $app['item_pic_dir']);
	$ipos->assign('items', $this->get_info($ids));
	$ipos->assign('subgrant', $this->subgrant);
	echo json_encode(array("success" => true, "msg" => $ipos->lang['items_msg_update'], "ids"=>$ids, "row"=>$ipos->fetch('items/table_row.tpl')));
}

public function generate_barcodes(&$ipos) {
	$var = filter_var($_REQUEST['ids'], FILTER_SANITIZE_SPECIAL_CHARS);
	$ids = explode(':', $var);
	$items = $this->get_info($ids);
	if (empty($items)) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang[$this->err ? $this->err : 'items_err_g_barcode']));
		return;
	}
	
	$config = $ipos->session->usrdata('config');
	if ($config['barcode_generate_if_empty']) {
		foreach($items as &$item) {
			if (empty($item['item_number'])) {
				// get the newly generated barcode
				$barcode_instance = Barcode_lib::barcode_instance($item, $config);
				$item['item_number'] = $barcode_instance->getData();
				$this->update_table('items', array('item_number'=>$item['item_number']), $item['item_id']);
			}
		}
	}

	$ipos->assign('items', $items);
	$ipos->assign('barcode', new Barcode_lib());
	$ipos->display('barcode/barcode.tpl');
}

public function check_number($number, $id = false, $control='items') {
	$this->db->query('SELECT item_id FROM items WHERE item_number=?');
	$row = $this->db->select(array(array($number)));
	if ($control == 'items')
		$result = ($row && $id) ? ($row[0]['item_id'] == $id) : empty($row);
	else
		$result = empty($row);
	
	if ($result === false) return false;
	
	$this->db->query('SELECT item_kit_id FROM item_kits WHERE item_number=?');
	$row = $this->db->select(array(array($number)));
	if ($control == 'items')
		$result = empty($row);
	else
		$result = ($row && $id) ? ($row[0]['item_kit_id'] == $id) : empty($row);
	
	return $result;
}

private function get_info($ids) {
	$data = array();
	foreach ($ids as $v) {
		$data[] = array($v);
	}
	$this->db->query('SELECT * FROM items as i 
		JOIN item_quantities as iq ON iq.item_id=i.item_id 
		LEFT JOIN suppliers as s ON s.person_id=i.supplier_id 
		WHERE i.item_id=?');
	return $this->db->select($data);
}

private function filter() {
	$var = filter_var_array($_REQUEST, $this->flt);
	if (!empty($var['supplier'])) {
		$result = supplier::get_info($this->db, 'company_name', $var['supplier']);
		if (!isset($result[0]['person_id'])) {
			$this->err = 'items_err_supplier';
			return false;
		}
		
		$var['supplier_id'] = $result[0]['person_id'];
		unset($result);
	} else {
		$var['supplier_id'] = 1;
	}
	$result['supplier'] = $var['supplier'];
	unset($var['supplier']);
	
	$tax_name = null;
	$tax = array();
	for ($i = 0; $i < count($_REQUEST['tax_names']); ++$i) {
		if (empty($_REQUEST['tax_names'][$i])
			|| (isset($_REQUEST['tax_percents'][$i]) &&  $_REQUEST['tax_percents'][$i]== null))
			continue;
		
		$tmp = str_replace(item_tax::needle, '', filter_var($_REQUEST['tax_names'][$i], FILTER_SANITIZE_SPECIAL_CHARS));
		$tax_name .= $tmp . item_tax::needle;
		$tax[$tmp] = floatval($_REQUEST['tax_percents'][$i]);
	}
	
	$result['item'] = $var;
	if ($tax_name !== null) {
		$result['item']['tax_name'] = rtrim($tax_name, item_tax::needle);
		$result['tax'] = $tax;
	} else {
		$result['item']['tax_name'] = '';
	}
	
	return $result;
}

public function update_table($table, $data, $id, $key='item_id') {
	$str = null;
	$tmp = array();
	foreach ($data as $k => $v) {
		$str .= $k . '=?,';
		$tmp[] = $v;
	}
	
	$query = 'UPDATE '. $table .' SET ' . rtrim($str, ',') . ' WHERE '. $key .'='. $id;
	$this->db->query($query);
	return $this->db->update(array($tmp));
}

public function save_table($table, $data, $id, $key='item_id') {
	$q1 = null;
	$tmp = array();
	foreach ($data as $k => $v) {
		$q1 .= ','. $k;
		$tmp[] = $v;
	}
	
	$query ='INSERT INTO '. $table .' ('. $key . $q1 . ') VALUES('. $id . str_repeat(',?', count($tmp)) . ')';
	$this->db->query($query);
	return $this->db->insert(array($tmp));
}

public function maxid() {
	 $this->db->query('SELECT MAX(item_id) AS id FROM items UNION SELECT MAX(item_kit_id) AS id FROM item_kits');
	 $result = $this->db->select();
	 $item = $result[0]['id'] !== null ? $result[0]['id'] : 0;
	 $kit = $result[1]['id'] !== null ? $result[1]['id'] : 0;
	 return $item > $kit ? $item : $kit;
}

private function get_all($offset=0, $limit=100) {
	$this->db->query('SELECT * FROM items as i 
		JOIN item_quantities as iq ON iq.item_id=i.item_id
		LEFT JOIN suppliers as s ON s.person_id=i.supplier_id 
		WHERE i.deleted=0 ORDER BY i.item_id ASC LIMIT '. $offset .','. $limit);
	return $this->db->select();
}

public function conversion(&$key, &$val, $index) {
	switch($key[$index]) {
	default:
		$key[$index] = $this->conv[$key[$index]];
	break;
	}
}

public function sugg_conv(&$key, &$val, $index) {
	switch($key[$index]) {
	default:
		$key[$index] = $this->sconv[$key[$index]];
	break;
	}
}

public function recv_conv(&$key, &$val, $index) {
		$key[$index] = $this->recv_con[$key[$index]];
}

private function search_data($var, $offset=0, $limit=100) {
	$query = 'SELECT * FROM items as i 
			LEFT JOIN suppliers as s ON i.supplier_id=s.person_id 
			JOIN item_quantities as iq ON iq.item_id=i.item_id 
			WHERE AND ';
	if (strpos($var['label'], 'deleted') === false) $query .= 'i.deleted=0 AND ';
	
	$this->db->query($query);
	$this->db->order('ORDER BY i.item_id ASC');
	$result = $this->db->search($var, $this->conv, array($this, 'conversion'), $offset, $limit);
	if ($result === -1) {
		$this->db->query('SELECT * FROM items as i 
			LEFT JOIN suppliers as s ON i.supplier_id=s.person_id 
			JOIN item_quantities as iq ON iq.item_id=i.item_id
			WHERE i.deleted=0
			AND (');
		$this->db->order('ORDER BY i.item_id ASC');
		$result = $this->db->search_suggestions($var['label'], $this->table_struct, $this->sconv, array($this, 'sugg_conv'), false, $offset, $limit);
	}
	
	return $result;
}
}
?>