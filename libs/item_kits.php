<?php
require_once '../libs/secure.php';
require_once '../libs/items.php';

class item_kits extends secure {
private $item;
private $flt = array('item_number'=>FILTER_SANITIZE_SPECIAL_CHARS,
					'name'=>FILTER_SANITIZE_SPECIAL_CHARS,
					'discount'=>FILTER_VALIDATE_INT,
					'quantity'=>FILTER_VALIDATE_INT,
					'description'=>FILTER_SANITIZE_SPECIAL_CHARS);

private $flt_more = array('offset' => FILTER_VALIDATE_INT, 
						'limit' => FILTER_VALIDATE_INT, 
						'type' => FILTER_SANITIZE_SPECIAL_CHARS, 
						'label' => FILTER_SANITIZE_SPECIAL_CHARS, 
						'value' => FILTER_SANITIZE_SPECIAL_CHARS);
private $flt_search = array('limit' => FILTER_VALIDATE_INT,
							'label' => FILTER_SANITIZE_SPECIAL_CHARS,
							'value' => FILTER_SANITIZE_SPECIAL_CHARS);
private $table_struct = array('number'=>array('item_number'),
							'string'=>array('name'));
private $conv = array('number'=>'it.item_number','n'=>'it.item_number','id'=>'iti.item_id','item'=>'iti.item_id','item_number'=>'it.item_number','name'=>'it.name');
private $sconv = array();

public function __construct($db, $grant, $permission) {
	$this->item = new items($db, $grant, $permission);
	$this->func_permission = array('view'=>'item_kits',
									'more'=>'item_kits',
									'create'=>'item_kits_insert',
									'save'=>'item_kits_insert',
									'delete'=>'item_kits_delete',
									'update'=>'item_kits_update',
									'get'=>'item_kits',
									'check_item_number'=>'item_kits',
									'suggest_search'=>'item_kits',
									'search'=>'item_kits',
									'generate_barcodes'=>'item_kits_update');
	parent::__construct($db, $grant, $permission);
}

public function view(&$ipos) {
	$ipos->assign('controller_name', 'item_kits');
	$ipos->assign('offset', 100);
	$ipos->assign('subgrant', $this->subgrant);
	$ipos->assign('items', $this->get_all());
	$ipos->display('item_kits/manage.tpl');
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
		$ipos->assign('controller_name', 'item_kits');
		$ipos->assign('items', $data);
		$ipos->assign('subgrant', $this->subgrant);
		echo json_encode(array('success'=>true, 'msg'=>'', 'data'=>$ipos->fetch('item_kits/table_row.tpl'), 'offset'=>($var['offset'] + $var['limit'])));
	} else if ($data === false) {
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['item_kits_err_get']));
	} else {
		echo json_encode(array('success'=>true, 'msg'=>$ipos->lang['common_no_more_data']));
	}
}

public function create(&$ipos) {
	$ipos->assign('kit_items', array());
	echo $ipos->fetch('item_kits/form.tpl');
}

public function get(&$ipos) {
	if (empty($_REQUEST['id'])
		|| empty($result = item_kits::get_info($this->db, array(array(intval($_REQUEST['id'])))))) {
		$ipos->assign('err', $ipos->lang['items_err_get']);
		echo $ipos->fetch('err_msg.tpl');
		return;
	}
	
	$var = $result[1];
	$ipos->assign('item', $var['item']);
	$ipos->assign('kit_items', $var['kit_items']);
	echo $ipos->fetch('item_kits/form.tpl');
}

public function check_item_number() {
	$var = filter_var_array($_REQUEST, $this->item->flt_number);
	if (empty($var['item_number'])) {
		echo 'true';
		return;
	}
	
	$result = $this->item->check_number($var['item_number'], $var['id'], 'item_kits') ? 'true' : 'false';
	echo $result;
}

