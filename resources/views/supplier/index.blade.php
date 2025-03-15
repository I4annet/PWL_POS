@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <a class="btn btn-sm btn-primary mt-1" href="{{ url('supplier/create') }}">Tambah</a>
        </div>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success')}}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error')}}</div>       
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="form-group row">
                    <label class="col-1 control-label col-form-label">Filter:</label>
                    <div class="col-3">
                        <select class="form-control" id="supplier_filter" name="supplier_filter" required>
                            <option value="">- Semua -</option>
                            @foreach ($supplier as $item) 
                                <option value="{{ $item->id }}">{{ $item->nama_supplier}}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Level Supplier</small>
                    </div>
                </div>
            </div>
        </div>
        <table class="table table-bordered table-striped table-hover table-sm" id="table_supplier">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode Supplier</th>
                    <th>Nama Supplier</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('css')
@endpush

@push('js')
<script>
    $(document).ready(function() {
        var dataSupplier = $('#table_supplier').DataTable({
            serverSide: true,
            ajax: {
                "url": "{{ url('supplier/list') }}", // Adjusted URL for supplier list
                "dataType": "json",
                "type": "POST",
                "data": function (d) {
                    d.level_id = $('#supplier_filter').val(); // Adjusted to match the filter ID
                }
            },
            columns: [
                {
                    data: "DT_RowIndex",
                    className: "text-center",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "supplier_kode", // Adjusted to match the supplier model
                    className: "",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "nama_supplier", // Adjusted to match the supplier model
                    className: "",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "aksi",
                    className: "",
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $('#supplier_filter').on('change', function() {
            dataSupplier.ajax.reload();
        });
        
    });
</script>
@endpush