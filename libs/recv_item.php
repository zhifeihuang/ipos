<?php
require_once '../libs/item_kits.php';

class recv_item {
private $db;

public function __construct($db) {
	$this->db = $db;
}

public function update($kits, $ids, $qs, $id) {
	if (!empty($ids) && empty($items = $this->get_info($ids))) return false;
	
	$recv = array();
	$itm = array();
	foreach ($ids as $v) {
		$recv[] = array($qs[$v], $items[$v]['cost_price'], $items[$v]['cost_discount'], $id, $v);
		$itm[] = array($qs[$v], $v);
	}
	
	$kit_itm = array();
	if (!empty($kits)) {
		$kit_ids = array();
		foreach ($kits as $v) {
			$kit_ids[] = array($v);
			$kit_itm[] = array($qs[$v], $v);
		}
		
		$items = item_kits::get_info($this->db, $kit_ids);
		foreach($items as $v) {
			$itm_kit_id = $v['item']['item_kit_id'];
			$recv[] = array($qs[$itm_kit_id], $v['item']['cost_price'], 0, $id, $itm_kit_id);
			foreach($v['kit_items'] as $kt) {
				$itm[] = array($qs[$itm_kit_id]*$kt['quantity'], $kt['item_id']);
			}
		}
	}
	
	return $this->update_table($recv) && $this->update_item($itm, '+') && (empty($kit_itm) || $this->update_item($kit_itm, '*0+'));
}

public function update_ret($data, $itm) {
	$this->db->query('UPDATE recv_items SET recv_quantity=recv_quantity-? WHERE recv_id=? AND item_id=?');
	return $this->db->update($data) && $this->update_item($itm, '-');
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
	$ids = array();
	foreach ($data as $v) {
		$ids[] = array($v);
	}
	
	$this->db->query('DELETE FROM recv_items WHERE recv_id='. $id .' AND item_id=?');
	return $this->db->delete($ids);
}

private function update_table($data) {
	$this->db->query('UPDATE recv_items SET recv_quantity=?,cost_price=?,discount=? WHERE recv_id=? AND item_id=?');
	return $this->db->update($data);
}

private function update_item($data, $sign) {
	$this->db->query('UPDATE item_quantities SET quantity=quantity'. $sign .'? WHERE item_id=?');
	return $this->db->update($data);
}

private function get_info($ids) {
	$data = array();
	foreach ($ids as $v) {
		$data[] = array($v);
	}
	$this->db->query('SELECT i.* FROM items as i 
		LEFT JOIN suppliers as s ON s.person_id=i.supplier_id 
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
}
?>