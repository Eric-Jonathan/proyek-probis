@extends('layout.layout')

@section('custom_css')
<style>
    body {
        background-color: #f8f9fa;
    }
    .withdraw-card {
        border-radius: 20px;
        border: none;
        background: #fff;
    }
    .wallet-banner {
        background: linear-gradient(135deg, #198754 0%, #146c43 100%);
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
    .btn-withdraw {
        background: #198754;
        color: white;
        border: none;
        border-radius: 50px;
        padding: 14px 28px;
        font-weight: 700;
        transition: all 0.2s;
        box-shadow: 0 4px 10px rgba(25, 135, 84, 0.2);
    }
    .btn-withdraw:hover {
        background: #146c43;
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(25, 135, 84, 0.3);
    }
    .form-label {
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #4b5563;
    }
    .form-control:focus, .form-select:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.1);
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
                    <h3 class="fw-bold mb-0 text-dark">Cairkan Saldo</h3>
                    <p class="text-secondary small mb-0">Tarik saldo Anda langsung ke rekening bank pribadi</p>
                </div>
            </div>

            {{-- Alert Error --}}
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3 p-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                        <div>
                            <strong class="d-block mb-1">Gagal mengajukan penarikan:</strong>
                            <ul class="mb-0 ps-3 small">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card withdraw-card shadow-sm p-4 p-md-5 mb-4">
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

                {{-- Withdrawal Form --}}
                <form action="{{ route('withdraw.process') }}" method="POST" id="withdraw-form">
                    @csrf
                    
                    {{-- Amount Input --}}
                    <div class="mb-4">
                        <label for="amount" class="form-label">Nominal Penarikan (Rupiah)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-end-0 text-secondary fw-semibold">Rp</span>
                            <input type="number" name="amount" id="amount" class="form-control border-start-0 fs-4 fw-bold text-dark" 
                                   placeholder="Minimal 10.000" min="10000" max="{{ $user->saldo }}" value="{{ old('amount') }}" required>
                        </div>
                        <div class="form-text text-muted small mt-1">
                            Jumlah penarikan minimal Rp 10.000 dan tidak boleh melebihi saldo aktif Anda.
                        </div>
                    </div>

                    {{-- Bank Name --}}
                    <div class="mb-4">
                        <label for="bank_name" class="form-label">Bank Tujuan</label>
                        <select name="bank_name" id="bank_name" class="form-select form-select-lg text-dark" required>
                            <option value="" disabled selected>-- Pilih Bank --</option>
                            <option value="BCA" {{ old('bank_name') == 'BCA' ? 'selected' : '' }}>Bank Central Asia (BCA)</option>
                            <option value="Mandiri" {{ old('bank_name') == 'Mandiri' ? 'selected' : '' }}>Bank Mandiri</option>
                            <option value="BNI" {{ old('bank_name') == 'BNI' ? 'selected' : '' }}>Bank Negara Indonesia (BNI)</option>
                            <option value="BRI" {{ old('bank_name') == 'BRI' ? 'selected' : '' }}>Bank Rakyat Indonesia (BRI)</option>
                            <option value="CIMB Niaga" {{ old('bank_name') == 'CIMB Niaga' ? 'selected' : '' }}>Bank CIMB Niaga</option>
                            <option value="Permata" {{ old('bank_name') == 'Permata' ? 'selected' : '' }}>Bank Permata</option>
                            <option value="Danamon" {{ old('bank_name') == 'Danamon' ? 'selected' : '' }}>Bank Danamon</option>
                            <option value="BTPN" {{ old('bank_name') == 'BTPN' ? 'selected' : '' }}>Bank BTPN</option>
                            <option value="Jago" {{ old('bank_name') == 'Jago' ? 'selected' : '' }}>Bank Jago</option>
                        </select>
                    </div>

                    {{-- Account Number --}}
                    <div class="mb-4">
                        <label for="account_number" class="form-label">Nomor Rekening</label>
                        <input type="text" name="account_number" id="account_number" class="form-control form-control-lg text-dark" 
                               placeholder="Masukkan nomor rekening bank" value="{{ old('account_number') }}" required>
                    </div>

                    {{-- Account Name --}}
                    <div class="mb-4">
                        <label for="account_name" class="form-label">Nama Pemilik Rekening</label>
                        <input type="text" name="account_name" id="account_name" class="form-control form-control-lg text-dark" 
                               placeholder="Nama lengkap sesuai buku tabungan" value="{{ old('account_name') }}" required>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="btn btn-withdraw w-100 py-3 mt-2 fs-5 fw-bold">
                        <i class="bi bi-check2-circle me-2"></i> Cairkan Saldo Sekarang
                    </button>
                </form>

            </div>
            
        </div>
    </div>
</div>
@endsection
