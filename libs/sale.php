<?php
require_once('../libs/secure.php');
require_once('../libs/item_tax.php');
require_once('../libs/customer.php');

class sale extends secure {
private $flt_pay = array('total' => FILTER_VALIDATE_FLOAT,
						'payment' => FILTER_VALIDATE_FLOAT,
						'suspend' => FILTER_VALIDATE_INT,
						'type' => FILTER_SANITIZE_SPECIAL_CHARS);


public function __construct($db, $grant, $permission) {
	$this->func_permission = array('view'=>'sales','sale'=>'sales_insert','pay'=>'sales_insert','suspend'=>'sales_insert','suspend_change'=>'sales_insert', 'calc'=>'sales_insert', 'return'=>'sales_delete', 'suggest'=>'sales_delete', 'search'=>'sales_delete');
	parent::__construct($db, $grant, $permission);
}

public function view(&$ipos) {
	$ipos->language(array('items','receivings'));
	
	$ipos->assign('controller_name', 'sales');
	$ipos->assign('subgrant', $this->subgrant);
	$ipos->display('sale/manage.tpl');
}

public function sale(&$ipos) {
	if (empty($_REQUEST['item'])) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['sales_err_param']));
		return;
	}
	
	$print = isset($_REQUEST['print']) ? 1 : 0;
	$customer = empty($_REQUEST['customer']) ? null : intval($_REQUEST['customer']);
	$comment = isset($_REQUEST['order_comment']) ? filter_var($_REQUEST['order_comment'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
	$discount = $customer === null ? 0 : customer::discount($customer);
	if ($discount === false) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['sales_err']));
		return;
	}
	
	$data = array();
	$data['sale_date'] = date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']);
	$data['emp_id'] = $ipos->session->usrdata('person_id');
	if ($customer !== null) $data['cm_id'] = $customer;
	if ($comment !== null) $data['comment'] = $comment;
	
	$config = $ipos->session->usrdata('config');
	$tax_included = empty($config['tax_included']) ? false : true;
	if ($tax_included && ($tax_name = $ipos->session->usrdata('tax_name')) === false) {
		if (($tax_name = $this->tax_name()) === false) {
			echo json_encode(array("success" => false, "msg" => $ipos->lang['sales_err']));
			return;
		}
		
		$ipos->session->param(array('tax_name'=>$tax_name));
	}
	
	$i = 0;
	$sum = 0;
	$total = 0;
	$ttotal = 0;
	$ids = array();
	$tax = array();
	$item = array();
	$sale_item = $ipos->session->usrdata('sale_item');
	$sale_item_kit = $ipos->session->usrdata('sale_item_kit');
	$sale_item_kit_info = $ipos->session->usrdata('sale_item_kit_info');
	foreach ($_REQUEST['item'] as $k => $val) {
		$id = intval($k);
		$quantity = intval($val);
		$sum += $quantity;
		if (isset($sale_item[$id])) {
			$cost = $sale_item[$id]['cost_price'];
			$uint = $sale_item[$id]['unit_price'];
			$cuint = $sale_item[$id]['unit_price'] * (1 - $discount / 100);
			$item[] = array('item_id'=>$id, 'line'=>$i, 'quantity'=>$quantity, 'cost_price'=>$cost, 'unit_price'=>$cuint);
			$in_arr = in_array($id, $ids);
			if ($tax_included && !empty($sale_item[$id]['tax_name']) && $in_arr === false) {
				$tmp = explode(item_tax::needle, $sale_item[$id]['tax_name']);
				foreach ($tmp as $v) {
					$tax[] = array('item_id'=>$id,'name'=>$v,'percent'=>(isset($tax_name[$v]) ? $tax_name[$v]['percent'] : 0));
				}
			}
			
			if ($in_arr === false) $ids[] = $id;
		} else if (isset($sale_item_kit[$id])) {
			$info = $sale_item_kit_info[$id];
			foreach ($info['kit_items'] as $ki) {
				$in_arr = in_array($ki['item_id'], $ids);
				if ($tax_included && !empty($ki['tax_name']) && $in_arr === false) {
					$tmp = explode(item_tax::needle, $ki['tax_name']);
					foreach ($tmp as $v) {
						$tax[] = array('item_id'=>$ki['item_id'],'name'=>$v,'percent'=>(isset($tax_name[$v]) ? $tax_name[$v]['percent'] : 0));
					}
				}
				
				$ttq = $quantity * $ki['quantity'];
				$item[] = array('item_id'=>$ki['item_id'], 'line'=>$i, 'quantity'=>$ttq, 'cost_price'=>$ki['cost_price'], 'unit_price'=>$ki['unit_price']);
			}
			
			$cuint = $uint = $sale_item_kit[$id]['unit_price'];
			if ($in_arr === false) $ids[] = $ki['item_id'];
		} else {
			echo json_encode(array("success" => false, "msg" => $ipos->lang['sales_err']));
			return;
		}
		
		
		$ttotal += $quantity * $cuint;
		$total += $quantity * $uint;
		++$i;
	}
	
	$pay_data['print'] = $print;
	$pay_data['item'] = $item;
	$pay_data['data'] = $data;
	$pay_data['total'] = $total;
	$pay_data['ttotal'] = $ttotal;
	$pay_data['sum'] = $sum;
	$pay_data['discount'] = $discount;
	if (!empty($tax)) $pay_data['tax'] = $tax;
	
	$ipos->session->param(array('pay_data'=>$pay_data));
	$ipos->assign('sale', $pay_data);
	echo json_encode(array("success" => true, "msg" => $ipos->lang['sales_msg_sale'], "data"=>$ipos->fetch('sale/form.tpl')));
}

