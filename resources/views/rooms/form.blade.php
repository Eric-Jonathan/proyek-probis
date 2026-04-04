@extends('layout.layout')

@section('content')
<div class="container py-5">

    {{-- HEADER --}}
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold">Tambah Ruangan</h2>
            <div class="alert alert-light border d-flex align-items-center" style="background-color:#f5f5f5;">
                <i class="bi bi-info-circle me-2"></i>
                <span class="small">Isi data ruangan dengan lengkap</span>
            </div>
        </div>
    </div>

    <div class="row">

        {{-- FORM --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 p-4" style="border-radius: 8px;">

                <h4 class="fw-bold mb-3">Room Details</h4>

                <form action="{{ route('rooms.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">

                        {{-- NAME --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Nama Ruangan *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- FLOOR --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Lantai *</label>
                            <input type="number" name="floor" class="form-control" value="{{ old('floor') }}">
                        </div>

                        {{-- CAPACITY --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Kapasitas *</label>
                            <input type="number" name="capacity" class="form-control" value="{{ old('capacity') }}">
                        </div>

                        {{-- PRICE --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Harga *</label>
                            <input type="number" name="price" class="form-control" value="{{ old('price') }}">
                        </div>

                        {{-- STATUS --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Status *</label>
                            <select name="status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                                <option value="2">Maintenance</option>
                            </select>
                        </div>

                        {{-- DESCRIPTION --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>

                       {{-- LOCATION --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small">Lokasi *</label>
                            <input type="text" name="location" class="form-control" value="{{ old('location') }}">
                            @error('location') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- DEPOSIT --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Deposit (%)</label>
                            <input type="number" name="deposit_percent" class="form-control" value="{{ old('deposit_percent', 0) }}">
                        </div>

                        {{-- RULES --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small">Peraturan Ruangan</label>
                            <textarea name="rules" class="form-control" rows="3">{{ old('rules') }}</textarea>
                        </div>

                        {{-- FACILITIES --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small">Fasilitas</label>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="form-check">
                                    <input type="checkbox" name="facilities[]" value="wifi" class="form-check-input">
                                    <label class="form-check-label small">WiFi</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="facilities[]" value="ac" class="form-check-input">
                                    <label class="form-check-label small">AC</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="facilities[]" value="tv" class="form-check-input">
                                    <label class="form-check-label small">TV</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="facilities[]" value="sound" class="form-check-input">
                                    <label class="form-check-label small">Sound System</label>
                                </div>
                            </div>
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
                            Simpan Ruangan
                            <i class="bi bi-chevron-right ms-2"></i>
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection