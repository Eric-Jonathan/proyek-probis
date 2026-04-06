@extends('layout.layout')

@section('content')
<div class="container py-5">

    {{-- HEADER --}}
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold">Edit Ruangan</h2>
            <div class="alert alert-light border d-flex align-items-center" style="background-color:#f5f5f5;">
                <i class="bi bi-info-circle me-2"></i>
                <span class="small">Perbarui data ruangan dengan benar</span>
            </div>
        </div>
    </div>

    <div class="row">

        {{-- FORM --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 p-4" style="border-radius: 8px;">

                <h4 class="fw-bold mb-3">Room Details</h4>

                <form action="{{ route('rooms.update', $room->room_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">

                        {{-- NAME --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Nama Ruangan *</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $room->name) }}">
                        </div>

                        {{-- FLOOR (opsional kalau masih mau dipakai) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Lantai</label>
                            <input type="number" name="floor" class="form-control"
                                value="{{ old('floor') }}">
                        </div>

                        {{-- CAPACITY --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Kapasitas *</label>
                            <input type="number" name="capacity" class="form-control"
                                value="{{ old('capacity', $room->capacity) }}">
                        </div>

                        {{-- PRICE --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Harga *</label>
                            <input type="number" name="price" class="form-control"
                                value="{{ old('price', $room->price) }}">
                        </div>

                        {{-- STATUS --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Status *</label>
                            <select name="status" class="form-select">
                                <option value="1" {{ $room->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $room->status == 0 ? 'selected' : '' }}>Inactive</option>
                                <option value="2" {{ $room->status == 2 ? 'selected' : '' }}>Maintenance</option>
                            </select>
                        </div>

                        {{-- DESCRIPTION --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3">
{{ old('description', $room->description) }}</textarea>
                        </div>

                        {{-- LOCATION --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small">Lokasi *</label>
                            <input type="text" name="location" class="form-control"
                                value="{{ old('location', $room->location) }}">
                        </div>

                        {{-- DEPOSIT --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Deposit (%)</label>
                            <input type="number" name="deposit_percent" class="form-control"
                                value="{{ old('deposit_percent', $room->deposit_percent) }}">
                        </div>

                        {{-- RULES --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small">Peraturan Ruangan</label>
                            <textarea name="rules" class="form-control" rows="3">
{{ old('rules', $room->rules) }}</textarea>
                        </div>

                        {{-- IMAGE --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small">Gambar</label>
                            <input type="file" name="image" class="form-control">
                        </div>

                    </div>

                    <hr class="my-4">

                    {{-- BUTTON --}}
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4 fw-bold"
                            style="background-color:#006ce4;">
                            Update Ruangan
                            <i class="bi bi-chevron-right ms-2"></i>
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection