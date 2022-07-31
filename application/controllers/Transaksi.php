<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaksi extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('status') !== 'login' ) {
			redirect('/');
		}
		$this->load->model('transaksi_model');
		$this->load->model('Produk_model');
	}

	public function index()
	{
		$this->load->view('transaksi');
	}

	public function v2()
	{
		$this->load->view('transaksiv2');
	}

	public function migrate_transaksi()
	{
		$sql1 = $this->db->get_where("transaksi")->result();
		foreach($sql1 as $produk)
		{
			$barcode = explode(',', $produk->barcode);
			$nama = "";
			$i = 0;
			foreach($barcode as $key => $none)
			{
				$sql = $this->db->get_where("produk",array("id"=>$none));
				if($sql->num_rows() > 0)
				{
					if($i == 0)
					{
						$nama = $sql->row()->nama_produk;
					} else {
						$nama = $nama.",".$sql->row()->nama_produk;
					}
					$i++;
				}
			}
			$data2 = array(
				'id' => $produk->id,
				'tanggal' => $produk->tanggal,
				'nama' => $nama,
				'barcode' => $produk->barcode,
				'diskon_barang' => $produk->diskon_barang,
				'qty' => $produk->qty,
				'total_bayar' => $produk->total_bayar,
				'jumlah_uang' => $produk->jumlah_uang,
				'diskon' => $produk->diskon,
				'pelanggan' => $produk->pelanggan,
				'nota' => $produk->nota,
				'kasir' => $produk->kasir
			);
			$this->db->insert("transaksiv2", $data2);
		}
	}

	public function read()
	{
		// header('Content-type: application/json');
		if ($this->transaksi_model->read()->num_rows() > 0) {
			foreach ($this->transaksi_model->read()->result() as $transaksi) {
				$nama = "";
				$barcode = explode(',', $transaksi->barcode);
				$nam = explode(',', $transaksi->nama);
				$qt = explode(',', $transaksi->qty);
				$diskon_barang = explode(',', $transaksi->diskon_barang);
				$tanggal = new DateTime($transaksi->tanggal);
				foreach($barcode as $key => $none)
				{
					$nama = $nama . $nam[$key]." - ".$diskon_barang[$key]." (". $qt[$key]. ")</br>--------------</br>";

					// $sql = $this->db->get_where("produk",array("id"=>$none));
					// if($sql->num_rows() > 0)
					// {
					// 	$nama = $nama . $sql->row()->nama_produk." - ".$diskon_barang[$key]." (". $qt[$key]. ")</br>--------------</br>";
					// } else {
					// 	$nama = $nama . "";
					// }
				}
				$data[] = array(
					'tanggal' => $tanggal->format('d-m-Y H:i:s'),
					'nama_produk' => $nama,
					//'nama_produk' => '<table>'.$this->transaksi_model->getProduk($barcode, $transaksi->qty).'</table>',
					'total_bayar' => $transaksi->total_bayar,
					'jumlah_uang' => $transaksi->jumlah_uang,
					'diskon' => $transaksi->diskon,
					'pelanggan' => $transaksi->pelanggan,
					'action' => '<a class="btn btn-sm btn-success" href="'.site_url('transaksi/cetak/').$transaksi->id.'">Print</a> <button class="btn btn-sm btn-danger" onclick="remove('.$transaksi->id.')">Delete</button>'
				);
			}
		} else {
			$data = array();
		}
		$transaksi = array(
			'data' => $data
		);
		echo json_encode($transaksi);
	}

	public function add()
	{
		$produk = json_decode($this->input->post('produk'));
		$produk2 = $this->input->post('produk2');
		$tanggal = new DateTime($this->input->post('tanggal'));
		$barcode = "";
		$nama = "";
		foreach ($produk as $produk) {
			$this->transaksi_model->removeStok($produk->id, $produk->stok);
			$this->transaksi_model->addTerjual($produk->id, $produk->terjual);
		}

		for($i = 0; $i < count($produk2); $i++)
		{
			$db = $this->db->get_where("produk",array("barcode" => $produk2[$i]));
			if($i == 0)
			{
				$barcode = $db->row()->id;
				$nama = $db->row()->nama_produk;
			} else {
				$barcode = $barcode. "," .$db->row()->id;
				$nama = $nama. "," .$db->row()->nama_produk;
			}
		}
		$data = array(
			'tanggal' => $tanggal->format('Y-m-d H:i:s'),
			'nama' => $nama,
			'barcode' => $barcode,
			'diskon_barang' => implode(',', $this->input->post('harga')),
			'qty' => implode(',', $this->input->post('qty')),
			'total_bayar' => $this->input->post('total_bayar'),
			'jumlah_uang' => $this->input->post('jumlah_uang'),
			'diskon' => $this->input->post('diskon'),
			'pelanggan' => $this->input->post('pelanggan'),
			'nota' => $this->input->post('nota'),
			'kasir' => $this->session->userdata('id')
		);
		if ($this->transaksi_model->create($data)) {
			echo json_encode($this->db->insert_id());
		}
		$data = $this->input->post('form');
	}

	public function RemoveBarang()
	{
		header('Content-type: application/json');
		$id = $this->input->post("id");
		$sql = $this->db->delete("keranjang", array("id"=>$id));
		echo json_encode("ok");
	}

	public function urlEditJumBarang()
	{
		header('Content-type: application/json');
		$id = $this->input->post("id");
		$jumlah = $this->input->post("jum");
		$sql = $this->db->update("keranjang", array("jumlah" => $jumlah),array("id"=>$id));
		echo json_encode("ok");
	}

	public function savetrxBarang()
	{
		$tanggal = new DateTime($this->input->post('tanggal'));
		$barcode = "";
		$qty="";
		$harga="";
		$nama = "";
		$sql = $this->db->get_where("keranjang", array('kasir' => $this->session->userdata('id')))->result();
		$i = 0;
		$produk = array();
		foreach($sql as $data2)
		{
			$dat = $this->db->get_where("produk", array("id"=>$data2->id_barang))->row();
			// $produk = array($dat->barcode);
			error_reporting(0);
			$this->transaksi_model->removeStok($data2->id_barang, ($dat->stok - $data2->jumlah));
			$this->transaksi_model->addTerjual($data2->id_barang, ($dat->terjual + $data2->jumlah));
			if($i == 0)
			{
				$barcode = $data2->id_barang;
				$qty = $data2->jumlah;
				$harga = $data2->harga;
				$nama = $dat->nama_produk;
			} else {
				$barcode = $barcode. "," .$data2->id_barang;
				$qty = $qty. "," .$data2->jumlah;
				$harga = $harga. "," .$data2->harga;
				$nama = $nama. "," . $dat->nama_produk;
			}
			$i++;
		}
		$data = array(
			'tanggal' => $tanggal->format('Y-m-d H:i:s'),
			'nama' => $nama,
			'barcode' => $barcode,
			'diskon_barang' => $harga,
			'qty' => $qty,
			'total_bayar' => $this->input->post('total_bayar'),
			'jumlah_uang' => $this->input->post('jumlah_uang'),
			'diskon' => $this->input->post('diskon'),
			'pelanggan' => $this->input->post('pelanggan'),
			'nota' => $this->input->post('nota'),
			'kasir' => $this->session->userdata('id')
		);
		// var_dump($sql);
		// exit();
		if ($this->transaksi_model->create($data)) {
			echo json_encode($this->db->insert_id());
			$sql = $this->db->delete("keranjang", array('kasir' => $this->session->userdata('id')));
		}
	}

	public function get_keranjang()
	{
		header('Content-type: application/json');
		$sql = $this->transaksi_model->cek_trx();
		$array = array();
		foreach($sql->result() as $key => $data)
		{
			$data1 = $this->db->get_where("produk", array("id" => $data->id_barang))->row();
			$array[$key]["barcode"] = $data1->barcode;
			$array[$key]["nama"] = $data1->nama_produk;
			$array[$key]["harga"] = $data->harga;
			$array[$key]["jumlah"] = $data->jumlah;
			$array[$key]["btn"] = "<button class='btn btn-sm btn-danger' onclick=".'"'."remove('".$data->id."')".'"'.">Hapus</btn> <button class='btn btn-sm btn-success' data-toggle='modal' data-target='#edit_data' onclick=".'"'."edit('".$data->id."')".'"'.">Edit</btn>";
		}
		echo json_encode($array);
	}

	public function add_keranjang()
	{
		header('Content-type: application/json');
		$barcode = $this->input->post("barcode");
		$type = $this->input->post("type");
		$jumlah = $this->input->post("jumlah");
		if($jumlah == "")
		{
			$jumlah = 1;
		}
		$barcode2 = $this->Produk_model->getBarcodev2($barcode);
		if($barcode2 == NULL)
		{
			$array = array(
				"response" => "error",
				"messages" => "barang tidak di temukan"
			);
		} else {
			if($barcode2->stok > 0)
			{
				$cek_keranjang = $this->db->get_where("keranjang", array("id_barang" => $barcode2->id, 'kasir' => $this->session->userdata('id')));
				if($cek_keranjang->num_rows() > 0)
				{
					$data_keranjang = $cek_keranjang->row();
					if(($data_keranjang->jumlah + $jumlah) <= $barcode2->stok)
					{
						if($data_keranjang->jumlah < 1)
						{
							$sql = $this->db->delete("keranjang", "id=".$data_keranjang->id);
						} else {
							if($data_keranjang->jumlah > $barcode2->minimal_grosir && $type == 1)
							{
								$sql = $this->db->update("keranjang", array("jumlah" => $data_keranjang->jumlah + $jumlah, "harga" => $barcode2->harga_grosir), "id=".$data_keranjang->id);
							} else {
								$sql = $this->db->update("keranjang", array("jumlah" => $data_keranjang->jumlah + $jumlah, "harga" => $barcode2->harga), "id=".$data_keranjang->id);
							}
						}
						$array = array(
							"response" => "success"
						);
					} else {
						$array = array(
							"response" => "error",
							"messages" => "stok kurang"
						);
					}
				} else {
					if($jumlah < 1)
					{
						$array = array(
							"response" => "error",
							"messages" => "tidak ada barang yang di kurangi"
						);
					} else {
						if($barcode2->stok < $jumlah)
						{
							$array = array(
								"response" => "error",
								"messages" => "stok kurang"
							);
						} else {
							$sql = $this->db->insert("keranjang", array(
								"id_barang" => $barcode2->id,
								"harga" => $barcode2->harga,
								"jumlah" => $jumlah,
								"kasir" => $this->session->userdata('id')
							));
							$array = array(
								"response" => "success"
							);
						}
					}
				}
			} else {
				$array = array(
					"response" => "error",
					"messages" => "stok kosong"
				);
			}
		}
		echo json_encode($array);

	}

	public function add_keranjang2()
	{
		header('Content-type: application/json');
		$barcode = $this->input->post("barcode");
		$type = $this->input->post("type");
		$jumlah = $this->input->post("jumlah");
		if($jumlah == "")
		{
			$jumlah = 1;
		}
		$barcode2 = $this->Produk_model->getBarcodev3($barcode);
		if($barcode2 == NULL)
		{
			$array = array(
				"response" => "error",
				"messages" => "barang tidak di temukan"
			);
		} else {
			if($barcode2->stok > 0)
			{
				$cek_keranjang = $this->db->get_where("keranjang", array("id_barang" => $barcode2->id, 'kasir' => $this->session->userdata('id')));
				if($cek_keranjang->num_rows() > 0)
				{
					$data_keranjang = $cek_keranjang->row();
					if(($data_keranjang->jumlah + $jumlah) <= $barcode2->stok)
					{
						if($data_keranjang->jumlah < 1)
						{
							$sql = $this->db->delete("keranjang", "id=".$data_keranjang->id);
						} else {
							if($data_keranjang->jumlah > $barcode2->minimal_grosir && $type == 1)
							{
								$sql = $this->db->update("keranjang", array("jumlah" => $data_keranjang->jumlah + $jumlah, "harga" => $barcode2->harga_grosir), "id=".$data_keranjang->id);
							} else {
								$sql = $this->db->update("keranjang", array("jumlah" => $data_keranjang->jumlah + $jumlah, "harga" => $barcode2->harga), "id=".$data_keranjang->id);
							}
						}
						$array = array(
							"response" => "success"
						);
					} else {
						$array = array(
							"response" => "error",
							"messages" => "stok kurang"
						);
					}
				} else {
					if($jumlah < 1)
					{
						$array = array(
							"response" => "error",
							"messages" => "tidak ada barang yang di kurangi"
						);
					} else {
						if($barcode2->stok < $jumlah)
						{
							$array = array(
								"response" => "error",
								"messages" => "stok kurang"
							);
						} else {
							$sql = $this->db->insert("keranjang", array(
								"id_barang" => $barcode2->id,
								"harga" => $barcode2->harga,
								"jumlah" => $jumlah,
								"kasir" => $this->session->userdata('id')
							));
							$array = array(
								"response" => "success"
							);
						}
					}
				}
			} else {
				$array = array(
					"response" => "error",
					"messages" => "stok kosong"
				);
			}
		}
		echo json_encode($array);

	}

	public function delete()
	{
		$id = $this->input->post('id');
		if ($this->transaksi_model->delete($id)) {
			echo json_encode('sukses');
		}
	}

	public function cetak($id)
	{
		$produk = $this->transaksi_model->getAll($id);
		$tanggal = new DateTime($produk->tanggal);
		$barcode = explode(',', $produk->barcode);
		$nama = explode(',', $produk->nama);
		$qty = explode(',', $produk->qty);
		$diskon_barang = explode(',', $produk->diskon_barang);
		$produk->tanggal = $tanggal->format('d m Y H:i:s');
		for($i = 0; $i < count($nama); $i++)
		{
			$dataProduk[$i]["nama"] = $nama[$i]; 
			$dataProduk[$i]["harga"] = $diskon_barang[$i]; 
			$dataProduk[$i]["qty"] = $qty[$i]; 
			$dataProduk[$i]["total_hrg"] = $diskon_barang[$i] * $qty[$i]; 
		}
		if($produk->diskon == "")
		{
			$produk->diskon = 0;
		}
		$data = array(
			'nota' => $produk->nota,
			'tanggal' => $produk->tanggal,
			'produk' => $dataProduk,
			'total' => $produk->total_bayar,
			'bayar' => $produk->jumlah_uang,
			'kembalian' => $produk->jumlah_uang - $produk->total_bayar,
			'kasir' => $produk->kasir,
			'diskon' => $produk->diskon
		);
		$this->load->view('print2', $data);
	}

	public function penjualan_bulan()
	{
		header('Content-type: application/json');
		$day = $this->input->post('day');
		foreach ($day as $key => $value) {
			$now = date($day[$value].' m Y');
			if ($qty = $this->transaksi_model->penjualanBulan($now) !== []) {
				$data[] = array_sum($this->transaksi_model->penjualanBulan($now));
			} else {
				$data[] = 0;
			}
		}
		echo json_encode($data);
	}

	public function transaksi_hari()
	{
		header('Content-type: application/json');
		$now = date('d m Y');
		$total = $this->transaksi_model->transaksiHari($now);
		echo json_encode($total);
	}

	public function transaksi_terakhir($value='')
	{
		header('Content-type: application/json');
		$now = date('d m Y');
		foreach ($this->transaksi_model->transaksiTerakhir($now) as $key) {
			$total = explode(',', $key);
		}
		echo json_encode($total);
	}

}

/* End of file Transaksi.php */
/* Location: ./application/controllers/Transaksi.php */