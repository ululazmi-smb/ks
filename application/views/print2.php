 <style type="text/css">

	.tabel{border-collapse: collapse;}
	.tabel th{padding: -1px 1px;  background-color:  #cccccc; margin: -1;}
	.tabel td{padding: -1px 1px; margin: -1;}
</style>
<script>
	

			window.print();
			window.onfocus=function() {window.close();}
				
	

</script>
</head>
	<table width="100%" >
	  <tr>
		<td width="0">&nbsp;</td>
		<td style="font-size: 20px;"><div align="center"><strong><?php echo $this->session->userdata('toko')->nama; ?><br></strong></div></td>
	  </tr>  
	  <tr>
		<td width="0">&nbsp;</td>
		<td ><div align="center"><?php echo $this->session->userdata('toko')->alamat; ?><br></div></td>
	  </tr>  
	</table>
	<hr/>

	<table width="100%" class="tabel">
	  <tr>
		<td width="50%"><?php echo $nota ?></td>
		<td style="float:right"><?php echo $kasir ?></td>
	  </tr>
	  <tr>
		<td colspan="2"><?php echo $tanggal ?></td>
	  </tr>
	</table>
	<hr/>
	<table width="100%" class="tabel">
	<?php 
		$jumlah_all = 0;
		for($i = 0; $i < count($produk) ; $i++)
		{
			$total = ($produk[$i]['qty'] * $produk[$i]['harga']);
		$jumlah_all = $jumlah_all + $total;
	?>
	  <tr>
		<td colspan="2"><?php echo $produk[$i]['nama'] ?></td>
	  </tr>
	  <tr>
		<td width="50%" style="text-align:right"><?php echo $produk[$i]['qty'] ?> X <?php echo $produk[$i]['harga']?></td>
	  	<td style="float:right"><?=$total;?></td>
	  </tr>
	<?php }?>
	</table>
	<hr/>
	<table width="100%" class="tabel">
	  <tr>
		<td width="50%" style="text-align:right">JUMLAH HARGA : </td>
		<td style="float:right"><?php echo $jumlah_all ?></td>
	  </tr>
	  <tr>
		<td width="50%" style="text-align:right">BAYAR TUNAI : </td>
		<td style="float:right"><?php echo $bayar ?></td>
	  </tr>
	  <tr>
		<td width="50%" style="text-align:right">DISKON : </td>
		<td style="float:right"><?php echo $diskon ?></td>
	  </tr>
	  <tr>
		<td width="50%" style="text-align:right">KEMBALI : </td>
		<td style="float:right"><?php echo (($bayar - $jumlah_all) + $diskon)?></td>
	  </tr>
	</table>
	<hr/>
		<p align="center">Terima kasih</p>
	<hr/>
  </tbody>
</table>





<div style="text-align: center; font-size: 12PX;">
PERHATIAN <br/>
BARANG YANG SUDAH DIBELI TIDAK DAPAT DI TUKAR ATAU DI KEMBALIKAN
</div><br><br>





