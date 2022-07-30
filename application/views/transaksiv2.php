<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Transaksi</title>
  <link rel="stylesheet" href="<?php echo base_url('assets/vendor/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/vendor/adminlte/plugins/sweetalert2/sweetalert2.min.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/vendor/adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/vendor/adminlte/plugins/select2/css/select2.min.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/vendor/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/vendor/adminlte/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') ?>">
  <?php $this->load->view('partials/head'); ?>
  <style>
    @media(max-width: 576px){
      .nota{
        justify-content: center !important;
        text-align: center !important;
      }
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <?php $this->load->view('includes/nav'); ?>

  <?php $this->load->view('includes/aside'); ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col">
            <h1 class="m-0 text-dark">Transaksi</h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
              <label>Barcode</label>
              <div class="input-group">
                <input type="text" class="form-control" placeholder="Barcode" onkeyup="load_tombol()" id="barcode">
                <button type="button" class="btn btn-primary" data-toggle='modal' data-target='#edit_data1' >Cari barang</button>
              </div>
            </div>
            <!-- <div class="form-group">
              <label>Barcode</label>
              <input type="text" class="form-control" placeholder="Barcode" id="barcode">
              </select>
            </div> -->
            <div class="form-group">
              <label>Jumlah</label>
              <input type="text" class="form-control" placeholder="jumlah" id="jumlah" value="1">
              </select>
            </div>
            <div class="form-group">
              <label>Pelanggan</label>
              <select name="type" id="type" class="form-control">
              <option value="0">biasa</option>
              <option value="1">member</option>
              </select>
            </div>
            
            <div class="form-group">
              <button id="bayar" class="btn btn-success" data-toggle="modal" data-target="#modal" disabled>Bayar</button>
              <button id="add_keranjang" class="btn btn-success" type="button" data-dismiss="modal" onclick="addKeranjang()" disabled>add</button>
            </div>
          </div>

          <div class="col-sm-6 d-flex justify-content-end text-right nota">
            <div>
              <div class="mb-0">
                <b class="mr-2">Nota</b> <span id="nota"></span>
              </div>
              <span id="total" style="font-size: 80px; line-height: 1" class="text-danger">0</span>
            </div>
          </div>
        </div>
        </div>
        <div class="card-body">
          <table class="table w-100 table-bordered table-hover" id="transaksi">
            <thead>
              <tr>
                <th>Barcode</th>
                <th>Nama</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Actions</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

</div>

<div class="modal fade" id="modal">
<div class="modal-dialog">
<div class="modal-content">
  <div class="modal-header">
    <h5 class="modal-title">Bayar</h5>
    <button class="close" data-dismiss="modal">
      <span>&times;</span>
    </button>
  </div>
  <div class="modal-body">
      <div class="form-group">
        <label>Tanggal</label>
        <input type="text" class="form-control" name="tanggal" id="tanggal" required>
      </div>
      <div class="form-group">
        <label>Pelanggan</label>
        <select name="pelannggan" id="pelanggan" class="form-control select2"></select>
      </div>
      <div class="form-group">
        <label>Jumlah Uang</label>
        <div class="input-group">
          <input placeholder="Jumlah Uang" type="number" class="form-control" name="jumlah_uang" onkeyup="kembalian()" required>
          <button id="uang_pas" class="btn btn-success" type="submit" onclick="uang_pas()">uang pas</button>
        </div>
      </div>
      <div class="form-group">
        <label>Diskon</label>
        <input placeholder="Diskon" type="number" class="form-control" onkeyup="kembalian()" name="diskon">
      </div>
      <div class="form-group">
        <b>Total Bayar:</b> <span class="total_bayar"></span>
      </div>
      <div class="form-group">
        <b>Kembalian:</b> <span class="kembalian"></span>
      </div>
      <button id="add" class="btn btn-success" type="submit" onclick="bayar()" disabled>Bayar</button>
      <button id="cetak" class="btn btn-success" type="submit" onclick="bayarCetak()" disabled>Bayar Dan Cetak</button>
      <button class="btn btn-danger" data-dismiss="modal">Close</button>
  </div>
</div>
</div>
</div>
<div class="modal fade" id="edit_data">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit</h5>
        <button class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form">
          <div class="form-group">
            <label>Jumlah</label>
            <input type="text" class="form-control" id="jumlah_prod" onkeyup="checking_nol()" required>
          </div>
          <button class="btn btn-success" type="button" data-dismiss="modal" onclick="save_jumlah()">save</button>
          <button class="btn btn-danger" data-dismiss="modal">Close</button>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="edit_data1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Manual</h5>
        <button class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form">
          <div class="form-group">
            <label>Nama Produk</label>
              <select id="src_barang" class="form-control select2 col-12" onchange="getNama()"></select>
          </div>
          <div class="form-group">
            <div class="row">
              <div class="col-sm-3">
                <div class="form-group">
                  <label>Sisa</label>
                  <div class="form-inline">
                    <input type="text" class="form-control  col-12" placeholder="Nama Produk" id="sisa_produk" disabled>
                  </div>
                </div>
              </div>
              <div class="col-sm-3">
                <div class="form-group">
                  <label>Harga</label>
                  <div class="form-inline">
                    <input type="text" class="form-control col-12" placeholder="Nama Produk" id="harga_produk" disabled>
                  </div>
                </div>
              </div>
              <div class="col-sm-3">
                <div class="form-group">
                  <label>Harga Grosir</label>
                  <div class="form-inline">
                    <input type="text" class="form-control col-12" placeholder="Nama Produk" id="harga_grosir" disabled>
                  </div>
                </div>
              </div>
              <div class="col-sm-3">
                <div class="form-group">
                  <label>Min Grosir</label>
                  <div class="form-inline">
                    <input type="text" class="form-control col-12" placeholder="Nama Produk" id="minimal_grosir" disabled>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group">
              <label>Pelanggan</label>
              <select name="type_pelanggan" id="type_pelanggan" class="form-control">
              <option value="0">biasa</option>
              <option value="1">member</option>
              </select>
            </div>
          <div class="form-group">
            <label>Jumlah Pembelian</label>
            <input type="text" class="form-control" name="jumlah_pembelian" id="jumlah_pembelian" value="1" require>
          </div>
          <button class="btn btn-success" type="button" data-dismiss="modal" onclick="addKeranjang2()">add</button>
          <button class="btn btn-danger" data-dismiss="modal">Close</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- ./wrapper -->
<?php $this->load->view('includes/footer'); ?>
<?php $this->load->view('partials/footer'); ?>
<script src="<?php echo base_url('assets/vendor/adminlte/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?php echo base_url('assets/vendor/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?php echo base_url('assets/vendor/adminlte/plugins/jquery-validation/jquery.validate.min.js') ?>"></script>
<script src="<?php echo base_url('assets/vendor/adminlte/plugins/sweetalert2/sweetalert2.min.js') ?>"></script>
<script src="<?php echo base_url('assets/vendor/adminlte/plugins/select2/js/select2.min.js') ?>"></script>
<script src="<?php echo base_url('assets/vendor/adminlte/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') ?>"></script>
<script src="<?php echo base_url('assets/vendor/adminlte/plugins/moment/moment.min.js') ?>"></script>
<script>
  var produkGetNamaUrl = '<?php echo site_url('produk/get_nama') ?>';
  var getNota = '<?php echo site_url('transaksi/get_keranjang') ?>';
  var urlAddKeranjang = '<?php echo site_url('transaksi/add_keranjang') ?>';
  var urlAddKeranjang2 = '<?php echo site_url('transaksi/add_keranjang2') ?>';
  var urlRemoveBarang = '<?php echo site_url('transaksi/RemoveBarang') ?>';
  var urlEditJumBarang = '<?php echo site_url('transaksi/urlEditJumBarang') ?>';
  var savetrxBarang = '<?php echo site_url('transaksi/savetrxBarang') ?>';
  var pelangganSearchUrl = '<?php echo site_url('pelanggan/search') ?>';
  var cetakUrl = '<?php echo site_url('transaksi/cetak/') ?>';
  var getBarcodeUrl = '<?php echo site_url('produk/get_barcode') ?>';
</script>
<script src="<?php echo base_url('assets/js/unminify/transaksiv2.js') ?>"></script>
</body>
</html>
