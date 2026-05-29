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

    // =========================================================================
    // 1. LOGIKA RANGE KALENDER PENYEWAAN DENGAN VALIDASI MINIMAL HARI
    // =========================================================================
    function updateDisplayDate() {
        if (startDate && !endDate) {
            $('#display-date').text(startDate.toLocaleDateString('id-ID', dateOptions));
        } else if (startDate && endDate) {
            const startStr = startDate.toLocaleDateString('id-ID', dateOptions);
            const endStr = endDate.toLocaleDateString('id-ID', dateOptions);
            
            // Hitung selisih hari sewa
            const diffTime = Math.abs(endDate - startDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; 
            
            $('#display-date').text(`${startStr} - ${endStr} (${diffDays} hari)`);

            // Tambahan sinkronisasi link dinamis ke form booking yang kita bahas sebelumnya
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

        for (let d = 1; d <= totalDays; d++) {
            const loopDate = new Date(year, month, d);
            const isToday = (d === today.getDate() && month === today.getMonth() && year === today.getFullYear());
            
            const $btn = $('<button></button>')
                .addClass('btn-date')
                .addClass(loopDate.getDay() === 0 ? 'is-holiday' : '');

            if (loopDate < minSelectableDate) {
                $btn.prop('disabled', true);
            }
            if (startDate && !endDate && loopDate.getTime() === startDate.getTime()) $btn.addClass('selected');
            if (startDate && endDate && loopDate >= startDate && loopDate <= endDate) $btn.addClass('selected');

            $btn.html(`
                ${isToday ? '<span class="today-label">Hari Ini</span>' : ''}
                <span class="num">${d}</span>
                <span class="price" style="color:var(--success-green)">Tersedia</span>
            `);

            // EVENT KLIK DENGAN CEK MINIMAL DURASI HARI SEWA
            $btn.on('click', function() {
                if (!startDate || (startDate && endDate)) {
                    startDate = loopDate; 
                    endDate = null;
                } else {
                    if (loopDate.getTime() === startDate.getTime()) {
                        startDate = null;
                    } else if (loopDate < startDate) {
                        startDate = loopDate;
                    } else {
                        // Cek apakah rentang yang dipilih memenuhi syarat minimal hari sewa
                        const checkTime = Math.abs(loopDate - startDate);
                        const totalSelectedDays = Math.ceil(checkTime / (1000 * 60 * 60 * 24)) + 1;

                        if (totalSelectedDays < minBookingDays) {
                            alert(`Gagal memilih tanggal! Ruangan ini memiliki batas minimal sewa selama ${minBookingDays} hari.`);
                            return; // Batalkan pemilihan rentang jika kurang dari minimal sewa
                        }
                        
                        endDate = loopDate;
                    }
                }
                updateDisplayDate(); 
                renderDoubleCalendar();
            });

            $grid.append($btn);
        }
    }

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

    $('#nextPhoto').on('click', function() {
        updateGalleryView((currentIndex + 1) % photoList.length);
    });

    $('#prevPhoto').on('click', function() {
        updateGalleryView((currentIndex - 1 + photoList.length) % photoList.length);
    });

    // =========================================================================
    // 3. WIDGET LOGIKA TAMBAHAN (WISHLIST & SCROLLSPY)
    // =========================================================================
    $('#btn-wishlist').on('click', function() {
        const $icon = $('#icon-wishlist');
        if ($icon.hasClass('bi-heart')) {
            $icon.attr('class', 'bi bi-heart-fill text-danger');
        } else {
            $icon.attr('class', 'bi bi-heart');
        }
    });

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

        // Jika href masih bawaan asli (#) berarti user belum memilih tanggal di kalender
        if (currentHref === '#' || currentHref === '') {
            e.preventDefault(); // Cegah reload/pindah halaman kosong
            
            alert('Mohon tentukan tanggal sewa terlebih dahulu pada kalender!');
            
            // Paksa buka modal picker tanggal secara otomatis demi kemudahan UX
            $('#datePickerModal').modal('show');
        }
    });
});