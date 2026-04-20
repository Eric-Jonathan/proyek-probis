@extends('layout.layout')

@section('content')
<style>
    :root {
        --primary-blue: #006ce4;
        --light-blue: #e8f2ff;
        --success-green: #28a745;
        --soft-gray: #f8f9fa;
        --text-dark: #1f2937;
    }

    body{
        background: #fff;
    }

    .transaction-header{
        background: linear-gradient(135deg, #006ce4, #2b8cff);
        color: white;
        border-radius: 18px;
        padding: 28px;
    }

    .transaction-card{
        border: 1px solid #eef2f7;
        border-radius: 18px;
        background: white;
        transition: 0.25s ease;
    }

    .transaction-card:hover{
        transform: translateY(-2px);
        box-shadow: 0 14px 30px rgba(0,0,0,.06);
    }

    .label-title{
        font-size: .78rem;
        color: #8b98a7;
        margin-bottom: 4px;
    }

    .value-text{
        font-weight: 600;
        color: var(--text-dark);
    }

    .badge-status{
        background: #e9f7ef;
        color: orange;
        padding: 8px 14px;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 700;
    }

    .price-box{
        background: var(--light-blue);
        color: var(--primary-blue);
        padding: 14px 18px;
        border-radius: 14px;
        font-size: 1.2rem;
        font-weight: 800;
        text-align: center;
    }

    .btn-midtrans{
        background: var(--primary-blue);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px 18px;
        font-weight: 700;
        transition: .2s;
    }

    .btn-midtrans:hover{
        background: #0056b8;
        color: white;
    }

    .mini-line{
        border-top: 1px dashed #e5e7eb;
    }

</style>

<div class="container py-5">

    {{-- HEADER --}}
    <div class="transaction-header shadow-sm mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <p class="mb-1 opacity-75">Dashboard</p>
                <h2 class="fw-bold mb-0">My Transaction</h2>
            </div>

            <span class="badge-status">
                Pending
            </span>
        </div>
    </div>

    {{-- CARD --}}
    <div class="transaction-card p-4 shadow-sm">

        <div class="row g-4">

            {{-- LEFT --}}
            <div class="col-lg-8">

                <div class="row g-4">

                    <div class="col-md-6">
                        <div class="label-title">Name</div>
                        <div class="value-text">{{ $transaction->name }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="label-title">Booking Date</div>
                        <div class="value-text">
                            {{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="label-title">Place</div>
                        <div class="value-text">{{ $transaction->place }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="label-title">Price</div>
                        <div class="value-text text-primary fw-bold">
                            Rp {{ number_format($transaction->price,0,',','.') }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="label-title">Start Date</div>
                        <div class="value-text">
                            {{ \Carbon\Carbon::parse($transaction->start_date)->format('d M Y') }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="label-title">End Date</div>
                        <div class="value-text">
                            {{ \Carbon\Carbon::parse($transaction->end_date)->format('d M Y') }}
                        </div>
                    </div>

                </div>

                <div class="mini-line my-4"></div>

                <div class="small text-muted">
                    Transaction ID :
                    <span class="fw-bold text-dark">
                        #TRX{{ $transaction->id }}
                    </span>
                </div>

            </div>

            {{-- RIGHT --}}
            <div class="col-lg-4">

                <div class="border rounded-4 p-4 h-100 bg-light">

                    <p class="text-muted small mb-2">Total Payment</p>

                    <div class="price-box mb-4">
                        Rp {{ number_format($transaction->price,0,',','.') }}
                    </div>

                    <button id="pay-button" class="btn btn-midtrans w-100 mb-3">
                        Pay with Midtrans
                    </button>

                    <div class="small text-muted text-center">
                        Secure payment gateway by Midtrans
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

{{-- MIDTRANS --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
data-client-key="{{ config('midtrans.client_key') }}">
</script>

<script>
document.getElementById('pay-button').onclick = function () {

    window.snap.pay('{{ $snapToken }}', {
        onSuccess: function(result){
            alert("Payment Success");
            window.location.reload();
        },

        onPending: function(result){
            alert("Waiting Payment");
        },

        onError: function(result){
            alert("Payment Failed");
        },

        onClose: function(){
            alert("You closed the popup.");
        }
    });

};
</script>

@endsection