$(document).on('click', '.btn-back', function(){
    window.location.href = '/penyewa/search';
});

$(document).ready(function() {
    
    // Ambil kontrol skema pricing dari elemen invoice sejak halaman pertama dimuat
    const $priceElement = $('#render-base-price');
    const jenisHarga = $priceElement.data('jenis-harga'); // Membaca data-jenis-harga baju HTML

    // =========================================================================
    // CODE BARU: PROTEKSI OTOMATIS PILIHAN WAKTU BERDASARKAN DATABASE (UX LOCK)
    // =========================================================================
    if (jenisHarga === 'Hari' || jenisHarga === 'Pax') {
        // Jika kebijakan flat harian/pax, kunci ke radio button Harian
        $('#tipe-hari').prop('checked', true);
        $('#tipe-jam').prop('disabled', true); // Kunci opsi jam
        $('#container-input-jam').addClass('d-none'); // Sembunyikan inputan jam
        
    } else if (jenisHarga === 'Jam' || jenisHarga === 'Pax_jam') {
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

    // 2. Bersihkan Duplikasi: Cukup SAtu Event Listener untuk Seluruh Form Input
    $(document).on('input change', '#input-capacity, #jam-mulai, #jam-selesai, .addon-service-checkbox', function() {
        calculateTotalPrice();
    });

    // 3. Fungsi Utama Kalkulator Multi-Skema Pricing ERP
    function calculateTotalPrice() {
        // Ambil metadata skema dari atribut data HTML
        let $priceElement = $('#render-base-price');
        let jenisHarga = $priceElement.data('jenis-harga'); // 'pax', 'hari', 'jam', 'pax_jam'
        let rawPrice = parseInt($priceElement.data('raw-price')) || 0;
        let totalDays = parseInt($priceElement.data('total-days')) || 1; // Menangkap jumlah hari booking
        let minOrder = parseInt($priceElement.data('min-order')) || 1;

        // Ambil data input dinamis dari form yang diisi user
        let totalPaxInput = parseInt($('#input-capacity').val()) || 0;
        
        // Proteksi minimal order
        if (totalPaxInput < minOrder && totalPaxInput !== 0) {
            totalPaxInput = minOrder;
        }

        let basePriceCalculated = 0;
        let durationHours = 1;

        // Hitung durasi jam jika container input jam aktif
        let timeStart = $('#jam-mulai').val();
        let timeEnd = $('#jam-selesai').val();
        if (timeStart && timeEnd) {
            let dateStart = new Date("01/01/2026 " + timeStart);
            let dateEnd = new Date("01/01/2026 " + timeEnd);
            let diff = dateEnd.getTime() - dateStart.getTime();
            durationHours = Math.ceil(diff / (1000 * 60 * 60));
            if (durationHours <= 0) durationHours = 1;
        }

        // =========================================================================
        // PERBAIKAN: FORMULA HARGA DASAR DIKALI TOTAL HARI BOOKING
        // =========================================================================
        if (jenisHarga === 'Pax') {
            // Skema Per Pax: Harga x Jumlah Orang x Total Hari Booking
            basePriceCalculated = rawPrice * (totalPaxInput || minOrder) * totalDays;
            $('#label-sewa-utama').text(`Sewa Ruangan (${totalPaxInput || minOrder} Pax x ${totalDays} Hari)`);
            
        } else if (jenisHarga === 'Hari') {
            // Skema Per Hari: Harga x Total Hari Booking
            basePriceCalculated = rawPrice * totalDays;
            $('#label-sewa-utama').text(`Sewa Ruangan (${totalDays} Hari)`);
            
        } else if (jenisHarga === 'Jam') {
            // Skema Per Jam: Harga x Durasi Jam x Total Hari Booking
            basePriceCalculated = rawPrice * durationHours * totalDays;
            $('#label-sewa-utama').text(`Sewa Ruangan (${durationHours} Jam x ${totalDays} Hari)`);
            
        } else if (jenisHarga === 'Pax_jam') {
            // Skema Kombinasi: Harga x Jumlah Orang x Durasi Jam x Total Hari Booking
            basePriceCalculated = rawPrice * (totalPaxInput || minOrder) * durationHours * totalDays;
            $('#label-sewa-utama').text(`Sewa Ruangan (${totalPaxInput || minOrder} Pax x ${durationHours} Jam x ${totalDays} Hari)`);
        }

        // =========================================================================
        // PERBAIKAN: HITUNG LAYANAN TAMBAHAN BERDASARKAN SIFAT BIAYA
        // =========================================================================
        let extraCost = 0;
        $('#render-extra-services-cost').empty();

        $('.addon-service-checkbox:checked').each(function() {
            let serviceName = $(this).parent().find('label').text().trim().split('(')[0];
            let pricePerItem = parseInt($(this).data('price')) || 0;
            let costCalculated = pricePerItem;

            // KATERING: Bersifat variabel per orang dan harus disediakan setiap hari acara
            if ($(this).attr('id') === 'catering') {
                costCalculated = pricePerItem * (totalPaxInput || minOrder) * totalDays; // Dikali orang DAN dikali total hari
            } 
            // DEKORASI / IT SUPPORT: Biasanya berupa flat rate / setup cost sekali bayar di awal acara
            else {
                costCalculated = pricePerItem; // Tetap flat sewa per event
            }
            
            extraCost += costCalculated;

            // Render rincian biaya ke invoice kiri
            $('#render-extra-services-cost').append(`
                <div class="d-flex justify-content-between mb-2 small text-secondary">
                    <span>+ ${serviceName}</span>
                    <span>Rp ${costCalculated.toLocaleString('id-ID')}</span>
                </div>
            `);
        });

        // Render hasil kalkulasi kumulatif ke komponen invoice halaman web
        $('#render-base-price').text('Rp ' + basePriceCalculated.toLocaleString('id-ID'));
        
        let finalTotal = basePriceCalculated + extraCost;
        $('#render-total-final').text('Rp ' + finalTotal.toLocaleString('id-ID'));
    }

    // 4. Validasi Batas Maksimal Tamu Sebelum Submit Form
    $('#main-booking-form').on('submit', function(e) {
        let inputCap = parseInt($('#input-capacity').val()) || 0;
        let maxCap = parseInt($('#input-capacity').attr('max')) || 0;

        if (inputCap > maxCap) {
            e.preventDefault();
            alert(`Jumlah tamu melebihi kapasitas maksimal ruangan! Ruangan ini hanya muat untuk maksimal ${maxCap} orang.`);
            $('#input-capacity').addClass('is-invalid').focus();
            return false;
        }
    });

    // Eksekusi kalkulasi perdana saat halaman dimuat pertama kali
    calculateTotalPrice();
});