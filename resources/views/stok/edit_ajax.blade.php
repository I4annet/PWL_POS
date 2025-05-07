@empty($stok)
<div id="modal-master" class="modal-dialog modal-lg" role="document"> 
    <div class="modal-content"> 
        <div class="modal-header"> 
            <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5> 
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div> 
        <div class="modal-body"> 
            <div class="alert alert-danger"> 
                <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5> 
                Data yang anda cari tidak ditemukan</div> 
            <a href="{{ url('/stok') }}" class="btn btn-warning">Kembali</a> 
        </div> 
    </div> 
</div> 
@else 
<form action="{{ url('/stok/' . $stok->stok_id .'/update_ajax') }}" method="POST" id="form-edit">
@csrf 
@method('PUT') 
<div id="modal-master" class="modal-dialog modal-lg" role="document"> 
    <div class="modal-content"> 
        <div class="modal-header"> 
            <h5 class="modal-title" id="exampleModalLabel">Edit Data Stok</h5> 
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
        </div> 
        <div class="modal-body"> 
            <div class="form-group"> 
                <label>Kode Stok</label> 
                <input value="{{ $stok->stok_id }}" type="text" name="stok_kode" id="stok_kode" class="form-control"> 
                <small id="error-stok_kode" class="error-text form-text text-danger"></small> 
            </div> 
            <div class="form-group"> 
                <label>Supplier</label> 
                <select name="supplier_id" id="supplier_id" class="form-control" required> 
                    <option value="">- Pilih Supplier-</option> 
                    @foreach($supplier as $sp) 
                        <option {{ ($sp->supplier_id == $stok->supplier_id) ? 'selected' : '' }} value="{{ $sp->supplier_id }}">{{ $sp->supplier_nama }}</option> 
                    @endforeach 
                </select> 
                <small id="error-supplier_id" class="error-text form-text text-danger"></small>
            </div>
            <div class="form-group"> 
                <label>Barang</label> 
                <select name="barang_id" id="barang_id" class="form-control" required> 
                    <option value="">- Pilih Barang -</option> 
                    @foreach($barang as $b) 
                        <option {{ ($b->barang_id == $stok->barang_id) ? 'selected' : '' }} value="{{ $b->barang_id }}">{{ $b->barang_nama }}</option> 
                    @endforeach 
                </select> 
                <small id="error-barang_id" class="error-text form-text text-danger"></small>
            </div>
            <div class="form-group"> 
                <label>Jumlah</label> 
                <input value="{{ $stok->stok_jumlah }}" type="number" name="stok_jumlah" id="stok_jumlah" class="form-control" required> 
                <small id="error-stok_jumlah" class="error-text form-text text-danger"></small> 
            </div>
            <div class="form-group"> 
                <label>Tanggal</label> 
                <input value="{{ $stok->stok_tanggal }}" type="date" name="stok_tanggal" id="stok_tanggal" class="form-control" required> 
                <small id="error-stok_tanggal" class="error-text form-text text-danger"></small> 
            </div>
        </div> 
        <div class="modal-footer"> 
            <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button> 
            <button type="submit" class="btn btn-primary">Simpan</button> 
        </div> 
    </div> 
</div> 
</form> 

<script> 
    $(document).ready(function() { 
        $("#form-edit").validate({ 
            rules: { 
                stok_id: { required: true, minlength: 3, maxlength: 20 }, 
                supplier_id: { required: true, number: true },
                stok_nama: { required: true, minlength: 3, maxlength: 100 }, 
                stok_jumlah: { required: true, number: true }, 
                stok_tanggal: { required: true, date: true }, 
                barang_id: { required: true, number: true }, 
            }, 
            submitHandler: (form) => { 
                $.ajax({ 
                    url: form.action, 
                    type: form.method, 
                    data: $(form).serialize(), 
                    success: (response) => { 
                        if(response.status) { 
                            $('#myModal').modal('hide'); 
                            Swal.fire({ 
                                icon: 'success', 
                                title: 'Berhasil', 
                                text: response.message 
                            }); 
                            dataStok.ajax.reload(); 
                        } else { 
                            $('.error-text').text(''); 
                            $.each(response.msgfield, function(prefix, val) { 
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
            highlight: function (element, errorClass, validClass) { 
                $(element).addClass('is-invalid'); 
            }, 
            unhighlight: function (element, errorClass, validClass) { 
                $(element).removeClass('is-invalid'); 
            } 
        }); 
    }); 
</script> 
@endempty
