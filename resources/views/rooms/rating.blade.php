@extends('layout.layout')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 16px;">
                <div class="text-center mb-4">
                    <h4 class="fw-bold">Beri Penilaian</h4>
                    <p class="text-muted small">Pengalaman Anda sangat berharga bagi kami.</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
                @endif

                <form action="{{ route('ratings.store') }}" method="POST">
                    @csrf
                    
                    {{-- Hidden fields sesuai kebutuhan controller kamu --}}
                    <input type="hidden" name="booking_id" value="{{ $booking->booking_id }}">
                    <input type="hidden" name="item_id" value="{{ $room->room_id }}">

                    {{-- Rating Kebersihan --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold d-block text-center mb-3">Kebersihan</label>
                        <div class="star-rating d-flex justify-content-center flex-row-reverse">
                            @for($i=5; $i>=1; $i--)
                                <input type="radio" id="clean-{{$i}}" name="kebersihan" value="{{$i}}" required />
                                <label for="clean-{{$i}}" class="bi bi-star-fill px-1"></label>
                            @endfor
                        </div>
                    </div>

                    {{-- Rating Pelayanan --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold d-block text-center mb-3">Pelayanan</label>
                        <div class="star-rating d-flex justify-content-center flex-row-reverse">
                            @for($i=5; $i>=1; $i--)
                                <input type="radio" id="service-{{$i}}" name="pelayanan" value="{{$i}}" required />
                                <label for="service-{{$i}}" class="bi bi-star-fill px-1"></label>
                            @endfor
                        </div>
                    </div>

                    {{-- Rating Kenyamanan --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold d-block text-center mb-3">Kenyamanan</label>
                        <div class="star-rating d-flex justify-content-center flex-row-reverse">
                            @for($i=5; $i>=1; $i--)
                                <input type="radio" id="comfort-{{$i}}" name="kenyamanan" value="{{$i}}" required />
                                <label for="comfort-{{$i}}" class="bi bi-star-fill px-1"></label>
                            @endfor
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn btn-tiket w-100 py-3 shadow-sm fw-bold">
                            Kirim Ulasan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Style Bintang Interaktif */
    .star-rating input { display: none; }
    .star-rating label {
        font-size: 2.2rem;
        color: #e9ecef;
        cursor: pointer;
        transition: color 0.2s ease-in-out;
    }
    .star-rating input:checked ~ label { color: #FDBE2D; }
    .star-rating label:hover,
    .star-rating label:hover ~ label { color: #FDBE2D; }
</style>
@endsection