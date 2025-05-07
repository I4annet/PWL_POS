<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Data Penjualan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <table class="table table-bordered table-striped table-hover table-sm">
                <tr>
                    <th>ID</th>
                    <td>{{ $penjualan->penjualan_id }}</td>
                </tr>
                <tr>
                    <th>Username</th>
                    <td>{{ $penjualan->user->username }}</td>
                </tr>
                <tr>
                    <th>Pembeli</th>
                    <td>{{ $penjualan->pembeli }}</td>
                </tr>
                <tr>
                    <th>Kode Penjualan</th>
                    <td>{{ $penjualan->penjualan_kode }}</td>
                </tr>
                <tr>
                    <th>Tanggal Penjualan</th>
                    <td>{{ $penjualan->penjualan_tanggal }}</td>
                </tr>
            </table>

            <h5>Detail Penjualan</h5>
            <table class="table table-bordered table-striped table-hover table-sm">
                <thead>
                <tr>
                    <th>Detail ID</th>
                    <th>Nama Barang</th>
                    <th>Kode Barang</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
                </thead>
                <tbody>
                @php $total = 0; @endphp
                @foreach ($penjualan->penjualan_detail as $detail)
                    @php $subtotal = $detail->harga * $detail->jumlah; $total += $subtotal; @endphp
                    <tr>
                        <td>{{ $detail->detail_id }}</td>
                        <td>{{ $detail->barang->barang_nama }}</td>
                        <td>{{ $detail->barang->barang_kode }}</td>
                        <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                        <td>{{ $detail->jumlah }}</td>
                        <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="5" class="text-right">Total:</th>
                    <th>Rp {{ number_format($total, 0, ',', '.') }}</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>