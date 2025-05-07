<form action="{{ url('/stok/ajax') }}" method="POST" id="form-tambah-stok">
    @csrf
    <div id="modal-stok" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data Stok</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Supplier</label>
                    <select name="supplier_id" id="supplier_id" class="form-control" required>
                        <option value="">- Pilih Supplier -</option>
                        @foreach($supplier as $s)
                            <option value="{{ $s->supplier_id }}">{{ $s->supplier_nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-supplier_id" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Barang</label>
                    <select name="barang_id" id="barang_id" class="form-control" required>
                        <option value="">- Pilih Barang -</option>
                        @foreach($barang as $b)
                            <option value="{{ $b->barang_id }}">{{ $b->barang_nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-barang_id" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Nama Input</label>
                    <select name="user_id" id="user_id" class="form-control" required>
                        <option value="">- Pilih User -</option>
                        @foreach($user as $u)
                            <option value="{{ $u->user_id }}">{{ $u->username }}</option>
                        @endforeach
                    </select>
                    <small id="error-user_id" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Jumlah Stok</label>
                    <input type="text" name="stok_jumlah" id="stok_jumlah" class="form-control" required>
                    <small id="error-stok_jumlah" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Tanggal Stok</label>
                    <input type="date" name="stok_tanggal" id="stok_tanggal" class="form-control" required>
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
<<script>
    $(document).ready(function () {
        $("#form-tambah-stok").validate({
            rules: {
                supplier_id: { required: true, number: true },
                barang_id: { required: true, number: true },
                user_id: { required: true, number: true },
                stok_jumlah: { required: true, number: true, min: 1 },
                stok_tanggal: { required: true, date: true },
            },
            submitHandler: function (form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: (response) => {
                        if (response.status) {
                            $("#myModal").modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            $('#table_stok').DataTable().ajax.reload();
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
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>