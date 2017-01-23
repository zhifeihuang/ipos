<?php
require_once('../libs/secure.php');

class report extends secure {
private $flt = array('start_date'=>FILTER_SANITIZE_SPECIAL_CHARS, 'end_date'=>FILTER_SANITIZE_SPECIAL_CHARS, 'supplier'=>FILTER_VALIDATE_INT, 'employee'=>FILTER_VALIDATE_INT);

public function __construct($db, $grant, $permission) {
	$this->func_permission = array('view'=>'reports', 'category'=>'reports_categories', 'supplier'=>'reports_suppliers', 'payment'=>'reports_payments', 'giftcard'=>'reports_giftcard');
	parent::__construct($db, $grant, $permission);
}

public function view(&$ipos) {
	if (isset($_REQUEST['get'])) {
		if (parent::has_grant($_REQUEST['get'])) {
			switch ($_REQUEST['get']) {
				case 'category': $tpl = 'report/category.tpl'; break;
				case 'supplier': $tpl = 'report/supplier.tpl'; break;
				case 'payment': $tpl = 'report/payment.tpl'; break;
				case 'giftcard': $tpl = 'report/giftcard.tpl'; break;
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
	
	$ipos->assign('controller_name', 'reports');
	$ipos->assign('subgrant', $this->subgrant);
	echo $ipos->display('report/manage.tpl');
}

public function category(&$ipos) {
	$var = filter_var_array($_REQUEST, $this->flt);
	$start = empty($var['start_date']) ? date('Y-m-d') : $var['start_date'];
	$end = empty($var['end_date']) ? date('Y-m-d') : $var['end_date'];
	
	$this->sale_table();
	$data = $this->category_data($start, $end);
	$ipos->assign('items', $data[0]);
	$ipos->assign('total', $data[1]);
	$ipos->assign('ptotal', $data[2]);
	
	echo json_encode(array('success'=>true, 'data'=>$ipos->fetch('report/category_row.tpl')));
}

public function supplier(&$ipos) {
	$var = filter_var_array($_REQUEST, $this->flt);
	$start = empty($var['start_date']) ? date('Y-m-d') : $var['start_date'];
	$end = empty($var['end_date']) ? date('Y-m-d') : $var['end_date'];
	
	$this->sale_table();
	$this->recv_table();
	$data = $this->supplier_data($start, $end, $var['supplier']);
		
	$ipos->assign('items', $data[0]);
	$ipos->assign('total', $data[1]);
	$ipos->assign('ptotal', $data[2]);
	$ipos->assign('rtotal', $data[3]);
	
	echo json_encode(array('success'=>true, 'data'=>$ipos->fetch(empty($var['supplier']) ? 'report/supplier_row.tpl' : 'report/supp_row.tpl')));
}

public function payment(&$ipos) {
	$ipos->language(array('sales'));
	$var = filter_var_array($_REQUEST, $this->flt);
	$start = empty($var['start_date']) ? date('Y-m-d') : $var['start_date'];
	$end = empty($var['end_date']) ? date('Y-m-d') : $var['end_date'];
	$data = $this->payment_data($start, $end, $var['employee']);
		
	$ipos->assign('items', $data[0]);
	$ipos->assign('total', $data[1]);
	
	echo json_encode(array('success'=>true, 'data'=>$ipos->fetch(empty($var['employee']) ? 'report/payment_row.tpl' : 'report/pay_row.tpl')));
}

public function giftcard(&$ipos) {
	$var = filter_var_array($_REQUEST, $this->flt);
	$start = empty($var['start_date']) ? date('Y-m-d') : $var['start_date'];
	$end = empty($var['end_date']) ? date('Y-m-d') : $var['end_date'];
	$data = $this->giftcard_data($start, $end, $var['employee']);
		
	$ipos->assign('items', $data[0]);
	$ipos->assign('total', $data[1]);
	
	echo json_encode(array('success'=>true, 'data'=>$ipos->fetch(empty($var['employee']) ? 'report/giftcard_row.tpl' : 'report/gift_row.tpl')));
}

private function payment_data($start, $end, $emp) {
	if (empty($emp)) {
		$this->db->query('SELECT SUM(sp.payment_amount) as subtotal, s.emp_id, CONCAT(p.first_name," ",p.last_name) as employee
										FROM sales AS s
										JOIN sale_payments AS sp ON sp.sale_id=s.sale_id
										JOIN person as p ON p.person_id=s.emp_id
										WHERE DATE(s.sale_date) BETWEEN ? AND ?
										GROUP BY s.emp_id ORDER BY s.emp_id');
		if ($result = $this->db->select(array(array($start, $end)))) {
			$total = 0;
			foreach ($result as $v) {
				$total += $v['subtotal'];
			}
			
			return array($result, $total);
		}
	} else {
		$this->db->query('SELECT s.sale_id, s.invoice_number, sp.payment_amount as subtotal, sp.payment_type, CONCAT(p.first_name," ",p.last_name) as employee
									FROM sale_payments AS sp
									JOIN sales AS s ON s.sale_id=sp.sale_id
									JOIN person as p ON p.person_id=s.emp_id
									WHERE s.emp_id=? AND DATE(s.sale_date) BETWEEN ? AND ? ORDER BY s.sale_id');
		if (($result = $this->db->select(array(array($emp, $start, $end)))) !== false) {
			$total = 0;
			foreach ($result as $v) {
				$total += $v['subtotal'];
			}
		
			return array($result, $total);
		}
	}
		
	return array(null, 0);
}

private function supplier_data($start, $end, $supplier) {
	if (empty($supplier)) {
		$this->db->query('CREATE TEMPORARY TABLE IF NOT EXISTS report_supplier_recv
				(SELECT sl.company_name as supplier, rit.supplier_id, SUM(rit.recv_quantity) as quantity, SUM(rit.cost) as cost
				FROM recv_items_temp as rit
                LEFT JOIN suppliers as sl ON rit.supplier_id=sl.person_id
				WHERE DATE(rit.recv_date) BETWEEN ? AND ?
				GROUP BY rit.supplier_id ORDER BY rit.supplier_id)');
		if ($this->db->execute(array($start, $end)) === false) return false;
		
		$this->db->query('CREATE TEMPORARY TABLE IF NOT EXISTS report_supplier_sale
				(SELECT SUM(sit.subtotal) as subtotal, sit.supplier_id
				FROM sale_items_temp as sit
				WHERE DATE(sit.sale_date) BETWEEN ? AND ?
				GROUP BY sit.supplier_id ORDER BY sit.supplier_id)');
		if ($this->db->execute(array($start, $end)) === false) return false;
		
		$this->db->query('SELECT rsr.*, rss.subtotal, (rss.subtotal - rsr.cost) as profit
				FROM report_supplier_recv as rsr
				LEFT JOIN report_supplier_sale as rss ON rsr.supplier_id=rss.supplier_id');
		if ($result = $this->db->select()) {
			$total = 0;
			$ptotal = 0;
			$rtotal = 0;
			foreach ($result as $v) {
				$total += $v['subtotal'];
				$ptotal += $v['profit'];
				$rtotal += $v['cost'];
			}
			
			return array($result, $total, $ptotal, $rtotal);
		}
	} else {
		$this->db->query('CREATE TEMPORARY TABLE IF NOT EXISTS report_supplier_recv
				(SELECT sl.company_name as supplier, rit.supplier_id, rit.item_id, rit.item_number, rit.name, rit.recv_date, SUM(rit.recv_quantity) as quantity, SUM(rit.cost) as cost, iq.quantity as stock
				FROM recv_items_temp as rit
				JOIN item_quantities as iq ON rit.item_id=iq.item_id
                LEFT JOIN suppliers as sl ON rit.supplier_id=sl.person_id
				WHERE rit.supplier_id=? AND DATE(rit.recv_date) BETWEEN ? AND ?
				GROUP BY rit.item_id ORDER BY rit.item_id)');
		if ($this->db->execute(array($supplier, $start, $end)) === false) return false;
		
		$this->db->query('CREATE TEMPORARY TABLE IF NOT EXISTS report_supplier_sale
				(SELECT SUM(sit.subtotal) as subtotal, sit.supplier_id
				FROM sale_items_temp as sit
				WHERE sit.supplier_id=? AND DATE(sit.sale_date) BETWEEN ? AND ?
				GROUP BY sit.item_id ORDER BY sit.item_id)');
		if ($this->db->execute(array($supplier, $start, $end)) === false) return false;
		
		$this->db->query('SELECT rsr.*, rss.subtotal, (rss.subtotal - rsr.cost) as profit
				FROM report_supplier_recv as rsr
				LEFT JOIN report_supplier_sale as rss ON rsr.supplier_id=rss.supplier_id
				GROUP BY rsr.item_id ORDER BY rsr.item_id');
		if (($result = $this->db->select()) !== false) {
			$total = 0;
			$ptotal = 0;
			$rtotal = 0;
			foreach ($result as $v) {
				$total += $v['subtotal'];
				$ptotal += $v['profit'];
				$rtotal += $v['cost'];
			}
		
			return array($result, $total, $ptotal, $rtotal);
		}
	}
		
	return array(null, 0, 0, 0);
}

private function category_data($start, $end) {
	$this->db->query('SELECT category, SUM(quantity) as quantity, SUM(subtotal) as subtotal, SUM(profit) as profit FROM sale_items_temp
	WHERE DATE(sale_date) BETWEEN ? AND ?
	GROUP BY category ORDER BY category');
	if (($result = $this->db->select(array(array($start, $end)))) !== false) {
		$total = 0;
		$ptotal = 0;
		foreach ($result as $v) {
			$total += $v['subtotal'];
			$ptotal += $v['profit'];
		}
		
		return array($result, $total, $ptotal);
	}
	
	return array(null, 0, 0);
}

private function giftcard_data($start, $end, $emp) {
	if (empty($emp)) {
		$this->db->query('SELECT g.*, SUM(g.val) as subtotal, CONCAT(first_name," ",last_name) as employee FROM giftcard_charge as g
					JOIN person as p ON p.person_id=g.emp_id
					WHERE DATE(g.record_time) BETWEEN ? AND ?
					GROUP BY g.emp_id ORDER BY g.emp_id');
		if (($result = $this->db->select(array(array($start, $end)))) !== false) {
			$total = 0;
			foreach ($result as $v) {
				$total += $v['subtotal'];
			}
		
			return array($result, $total);
		}
	} else {
		$this->db->query('SELECT g.*, gs.giftcard_number FROM giftcard_charge as g
					JOIN giftcards as gs ON gs.giftcard_id=g.giftcard_id
					WHERE g.emp_id=? AND DATE(g.record_time) BETWEEN ? AND ? ORDER BY g.giftcard_id');
		if (($result = $this->db->select(array(array($emp, $start, $end)))) !== false) {
			$total = 0;
			foreach ($result as $v) {
				$total += $v['val'];
			}
		
			return array($result, $total);
		}
	}
	
	return array(null, 0);
}

private function sale_table() {
	$query = 'CREATE TEMPORARY TABLE IF NOT EXISTS sale_items_temp
                SELECT DATE(s.sale_date) as sale_date, s.sale_id as sale_id, s.comment as comment, s.invoice_number as invoice_number, s.emp_id as emp_id, si.cost_price as cost_price, si.unit_price as unit_price, si.quantity as quantity,
                i.item_id, i.supplier_id, i.category,
                (si.unit_price * si.quantity) as subtotal,
                si.line as line, si.description as description,
                (si.unit_price * si.quantity - si.cost_price * si.quantity) as profit,
                (si.cost_price * si.quantity) as cost
                FROM sale_items as si
                INNER JOIN sales as s ON si.sale_id=s.sale_id
                INNER JOIN items as i ON si.item_id=i.item_id
                GROUP BY s.sale_id, si.item_id, si.line
				UNION
				SELECT DATE(s.sale_date) as sale_date, s.sale_id as sale_id, s.comment as comment, s.invoice_number as invoice_number, s.emp_id as emp_id, si.cost_price as cost_price, si.unit_price as unit_price, si.quantity as quantity,
				itk.item_kit_id as item_id, itk.supplier_id , itk.category,
                (si.unit_price * si.quantity) as subtotal,
                si.line as line, si.description as description,
                (si.unit_price * si.quantity - si.cost_price * si.quantity) as profit,
                (si.cost_price * si.quantity) as cost
                FROM sale_items as si
                INNER JOIN sales as s ON si.sale_id=s.sale_id
				INNER JOIN (SELECT distinct(it.item_kit_id), i.category, i.supplier_id FROM item_kits as it 
							JOIN item_kit_items as iti ON it.item_kit_id=iti.item_kit_id
							JOIN items as i ON iti.item_id=i.item_id) AS itk ON si.item_id=itk.item_kit_id
                GROUP BY s.sale_id, si.item_id, si.line';
	$this->db->query($query);
	$this->db->execute();
}

private function recv_table() {
	$query = 'CREATE TEMPORARY TABLE IF NOT EXISTS recv_items_temp
                SELECT DATE(r.recv_date) as recv_date, r.comment as comment, r.invoice_number as invoice_number, r.recv_person as recv_person, ri.recv_id, ri.recv_quantity, ri.line as line, ri.cost_price,
                i.item_id, i.supplier_id, i.item_number, i.name,
                (ri.cost_price*ri.recv_quantity*(1 - ri.discount/100)) as cost
                FROM recv_items as ri
                INNER JOIN recv as r ON  ri.recv_id=r.recv_id
                INNER JOIN items as i ON  ri.item_id=i.item_id
                GROUP BY r.recv_id, ri.item_id, ri.line
				UNION
				SELECT DATE(r.recv_date) as recv_date, r.comment as comment, r.invoice_number as invoice_number, r.recv_person as recv_person, ri.recv_id, ri.recv_quantity, ri.line as line, ri.cost_price,
                itk.item_kit_id as item_id, itk.supplier_id, itk.item_number, itk.name,
                (ri.cost_price*ri.recv_quantity*(1 - ri.discount/100)) as cost
                FROM recv_items as ri
                INNER JOIN recv as r ON  ri.recv_id=r.recv_id
				INNER JOIN (SELECT distinct(it.item_kit_id), it.item_number, it.name, i.supplier_id FROM item_kits as it 
							JOIN item_kit_items as iti ON it.item_kit_id=iti.item_kit_id
							JOIN items as i ON iti.item_id=i.item_id) AS itk ON ri.item_id=itk.item_kit_id
                GROUP BY r.recv_id, ri.item_id, ri.line';
	$this->db->query($query);
	$this->db->execute();
}
}
?>