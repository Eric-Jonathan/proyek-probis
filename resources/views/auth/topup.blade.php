@extends('layout.layout')

@section('custom_css')
<style>
    body {
        background-color: #f8f9fa;
    }
    .topup-card {
        border-radius: 20px;
        border: none;
        background: #fff;
    }
    .wallet-banner {
        background: linear-gradient(135deg, #006ce4 0%, #004b9e 100%);
        color: white;
        border-radius: 16px;
        padding: 24px;
        position: relative;
        overflow: hidden;
    }
    .wallet-banner::after {
        content: '';
        position: absolute;
        width: 150px;
        height: 150px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
        top: -50px;
        right: -50px;
    }
    .quick-amount-btn {
        background-color: #f3f4f6;
        color: #1f2937;
        border: 2px solid transparent;
        border-radius: 12px;
        padding: 14px;
        font-weight: 700;
        transition: all 0.2s;
        cursor: pointer;
        text-align: center;
        font-size: 0.95rem;
    }
    .quick-amount-btn:hover {
        background-color: #e5e7eb;
        transform: translateY(-1px);
    }
    .quick-amount-btn.active {
        background-color: #e8f2ff;
        color: #006ce4;
        border-color: #006ce4;
    }
    .btn-topup {
        background: #006ce4;
        color: white;
        border: none;
        border-radius: 50px;
        padding: 14px 28px;
        font-weight: 700;
        transition: all 0.2s;
        box-shadow: 0 4px 10px rgba(0, 108, 228, 0.2);
    }
    .btn-topup:hover {
        background: #0056b8;
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(0, 108, 228, 0.3);
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            
            {{-- Header with Back Button --}}
            <div class="d-flex align-items-center mb-4">
                <a href="javascript:history.back()" class="btn btn-light rounded-circle me-3 shadow-sm border d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-0 text-dark">Top Up Saldo</h3>
                    <p class="text-secondary small mb-0">Isi ulang saldo akun Anda untuk kemudahan transaksi persewaan ruangan</p>
                </div>
            </div>

            <div class="card topup-card shadow-sm p-4 p-md-5 mb-4">
                {{-- Wallet Balance Banner --}}
                <div class="wallet-banner mb-4 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="small opacity-75 mb-1 text-uppercase fw-semibold" style="letter-spacing: 0.5px;">Saldo Saat Ini</div>
                        <h2 class="fw-bold mb-0 fs-1">Rp {{ number_format($user->saldo, 0, ',', '.') }}</h2>
                    </div>
                    <div class="fs-1 opacity-50">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>

                {{-- Top Up Form --}}
                <form id="topup-form" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="amount" class="form-label fw-bold small text-uppercase text-secondary" style="letter-spacing: 0.5px;">Masukkan Nominal (Rupiah)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-end-0 fw-bold text-muted" style="font-size: 1.25rem;">Rp</span>
                            <input type="number" name="amount" id="amount" class="form-control border-start-0 fw-bold text-dark" style="font-size: 1.25rem;" min="10000" placeholder="Minimal Rp 10.000" required>
                        </div>
                        <div class="form-text text-muted mt-2">Minimal nominal pengisian adalah Rp 10.000.</div>
                    </div>

                    {{-- Quick Amount Grid --}}
                    <div class="mb-5">
                        <label class="form-label fw-bold small text-uppercase text-secondary mb-3" style="letter-spacing: 0.5px;">Pilihan Instan</label>
                        <div class="row g-3">
                            <div class="col-6 col-md-4">
                                <div class="quick-amount-btn" data-value="20000">Rp 20.000</div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="quick-amount-btn" data-value="50000">Rp 50.000</div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="quick-amount-btn" data-value="100000">Rp 100.000</div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="quick-amount-btn" data-value="250000">Rp 250.000</div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="quick-amount-btn" data-value="500000">Rp 500.000</div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="quick-amount-btn" data-value="1000000">Rp 1.000.000</div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="submit-topup" class="btn btn-topup w-100 py-3 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-wallet2 fs-5"></i> Top Up Sekarang
                    </button>
                </form>
            </div>

            <div class="text-center text-muted small">
                Transaksi Anda dilindungi dan diproses secara aman menggunakan payment gateway Midtrans Sandbox.
            </div>

        </div>
    </div>
</div>
@endsection

@section('custom_js')
{{-- Midtrans Snap JS Integration --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const amountInput = document.getElementById('amount');
        const quickBtns = document.querySelectorAll('.quick-amount-btn');
        const form = document.getElementById('topup-form');
        const submitBtn = document.getElementById('submit-topup');

        // Quick button selection
        quickBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                quickBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                amountInput.value = this.dataset.value;
            });
        });

        amountInput.addEventListener('input', function() {
            quickBtns.forEach(btn => {
                if (btn.dataset.value === this.value) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        });

        // Form Submission with Midtrans Snap
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const amount = amountInput.value;
            if (amount < 10000) {
                alert('Nominal minimal adalah Rp 10.000');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Memproses...';

            fetch('{{ route("topup.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ amount: amount })
            })
            .then(response => response.json())
            .then(data => {
                if (data.isSimulated) {
                    // Simulation flow fallback
                    alert("Simulasi Pembayaran Midtrans Sandbox Aktif!\nMenambah Rp " + new Intl.NumberFormat('id-ID').format(amount) + " ke Saldo Anda...");
                    finalizeTopup(amount);
                } else {
                    // Real Snap flow
                    window.snap.pay(data.token, {
                        onSuccess: function(result) {
                            finalizeTopup(amount);
                        },
                        onPending: function(result) {
                            alert("Pembayaran tertunda. Silakan selesaikan pembayaran Anda.");
                            resetSubmitBtn();
                        },
                        onError: function(result) {
                            alert("Top up gagal! Silakan coba lagi.");
                            resetSubmitBtn();
                        },
                        onClose: function() {
                            alert("Anda menutup jendela pembayaran sebelum menyelesaikan transaksi.");
                            resetSubmitBtn();
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error initiating topup:', error);
                alert('Terjadi kesalahan saat menghubungi server.');
                resetSubmitBtn();
            });
        });

        function finalizeTopup(amount) {
            fetch('{{ route("topup.callback") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ amount: amount })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Top up berhasil! Saldo Anda sekarang: Rp ' + new Intl.NumberFormat('id-ID').format(data.new_balance));
                    window.location.href = '{{ route("profile.show") }}';
                } else {
                    alert('Gagal memperbarui saldo di database.');
                    resetSubmitBtn();
                }
            })
            .catch(error => {
                console.error('Error finalizing topup:', error);
                alert('Terjadi kesalahan saat memfinalisasi top up.');
                resetSubmitBtn();
            });
        }

        function resetSubmitBtn() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-wallet2 fs-5"></i> Top Up Sekarang';
        }
    });
</script>
@endsection
