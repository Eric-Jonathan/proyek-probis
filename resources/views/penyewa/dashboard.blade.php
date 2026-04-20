@extends('layout.layout')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h3 class="fw-bold">Status Pesanan Saya</h3>
        <p class="text-muted">Pantau jadwal dan berikan penilaian setelah masa sewa selesai.</p>
    </div>
</div>

{{-- 
    KARENA CONTROLLER TIDAK MENGIRIM DATA, 
    KITA BUAT DATA DUMMY DI SINI AGAR DASHBOARD TIDAK KOSONG/ERROR 
--}}
@php
    $bookings_dummy = [
        (object)[
            'booking_id' => 101,
            'room_name' => 'Kontena Hotel - Ball Room',
            'location' => 'KH. Agus Salim No.106, Kota Batu',
            'start_date' => '2026-04-25 09:00:00',
            'end_date' => '2026-04-25 12:00:00',
            'status' => 1, // Booked
            'room_id' => 1
        ],
        (object)[
            'booking_id' => 99,
            'room_name' => 'Kontena Hotel - Meeting Room',
            'location' => 'KH. Agus Salim No.106, Kota Batu',
            'start_date' => '2026-04-18 14:00:00',
            'end_date' => '2026-04-18 16:00:00',
            'status' => 2, // Occupied / Selesai
            'room_id' => 1
        ]
    ];
@endphp

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Ruangan & Lokasi</th>
                        <th>Waktu Sewa</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings_dummy as $b)
                    <tr>
                        <td>#{{ $b->booking_id }}</td>
                        <td>
                            <div class="fw-bold text-primary">{{ $b->room_name }}</div>
                            <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $b->location }}</small>
                        </td>
                        <td>
                            <div class="small">{{ date('d M Y', strtotime($b->start_date)) }}</div>
                            <div class="text-muted small">{{ date('H:i', strtotime($b->start_date)) }} - {{ date('H:i', strtotime($b->end_date)) }}</div>
                        </td>
                        <td>
                            @if($b->status == 1)
                                <span class="badge bg-primary">Booked</span>
                            @elseif($b->status == 2)
                                <span class="badge bg-success">Occupied / Selesai</span>
                            @else
                                <span class="badge bg-danger">Cancelled</span>
                            @endif
                        </td>
                        <td>
                            @if($b->status == 1)
                                <button class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-info-circle"></i> Detail
                                </button>
                            @elseif($b->status == 2)
                                <button type="button" class="btn btn-sm btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#modalRating{{ $b->booking_id }}">
                                    <i class="bi bi-star-fill"></i> Beri Rating
                                </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL RATING --}}
@foreach($bookings_dummy as $b)
    @if($b->status == 2)
    <div class="modal fade" id="modalRating{{ $b->booking_id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('ratings.store') }}" method="POST">
                @csrf
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">Rating: {{ $b->room_name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="booking_id" value="{{ $b->booking_id }}">
                        <input type="hidden" name="item_id" value="{{ $b->room_id }}">
                        <input type="hidden" name="item_type" value="1">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small">KEBERSIHAN</label>
                            <select name="kebersihan" class="form-select border-primary shadow-sm">
                                <option value="5">⭐⭐⭐⭐⭐ (Sangat Bersih)</option>
                                <option value="4">⭐⭐⭐⭐ (Bersih)</option>
                                <option value="3">⭐⭐⭐ (Cukup)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">PELAYANAN</label>
                            <select name="pelayanan" class="form-select border-primary shadow-sm">
                                <option value="5">⭐⭐⭐⭐⭐</option>
                                <option value="4">⭐⭐⭐⭐</option>
                                <option value="3">⭐⭐⭐</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">KENYAMANAN</label>
                            <select name="kenyamanan" class="form-select border-primary shadow-sm">
                                <option value="5">⭐⭐⭐⭐⭐</option>
                                <option value="4">⭐⭐⭐⭐</option>
                                <option value="3">⭐⭐⭐</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary w-100 shadow">Simpan Penilaian</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
@endforeach
@endsection