@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('/stok/import') }}')" class="btn btn-sm btn-info mt-1">Import Stok</button>
                <a href="{{ url('/stok/export_excel') }}" class="btn btn-sm btn-primary mt-1"><i class="fa fa-file-excel"></i> Export Excel Stok</a>
                <a href="{{ url('/stok/export_pdf') }}" class="btn btn-sm btn-warning mt-1"><i class="fa fa-file-pdf"></i> Export PDF Stok</a> 
                <button onclick="modalAction('{{ url('/stok/create_ajax') }}')" class="btn btn-sm btn-success mt-1">
                    Tambah Stok (Ajax)
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter -->
            <div id="filter" class="form-horizontal filter-date p-2 border-bottom mb-2">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group form-group-sm row text-sm mb-0">
                            <label for="filter_barang" class="col-md-1 col-form-label">Filter</label>
                            <div class="col-md-3">
                                <select name="barang_id" class="form-control" id="barang_id" required>
                                    <option value="">- Semua Barang -</option>
                                    @foreach ($barang as $b)
                                        <option value="{{ $b->barang_id }}">{{ $b->barang_nama }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Nama Barang</small>
                            </div>
                        </div>
                        {{-- <div class="form-group form-group-sm row text-sm mb-0">
                            <label for="filter_supplier" class="col-md-1 col-form-label">Filter</label>
                            <div class="col-md-3">
                                <select name="supplier_id" class="form-control" id="supplier_id" required>
                                    <option value="">- Semua Supplier -</option>
                                    @foreach ($supplier as $s)
                                        <option value="{{ $s->supplier_id }}">{{ $s->supplier_nama }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Nama Supplier</small>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <table class="table table-bordered table-sm table-striped table-hover" id="table-stok">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Supplier</th>
                        <th>Nama Barang</th>
                        <th>Nama User</th>
                        <th>Stok Tanggal</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" data-backdrop="static" data-keyboard="false"
        data-width="75%"></div>
@endsection

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        var tableStok;
        $(document).ready(function() {
            tableStok = $('#table-stok').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('stok/list') }}",
                    type: "POST",
                    data: function(d) {
                        d.barang_id = $('#barang_id').val();
                    }
                },
                columns: [
                    { data: "DT_RowIndex", className: "text-center", width: "5%", orderable: false, searchable: false },
                    { data: "supplier.supplier_nama", width: "15%" },
                    { data: "barang.barang_nama", width: "15%" },
                    { data: "user.username", width: "15%" },
                    { data: "stok_tanggal", width: "15%" },
                    { data: "stok_jumlah", className: "text-right", width: "10%" },
                    { data: "aksi", className: "text-center", width: "15%", orderable: false, searchable: false }
                ]
            });

            $('#table-stok_filter input').unbind().bind().on('keyup', function(e) {
                if (e.keyCode == 13) {
                    tableStok.search(this.value).draw();
                }
            });

            $('.filter_barang').change(function() {
                tableStok.draw();
            });

            $('#barang_id').on('change', function() {
                tableBarang.ajax.reload();
            });
        });
    </script>
@endpush