public function save(&$ipos) {
	$result = $this->filter();
	if (empty($result['kit_item']) || ($id = $this->item->maxid()) === false) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['item_kits_err_save']));
		return;
	}
	++$id;
	
	$this->db->beginTransaction();
	
	if ($this->item->save_table('item_kits', $result['kit'], $id, 'item_kit_id') === false
		|| $this->item->save_table('item_quantities', array('quantity'=>0), $id) === false) {
		$this->db->rollBack();
		echo json_encode(array("success" => false, "msg" => $ipos->lang['item_kits_err_save']));
		return;
	}
	
	foreach ($result['kit_item'] as $v) {
		if ($this->item->save_table('item_kit_items', $v, $id, 'item_kit_id') === false) {
			$this->db->rollBack();
			echo json_encode(array("success" => false, "msg" => $ipos->lang['item_kits_err_save']));
			return;
		}
	}
	$this->db->commit();
	
	$result['kit']['item_kit_id'] = $id;
	$result['kit']['cost_price'] = $result['cost_price'];
	$result['kit']['unit_price'] = $result['unit_price'];
	$result['kit']['quantity'] = $result['quantity'];
	$ipos->assign('items', array($result['kit']));
	$ipos->assign('subgrant', $this->subgrant);
	echo json_encode(array("success" => true, "msg" => $ipos->lang['item_kits_msg_save'], "id"=>$id, "row"=>$ipos->fetch('item_kits/table_row.tpl')));
}

public function update(&$ipos) {
	$result = $this->filter();
	if (empty($_REQUEST['id']) || empty($result['kit_item'])) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['item_kits_err_update']));
		return;
	}
	
	$id = intval($_REQUEST['id']);
	
	$this->db->beginTransaction();
	
	if ($this->item->update_table('item_kits', $result['kit'], $id, 'item_kit_id') === false
		|| $this->item->update_table('item_quantities', array('quantity'=>$result['quantity']), $id) === false
		|| $this->del('item_kit_items', array(array($id))) === false) {
		$this->db->rollBack();
		echo json_encode(array("success" => false, "msg" => $ipos->lang['item_kits_err_update']));
		return;
	}

	foreach ($result['kit_item'] as $v) {
		if ($this->item->save_table('item_kit_items', $v, $id, 'item_kit_id') === false) {
			$this->db->rollBack();
			echo json_encode(array("success" => false, "msg" => $ipos->lang['item_kits_err_update']));
			return;
		}
	}
	
	$this->db->commit();
	
	$result['kit']['item_kit_id'] = $id;
	$result['kit']['cost_price'] = $result['cost_price'];
	$result['kit']['unit_price'] = $result['unit_price'];
	$result['kit']['quantity'] = $result['quantity'];
	$ipos->assign('items', array($result['kit']));
	$ipos->assign('subgrant', $this->subgrant);
	echo json_encode(array("success" => true, "msg" => $ipos->lang['item_kits_msg_save'], "id"=>$id, "row"=>$ipos->fetch('item_kits/table_row.tpl')));
}

public function delete (&$ipos) {
	if (!isset($_REQUEST['ids']) || !is_array($_REQUEST['ids'])) {
		$ipos->assign('err', $ipos->lang['item_kits_err_delete']);
		echo $ipos->fetch('err_msg.tpl');
		return;
	}
	
	$ids = array();
	foreach ($_REQUEST['ids'] as $v)
		$ids[] = array(intval($v));
		
	$this->db->beginTransaction();
	if ($this->del('item_kit_items', $ids) === false
		|| $this->del('item_kits', $ids) === false
		|| $this->del('item_quantities', $ids, 'item_id') === false) {
		$this->db->rollBack();
		echo json_encode(array("success" => false, "msg" => $ipos->lang['item_kits_err_delete']));
		return;
	}
	
	$this->db->commit();
	echo json_encode(array("success" => true, "msg" => $ipos->lang['item_kits_msg_delete']));
}

