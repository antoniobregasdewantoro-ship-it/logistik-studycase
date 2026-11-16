@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark">Manajemen Pesanan & Manifest (CRUD)</h1>
            <p class="text-muted small">Kelola data pengiriman, verifikasi manifest, dan hapus data pesanan.</p>
        </div>
        <a href="{{ route('admin.orders.create') }}" class="btn btn-primary fw-semibold" style="background: #FF6B00; border: none;">
            <i class="fa-solid fa-plus me-1"></i> Tambah Pesanan Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No. Resi</th>
                            <th>Pengirim</th>
                            <th>Tujuan</th>
                            <th>Layanan</th>
                            <th>Status</th>
                            <th>Aksi (CRUD)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td class="fw-bold">{{ $order->resi }}</td>
                            <td>{{ $order->sender }}</td>
                            <td>{{ $order->destination }}</td>
                            <td><span class="badge bg-secondary">{{ $order->service }}</span></td>
                            <td>
                                <span class="badge bg-warning text-dark">{{ $order->status }}</span>
                            </td>
                            <td>
                                <!-- Tombol Update / Verifikasi -->
                                <form action="{{ route('admin.orders.verify', $order->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success px-2 py-1" title="Verifikasi Manifest">
                                        <i class="fa-solid fa-check"></i> Verifikasi
                                    </button>
                                </form>

                                <!-- Tombol Delete -->
                                <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger px-2 py-1" title="Hapus Data">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada data pesanan masuk.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection