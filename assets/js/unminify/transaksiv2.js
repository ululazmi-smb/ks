let isCetak = false,
    transaksi = $("#transaksi").DataTable({
        responsive: true,
        lengthChange: false,
        searching: false,
        scrollX: true
    });
var id_produk = 0;
function reloadTable() {
    transaksi.ajax.reload()
}

$(document).ready(function(e) {
    getNota1();
});

$("#pelanggan").select2({
    placeholder: "Pelanggan",
    ajax: {
        url: pelangganSearchUrl,
        type: "post",
        dataType: "json",
        data: params => ({
            pelanggan: params.term
        }),
        processResults: res => ({
            results: res
        }),
        cache: true
    }
});

$("#src_barang").select2({
    placeholder: "cari barang",
    ajax: {
        url: getBarcodeUrl,
        type: "post",
        dataType: "json",
        data: params => ({
            barcode: params.term
        }),
        processResults: res => ({
            results: res
        }),
        cache: true
    }
});

function getNama() {
    $.ajax({
        url: produkGetNamaUrl,
        type: "post",
        dataType: "json",
        data: {
            id: $("#src_barang").val()
        },
        success: res => {
            console.log(res);
        },
        error: err => {
            console.log(err)
        }
    })
}

function getNota1()
{
    transaksi.clear().draw();
    $.ajax({
        url: getNota,
        type: "get",
        dataType: "json",
        data: "",
        success: res => {
            if(res.length > 0)
            {
                $("#bayar").removeAttr("disabled");
                let total = 0;
                for(let i = 0; i < res.length; i++)
                {
                    transaksi.row.add([res[i].barcode, res[i].nama, res[i].harga, res[i].jumlah, res[i].btn]).draw().node();
                    total = total + (res[i].harga * res[i].jumlah);
                }
                $("#total").html(total);
            } else {
                $("#bayar").attr("disabled", "disabled")
            }
        },
        error: err => {
            console.log(err)
        }
    });
}

function add()
{
    $.ajax({
        url: savetrxBarang,
        type: "POST",
        dataType: "json",
        data: {
            tanggal: $("#tanggal").val(),
            total_bayar: $("#total").html(),
            jumlah_uang: $('[name="jumlah_uang"]').val(),
            diskon: $('[name="diskon"]').val(),
            pelanggan: $("#pelanggan").val(),
            nota: $("#nota").html()
        },
        success: res => {
            if (isCetak) {
                Swal.fire("Sukses", "Sukses Membayar", "success").
                    then(() => window.location.href = `${cetakUrl}${res}`)
            } else {
                Swal.fire("Sukses", "Sukses Membayar", "success").
                    then(() => window.location.reload())
            }
        },
        error: err => {
            console.log(err)
        }
    })
}

function checking_nol()
{
    console.log("ok");
    // var jum_prod = document.getElementById("jumlah_prod").value;
    // if(jum_prod < 1)
    // {
    //     jum_prod = 1;
    // }
}

function save_jumlah()
{
    $.ajax({
        url: urlEditJumBarang,
        type: "POST",
        dataType: "json",
        data: {
            id: id_produk,
            jum: $("#jumlah_prod").val()

        },
        success: res => {
            getNota1();
        },
        error: err => {
            console.log(err)
        }
    })
}

function edit(id)
{
    id_produk = id;
}

function addKeranjang()
{
    $.ajax({
        url: urlAddKeranjang,
        type: "POST",
        dataType: "json",
        data: {
            barcode: $("#barcode").val(),
            type: $("#type").val(),
            jumlah: $("#jumlah").val()
        },
        success: res => {
            getNota1();
            $("#jumlah").val("1");
            document.getElementById("barcode").select();
            $("#barcode").val("");
            if(res.response == "success")
            {
            } else if(res.response == "error")
            {
                Swal.fire("Sukses", res.messages, "danger");
            }
        },
        error: err => {
            console.log(err)
        }
    })

}

function nota(jumlah) {
    let hasil = "",
        char = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
        total = char.length;
    for (var r = 0; r < jumlah; r++) hasil += char.charAt(Math.floor(Math.random() * total));
    return hasil
}

$(document).on('keypress',function(e) {
    if(e.which == 13) {
        if($("#barcode").val() != "")
        {
            addKeranjang();
        }
    }
});

function bayarCetak() {
    isCetak = true;
    add();
}

function bayar() {
    isCetak = false,
    add();
}

function checkUang() {
    let jumlah_uang = $('[name="jumlah_uang"').val(),
        total_bayar = parseInt($(".total_bayar").html());
    if (jumlah_uang !== "" && jumlah_uang >= total_bayar) {
        $("#add").removeAttr("disabled");
        $("#cetak").removeAttr("disabled")
    } else {
        $("#add").attr("disabled", "disabled");
        $("#cetak").attr("disabled", "disabled")
    }
}

function remove(nama) {
    $("#total").html("0");
    $.ajax({
        url: urlRemoveBarang,
        type: "POST",
        dataType: "json",
        data: {
            id: nama,

        },
        success: res => {
            getNota1();
        },
        error: err => {
            console.log(err)
        }
    })
}

function kembalian() {
    let total = $("#total").html(),
        jumlah_uang = $('[name="jumlah_uang"').val(),
        diskon = $('[name="diskon"]').val();
    $(".kembalian").html(jumlah_uang - (total - diskon));
    checkUang()
}


$("#tanggal").datetimepicker({
    format: "dd-mm-yyyy h:ii:ss"
});
$(".modal").on("hidden.bs.modal", () => {
    $("#form")[0].reset();
    $("#form").validate().resetForm()
});
$(".modal").on("show.bs.modal", () => {
    let now = moment().format("D-MM-Y H:mm:ss"),
        total = $("#total").html(),
        jumlah_uang = $('[name="jumlah_uang"').val();
    $("#tanggal").val(now), $(".total_bayar").html(total), $(".kembalian").html(Math.max(jumlah_uang - total, 0))
});

// $("#form").validate({
//     errorElement: "span",
//     errorPlacement: (err, el) => {
//         err.addClass("invalid-feedback"), el.closest(".form-group").append(err)
//     },
//     submitHandler: () => {
//         add()
//     }
// });
$("#nota").html(nota(15));