public function pay(&$ipos) {
	$var = filter_var_array($_REQUEST, $this->flt_pay);
	$pay = $ipos->session->usrdata('pay_data');
	if ($pay['ttotal'] != $var['total']
		|| $var['payment'] < $pay['ttotal']
		|| ($maxid = $this->maxid()) === false) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['sales_err_param']));
		return;
	}
	++$maxid;
	
	$type['payment_type'] = $var['type'];
	$type['payment_amount'] = $pay['ttotal'];
	
	// item_quantities
	foreach ($pay['item'] as $v) {
		if (isset($item[$v['item_id']])) {
			$itq = $item[$v['item_id']][0] + $v['quantity'];
			$item[$v['item_id']] =array($itq, $v['item_id']);
		} else {
			$ids[] = array($v['item_id']);
			$item[$v['item_id']] =array($v['quantity'], $v['item_id']);
		}
	}
	
	
	$invoice_number = date("Ymd", $_SERVER['REQUEST_TIME']) .'-'. $pay['data']['emp_id'] .'-'. $maxid;
	$pay['data']['invoice_number'] = $invoice_number;
	$this->db->beginTransaction();
	if ($this->item_update($ids, $item) === false
		|| $this->save_table('sales', array($pay['data']), $maxid) === false
		|| $this->save_table('sale_items', $pay['item'], $maxid) === false
		|| (isset($pay['tax']) && ($this->save_table('sale_item_tax', $pay['tax'], $maxid) === false))
		|| $this->save_table('sale_payments', array($type), $maxid) === false) {
		$this->db->rollBack();
		echo json_encode(array("success" => false, "msg" => $ipos->lang['sales_err_pay']));
		return;
	}
	$this->db->commit();
	
	if ($var['suspend'] > -1 && ($suspend_id = $ipos->session->usrdata('sale_suspend_id')) !== false) {
		$name = $this->table_name($ipos->session->usrdata('person_id'));
		$this->db->query('DELETE FROM '. $name .' WHERE id='. $suspend_id);
		$this->db->execute();
	}
	
	$pay['payment'] = $var['payment'];
	$ipos->assign('sale', $pay);
	$ipos->session->del(array('sale_item', 'pay_data', 'sale_item_kit', 'sale_item_kit_info', 'sale_suspend_id'));
	echo json_encode(array("success" => true, "msg" => $ipos->lang['sales_msg_pay'], "data"=>$ipos->fetch('sale/change.tpl')));
}

