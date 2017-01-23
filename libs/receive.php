<?php
require_once '../libs/secure.php';
require_once '../libs/employee.php';
require_once '../libs/recv_item.php';
require_once '../libs/item_kits.php';

class receive extends secure {

public function __construct($db, $grant, $permission) {
	$this->func_permission = array('view'=>'receivings',
									'order'=>'receivings_insert',
									'get'=>'receivings_update',
									'suggest_search'=>'receivings',
									'receive'=>'receivings_update',
									'item'=>'receivings_delete',
									'return'=>'receivings_delete');
	parent::__construct($db, $grant, $permission);
}

public function view(&$ipos) {
	$ipos->language(array('items'));
	$ipos->assign('emp_id', $ipos->session->usrdata('person_id'));
	
	if (isset($_REQUEST['get'])) {
		if (parent::has_grant($_REQUEST['get'])) {
			switch ($_REQUEST['get']) {
				case 'order': $tpl = 'receivings/order.tpl'; break;
				case 'receive': $tpl = 'receivings/receive.tpl'; break;
				case 'return': $tpl = 'receivings/return.tpl'; break;
			}
			
			if ($tpl)
				echo json_encode(array("success" => true, "data" => $ipos->fetch($tpl)));
			else
				echo json_encode(array("success" => false, "msg" => $ipos->lang['giftcards_err_param']));
				
			return;
		} else {
			echo json_encode(array("success" => false, "msg" => $ipos->lang['no_access']));
			return;
		}
	}

	$ipos->assign('controller_name', 'receivings');
	$ipos->assign('subgrant', $this->subgrant);
	$ipos->display('receivings/manage.tpl');
}

public function order(&$ipos) {
	if (empty($_REQUEST['item']) || !isset($_REQUEST['order_person'])
		|| ($maxid = $this->maxid()) === false) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['recvs_err_param']));
		return;
	}
	++$maxid;
	
	$i = 0;
	$item = array();
	foreach ($_REQUEST['item'] as $k => $v) {
		$tmp = intval($v);
		if ($tmp <= 0) {
			echo json_encode(array("success" => false, "msg" => $ipos->lang['recvs_err_param']));
			return;
		}
		
		$item[] = array('item_id'=>intval($k), 'order_quantity'=>$tmp, 'line'=>$i++);
	}
	
	$emp_id = intval($_REQUEST['order_person']);
	$comment = isset($_REQUEST['order_comment']) ? filter_var($_REQUEST['order_comment'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
	
	$recv = array();
	$recv['recv_date'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
	$recv['invoice_number'] = date('Ymd') .'-'. $emp_id .'-'. $maxid;
	$recv['order_person'] = $emp_id;
	if ($comment !== null) $recv['comment'] = $comment;
	
	$recv_item = new recv_item($this->db);
	
	$this->db->beginTransaction();
	if ($this->save_table('recv', $recv, $maxid) === false
		|| $recv_item->save($item, $maxid) === false) {
		$this->db->rollBack();
		echo json_encode(array("success" => false, "msg" => $ipos->lang['recvs_err_order']));
		return;
	}
	
	$this->db->commit();
	echo json_encode(array("success" => true, "msg" => $ipos->lang['recvs_msg_order'], "number"=>$recv['invoice_number']));
}

public function suggest_search() {
	if (empty($_REQUEST['term'])) return;
	
	$id = '%'. intval($_REQUEST['term']) .'%';
	$suggestions = array();
	$this->db->query('SELECT invoice_number, recv_id FROM recv WHERE recv_person=-1 AND invoice_number LIKE ? ORDER BY recv_id ASC LIMIT 0,25');
	if ($result = $this->db->select(array(array($id)))) {
		foreach ($result as $v)
			$suggestions[] = array('label'=>$v['invoice_number'], 'value'=>$v['recv_id']);
			
		echo json_encode($suggestions);
	}
}

public function get(&$ipos) {
	if (!isset($_REQUEST['id'])) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['recvs_err_param']));
		return;
	}
	
	$id = intval($_REQUEST['id']);
	$this->db->query('SELECT r.*, ri.line as line, ri.order_quantity as order_quantity, ri.item_id as item_id, i.cost_price as cost_price, i.cost_discount as discount, i.name as name, i.item_number as item_number, s.company_name as company_name FROM recv as r
		JOIN recv_items as ri ON ri.recv_id=r.recv_id
		JOIN items as i ON i.item_id=ri.item_id
		LEFT JOIN suppliers as s ON i.supplier_id=s.person_id
		WHERE recv_person=-1 AND r.recv_id=? ORDER BY line ASC');
	$total = 0;
	$data = array();
	if ($result = $this->db->select(array(array($id)))) {
		foreach ($result as $row) {
			$row['total'] = $row['order_quantity'] * $row['cost_price'] * (1 - $row['discount'] / 100);
			$total += $row['total'];
			$data[$row['line']] = $row;
		}
	}
	
	$this->db->query('SELECT r.*, ri.line as line, ri.order_quantity as order_quantity, ri.item_id as item_id FROM recv as r
		JOIN recv_items as ri ON ri.recv_id=r.recv_id
		JOIN item_kits as it ON it.item_kit_id=ri.item_id
		WHERE recv_person=-1 AND r.recv_id=? ORDER BY line ASC');
	if ($result = $this->db->select(array(array($id)))) {
		$ids = array();
		$kit_ids = array();
		foreach ($result as $row) {
			$ids[] = $row['item_id'];
			$kit_ids[] = array($row['item_id']);
		}
		
		$kit = item_kits::get_info($this->db, $kit_ids);
		$i = 0;
		foreach ($kit as $v) {
			$row = $result[$i];
			$v['item']['discount'] = 0;
			$row['total'] = $row['order_quantity'] * $v['item']['cost_price'];
			$total += $row['total'];
			$data[$row['line']] = array_merge($row, $v['item']);
			++$i;
		}
		
		$ipos->session->param(array('recive_kit'=>array($id=>$ids)));
	}
	ksort($data);
	
	if (isset($data[0])
		&& ($emp = employee::get_info($this->db, $data[0]['order_person'])) !== false) {
		$recv['date'] =  $data[0]['recv_date'];
		$recv['number'] =  $data[0]['invoice_number'];
		$recv['id'] =  $data[0]['recv_id'];
		$recv['emp'] = $emp[0]['first_name'] .' '. $emp[0]['last_name'];
		$recv['total'] = $total;
		$ipos->assign('items', $data);
		echo json_encode(array("success" => true, "msg" => $ipos->lang['recvs_msg_get'], "recv"=>$recv, "row"=>$ipos->fetch('receivings/receive_row.tpl')));
	} else {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['recvs_err_get']));
	}
}

