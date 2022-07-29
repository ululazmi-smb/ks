<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Satuan_produk_model extends CI_Model {

	private $table = 'satuan_produk';

	public function create($data)
	{
		return $this->db->insert($this->table, $data);
	}

	public function read()
	{
		return $this->db->get($this->table);
	}

	public function update($id, $data)
	{
		$this->db->where('id', $id);
		return $this->db->update($this->table, $data);
	}

	public function delete($id)
	{
		// $db = $this->db->where('id', $id);
		$db2 = $this->db->get_where("produk", array('satuan', $id));
		if($db2->num_rows() > 0)
		{
			return false;
		} else {
			$this->db->where('id', $id);
			$this->db->delete($this->table);
			return true;
		}
		// return $this->db->delete($this->table,array('id', $id));
	}

	public function getKategori($id)
	{
		$this->db->where('id', $id);
		return $this->db->get($this->table);
	}

	public function search($search="")
	{
		$this->db->like('satuan', $search);
		return $this->db->get($this->table)->result();
	}

}

/* End of file Satuan_produk_model.php */
/* Location: ./application/models/Satuan_produk_model.php */