@empty($supplier)
<div id="modal-master" class="modal-dialog modal-lg" role="document"> 
    <div class="modal-content"> 
        <div class="modal-header"> 
            <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5> 
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div> 
        <div class="modal-body"> 
            <div class="alert alert-danger"> 
                <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5> 
                Data supplier tidak ditemukan
            </div> 
            <a href="{{ url('/supplier') }}" class="btn btn-warning">Kembali</a> 
        </div> 
    </div> 
</div> 
@else
<form action="{{ url('/supplier/' . $supplier->supplier_id . '/update_ajax') }}" method="POST" id="form-edit-supplier">
@csrf
@method('PUT')
<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Edit Data Supplier</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Kode Supplier</label>
                <input type="text" name="supplier_kode" value="{{ $supplier->supplier_kode }}" class="form-control" readonly>
                <small id="error-supplier_kode" class="error-text text-danger"></small>
            </div>
            <div class="form-group">
                <label>Nama Supplier</label>
                <input type="text" name="supplier_nama" value="{{ $supplier->supplier_nama }}" class="form-control" required>
                <small id="error-supplier_nama" class="error-text text-danger"></small>
            </div>
            <div class="form-group">
                <label>Alamat Supplier</label>
                <input type="text" name="supplier_alamat" value="{{ $supplier->supplier_alamat }}" class="form-control" required>
                <small id="error-supplier_alamat" class="error-text text-danger"></small>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </div>
</div>
</form>

<script>
$(document).ready(function () {
    $("#form-edit-supplier").validate({
        rules: {
            supplier_kode: { required: true, minlength: 3 },
            supplier_nama: { required: true, minlength: 3 },
            supplier_alamat: { required: true, minlength: 3 },
        },
        submitHandler: function (form) {
            $.ajax({
                url: form.action,
                type: form.method,
                data: $(form).serialize(),
                success: function (response) {
                    if (response.status) {
                        $('#myModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                        dataSupplier.ajax.reload();
                    } else {
                        $('.error-text').text('');
                        $.each(response.msgfield, function (prefix, val) {
                            $('#error-' + prefix).text(val[0]);
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: response.message
                        });
                    }
                }
            });
            return false;
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
        }
    });
});
</script>
@endempty