public function suspend(&$ipos) {
	if (!isset($_REQUEST['suspend'])) return;
	
	$id = intval($_REQUEST['suspend']);
	
	$print = isset($_REQUEST['print']) ? 1 : 0;
	$customer = empty($_REQUEST['customer']) ? null : intval($_REQUEST['customer']);
	$comment = isset($_REQUEST['order_comment']) ? filter_var($_REQUEST['order_comment'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
	
	foreach ($_REQUEST['item'] as $k => $val) {
		$item[intval($k)] = intval($val);
	}
	
	$sale_item = $ipos->session->usrdata('sale_item');
	$sale_item_kit = $ipos->session->usrdata('sale_item_kit');
	$sale_item_kit_info = $ipos->session->usrdata('sale_item_kit_info');
	
	$suspend = array('print'=>$print,'customer'=>$customer,'comment'=>$comment,'item'=>$item,'sale_item'=>$sale_item,'sale_item_kit'=>$sale_item_kit,'sale_item_kit_info'=>$sale_item_kit_info);
	$name = $this->table_name($ipos->session->usrdata('person_id'));
	$this->db->query('SELECT val FROM '. $name .' WHERE id='. $id);
	if ($id < 0 || empty($this->db->select())) {
		$this->db->query('INSERT INTO '. $name .' (val) VALUES(?)');
		if ($this->db->insert(array(array(serialize($suspend)))) === false) {
			echo json_encode(array("success" => false, "msg" => $ipos->lang['sales_err_suspend_table']));
			return;
		}
	} else {
		$this->db->query('UPDATE '. $name .' SET val=? WHERE id=?');
		if ($this->db->update(array(array(serialize($suspend), $id))) === false) {
			echo json_encode(array("success" => false, "msg" => $ipos->lang['sales_err_suspend_table']));
			return;
		}
	}
	
	$ipos->session->del(array('sale_item', 'sale_item_kit', 'sale_item_kit_info', 'pay_data'));
	echo json_encode(array("success" => true, "msg" => $ipos->lang['sales_msg_suspend']));
}

public function suspend_change(&$ipos) {
	if (!isset($_REQUEST['suspend'])) return;
	
	$id = intval($_REQUEST['suspend']);
	$name = $this->table_name($ipos->session->usrdata('person_id'));
	$this->db->query('SELECT id,val FROM '. $name .' ORDER BY id DESC LIMIT '. $id .',1');
	if ($id < 0 || empty($result = $this->db->select())) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['common_no_more_data']));
		return;
	}
	$suspend = unserialize($result[0]['val']);
	$suspend_id = $result[0]['id'];
	
	$print = $suspend['print'];
	$customer = $suspend['customer'];
	$comment = $suspend['comment'];
	$item = $suspend['item'];
	$sale_item = $suspend['sale_item'];
	$sale_item_kit = $suspend['sale_item_kit'];
	$sale_item_kit_info = $suspend['sale_item_kit_info'];
	
	foreach ($item as $k => $v) {
		if (isset($sale_item[$k])) {
			$sale_item[$k]['sale_quantity'] = $v;
			$items[] = $sale_item[$k];
		} else if (isset($sale_item_kit[$k])) {
			$sale_item_kit[$k]['sale_quantity'] = $v;
			$items[] = $sale_item_kit[$k];
		} else {
			$ipos->session->del(array('sale_suspend'));
			echo json_encode(array("success" => false, "msg" => $ipos->lang['sales_err_suspend_data']));
			return;
		}
	}
	
	$ipos->session->param(array('sale_item'=>$sale_item, 'sale_item_kit'=>$sale_item_kit, 'sale_item_kit_info'=>$sale_item_kit_info, 'sale_suspend_id'=>
	$suspend_id));
	$ipos->assign('items', $items);
	echo json_encode(array('success' => true, 'id'=>$id, 'print'=>$print, 'customer'=>$customer, 'comment'=>$comment, 'data'=>$ipos->fetch('sale/table_row.tpl')));
}

public function calc(&$ipos) {
	$query = 'SELECT SUM(payment_amount) FROM sale_payments as sp
				JOIN sales as s ON sp.sale_id=s.sale_id
				WHERE DATE(s.sale_date)=CURDATE() AND s.emp_id='. $ipos->session->usrdata('person_id');
	$this->db->query($query);
	if (($total = $this->db->select()) !== false) {
		foreach ($ipos->lang['sales_payment_type'] as $k => $v) {
			$q = $query . ' AND sp.payment_type=?';
			$this->db->query($q);
			if (($result = $this->db->select(array(array($k)))) === false) {
				echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['sales_err_calc']));
				return;
			}
			
			$sub[$k] = isset($result[0]['SUM(payment_amount)']) ? $result[0]['SUM(payment_amount)'] : 0;
		}
		
		// clear sale_suspend_* table
		$name = $this->table_name($ipos->session->usrdata('person_id'));
		$this->db->query('DELETE FROM '. $name);
		$this->db->execute();
		
		$ipos->assign('total', isset($total[0]['SUM(payment_amount)']) ? $total[0]['SUM(payment_amount)'] : 0);
		$ipos->assign('sub', $sub);
		echo json_encode(array('success'=>true, 'data'=>$ipos->fetch('sale/calc.tpl')));
		return;
	}
	
	echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['sales_err_calc']));
}

