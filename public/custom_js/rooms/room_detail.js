$(document).ready(function() {
    const sections = $('#section-info, #section-review, #section-fasilitas, #section-lokasi, #section-tentang');
    const navLinks = $('#main-nav .nav-link');

    let today = new Date();
    let startDate = null;
    let endDate = null;
    let currentViewDate = new Date(today.getFullYear(), today.getMonth(), 1);
    const dateOptions = { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' };

    // Ambil data minimal hari sewa dari atribut HTML secara dinamis
    const minBookingDays = parseInt($('#display-date').data('min-day')) || 1;
    
    // Ganti baris pengambilan data-jenis-harga menjadi super aman seperti ini:
    const jenisHargaRaw = $('#display-date').data('jenis-harga') || '';
    const jenisHargaRuangan = String(jenisHargaRaw).trim().toLowerCase(); 
    const bookedDates = $('#display-date').data('booked-dates') || [];

    // =========================================================================
    // 1. LOGIKA RANGE KALENDER PENYEWAAN 
    // =========================================================================
    function updateDisplayDate() {
        if (startDate && !endDate) {
            $('#display-date').text(startDate.toLocaleDateString('id-ID', dateOptions));
        } else if (startDate && endDate) {
            const startStr = startDate.toLocaleDateString('id-ID', dateOptions);
            const endStr = endDate.toLocaleDateString('id-ID', dateOptions);
            
            const diffTime = Math.abs(endDate - startDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; 
            
            let infoKeterangan = "";
            if (jenisHargaRuangan === 'jam' || jenisHargaRuangan === 'pax_jam') {
                infoKeterangan = " (Sewa Per Jam - Maks 1 Hari)";
            } else {
                infoKeterangan = ` (${diffDays} Hari - Mendukung Multi-Hari)`;
            }

            $('#display-date').text(`${startStr} - ${endStr}${infoKeterangan}`);

            if ($('#btn-trigger-booking').length > 0) {
                let startIso = startDate.getFullYear() + '-' + String(startDate.getMonth() + 1).padStart(2, '0') + '-' + String(startDate.getDate()).padStart(2, '0');
                let endIso = endDate.getFullYear() + '-' + String(endDate.getMonth() + 1).padStart(2, '0') + '-' + String(endDate.getDate()).padStart(2, '0');
                
                $('#btn-trigger-booking').attr('href', `/booking/${$('#display-date').data('room-id')}?start_date=${startIso}&end_date=${endIso}`);
            }
        } else {
            $('#display-date').text("Pilih tanggal penyewaan...");
        }
    }

    function renderSingleMonth(gridId, labelId, dateObj) {
        const $grid = $('#' + gridId);
        const year = dateObj.getFullYear();
        const month = dateObj.getMonth();
        const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        
        $('#' + labelId).text(`${monthNames[month]} ${year}`);
        $grid.empty();

        ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'].forEach((day, idx) => {
            $grid.append(`<div class="calendar-day-head ${idx === 0 ? 'sun' : ''}">${day}</div>`);
        });

        const firstDay = new Date(year, month, 1).getDay();
        const totalDays = new Date(year, month + 1, 0).getDate();

        for (let i = 0; i < firstDay; i++) {
            $grid.append('<div></div>');
        }

        const minSelectableDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() + 1);
        minSelectableDate.setHours(0, 0, 0, 0);

        for (let d = 1; d <= totalDays; d++) {
            const loopDate = new Date(year, month, d);
            loopDate.setHours(0, 0, 0, 0); 
            
            const isToday = (d === today.getDate() && month === today.getMonth() && year === today.getFullYear());
            
            const $btn = $('<button></button>')
                .addClass('btn-date')
                .addClass(loopDate.getDay() === 0 ? 'is-holiday' : '');

            if (loopDate < minSelectableDate) {
                $btn.prop('disabled', true);
            }

            // Formulasi tanggal ISO untuk loopDate (YYYY-MM-DD)
            const y = loopDate.getFullYear();
            const m = String(loopDate.getMonth() + 1).padStart(2, '0');
            const dt = String(loopDate.getDate()).padStart(2, '0');
            const loopDateIso = `${y}-${m}-${dt}`;

            const isBooked = bookedDates.includes(loopDateIso);
            if (isBooked) {
                $btn.prop('disabled', true);
                $btn.addClass('is-fullbook');
            }

            // PEWARNAAN RENTANG SELECTION (IN-BETWEEN)
            const checkTime = loopDate.getTime();
            const startTime = startDate ? new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate()).getTime() : null;
            const endTime = endDate ? new Date(endDate.getFullYear(), endDate.getMonth(), endDate.getDate()).getTime() : null;

            if (startTime && !endTime) {
                if (checkTime === startTime) $btn.addClass('selected');
            } else if (startTime && endTime) {
                if (checkTime >= startTime && checkTime <= endTime) $btn.addClass('selected');
            }

            $btn.html(`
                ${isToday ? '<span class="today-label">Hari Ini</span>' : ''}
                <span class="num">${d}</span>
                ${isBooked ? '<span class="price" style="color:#dc3545">Full Book</span>' : '<span class="price" style="color:var(--success-green)">Tersedia</span>'}
            `);

            // SATU-SATUNYA EVENT KLIK TOMBOL TANGGAL YANG AKTIF
            $btn.on('click', function() {
                // Skenario A: Jika berbasis jam, langsung kunci di hari yang sama (Maksimal 1 Hari)
                if (jenisHargaRuangan === 'jam' || jenisHargaRuangan === 'pax_jam') {
                    startDate = loopDate;
                    endDate = loopDate;
                    
                    // GANTI ALERT MENJADI TEKS HTML INTERAKTIF:
                    $('#calendar-info-note')
                        .html('<i class="bi bi-info-circle-fill me-2"></i><strong>Mode Per Jam:</strong> Tanggal selesai otomatis disamakan dengan tanggal mulai (Maksimal 1 Hari penggunaan).')
                        .removeClass('d-none alert-success')
                        .addClass('alert-warning');
                } 
                // Skenario B: Jika berbasis harian atau per pax, izinkan rentang multi-hari
                else {
                    if (!startDate || (startDate && endDate)) {
                        startDate = loopDate; 
                        endDate = null;
                        
                        // Beri tahu user untuk memilih tanggal selesai
                        $('#calendar-info-note')
                            .html('<i class="bi bi-calendar-event me-2"></i>Silakan tentukan <strong>Tanggal Selesai</strong> penyewaan Anda.')
                            .removeClass('d-none alert-warning')
                            .addClass('alert-success');
                    } else {
                        if (loopDate.getTime() === startDate.getTime()) {
                            startDate = null;
                            $('#calendar-info-note').addClass('d-none'); // Sembunyikan jika batal
                        } else if (loopDate < startDate) {
                            startDate = loopDate;
                            endDate = null;
                        } else {
                            // Cek syarat minimal hari sewa
                            const checkTimeDiff = Math.abs(loopDate - startDate);
                            const totalSelectedDays = Math.ceil(checkTimeDiff / (1000 * 60 * 60 * 24)) + 1;

                            if (totalSelectedDays < minBookingDays) {
                                $('#calendar-info-note')
                                    .html(`<i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal memilih! Ruangan ini memiliki batas minimal sewa selama <strong>${minBookingDays} hari</strong>.`)
                                    .removeClass('d-none alert-success')
                                    .addClass('alert-warning');
                                return; 
                            }

                            // Cek jika terdapat tanggal terbooking di dalam rentang pilihan
                            let hasBookedDateInRange = false;
                            let currentCheck = new Date(startDate.getTime());
                            while (currentCheck <= loopDate) {
                                const yCheck = currentCheck.getFullYear();
                                const mCheck = String(currentCheck.getMonth() + 1).padStart(2, '0');
                                const dCheck = String(currentCheck.getDate()).padStart(2, '0');
                                const checkIso = `${yCheck}-${mCheck}-${dCheck}`;
                                if (bookedDates.includes(checkIso)) {
                                    hasBookedDateInRange = true;
                                    break;
                                }
                                currentCheck.setDate(currentCheck.getDate() + 1);
                            }

                            if (hasBookedDateInRange) {
                                $('#calendar-info-note')
                                    .html(`<i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal memilih! Terdapat tanggal yang sudah penuh (Full Book) di dalam rentang pilihan Anda.`)
                                    .removeClass('d-none alert-success')
                                    .addClass('alert-warning');
                                return;
                            }
                            
                            endDate = loopDate;
                            // Informasikan rentang sukses dipilih
                            $('#calendar-info-note')
                                .html(`<i class="bi bi-check-circle-fill me-2"></i>Rentang tanggal sewa berhasil ditentukan (${totalSelectedDays} Hari).`)
                                .removeClass('d-none alert-warning')
                                .addClass('alert-success');
                        }
                    }
                }
                
                updateDisplayDate(); 
                renderDoubleCalendar();
            });

            $grid.append($btn);
        }
    }

    $('#datePickerModal').on('hidden.bs.modal', function () {
        $('#calendar-info-note').addClass('d-none').empty();
    });

    function renderDoubleCalendar() {
        renderSingleMonth('gridLeft', 'labelMonthLeft', new Date(currentViewDate));
        let nextMonth = new Date(currentViewDate); 
        nextMonth.setMonth(nextMonth.getMonth() + 1);
        renderSingleMonth('gridRight', 'labelMonthRight', nextMonth);
        
        const isCurrentMonth = (currentViewDate.getMonth() === today.getMonth() && currentViewDate.getFullYear() === today.getFullYear());
        $('#prevMonthBtn').css('visibility', isCurrentMonth ? 'hidden' : 'visible');
    }

    $('#nextMonthBtn').on('click', function() { currentViewDate.setMonth(currentViewDate.getMonth() + 1); renderDoubleCalendar(); });
    $('#prevMonthBtn').on('click', function() { currentViewDate.setMonth(currentViewDate.getMonth() - 1); renderDoubleCalendar(); });

    renderDoubleCalendar();

    // =========================================================================
    // 2. LOGIKA GALERI MODAL POP-UP
    // =========================================================================
    const $mainImage = $('#mainGalleryImage');
    const $photoCounter = $('#photoCounter');
    const $gridItems = $('.gallery-grid-item');
    let photoList = $gridItems.map(function() { return $(this).attr('src'); }).get();
    let currentIndex = 0;

    function updateGalleryView(index) {
        if(photoList.length === 0) return;
        currentIndex = index;
        $mainImage.attr('src', photoList[currentIndex]);
        $photoCounter.text(`${currentIndex + 1}/${photoList.length}`);
        $gridItems.removeClass('active');
        $gridItems.eq(currentIndex).addClass('active');
    }

    $gridItems.on('click', function() {
        updateGalleryView(parseInt($(this).attr('data-index')));
    });

    $('#nextPhoto').on('click', function() { updateGalleryView((currentIndex + 1) % photoList.length); });
    $('#prevPhoto').on('click', function() { updateGalleryView((currentIndex - 1 + photoList.length) % photoList.length); });

    // =========================================================================
    // 3. WIDGET LOGIKA TAMBAHAN (WISHLIST & ACTION BUTTON)
    // =========================================================================


    $(window).on('scroll', function() {
        let currentSectionId = "";
        let scrollPosition = $(window).scrollTop();

        sections.each(function() {
            if (scrollPosition >= ($(this).offset().top - 160)) {
                currentSectionId = $(this).attr('id');
            }
        });

        navLinks.each(function() {
            $(this).removeClass('active text-primary fw-bold border-bottom border-primary border-3').addClass('text-muted');
            if ($(this).attr('href') === `#${currentSectionId}`) {
                $(this).attr('class', 'nav-link active text-primary fw-bold border-0 border-bottom border-primary border-3');
            }
        });
    });

    $('#btn-trigger-booking').on('click', function(e) {
        const currentHref = $(this).attr('href');
        if (currentHref === '#' || currentHref === '') {
            e.preventDefault();
            alert('Mohon tentukan tanggal sewa terlebih dahulu pada kalender!');
            $('#datePickerModal').modal('show');
        }
    });
});

$(document).on('click', '.btn-back-search', function(e) {
    e.preventDefault();
    
    window.history.back();
});