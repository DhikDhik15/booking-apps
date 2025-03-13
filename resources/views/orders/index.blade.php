<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking PS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="container p-4 bg-white shadow rounded" style="max-width: 1100px;">
        <!-- Tombol Create Order -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0">Daftar Order</h5>
            <a href="{{ route('orders.index') }}" class="btn btn-primary">Create Order</a>
        </div>

        <!-- Tabel Responsive -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>Order ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Tanggal Sewa</th>
                        <th>Item</th>
                        <th>Harga</th>
                        <th>Tanggal Order</th>
                        <th>Status Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $key => $value)
                        <tr class="align-middle text-center">
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $value->order_id }}</td>
                            <td>{{ $value->name }}</td>
                            <td>{{ $value->email }}</td>
                            <td>{{ $value->phone }}</td>
                            <td>{{ $value->booking_date }}</td>
                            <td>{{ $value->console_type }}</td>
                            <td>Rp {{ number_format($value->total_price, 2, ',', '.') }}</td>
                            <td>{{ $value->created_at }}</td>
                            <td>
                                @php
                                    $statusClass = match ($value->payment_status) {
                                        'pending' => 'warning',
                                        'success' => 'success',
                                        default => 'danger',
                                    };
                                @endphp
                                <span class="badge text-bg-{{ $statusClass }} text-capitalize">
                                    {{ $value->payment_status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Informasi Pagination -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
            <p class="m-0">Showing {{ $bookings->count() }} from total {{ $bookings->total() }} data.</p>
            <div>
                {{ $bookings->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</body>