public function recv(&$ipos) {
	if (!isset($_REQUEST['id']) || empty($_REQUEST['item'])) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['recvs_err_param']));
		return;
	}
		
	$ids = array();
	$qs = array();
	$item_delete = array();
	foreach ($_REQUEST['item'] as $k => $v) {
		$id = intval($k);
		$tmp = intval($v);
		if ($tmp > 0) {
			$ids[] = $id;
			$qs[$id] = $tmp;
		}
		else if ($tmp === 0) {
			$item_delete[] = $id;
		}
		else {
			echo json_encode(array("success" => false, "msg" => $ipos->lang['recvs_err_param']));
			return;
		}
	}
	
	$id = intval($_REQUEST['id']);
	$kit_ids = $ipos->session->usrdata('recive_kit');
	if (isset($kit_ids[$id])) {
		$kit_ids = array_diff($kit_ids[$id], $item_delete);
		$ids = array_diff($ids, $kit_ids);
	} else {
		$kit_ids = null;
	}
	
	$data = array('recv_person'=>$ipos->session->usrdata('person_id'));
	$recv_item = new recv_item($this->db);

	$this->db->beginTransaction();
	if ((isset($item_delete[0]) && $recv_item->delete($item_delete, $id) === false)
		|| $recv_item->update($kit_ids, $ids, $qs, $id) === false
		|| $this->update_table('recv', $data, $id) === false) {
		$this->db->rollBack();
		echo json_encode(array("success" => false, "msg" => $ipos->lang['recvs_err']));
		return;
	}
	
	$this->db->commit();
	echo json_encode(array("success" => true, "msg" => $ipos->lang['recvs_msg_recv']));
}

