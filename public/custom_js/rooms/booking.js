$(document).on('click', '.btn-back', function(){
    window.location.href = '/penyewa/search';
});

$(document).ready(function() {
    
    // Ambil kontrol skema pricing dari elemen invoice sejak halaman pertama dimuat
    const $priceElement = $('#render-base-price');
    const roomId = $('#main-booking-form').data('room-id');
    const draftKey = 'booking_draft_' + roomId;

    // Hapus draf untuk ruangan lain agar bersih
    if (roomId) {
        for (let i = 0; i < sessionStorage.length; i++) {
            let key = sessionStorage.key(i);
            if (key && key.startsWith('booking_draft_') && key !== draftKey) {
                sessionStorage.removeItem(key);
                i--;
            }
        }
    }
    
    // PERBAIKAN: Langsung paksa ke huruf kecil sejak awal agar sinkron dengan database
    const jenisHarga = String($priceElement.data('jenis-harga') || '').trim().toLowerCase(); 

    // Muat draf booking jika ada
    if (roomId) {
        let draft = sessionStorage.getItem(draftKey);
        if (draft) {
            try {
                let data = JSON.parse(draft);
                if (data.instansi) $('input[name="instansi"]').val(data.instansi);
                if (data.jenis_acara) $('select[name="jenis_acara"]').val(data.jenis_acara);
                if (data.phone) $('input[name="phone"]').val(data.phone);
                if (data.total_capacity) $('#input-capacity').val(data.total_capacity);
                if (data.sewa_tipe) {
                    $(`.select-tipe-sewa[value="${data.sewa_tipe}"]`).prop('checked', true);
                    if (data.sewa_tipe === 'jam') {
                        $('#container-input-jam').removeClass('d-none');
                    } else {
                        $('#container-input-jam').addClass('d-none');
                    }
                }
                if (data.jam_mulai) $('#jam_mulai').val(data.jam_mulai);
                if (data.jam_selesai) $('#jam_selesai').val(data.jam_selesai);
                if (data.services && Array.isArray(data.services)) {
                    $('.addon-service-checkbox').each(function() {
                        let val = $(this).val();
                        $(this).prop('checked', data.services.includes(val));
                    });
                }
                if (data.payment_scheme) $('#payment_scheme').val(data.payment_scheme);
                if (data.notes) $('textarea[name="notes"]').val(data.notes);
            } catch (e) {
                console.error("Gagal memuat draf booking:", e);
            }
        }
    } 

    // =========================================================================
    // CODE FIX: PROTEKSI OTOMATIS PILIHAN WAKTU BERDASARKAN DATABASE (UX LOCK)
    // =========================================================================
    if (jenisHarga === 'hari' || jenisHarga === 'pax' || jenisHarga === 'pax_hari') {
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

    $(document).on('change blur', '#input-capacity', function() {
        let val = parseInt($(this).val());
        let maxCap = parseInt($(this).attr('max')) || 999999;
        let minOrder = parseInt($('#render-base-price').data('min-order')) || 1;
        let jenisHargaRaw = $('#render-base-price').data('jenis-harga');
        let jenisHarga = String(jenisHargaRaw).trim().toLowerCase();
        let needsMinOrder = (jenisHarga === 'pax' || jenisHarga === 'pax_hari' || jenisHarga === 'pax_jam');

        if (isNaN(val) || val <= 0) {
            $(this).val(needsMinOrder ? minOrder : 1);
            Swal.fire({
                icon: 'warning',
                title: 'Jumlah Tamu Tidak Valid',
                text: 'Estimasi jumlah tamu tidak boleh 0 atau kosong. Jumlah tamu disesuaikan ke minimal.',
                confirmButtonColor: '#0064D2'
            });
            calculateTotalPrice();
        } else if (val > maxCap) {
            $(this).val(maxCap);
            Swal.fire({
                icon: 'error',
                title: 'Melebihi Kapasitas',
                text: `Estimasi jumlah tamu melebihi kapasitas maksimal ruangan (${maxCap} orang).`,
                confirmButtonColor: '#0064D2'
            });
            calculateTotalPrice();
        } else if (needsMinOrder && val < minOrder) {
            $(this).val(minOrder);
            Swal.fire({
                icon: 'warning',
                title: 'Kurang Dari Batas Minimum',
                text: `Ruangan ini memiliki batas minimal order sebanyak ${minOrder} pax. Jumlah tamu disesuaikan ke minimal order.`,
                confirmButtonColor: '#0064D2'
            });
            calculateTotalPrice();
        }
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
        let totalPaxInput = parseInt($('#input-capacity').val());
        if (isNaN(totalPaxInput) || totalPaxInput < 1) {
            totalPaxInput = 1;
        }
        
        // Evaluasi penentu jumlah pengali orang (pax) untuk hitungan berbasis kapasitas
        let paxMultiplier = totalPaxInput;

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
        // EKSEKUSI DATA FORMULA PRICING (SINKRONISASI BIAYA PER PAX DAN JAM)
        // =========================================================================
        if (jenisHarga === 'pax') {
            // Skema Cuma Per Pax: Harga x Pengali Orang (Tanpa Hari)
            basePriceCalculated = rawPrice * paxMultiplier;
            $('#label-sewa-utama').html(`Detail: Rp ${rawPrice.toLocaleString('id-ID')} / pax &times; ${paxMultiplier} Pax`);
            
        } else if (jenisHarga === 'pax_hari') {
            // Skema Per Pax Per Hari: Harga x Pengali Orang x Total Hari
            basePriceCalculated = rawPrice * paxMultiplier * totalDays;
            $('#label-sewa-utama').html(`Detail: Rp ${rawPrice.toLocaleString('id-ID')} / pax &times; ${paxMultiplier} Pax &times; ${totalDays} Hari`);
            
        } else if (jenisHarga === 'hari') {
            // Skema Cuma Per Hari: Harga x Total Hari
            basePriceCalculated = rawPrice * totalDays;
            $('#label-sewa-utama').html(`Detail: Rp ${rawPrice.toLocaleString('id-ID')} / hari &times; ${totalDays} Hari<br><span class="text-secondary" style="font-size: 0.75rem;">(Tamu: ${totalPaxInput} orang)</span>`);
            
        } else if (jenisHarga === 'jam') {
            // Skema Cuma Per Jam: Harga x Durasi Jam
            basePriceCalculated = rawPrice * durationHours;
            $('#label-sewa-utama').html(`Detail: Rp ${rawPrice.toLocaleString('id-ID')} / jam &times; ${durationHours} Jam<br><span class="text-secondary" style="font-size: 0.75rem;">(Tamu: ${totalPaxInput} orang)</span>`);
            
        } else if (jenisHarga === 'pax_jam') {
            // Skenario Gabungan: Harga x Pengali Orang x Durasi Jam x Total Hari
            basePriceCalculated = rawPrice * paxMultiplier * durationHours * totalDays;
            $('#label-sewa-utama').html(`Detail: Rp ${rawPrice.toLocaleString('id-ID')} / pax/jam &times; ${paxMultiplier} Pax &times; ${durationHours} Jam &times; ${totalDays} Hari`);
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
        // Validasi nomor telepon (10-12 digit)
        let phoneVal = $('#phone-input').val() || '';
        if (phoneVal.length < 10 || phoneVal.length > 12) {
            e.preventDefault();
            $('#phone-input').addClass('is-invalid');
            $('#phone-error').removeClass('d-none');
            $('#phone-input').focus();
            return false;
        } else {
            $('#phone-input').removeClass('is-invalid');
            $('#phone-error').addClass('d-none');
        }

        let inputCap = parseInt($('#input-capacity').val()) || 0;
        let maxCap = parseInt($('#input-capacity').attr('max')) || 0;
        let minOrder = parseInt($('#render-base-price').data('min-order')) || 1;
        let jenisHargaRaw = $('#render-base-price').data('jenis-harga');
        let jenisHarga = String(jenisHargaRaw).trim().toLowerCase();
        let needsMinOrder = (jenisHarga === 'pax' || jenisHarga === 'pax_hari' || jenisHarga === 'pax_jam');

        if (inputCap <= 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Jumlah Tamu Tidak Valid',
                text: 'Jumlah tamu tidak boleh kosong atau 0.',
                confirmButtonColor: '#0064D2'
            });
            return false;
        }

        if (inputCap > maxCap) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Melebihi Kapasitas',
                text: `Estimasi jumlah tamu melebihi kapasitas maksimal ruangan (${maxCap} orang).`,
                confirmButtonColor: '#0064D2'
            });
            $('#input-capacity').addClass('is-invalid').focus();
            return false;
        }

        if (needsMinOrder && inputCap < minOrder) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Kurang Dari Batas Minimum',
                text: `Jumlah tamu minimal untuk pemesanan ini adalah ${minOrder} pax.`,
                confirmButtonColor: '#0064D2'
            });
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
            Swal.fire({
                icon: 'error',
                title: 'Saldo Tidak Cukup',
                text: `Saldo Tempat-In Anda (Rp ${userSaldo.toLocaleString('id-ID')}) tidak mencukupi untuk melakukan pembayaran sebesar Rp ${paymentRequired.toLocaleString('id-ID')}. Silakan Top Up terlebih dahulu!`,
                confirmButtonColor: '#0064D2'
            });
            return false;
        }

        // Sukses validasi, hapus draf booking
        if (roomId) {
            sessionStorage.removeItem(draftKey);
        }
    });

    // Fungsi menyimpan draf input formulir booking secara real-time
    function saveDraft() {
        if (!roomId) return;
        let services = [];
        $('.addon-service-checkbox:checked').each(function() {
            services.push($(this).val());
        });

        let draftData = {
            instansi: $('input[name="instansi"]').val(),
            jenis_acara: $('select[name="jenis_acara"]').val(),
            phone: $('input[name="phone"]').val(),
            total_capacity: $('#input-capacity').val(),
            sewa_tipe: $('.select-tipe-sewa:checked').val(),
            jam_mulai: $('#jam_mulai').val(),
            jam_selesai: $('#jam_selesai').val(),
            services: services,
            payment_scheme: $('#payment_scheme').val(),
            notes: $('textarea[name="notes"]').val()
        };

        sessionStorage.setItem(draftKey, JSON.stringify(draftData));
    }

    // Pemicu otomatis draf saat ada perubahan atau pengetikan di formulir
    $(document).on('input change keyup blur', '#main-booking-form input, #main-booking-form select, #main-booking-form textarea', function() {
        saveDraft();
    });

    // Validasi nomor telepon secara dinamis saat mengetik
    $(document).on('input', '#phone-input', function() {
        let val = $(this).val() || '';
        if (val.length >= 10 && val.length <= 12) {
            $(this).removeClass('is-invalid');
            $('#phone-error').addClass('d-none');
        }
    });

    // =========================================================================
    // TRIGGER KALKULASI AWAL SAAT HALAMAN PERTAMA KALI DIJALANKAN
    // =========================================================================
    calculateTotalPrice();
});