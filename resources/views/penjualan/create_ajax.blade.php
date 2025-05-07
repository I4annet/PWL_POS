<form action="{{ url('/penjualan/ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Pembeli</label>
                    <input value="" type="text" name="pembeli" id="pembeli" class="form-control" required>
                    <small id="error-pembeli" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Tanggal Penjualan</label>
                    <input value="" type="date" name="penjualan_tanggal" id="penjualan_tanggal" class="form-control" required>
                    <small id="error-penjualan_tanggal" class="error-text form-text text-danger"></small>
                </div>

                <hr>
                <h5>Detail Penjualan</h5>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-success btn-sm" id="btn-add-item">
                            <i class="fa fa-plus"></i> Tambah Barang
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="detail-table">
                        <thead>
                        <tr>
                            <th>Barang</th>
                            <th width="150">Harga (Rp)</th>
                            <th width="100">Jumlah</th>
                            <th width="150">Subtotal (Rp)</th>
                            <th width="50">Aksi</th>
                        </tr>
                        </thead>
                        <tbody id="detail-items">
                        <!-- Detail items will be added here -->
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Total:</th>
                            <th id="total-amount">0</th>
                            <th></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

<!-- Template for detail item row -->
<template id="detail-row-template">
    <tr class="detail-row">
        <td>
            <select name="barang_id[]" class="form-control barang-select" required>
                <option value="">- Pilih Barang -</option>
                @foreach ($barang as $b)
                    <option value="{{ $b->barang_id }}" data-harga="{{ $b->harga_jual }}">{{ $b->barang_nama }}</option>
                @endforeach
            </select>
            <small class="error-text form-text text-danger"></small>
        </td>
        <td>
            <input type="number" name="harga[]" class="form-control harga-input" readonly>
            <small class="error-text form-text text-danger"></small>
        </td>
        <td>
            <input type="number" name="jumlah[]" class="form-control jumlah-input" min="1" value="1" required>
            <small class="error-text form-text text-danger"></small>
        </td>
        <td>
            <input type="text" class="form-control subtotal-display" readonly>
            <input type="hidden" class="subtotal-input">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm btn-remove-item">
                <i class="fa fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

<script>
    $(document).ready(function() {
        // Add first item row by default
        addDetailRow();

        // Add new detail row
        $("#btn-add-item").click(function() {
            addDetailRow();
        });

        // Remove detail row
        $(document).on('click', '.btn-remove-item', function() {
            if ($('.detail-row').length > 1) {
                $(this).closest('tr').remove();
                calculateTotal();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Tidak dapat dihapus',
                    text: 'Minimal harus ada satu barang'
                });
            }
        });

        // When barang is selected, set the price
        $(document).on('change', '.barang-select', function() {
            var row = $(this).closest('tr');
            var harga = $(this).find(':selected').data('harga') || 0;
            row.find('.harga-input').val(harga);
            updateRowTotal(row);
        });

        // When quantity changes, update subtotal
        $(document).on('input', '.jumlah-input', function() {
            updateRowTotal($(this).closest('tr'));
        });

        // Form Validation
        $("#form-tambah").validate({
            rules: {
                pembeli: { required: true, maxlength: 200 },
                penjualan_tanggal: { required: true },
                user_id: { required: true },
                "barang_id[]": { required: true },
                "jumlah[]": { required: true, min: 1 }
            },
            submitHandler: function(form) {
                console.log('debug 1');
                // Check if at least one item exists
                if ($('.detail-row').length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Minimal harus ada satu barang'
                    });
                    return false;
                }

                console.log('debug 2');
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.status) {
                            console.log('debug success');
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            tablePenjualan.ajax.reload();
                        } else {
                            console.log('debug success');
                            $('.error-text').text('');
                            $.each(response.msgField, function(prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message
                            });
                        }
                    },
                });
                return false;
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });

        // Function to add a new detail row
        function addDetailRow() {
            var template = document.getElementById('detail-row-template');
            var clone = document.importNode(template.content, true);
            $('#detail-items').append(clone);
        }

        // Function to update a row's subtotal
        function updateRowTotal(row) {
            var harga = parseFloat(row.find('.harga-input').val()) || 0;
            var jumlah = parseInt(row.find('.jumlah-input').val()) || 0;
            var subtotal = harga * jumlah;

            row.find('.subtotal-input').val(subtotal);
            row.find('.subtotal-display').val(formatRupiah(subtotal));

            calculateTotal();
        }

        // Function to calculate the total amount
        function calculateTotal() {
            var total = 0;
            $('.subtotal-input').each(function() {
                total += parseFloat($(this).val()) || 0;
            });

            $('#total-amount').text(formatRupiah(total));
        }

        // Function to format number as Rupiah
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }
    });

    document.getElementById("penjualan_tanggal").value = new Date().toISOString().split('T')[0];

    $('#penjualan_tanggal').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true
    });
</script>