public function ret(&$ipos) {
	if (empty($_REQUEST['item']) || empty($_REQUEST['sale_id'])) {
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['sales_err_param']));
		return;
	}
	
	$id = intval($_REQUEST['sale_id']);
	foreach ($_REQUEST['item'] as $k => $v) {
		$q = intval($v);
		$tmp = explode('-', $k);
		if (count($tmp) !== 2) return;
		
		$item[] = array($q, intval($tmp[0]), intval($tmp[1]));
		$inf[] = array(intval($tmp[0]), intval($tmp[1]));
		$itemq[] = array($q, intval($tmp[0]));
	}
	
	$this->db->query('SELECT quantity FROM sale_items WHERE item_id=? AND line=? AND sale_id='. $id);
	if (($result = $this->db->select($inf)) === false && count($result) != count($inf)) {
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['sales_err_ret']));
		return;
	}
	
	$i = 0;
	foreach ($item as &$v) {
		if (($v[0] = $result[$i]['quantity'] - $v[0]) < 0) {
			echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['sales_err_ret']));
			return;
		}
		
		++$i;
	}
	
	$this->db->beginTransaction();
	$this->db->query('UPDATE sale_items SET quantity=? WHERE item_id=? AND line=? AND sale_id='. $id);
	if ($this->db->update($item) && $this->ret_item($itemq)) {
		$this->db->commit();
		echo json_encode(array('success'=>true, 'msg'=>$ipos->lang['sales_msg_ret']));
	} else {
		$this->db->rollBack();
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['sales_err_ret']));
	}
}

public function suggest() {
	if (!isset($_REQUEST['term']) || empty($var = filter_var($_REQUEST['term'], FILTER_SANITIZE_SPECIAL_CHARS)))
		return;
	
	$var = '%'. $var .'%';
	$this->db->query('SELECT invoice_number FROM sales WHERE invoice_number LIKE ?');
	if (!empty($result = $this->db->select(array(array($var))))) {
		$suggestions = array();
		foreach ($result as $v) {
			$suggestions[] = array('label'=>$v['invoice_number']);
		}
	
		echo json_encode($suggestions);
	}
}

public function search(&$ipos) {
	if (!isset($_REQUEST['term']) || empty($var = filter_var($_REQUEST['term'], FILTER_SANITIZE_SPECIAL_CHARS)))
		return;
	
	$this->db->query('SELECT si.*, i.item_number as item_number, i.name as name FROM sales as s 
				JOIN sale_items as si ON s.sale_id=si.sale_id
				JOIN items as i ON si.item_id=i.item_id
				WHERE s.invoice_number=? ORDER BY si.line ASC');
	if (!empty($result = $this->db->select(array(array($var))))) {
		$id = $result[0]['sale_id'];
		$ipos->assign('items', $result);
		echo json_encode(array('success'=>true, 'id'=>$id, 'data'=>$ipos->fetch('sale/return_row.tpl')));
	}
}

public static function table_name($id) {
	return 'sale_suspend_'. $id;
}

private function save_table($table, $data, $id, $key='sale_id') {
	$q1 = null;
	foreach ($data[0] as $k => $v) {
		$q1 .= ',' . $k;
	}
	
	$query ='INSERT INTO '. $table .' ('. $key . $q1 . ') VALUES('. $id . str_repeat(',?', count($data[0])) . ')';
	$this->db->query($query);
	return $this->db->insert($data);
}

private function ret_item($item) {
	$this->db->query('UPDATE item_quantities SET quantity=quantity+? WHERE item_id=?');
	return $this->db->update($item);
}

private function item_update($ids, $item) {
	$this->db->query('SELECT quantity FROM item_quantities WHERE item_id=?');
	if (($quantity = $this->db->select($ids)) === false) return false;
	
	$i = 0;
	foreach ($item as &$v) {
		if (($v[0] = $quantity[$i++]['quantity'] - $v[0]) < 0) return false;
	}
	
	$this->db->query('UPDATE item_quantities SET quantity=? WHERE item_id=?');
	return $this->db->update($item);
}

private function maxid() {
	$this->db->query('SELECT MAX(sale_id) FROM sales');
	return $this->db->max();
}

private function tax_name() {
	$tax = new item_tax($this->db);
	return $tax->get_all();
}
}
?>