public function item(&$ipos) {
	if (!isset($_REQUEST['id'])) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['recvs_err_param']));
		return;
	}
	
	$var = filter_var($_REQUEST['id'], FILTER_SANITIZE_SPECIAL_CHARS);
	$var = explode(',', $_REQUEST['id']);
	$id = intval($var[0]);
	$is_kit = empty($var[1]) ? false:true;
	if ($is_kit) {
		$this->db->query('SELECT s.company_name as company_name FROM item_kits as it
					JOIN item_kit_items as iti ON it.item_kit_id=iti.item_kit_id
					JOIN items as i ON iti.item_id=i.item_id
					LEFT JOIN suppliers as s ON i.supplier_id=s.person_id
					WHERE it.item_kit_id='. $id);
		$company = $this->db->select();
	}
	
	$query = $is_kit ? 'SELECT ri.*, r.invoice_number, it.name as name, it.item_number as item_number, iq.quantity as quantity
		FROM recv_items as ri
		JOIN recv as r ON ri.recv_id=r.recv_id
		JOIN item_kits as it ON ri.item_id=it.item_kit_id
		JOIN item_quantities as iq ON it.item_kit_id=iq.item_id
		WHERE r.recv_person!=-1 AND ri.item_id='. $id .' ORDER BY ri.recv_id DESC'
		: 'SELECT ri.*, r.invoice_number, i.name as name, i.item_number as item_number, iq.quantity as quantity, s.company_name as company_name
		FROM recv_items as ri
		JOIN recv as r ON ri.recv_id=r.recv_id
		JOIN items as i ON ri.item_id=i.item_id
		JOIN item_quantities as iq ON i.item_id=iq.item_id
		LEFT JOIN suppliers as s ON i.supplier_id=s.person_id
		WHERE r.recv_person!=-1 AND ri.item_id='. $id .' ORDER BY ri.recv_id DESC';
	$this->db->query($query);
	if ($sel = $this->db->select()) {
		$total = 0;
		$ids = array();
		$result = array();
		foreach ($sel as $row) {
			if ($is_kit)  {
				$row['company_name'] = $company[0]['company_name'];
				$row['is_kit'] = 1;
			} else {
				$row['is_kit'] = 0;
			}
			
			$ids[] = $row['recv_id'] .'-'. $row['item_id'];
			if ($total + $row['recv_quantity'] >= $row['quantity'])  {
				$row['recv_quantity'] = $row['quantity'] - $total;
				$result[] = $row;
				break;
			}
			
			$result[] = $row;
			$total +=  $row['recv_quantity'];
		}
		
		$ipos->assign('items', $result);
		echo json_encode(array("success" => true, "msg" => $ipos->lang['recvs_msg_item'], "ids"=>$ids, "row"=>$ipos->fetch('receivings/return_row.tpl')));
	} else {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['recvs_err']));
	}
}

public function ret(&$ipos) {
	if (empty($_REQUEST['item'])) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['recvs_err_param']));
		return;
	}
	
	$kit = array();
	$itm = array();
	$data = array();
	foreach ($_REQUEST['item'] as $k => $v) {
		$tmp = explode('-', $k);
		$q = intval($v);
		if (count($tmp) !== 3 || $q <= 0) {
			echo json_encode(array("success" => false, "msg" => $ipos->lang['recvs_err_param']));
			return;
		}
		
		$is_kit = intval($tmp[2]);
		$item_id = intval($tmp[1]);
		$data[] = array($q, intval($tmp[0]),  $item_id,  $is_kit);		// quantity recv_id item_id is_kit
		$itm[] = array($q, $item_id);
		if ($is_kit === 1) {
			$kit[] = $item_id;
		}
	}
	
	if (!empty($kit)) {
		$kit_ids = array();
		$kit = array_unique($kit, SORT_NUMERIC);
		foreach ($kit as $v) {
			$kit_ids[] = array($v);
		}
		
		if (($items = item_kits::get_info($this->db, $kit_ids)) === false) {
			echo json_encode(array("success" => false, "msg" => $ipos->lang['recvs_err']));
			return;
		}
	}
	
	foreach ($data as &$v) {
		if ($v[3] !== 0) {
			foreach ($items as $it) {
				if ($v[2] == $it['item']['item_kit_id']) {
					foreach ($it['kit_items'] as $ki) {
						$itm[] = array($v[0]*$ki['quantity'], $ki['item_id']);
					}
					break;
				}
			}
		}
		
		unset($v[3]);
	}
	
	$recv_item = new recv_item($this->db);
	$this->db->beginTransaction();
	if ($recv_item->update_ret($data, $itm)) {
		$this->db->commit();
		echo json_encode(array("success" => true, "msg" => $ipos->lang['recvs_msg_ret']));
	} else {
		$this->db->rollBack();
		echo json_encode(array("success" => false, "msg" => $ipos->lang['recvs_err']));
	}
}

private function update_table($table, $data, $id, $key='recv_id') {
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

private function save_table($table, $data, $id, $key='recv_id') {
	$q1 = null;
	$tmp = array();
	foreach ($data as $k => $v) {
		$q1 .= ',' . $k;
		$tmp[] = $v;
	}
	
	$query ='INSERT INTO '. $table .' ('. $key . $q1 . ') VALUES('. $id . str_repeat(',?', count($data)) . ')';
	$this->db->query($query);
	return $this->db->insert(array($tmp));
}

private function maxid() {
	$this->db->query('SELECT MAX(recv_id) FROM recv');
	return $this->db->max();
}
}
?>