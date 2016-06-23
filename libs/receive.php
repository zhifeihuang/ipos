<?php
require_once '../libs/secure.php';
require_once '../libs/employee.php';
require_once '../libs/recv_item.php';

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
	$ipos->assign('controller_name', 'receivings');
	$ipos->assign('emp_id', $ipos->session->usrdata('person_id'));
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
	if ($result = $this->db->select(array(array($id)))) {
		foreach ($result as &$row) {
			$row['total'] = $row['order_quantity'] * $row['cost_price'] * (1 - $row['discount'] / 100);
			$total += $row['total'];
		}
	}
	
	if (isset($result[0])
		&& ($emp = employee::get_info($this->db, $result[0]['order_person'])) !== false) {
		$recv['date'] =  $result[0]['recv_date'];
		$recv['number'] =  $result[0]['invoice_number'];
		$recv['id'] =  $result[0]['recv_id'];
		$recv['emp'] = $emp[0]['first_name'] .' '. $emp[0]['last_name'];
		$recv['total'] = $total;
		$ipos->assign('items', $result);
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
		$tmp = intval($v);
		if ($tmp > 0) {
			$ids[] = intval($k);
			$qs[] = $tmp;
		}
		else if ($tmp === 0) {
			$item_delete[] = $k;
		}
		else {
			echo json_encode(array("success" => false, "msg" => $ipos->lang['recvs_err_param']));
			return;
		}
	}
	$id = intval($_REQUEST['id']);
	$data = array('recv_person'=>$ipos->session->usrdata('person_id'));
	$recv_item = new recv_item($this->db);

	$this->db->beginTransaction();
	if ((isset($item_delete[0]) && $recv_item->delete($item_delete, $id) === false)
		|| (isset($ids[0]) && $recv_item->update($ids, $qs, $id) === false)
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
	
	$id = intval($_REQUEST['id']);
	$this->db->query('SELECT ri.*, r.invoice_number, i.name as name, i.item_number as item_number, iq.quantity as quantity, s.company_name as company_name 
		FROM recv_items as ri
		JOIN recv as r ON ri.recv_id=r.recv_id
		JOIN items as i ON ri.item_id=i.item_id
		JOIN item_quantities as iq ON i.item_id=iq.item_id
		LEFT JOIN suppliers as s ON i.supplier_id=s.person_id
		WHERE ri.item_id='. $id .' ORDER BY ri.recv_id DESC');
	if ($sel = $this->db->select()) {
		$total = 0;
		$ids = array();
		$result = array();
		foreach ($sel as &$row) {
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
	
	$data = array();
	foreach ($_REQUEST['item'] as $k => $v) {
		$tmp = explode('-', $k);
		if (count($tmp) !== 2) {
			echo json_encode(array("success" => false, "msg" => $ipos->lang['recvs_err_param']));
			return;
		}
			
		$data[intval($tmp[1])][] = array(intval($tmp[0]), intval($v));
	}
	
	$recv_item = new recv_item($this->db);
	$this->db->beginTransaction();
	if ($recv_item->update_ret($data)) {
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