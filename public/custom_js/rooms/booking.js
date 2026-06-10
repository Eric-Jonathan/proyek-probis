$(document).on('click', '.btn-back', function(){
    window.location.href = '/penyewa/search';
});

$(document).ready(function() {
    
    // Ambil kontrol skema pricing dari elemen invoice sejak halaman pertama dimuat
    const $priceElement = $('#render-base-price');
    
    // PERBAIKAN: Langsung paksa ke huruf kecil sejak awal agar sinkron dengan database
    const jenisHarga = String($priceElement.data('jenis-harga') || '').trim().toLowerCase(); 

    // =========================================================================
    // CODE FIX: PROTEKSI OTOMATIS PILIHAN WAKTU BERDASARKAN DATABASE (UX LOCK)
    // =========================================================================
    if (jenisHarga === 'hari' || jenisHarga === 'pax') {
        // Jika kebijakan flat harian/pax, kunci ke radio button Harian
        $('#tipe-hari').prop('checked', true);
        $('#tipe-jam').prop('disabled', true); // Kunci opsi jam
        $('#container-input-jam').addClass('d-none'); // Sembunyikan inputan jam
        
    } else if (jenisHarga === 'jam' || jenisHarga === 'pax_jam') {
        // Jika kebijakan wajib per jam, kunci ke radio button Sistem Jam
        $('#tipe-jam').prop('checked', true);
        $('#tipe-hari').prop('disabled', true); // Kunci opsi harian
        $('#container-input-jam').removeClass('d-none'); // Tampilkan inputan jam
    }

    // 1. Logika Manual Toggle Jam / Hari jika di kemudian hari ada skema bebas
    $('.select-tipe-sewa').on('change', function() {
        if ($('#tipe-jam').is(':checked')) {
            $('#container-input-jam').removeClass('d-none');
        } else {
            $('#container-input-jam').addClass('d-none');
        }
        calculateTotalPrice(); // Hitung ulang harga jika tipe sewa bergeser
    });

    // 2. Cukup Satu Event Listener untuk Seluruh Form Input
    $(document).on('input change keyup blur', '#input-capacity, #jam_mulai, #jam_selesai, .addon-service-checkbox', function() {
        calculateTotalPrice();
    });

    // 3. Fungsi Utama Kalkulator Multi-Skema Pricing ERP
    function calculateTotalPrice() {
        let $priceElement = $('#render-base-price');
        let jenisHargaRaw = $priceElement.data('jenis-harga'); // 'pax', 'hari', 'jam', 'pax_jam'
        let jenisHarga = String(jenisHargaRaw).trim().toLowerCase(); 
        
        let rawPrice = parseInt($priceElement.data('raw-price')) || 0;
        let totalDays = parseInt($priceElement.data('total-days')) || 1;
        let minOrder = parseInt($priceElement.data('min-order')) || 1;

        // Ambil nilai input kapasitas
        let totalPaxInput = parseInt($('#input-capacity').val()) || 0;
        
        // Evaluasi penentu jumlah pengali orang (pax) untuk hitungan berbasis kapasitas
        let paxMultiplier = totalPaxInput;
        if (paxMultiplier < minOrder) {
            // Jika form kosong (0) atau di bawah ketentuan, paksa gunakan batas minimal order database
            paxMultiplier = minOrder; 
        }

        let basePriceCalculated = 0;
        let durationHours = 1; 

        // =========================================================================
        // REVISI TOTAL: NORMALISASI STRING FORMAT JAM UNTUK OBJECT DATE
        // =========================================================================
        let timeStartRaw = $('#jam_mulai').val() || "08:00"; 
        let timeEndRaw = $('#jam_selesai').val() || "16:00";

        // Ganti pemisah titik (.) menjadi titik dua (:) agar lolos parsing standarisasi mesin JavaScript
        let timeStart = timeStartRaw.replace('.', ':');
        let timeEnd = timeEndRaw.replace('.', ':');

        if (timeStart && timeEnd) {
            let dateStart = new Date("01/01/2026 " + timeStart);
            let dateEnd = new Date("01/01/2026 " + timeEnd);
            
            // Periksa apakah konversi objek tanggal berhasil dan valid
            if (!isNaN(dateStart.getTime()) && !isNaN(dateEnd.getTime())) {
                let diffMs = dateEnd.getTime() - dateStart.getTime();
                durationHours = Math.ceil(diffMs / (1000 * 60 * 60)); // Konversi ke satuan Jam
            }

            // Proteksi UX jika jam terbalik atau salah ketik mundur
            if (durationHours <= 0) {
                durationHours = 1; 
                $('#jam_selesai').addClass('is-invalid');
            } else {
                $('#jam_selesai').removeClass('is-invalid');
            }
        }

        // =========================================================================
        // EKSEKUSI DATA 4 FORMULA PRICING (SINKRONISASI BIAYA PER PAX DAN JAM)
        // =========================================================================
        if (jenisHarga === 'pax') {
            // Skema Cuma Per Pax: Harga x Pengali Orang x Total Hari
            basePriceCalculated = rawPrice * paxMultiplier * totalDays;
            $('#label-sewa-utama').text(`Sewa Ruangan (${paxMultiplier} Pax x ${totalDays} Hari)`);
            
        } else if (jenisHarga === 'hari') {
            // Skema Cuma Per Hari: Harga x Total Hari
            basePriceCalculated = rawPrice * totalDays;
            $('#label-sewa-utama').text(`Sewa Ruangan (${totalDays} Hari)`);
            
        } else if (jenisHarga === 'jam') {
            // Skema Cuma Per Jam: Harga x Durasi Jam
            basePriceCalculated = rawPrice * durationHours;
            $('#label-sewa-utama').text(`Sewa Ruangan (${durationHours} Jam)`);
            
        } else if (jenisHarga === 'pax_jam') {
            // Skenario Gabungan: Harga x Pengali Orang x Durasi Jam x Total Hari
            basePriceCalculated = rawPrice * paxMultiplier * durationHours * totalDays;
            $('#label-sewa-utama').text(`Sewa Ruangan (${paxMultiplier} Pax x ${durationHours} Jam)`);
        }

        // =========================================================================
        // HITUNG LAYANAN TAMBAHAN (ADDONS)
        // =========================================================================
        let extraCost = 0;
        $('#render-extra-services-cost').empty();

        $('.addon-service-checkbox:checked').each(function() {
            let serviceName = $(this).parent().find('label').text().trim().split('(')[0];
            let pricePerItem = parseInt($(this).data('price')) || 0;
            let costCalculated = pricePerItem;

            if ($(this).attr('id') === 'catering') {
                costCalculated = pricePerItem * paxMultiplier * totalDays;
            } else {
                costCalculated = pricePerItem;
            }
            extraCost += costCalculated;

            $('#render-extra-services-cost').append(`
                <div class="d-flex justify-content-between mb-2 small text-secondary">
                    <span>+ ${serviceName}</span>
                    <span>Rp ${costCalculated.toLocaleString('id-ID')}</span>
                </div>
            `);
        });

        // Perbarui visual teks invoice pada template Tempat-In
        $('#render-base-price').text('Rp ' + basePriceCalculated.toLocaleString('id-ID'));
        
        let finalTotal = basePriceCalculated + extraCost;
        $('#render-total-final').text('Rp ' + finalTotal.toLocaleString('id-ID'));

        // Dynamic payment scheme summary and calculations
        let scheme = $('#payment_scheme').val();
        let depositPercent = parseInt($('#render-base-price').data('deposit-percent')) || 0;
        let depositAmount = Math.ceil((depositPercent / 100) * finalTotal);

        if (scheme === 'installment') {
            let installmentPokok = Math.ceil(finalTotal / 3);
            let initialPaymentTotal = installmentPokok + depositAmount;

            $('#installment-container').removeClass('d-none');
            $('#render-installment-pax').text('Rp ' + installmentPokok.toLocaleString('id-ID'));
            $('#render-deposit-amount').text('Rp ' + depositAmount.toLocaleString('id-ID'));
            $('#render-initial-payment').text('Rp ' + initialPaymentTotal.toLocaleString('id-ID'));

            $('#payment-scheme-summary').html(
                `Pembayaran awal yang akan dipotong: <strong class="text-success">Rp ${initialPaymentTotal.toLocaleString('id-ID')}</strong> (Cicilan 1/3: Rp ${installmentPokok.toLocaleString('id-ID')} + Deposit: Rp ${depositAmount.toLocaleString('id-ID')}).`
            );
        } else {
            $('#installment-container').addClass('d-none');
            $('#payment-scheme-summary').html(
                `Jumlah yang akan dipotong: <strong class="text-primary">Rp ${finalTotal.toLocaleString('id-ID')}</strong> (Lunas).`
            );
        }
    }

    // Bind event for payment scheme select
    $(document).on('change', '#payment_scheme', function() {
        calculateTotalPrice();
    });

    // 4. Validasi Batas Maksimal Tamu & Saldo Sebelum Submit Form
    $('#main-booking-form').on('submit', function(e) {
        let inputCap = parseInt($('#input-capacity').val()) || 0;
        let maxCap = parseInt($('#input-capacity').attr('max')) || 0;

        if (inputCap > maxCap) {
            e.preventDefault();
            alert(`Jumlah tamu melebihi kapasitas maksimal ruangan! Ruangan ini hanya muat untuk maksimal ${maxCap} orang.`);
            $('#input-capacity').addClass('is-invalid').focus();
            return false;
        }

        // Validasi kecukupan saldo Tempat-In
        let userSaldo = parseInt($('#user-saldo').data('saldo')) || 0;
        let scheme = $('#payment_scheme').val();
        let finalTotalStr = $('#render-total-final').text().replace(/[^\d]/g, '');
        let finalTotal = parseInt(finalTotalStr) || 0;

        let depositPercent = parseInt($('#render-base-price').data('deposit-percent')) || 0;
        let depositAmount = Math.ceil((depositPercent / 100) * finalTotal);

        let paymentRequired = scheme === 'installment' ? (Math.ceil(finalTotal / 3) + depositAmount) : finalTotal;

        if (userSaldo < paymentRequired) {
            e.preventDefault();
            alert(`Saldo Tempat-In Anda (Rp ${userSaldo.toLocaleString('id-ID')}) tidak mencukupi untuk melakukan pembayaran sebesar Rp ${paymentRequired.toLocaleString('id-ID')}. Silakan Top Up terlebih dahulu!`);
            return false;
        }
    });

    // =========================================================================
    // TRIGGER KALKULASI AWAL SAAT HALAMAN PERTAMA KALI DIJALANKAN
    // =========================================================================
    calculateTotalPrice();
});