<?php
class recv_item {
private $db;

public function __construct($db) {
	$this->db = $db;
}

public function update($ids, $qs, $id) {
	if (empty($items = $this->get_info($ids))) return false;
	
	$tmp = array();
	$recv = array();
	$itm = array();
	$i = 0;
	foreach ($ids as $v) {
		$tmp['recv_quantity'] = $qs[$i];
		$tmp['cost_price'] = $items[$v]['cost_price'];
		$tmp['discount'] = $items[$v]['cost_discount'];
		$recv[] = $tmp;
		$itm[] = array($qs[$i] + $items[$v]['quantity'], $v);
		++$i;
	}
	
	if ($this->update_table($recv, $id, $ids) === false
		|| $this->update_item($itm) === false)
		return false;

	return true;
}

public function update_ret($data) {
	if (($items = $this->get_ret($data)) === false) return false;
	
	$this->db->query('UPDATE recv_items SET recv_quantity=? WHERE recv_id=? AND item_id=?');
	return $this->db->update($items[0]) && $this->update_item($items[1]);
}

public function save($data, $id) {
	$q1 = null;
	foreach ($data[0] as $k => $v) {
		$q1 .= ',' . $k;
	}
	
	$tmp = array();
	foreach ($data as $v) {
		$tmp[] = array_values($v);
	}
	
	$query = 'INSERT INTO recv_items (recv_id'. $q1 . ') VALUES('. $id . str_repeat(',?', count($data[0])) . ')';
	$this->db->query($query);
	return $this->db->insert($tmp);
}

public function delete($data, $id) {
	$this->db->query('DELETE FROM recv_items WHERE recv_id='. $id .' AND item_id=?');
	return $this->db->delete($data);
}

private function update_table($data, $id, $item_id) {
	$tmp = array();
	$i = 0;
	foreach ($data as $d) {
		$c = array_values($d);
		$c[] = $id;
		$c[] = $item_id[$i++];
		$tmp[] = $c;
	}
	
	$str = null;
	foreach ($data[0] as $k => $v) {
		$str .= $k .'=?,';
	}
	
	$this->db->query('UPDATE recv_items SET '. rtrim($str, ',') .' WHERE recv_id=? AND item_id=?');
	return $this->db->update($tmp);
}

private function update_item($data) {
	$this->db->query('UPDATE item_quantities SET quantity=? WHERE item_id=?');
	return $this->db->update($data);
}

private function get_info($ids) {
	$data = array();
	foreach ($ids as $v) {
		$data[] = array($v);
	}
	$this->db->query('SELECT i.*, iq.quantity as quantity FROM items as i 
		LEFT JOIN suppliers as s ON s.person_id=i.supplier_id 
		JOIN item_quantities as iq ON iq.item_id=i.item_id 
		WHERE s.deleted=0 AND i.deleted=0 AND i.item_id=?');
	if ($sel = $this->db->select($data)) {
		$result = array();
		foreach ($sel as $row)
			$result[$row['item_id']] = $row;
			
		return $result;
	} else {
		return false;
	}
}

private function get_ret(&$data) {
	$item = array();
	$recv = array();
	$tmp = array();
	foreach ($data as $k => $v) {
		foreach ($v as $r) {
			$tmp[] = array($r[0], $k);
			$recv[] = array($r[1], $r[0], $k);
		}
	}
	
	$this->db->query('SELECT ri.*, iq.quantity as quantity FROM recv_items as ri
		JOIN item_quantities as iq ON iq.item_id=ri.item_id
		WHERE ri.recv_id=? AND ri.item_id=?');
	if ($sel = $this->db->select($tmp)) {
		if (count($sel) !== count($tmp)) return false;
		
		$i = 0;
		$total = 0;
		$item_id = -1;
		foreach ($recv as &$v) {
			if ($item_id !== $v[2]) {
				if ($item_id !== -1) {
					if ($total > $sel[$i - 1]['quantity']) return false;
					
					$item[] = array($sel[$i - 1]['quantity'] - $total, $item_id);
				}
				
				$total = 0;
				$item_id = $v[2];
			}
			
			$total += $v[0];
			$v[0] = $sel[$i]['recv_quantity'] - $v[0];
			if ($v[0] < 0) return false;
			
			++$i;
		}
		
		$item[] = array($sel[$i - 1]['quantity'] - $total, $item_id);
	} else {
		return false;
	}
	
	return array($recv, $item);
}
}
?>