public function generate_barcodes(&$ipos) {
	$var = filter_var($_REQUEST['ids'], FILTER_SANITIZE_SPECIAL_CHARS);
	$ids = explode(':', $var);
	$tmp = array();
	foreach ($ids as $v)
		$tmp[] = array($v);
		
	
	$this->db->query('SELECT * FROM item_kits WHERE item_kit_id=?');
	$result = $this->db->select($tmp);
	if (empty($result)) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang[$this->err ? $this->err : 'items_err_g_barcode']));
		return;
	}
	
	$items = array();
	foreach ($result as &$v) {
		$v['item_id'] = '';
		$items[] = $v;
	}

	$config = $ipos->session->usrdata('config');
	if ($config['barcode_generate_if_empty']) {
		foreach($items as &$item) {
			if (empty($item['item_number'])) {
				// get the newly generated barcode
				$barcode_instance = Barcode_lib::barcode_instance($item, $config);
				$item['item_number'] = $barcode_instance->getData();
				$this->item->update_table('item_kits', array('item_number'=>$item['item_number']), $item['item_kit_id'], 'item_kit_id');
			}
		}
	}

	$ipos->assign('items', $items);
	$ipos->assign('barcode', new Barcode_lib());
	$ipos->display('barcode/barcode.tpl');
}

public function suggest_search() {
	if (empty($_REQUEST['term'])) return;
	
	$var = filter_var($_REQUEST['term'], FILTER_SANITIZE_SPECIAL_CHARS);
	$this->db->query('SELECT * FROM item_kits WHERE (');
	$this->db->order('ORDER BY item_kit_id ASC');
	if (!empty($result = $this->db->search_suggestions($var, $this->table_struct, $this->sconv, array($this, 'sugg_conv'))))
		echo json_encode($result);
}

public function search(&$ipos) {
	$var = filter_var_array($_REQUEST, $this->flt_search);
	$limit = empty($var['limit']) ? 100 : $var['limit'];
	$data = $this->search_data($var, 0, $limit);
	
	$ipos->assign('items', $data);
	$ipos->assign('subgrant', $this->subgrant);
	echo json_encode(array('rows' => $ipos->fetch('item_kits/table_row.tpl'), 'offset' => 100, 'total_rows'=>count($data)));
}

private function del($table, $ids, $key="item_kit_id") {
	$this->db->query('DELETE FROM '. $table .' WHERE '. $key .'=?');
	return $this->db->delete($ids);
}

