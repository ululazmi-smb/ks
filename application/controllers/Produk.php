<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produk extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('status') !== 'login' ) {
			redirect('/');
		}
		$this->load->model('produk_model');
	}

	public function index()
	{
		$this->load->view('produk');
	}

	public function read()
	{
		header('Content-type: application/json');
		if ($this->produk_model->read()->num_rows() > 0) {
			foreach ($this->produk_model->read()->result() as $produk) {
				$data[] = array(
					'barcode' => $produk->barcode,
					'nama' => $produk->nama_produk,
					'kategori' => $produk->kategori,
					'satuan' => $produk->satuan,
					'beli' => $produk->harga_beli,
					'jual' => $produk->harga,
					'grosir' => $produk->harga_grosir,
					'stok' => $produk->stok,
					'minimal_grosir' => $produk->minimal_grosir,
					'action' => '<button class="btn btn-sm btn-success" onclick="edit('.$produk->id.')">Edit</button> <button class="btn btn-sm btn-danger" onclick="remove('.$produk->id.')">Delete</button>'
				);
			}
		} else {
			$data = array();
		}
		$produk = array(
			'data' => $data
		);
		echo json_encode($produk);
	}

	public function add()
	{
		$data = array(
			'barcode' => $this->input->post('barcode'),
			'nama_produk' => $this->input->post('nama_produk'),
			'satuan' => $this->input->post('satuan'),
			'kategori' => $this->input->post('kategori'),
			'harga' => $this->input->post('harga'),
			'harga_grosir' => $this->input->post('harga_grosir'),
			'harga_beli' => $this->input->post('harga_beli'),
			'minimal_grosir' => $this->input->post('minimal_grosir'),
			'stok' => $this->input->post('stok')
		);
		$sql = $this->db->get_where("produk",array("barcode"=>$data["barcode"]));
		if($sql->num_rows() > 0)
		{
			echo json_encode("sudahada");
		}else {
			if ($this->produk_model->create($data)) {
				echo json_encode($data);
			}
		}
	}

	public function delete()
	{
		$id = $this->input->post('id');
		if ($this->produk_model->delete($id)) {
			echo json_encode('sukses');
		}
	}

	public function edit()
	{
		$id = $this->input->post('id');
		$data = array(
			'barcode' => $this->input->post('barcode'),
			'nama_produk' => $this->input->post('nama_produk'),
			'satuan' => $this->input->post('satuan'),
			'kategori' => $this->input->post('kategori'),
			'harga' => $this->input->post('harga'),
			'harga_grosir' => $this->input->post('harga_grosir'),
			'harga_beli' => $this->input->post('harga_beli'),
			'minimal_grosir' => $this->input->post('minimal_grosir'),
			'stok' => $this->input->post('stok')
		);
		if ($this->produk_model->update($id,$data)) {
			echo json_encode('sukses');
		}
	}

	public function get_produk()
	{
		header('Content-type: application/json');
		$id = $this->input->post('id');
		// $id = "6";
		$kategori = $this->produk_model->getProduk($id);
		if ($kategori->row()) {
			echo json_encode($kategori->row());
		}
	}

	public function get_barcode()
	{
		header('Content-type: application/json');
		$barcode = $this->input->post('barcode');
		$search = $this->produk_model->getBarcode($barcode);
		foreach ($search as $barcode) {
			$data[] = array(
				'id' => $barcode->id,
				'text' => $barcode->barcode . " " . $barcode->nama_produk
			);
		}
		echo json_encode($data);
	}

	public function get_data_barcode()
	{
		header('Content-type: application/json');
		$barcode = $this->input->post('barcode');
		$search = $this->produk_model->getBarcodev2($barcode);
		// foreach ($search as $barcode) {
		// 	$data[] = array(
		// 		'id' => $barcode->id,
		// 		'text' => $barcode->barcode . " " . $barcode->nama_produk
		// 	);
		// }
		echo json_encode($search);
	}

	public function get_nama()
	{
		header('Content-type: application/json');
		$id = $this->input->post('id');
		echo json_encode($this->produk_model->getNama($id));
	}

	public function get_stok()
	{
		header('Content-type: application/json');
		$id = $this->input->post('id');
		echo json_encode($this->produk_model->getStok($id));
	}

	public function produk_terlaris()
	{
		header('Content-type: application/json');
		$produk = $this->produk_model->produkTerlaris();
		foreach ($produk as $key) {
			$label[] = $key->nama_produk;
			$data[] = $key->terjual;
		}
		$result = array(
			'label' => $label,
			'data' => $data,
		);
		echo json_encode($result);
	}

	public function data_stok()
	{
		header('Content-type: application/json');
		$produk = $this->produk_model->dataStok();
		echo json_encode($produk);
	}

	public function print()
	{
		$produk = $this->db->get("produk")->result();
		$i = 0;
		$produk_qr = array();
		foreach($produk as $produk)
		{
			for($i2 = 0; $i2 < $produk->stok; $i2++)
			{
				$produk_qr[$i]["barcode"] = $produk->barcode;
				$produk_qr[$i]["nama"] = $produk->nama_produk;
				$i++;
			}
		}
		$data["produk"] = $produk_qr;
		$this->load->view('print_produk', $data);
	}

}

/* End of file Produk.php */
/* Location: ./application/controllers/Produk.php */