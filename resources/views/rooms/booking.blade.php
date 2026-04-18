@extends('layout.layout')

@section('content')
<body>
    <div class="container">
    <button class="btn btn-outline-danger btn-back mb-2">< Back</button>
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold">Don't forget your booking</h2>
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i>
                <div>
                    Hampir selesai! Selesaikan rincian untuk pemesanan Anda. 
                    <strong>Tinggal 7 hari lagi!</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5 row">
        <div class="row col-4">
            <div class="col-lg-12 mb-4">
                <div class="card shadow-sm border-0 overflow-hidden" style="border-radius: 12px;">
                    <img src="https://via.placeholder.com/600x400" class="card-img-top" alt="Kontena Hotel Lobby" style="height: 250px; object-fit: cover;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-2">
                            <div class="text-warning me-2">
                                <i class="bi bi-star-fill small"></i>
                                <i class="bi bi-star-fill small"></i>
                                <i class="bi bi-star-fill small"></i>
                            </div>
                            <div class="bg-warning text-white d-flex align-items-center justify-content-center rounded" style="width: 24px; height: 24px;">
                                <i class="bi bi-hand-thumbs-up-fill small"></i>
                            </div>
                        </div>

                        <h4 class="fw-bold mb-2">{{ $room->name }}</h4>
                        <p class="text-muted small mb-1">
                            JL. {{ $room->location }}
                        </p>

                        <p class="text-success small fw-semibold mb-3">
                            Excellent Location — 9.0
                        </p>

                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary text-white fw-bold rounded d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px; background-color: #003580 !important;">
                                8.7
                            </div>
                            <div class="small">
                                <span class="fw-bold">Excellent</span>
                                <span class="text-muted"> · 37 reviews</span>
                            </div>
                        </div>

                        <div class="row g-3 text-dark small">
                            <div class="col-auto">
                                <i class="bi bi-fork-knife"></i> Restaurant
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-p-circle me-1"></i> Parking
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-snow me-1"></i> Air conditioning
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-droplet me-1"></i> Private bathroom
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-binoculars"></i> View
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0" style="border-radius: 12px; max-width: 450px; border: 1px solid #e7e7e7 !important; margin-top: 2vw;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" style="color: #1a1a1a;">Your booking details</h5> 
                    
                        <div class="row mb-3 position-relative">
                            <div class="position-absolute start-50 top-0 bottom-0 border-start" style="width: 1px; height: 100%; opacity: 0.1; "></div>
                        
                            <div class="col-6">
                                <p class="mb-1 fw-bold small">Check-in</p> 
                                <p class="mb-0 fw-bold" style="font-size: 1.1rem;">Thu, Apr 9, 2026</p> 
                                <p class="text-muted small mb-0">From 3:00 PM</p> 
                            </div>
                        
                            <div class="col-6 ps-4">
                                <p class="mb-1 fw-bold small">Check-out</p> 
                                <p class="mb-0 fw-bold" style="font-size: 1.1rem;">Sun, Apr 12, 2026</p> 
                                <p class="text-muted small mb-0">12:00 AM – 12:00 PM</p> 
                            </div>
                        </div>
                    
                        <div class="d-flex align-items-center mt-3" style="color: #a35d14;">
                            <div class="rounded-circle border border-2 d-flex align-items-center justify-content-center me-2" style="width: 26px; height: 26px; border-color: #a35d14 !important;">
                                <span class="fw-bold" style="font-size: 0.9rem;">!</span>
                            </div>
                            <span class="fw-bold small">Just 7 days away!</span> 
                        </div>
                    
                        <hr class="my-4" style="opacity: 0.1;">
                    
                        <div class="mb-1">
                            <p class="mb-2 fw-bold small">You selected</p>
                            <p class="mb-1 fw-bold" style="font-size: 1.1rem;">3 nights, 1 room for 2 adults</p>
                            <p class="mb-3 text-dark">1 x Kontena Twin Room</p>
                            <a href="#" class="text-decoration-none small fw-bold" style="color: #006ce4;">Change your selection</a> 
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0" style="border-radius: 12px; max-width: 450px; border: 1px solid #e7e7e7 !important; margin-top: 2vw;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3" style="color: #1a1a1a;">Your price summary</h5>

                        <div class="d-flex justify-content-between mb-1">
                            <span>Original price</span>
                            <span>Rp 1,449,000</span>
                        </div>

                        <div class="d-flex justify-content-between mb-1">
                            <span>Genius Discount</span>
                            <span>- Rp 169,050</span>
                        </div>

                        <p class="text-muted small mb-0" style="line-height: 1.2;">
                            You're getting a reduced rate because you're a Genius member.
                        </p>
                    </div>
                
                    <div class="p-4" style="background-color: #ebf3ff;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="fw-bold mb-0">Total</h3>
                            <div class="text-end">
                                <del class="text-danger small d-block mb-1">Rp 1,449,000</del>
                                <h3 class="fw-bold mb-0">Rp 1,279,950</h3>
                                <p class="text-muted small mb-0">Includes taxes and fees</p>
                            </div>
                        </div>
                    </div>
                
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3">Price information</h6>

                        <div class="d-flex align-items-start mb-2">
                            <i class="bi bi-cash-stack me-2 mt-1"></i>
                            <span class="small fw-semibold">Includes Rp 116,359 in taxes and fees</span>
                        </div>

                        <div class="d-flex justify-content-between text-muted small ps-4">
                            <span>10 % Tax</span>
                            <span>Rp 116,359</span>
                        </div>
                    
                        <div class="mt-4">
                            <a href="#" class="text-decoration-none small fw-bold" style="color: #006ce4;">Hide details</a>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0" style="border-radius: 12px; max-width: 500px; border: 1px solid #e7e7e7 !important; margin-top: 2vw;">
                    <div class="card-body p-4">
                        <h7 class="fw-bold" style="color: #1a1a1a;">How much will it cost to cancel?</h7>

                        <div class="mb-2">
                            <p class="text-success fw-bold mb-1" style="font-size: 1.1rem;">
                                Free cancellation before Apr 
                            </p>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="text-dark" style="font-size: 1.05rem;">After 12:00 AM on Apr 8</span>
                            <span class="fw-bold" style="font-size: 1.05rem;">Rp 1,279,950</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row col-8">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 8px; border: 1px solid #e7e7e7 !important;">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="me-3">
                            <div class="rounded-circle overflow-hidden" style="width: 48px; height: 48px; border: 2px solid #ffbb00;">
                                <img src="path_ke_avatar_anda.png" alt="User Avatar" class="img-fluid">
                            </div>
                        </div>

                        <div>
                            <p class="mb-0 fw-bold" style="color: #1a1a1a;">You are signed in </p>
                            <p class="mb-0 text-muted small">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 p-4" style="border-radius: 8px;">
                    <h4 class="fw-bold mb-3">Enter your details</h4>

                    <div class="alert alert-light border d-flex align-items-center py-2 mb-4" style="background-color: #f5f5f5;">
                        <i class="bi bi-info-circle me-2"></i>
                        <span class="small">Almost done! Just fill in the <span class="text-danger">*</span> required info</span>
                    </div>
                
                    <form class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">First name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Last name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="">
                        </div>
                    
                        <div class="col-12">
                            <label class="form-label fw-bold small">Email address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" value="">
                            <div class="form-text small" style="font-size: 0.75rem;">Confirmation email sent to this address</div>
                        </div>
                    
                        <div class="col-12">
                            <label class="form-label fw-bold small">Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Zip Code (optional)</label>
                            <input type="text" class="form-control">
                        </div>
                    
                        <div class="col-12">
                            <label class="form-label fw-bold small">Country/Region <span class="text-danger">*</span></label>
                            <select class="form-select">
                                <option selected>Indonesia</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Phone number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select class="form-select" style="max-width: 100px;">
                                    <option>ID +62</option>
                                </select>
                                <input type="text" class="form-control">
                            </div>
                            <div class="form-text text-muted small" style="font-size: 0.75rem;">To verify your booking, and for the property to connect if needed</div>
                        </div>
                    
                        <div class="col-12 mt-4">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="paperless">
                                <label class="form-check-label small" for="paperless">
                                    <strong>Yes, I want free paperless confirmation (recommended)</strong><br>
                                    <span class="text-muted">We'll text you a link to download our app</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="updateAccount">
                                <label class="form-check-label small" for="updateAccount">
                                    Update my account to include these new details
                                </label>
                            </div>
                        </div>
                    
                        <hr class="my-4">
                    
                        <div class="col-12">
                            <p class="small fw-bold mb-2">Who are you booking for? <span class="text-muted fw-normal">(optional)</span></p>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="bookingFor" id="mainGuest" checked>
                                <label class="form-check-label small" for="mainGuest">I'm the main guest</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="bookingFor" id="otherGuest">
                                <label class="form-check-label small" for="otherGuest">I'm booking for someone else</label>
                            </div>
                        </div>
                    
                        <div class="col-12 mt-3">
                            <p class="small fw-bold mb-2">Are you traveling for work? <span class="text-muted fw-normal">(optional)</span></p>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="workTrip" id="workYes">
                                    <label class="form-check-label small" for="workYes">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="workTrip" id="workNo">
                                    <label class="form-check-label small" for="workNo">No</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card border-0 shadow-sm mb-4" style="border-radius: 8px; border: 1px solid #e7e7e7 !important; margin-top: 2vw;">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-2" style="color: #1a1a1a;">Good to know:</h6>

                        <div class="d-flex align-items-start">
                            <div class="me-2 mt-1">
                                <i class="bi bi-check-circle text-success" style="font-size: 1.1rem;"></i>
                            </div>
                            <p class="mb-0 small">
                                <span class="fw-bold">Stay flexible:</span> 
                                You can cancel for free before April 8, 2026 – lock in this great price today. 
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4" style="border-radius: 8px; border: 1px solid #e7e7e7 !important;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" style="color: #1a1a1a;">Add to your stay</h5>

                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="d-flex align-items-start">
                                <div class="form-check me-2">
                                    <input class="form-check-input" type="checkbox" value="" id="rentCar" style="width: 1.5em; height: 1.5em;">
                                </div>
                                <div class="ms-2">
                                    <label class="form-check-label fw-bold d-block" for="rentCar" style="font-size: 1.05rem;">
                                        I'm interested in renting a car
                                    </label>
                                    <p class="text-muted small mb-0">Make the most of your trip — check out car rental options in your booking confirmation.</p>
                                </div>
                            </div>
                            <div class="text-muted ms-3">
                                <i class="bi bi-car-front" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    
                        <hr class="my-4" style="opacity: 0.1;">
                    
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex align-items-start">
                                <div class="form-check me-2">
                                    <input class="form-check-input" type="checkbox" value="" id="bookTaxi" style="width: 1.5em; height: 1.5em;">
                                </div>
                                <div class="ms-2">
                                    <label class="form-check-label fw-bold d-block" for="bookTaxi" style="font-size: 1.05rem;">
                                        Want to book a taxi or shuttle ride in advance?
                                    </label>
                                    <p class="text-muted small mb-0">Avoid surprises — get from the airport to your accommodations without any hassle. We'll add taxi options to your booking confirmation.</p>
                                </div>
                            </div>
                            <div class="text-muted ms-3">
                                <i class="bi bi-taxi-front" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4" style="border-radius: 8px; border: 1px solid #e7e7e7 !important;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3" style="color: #1a1a1a;">Special requests</h5>

                        <p class="small mb-3" style="color: #4a4a4a; line-height: 1.5;">
                            Special requests can't be guaranteed, but the property will do its best to meet your needs. You can always make a special request after your booking is complete.
                        </p>
                    
                        <label for="specialRequestsArea" class="form-label small fw-bold mb-2">
                            Please write your requests in English or Indonesian. <span class="text-muted fw-normal">(optional)</span> 
                        </label>
                    
                        <textarea 
                            class="form-control" 
                            id="specialRequestsArea" 
                            rows="4" 
                            style="border-color: #868686; resize: vertical;">
                        </textarea>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4" style="border-radius: 8px; border: 1px solid #e7e7e7 !important;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" style="color: #1a1a1a;">Check-in details</h5>

                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
                            </div>
                            <p class="mb-0" style="color: #1a1a1a;">
                                Your room will be ready for check-in at 3:00 PM 
                            </p>
                        </div>
                    
                        <div class="d-flex align-items-center mb-4">
                            <div class="me-3">
                                <i class="bi bi-person-workspace text-success" style="font-size: 1.5rem;"></i>
                            </div>
                            <p class="mb-0" style="color: #1a1a1a;">
                                24-hour front desk – help whenever you need it! 
                            </p>
                        </div>
                    
                        <div class="col-md-6">
                            <label for="arrivalTime" class="form-label fw-bold small mb-2">
                                Add your estimated arrival time <span class="text-muted fw-normal">(optional)</span>
                            </label>
                            <select class="form-select" id="arrivalTime" style="border-color: #868686;">
                                <option selected>Please select</option>
                                </select>
                        </div>
                    </div>
                </div>     
                
                <div class="card border shadow-sm mb-4" style="border-radius: 8px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Cribs and extra beds</h5>
                        
                        <ul class="list-unstyled mb-3">
                            <li class="mb-2 d-flex align-items-start small">
                                <span class="me-2">•</span>
                                <span>Requests are subject to availability</span>
                            </li>
                            <li class="mb-2 d-flex align-items-start small">
                                <span class="me-2">•</span>
                                <span>Requests need to be confirmed by the property </span>
                            </li>
                            <li class="mb-2 d-flex align-items-start small">
                                <span class="me-2">•</span>
                                <span>Requests not labeled "Free" could incur extra charges </span>
                            </li>
                        </ul>
                    
                        <div class="mb-4">
                            <a href="#" class="text-decoration-none small fw-bold" style="color: #006ce4;">
                                Read full crib and extra bed policy
                            </a>
                        </div>
                    
                        <div class="mt-2">
                            <p class="small text-muted mb-3">Add to your <strong>Kontena Twin Room</strong></p>
                            <div class="form-check">
                                <input class="form-check-input shadow-none" type="checkbox" id="extraBedCheck" style="width: 1.5em; height: 1.5em; border-radius: 4px;">
                                <label class="form-check-label ms-2 small pt-1" for="extraBedCheck">
                                    Extra bed
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column align-items-end py-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-4 d-flex align-items-center" style="color: #006ce4;">
                            <i class="bi bi-tag me-1" style="transform: rotate(90deg);"></i>
                            <span class="small fw-bold">We Price Match</span>
                        </div>

                        <button class="btn btn-primary d-flex align-items-center px-4 py-2 fw-bold" style="background-color: #006ce4; border-radius: 4px;">
                            Next: Final details
                            <i class="bi bi-chevron-right ms-3"></i>
                        </button>
                    </div>
                
                    <div>
                        <a href="#" class="text-decoration-none small fw-bold" style="color: #006ce4;">
                            What are my booking conditions?
                        </a>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
    <script src="{{ asset('custom_js/rooms/booking.js') }}"></script>
@endsection