public static function get_info($db, $ids) {
	$db->query("select it.*, iti.item_id as item_id, iti.quantity as quantity, i.cost_price as cost_price, i.name as item_name, i.cost_discount as cost_discount, i.tax_name as tax_name, i.unit_price as unit_price, iq.quantity as qty, s.company_name as company_name from item_kits as it 
		join item_quantities as iq on iq.item_id=it.item_kit_id
		join item_kit_items as iti on iti.item_kit_id=it.item_kit_id 
		join items as i on i.item_id=iti.item_id and i.deleted=0
		left join suppliers as s on s.person_id=i.supplier_id
		where it.item_kit_id=?");
	if ($sel = $db->select($ids)) {
		$cost = 0;
		$unit = 0;
		$item_kit_id = -1;
		$item = array();
		$kit_items = array();
		$result = array();
		foreach ($sel as $row) {
			if ($item_kit_id !== $row['item_kit_id']) {
				$item['cost_price'] = $cost;
				$item['unit_price'] = $unit * (1 - $row['discount'] / 100);
				$result[] = array('item'=>$item, 'kit_items'=>$kit_items);
				$cost = $unit = 0;
				$kit_items = array();
				$item_kit_id = $row['item_kit_id'];
				$item['item_kit_id'] = $row['item_kit_id'];
				$item['item_number'] = $row['item_number'];
				$item['name'] = $row['name'];
				$item['description'] = $row['description'];
				$item['discount'] = $row['discount'];
				$item['quantity'] = $row['qty'];
				$item['company_name'] = $row['company_name'];
			}
			
			if (!empty($row['item_id'])) {
				$cost += $row['quantity'] * $row['cost_price'] * (1 - $row['cost_discount'] / 100);
				$unit += $row['quantity'] * $row['unit_price'];
				$tmp['item_id'] = $row['item_id'];
				$tmp['quantity'] = $row['quantity'];
				$tmp['name'] = $row['item_name'];
				$tmp['cost_price'] = $row['cost_price'];
				$tmp['cost_discount'] = $row['cost_discount'];
				$tmp['unit_price'] = $row['unit_price'];
				$tmp['tax_name'] = $row['tax_name'];
				$kit_items[] = $tmp;
			}
		}
		
		$item['cost_price'] = $cost;
		$item['unit_price'] = $unit * (1 - $row['discount'] / 100);
		$result[] = array('item'=>$item, 'kit_items'=>$kit_items);
		unset($result[0]);
		return $result;
	} else {
		return false;
	}
}

private function get_all($offset=0, $limit=100) {
	$this->db->query('select it.*, iti.item_id as item_id, iti.quantity as quantity, i.cost_price as cost_price, i.name as item_name, i.unit_price as unit_price, i.cost_discount as cost_discount, iq.quantity as qty
		from item_kits as it 
		join item_quantities as iq on iq.item_id=it.item_kit_id
		left join item_kit_items as iti on iti.item_kit_id=it.item_kit_id 
		left join items as i on i.item_id=iti.item_id and i.deleted=0 order by it.item_kit_id asc limit '. $offset .','. $limit);
	$items = array();
	if ($sel = $this->db->select()) {
		$element = 0;
		$total = 0;
		$cost = 0;
		$unit = 0;
		$item_kit_id = -1;
		foreach ($sel as $row) {
			++$total;
			++$element;
			if ($item_kit_id != $row['item_kit_id']) {
				$item['cost_price'] = $cost;
				$item['unit_price'] = $unit * (1 - $row['discount'] / 100);
				$items[] = $item;
				$cost = $unit = $element = 0;
				
				$item_kit_id = $row['item_kit_id'];
				$item['item_kit_id'] = $row['item_kit_id'];
				$item['item_number'] = $row['item_number'];
				$item['name'] = $row['name'];
				$item['description'] = $row['description'];
				$item['quantity'] = $row['qty'];
			}
			
			if (!empty($row['item_id'])) {
				$cost += $row['quantity'] * $row['cost_price'] * (1 - $row['cost_discount'] / 100);
				$unit += $row['quantity'] * $row['unit_price'];
			}
		}
		
		if ($total < $limit && $total > 0) {
			$item['cost_price'] = $cost;
			$item['unit_price'] = $unit * (1 - $row['discount'] / 100);
			$items[] = $item;
		} else if ($total == $limit) {
			// $total - $element is total of selection.
		}
		
		unset($items[0]);
	}
	
	return $items;
}

private function filter() {
	$var = filter_var_array($_REQUEST, $this->flt);
	if ($var['discount'] < 0 || $var['discount'] >= 100) $var['discount'] = 0;

	$kit_item = null;
	if (!empty($_REQUEST['item_kit_item'])) {
		foreach ($_REQUEST['item_kit_item'] as $k => $v) {
			$kit_item[] = array('item_id' => intval($k), 'quantity' => intval($v));
		}
	}
	
	$result['quantity'] = $var['quantity'] > 0 ? $var['quantity'] : 0;
	unset($var['quantity']);
	$result['kit'] = $var;
	$result['kit_item'] = $kit_item;
	$result['cost_price'] = isset($_REQUEST['cost_price']) ? floatval($_REQUEST['cost_price']) : '';
	$result['unit_price'] = isset($_REQUEST['unit_price']) ? floatval($_REQUEST['unit_price']) : '';
	
	return $result;
}

private function search_data($var, $offset=0, $limit=100) {
	$this->db->query('SELECT distinct(it.item_kit_id)
		FROM item_kit_items as iti
		JOIN items as i ON i.item_id=iti.item_id AND i.deleted=0
		JOIN item_kits as it ON it.item_kit_id=iti.item_kit_id
		WHERE ');
	$this->db->order('ORDER BY it.item_kit_id ASC');
	$result = $this->db->search($var, $this->conv, array($this, 'conversion'), $offset, $limit);
	if ($result === -1) {
		$this->db->query('SELECT distinct(item_kit_id) FROM item_kitsWHERE (');
		$this->db->order('ORDER BY item_kit_id ASC');
		$result = $this->db->search_suggestions($var['label'], $this->table_struct, $this->sconv, array($this, 'sugg_conv'), false, $offset, $limit);
	}
	
	$data = array();
	if (!empty($result) && ($tmp = item_kits::get_info($this->db, $result))) {
		foreach ($tmp as $v) {
			$data[] = $v['item'];
		}
	}
	
	return $data;
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
}
?>