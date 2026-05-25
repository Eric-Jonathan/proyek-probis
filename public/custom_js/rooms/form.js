let timeout = null;
let isSelected = false; // Flag untuk menandai apakah user sudah klik hasil

$(document).ready(function() {
    $('#jenis_deposit').trigger('change');
    $('#jenis_harga').trigger('change');

    // AUTOFILL CHECK: Jika saat page load input hidden sudah ada isinya (Mode Edit / Gagal Validasi)
    // Sesuaikan ID-nya dengan HTML-mu: #latitude-input dan #longitude-input
    if ($('#latitude-input').val() !== '' && $('#longitude-input').val() !== '') {
        isSelected = true; // Langsung ijinkan submit karena data lama sudah valid
    }
});

$('#address-search').on('input', function() {
    clearTimeout(timeout);
    $('#address-search').removeClass('is-invalid');
    
    // Setiap kali user mengetik ulang, anggap belum valid/belum pilih
    isSelected = false; 
    $('#latitude-input, #longitude-input, #full-address').val(''); 
    $(this).removeClass('is-valid is-invalid');
    let query = $(this).val();
    let $results = $('#autocomplete-results');
    let autocompleteUrl = $(this).data('url');
    if (query.length < 1) {
        $results.empty();
        return;
    }

    timeout = setTimeout(function() {
        $.ajax({
            url: autocompleteUrl,
            data: { q: query },
            method: 'GET',
            success: function(data) {
                $results.empty();
                if (!data || data.length === 0 || data.error) return;
                $.each(data, function(i, item) {
                    let listItem = $('<a href="#" class="list-group-item list-group-item-action py-2 small"></a>')
                        .text(item.display_name)
                        .on('click', function(e) {
                            e.preventDefault();
                            
                            // Set data ke input
                            $('#address-search').val(item.display_name).addClass('is-valid').removeClass('is-invalid');
                            $('#full-address').val(item.display_name);
                            $('#latitude-input').val(item.lat);
                            $('#longitude-input').val(item.lon);
                            
                            isSelected = true; // Tandai sudah klik
                            $results.empty();
                        });
                    $results.append(listItem);
                });
            }
        });
    }, 500);
});

var quill = new Quill('#editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'list': 'ordered'}, { 'list': 'bullet' }]
        ]
    },
    placeholder: 'Masukkan peraturan ruangan...'
});

let existingRules = $('#rules-input').val();
if (existingRules) {
    quill.root.innerHTML = existingRules;
} else {
    // Jika data kosong (Mode Create), paksa baris pertama langsung jadi bullet
    setTimeout(() => {
        quill.formatLine(0, 1, 'list', 'bullet');
    }, 100);
}

// 2. Handler otomatisasi list saat mengetik
quill.on('text-change', function(delta, oldDelta, source) {
    if (source === 'user') {
        const selection = quill.getSelection();
        if (selection) {
            const [line, offset] = quill.getLine(selection.index);
            const formats = line.formats();

            if (!formats.list) {
                quill.formatLine(selection.index, 1, 'list', 'bullet');
            }
        }
    }
});

setTimeout(() => {
    quill.formatLine(0, 1, 'list', 'bullet');
}, 100);

// Handler klik tombol Tambah di dalam modal
$(document).on('click', '#btn-confirm-add-facility', function(e) {
    e.preventDefault();
    executeAddFacility();
});

// Handler menekan tombol Enter di dalam input modal
$(document).on('keypress', '#custom-facility-input', function(e) {
    if(e.which === 13) {
        e.preventDefault();
        executeAddFacility();
    }
});

function executeAddFacility() {
    let inputField = $('#custom-facility-input');
    let facilityName = inputField.val().trim();
    
    // 1. Validasi jika input kosong
    if (facilityName === '') {
        inputField.addClass('is-invalid');
        return;
    }
    
    inputField.removeClass('is-invalid');
    
    // 2. Buat ID unik berbasis string slug
    let internalId = 'custom-' + facilityName.toLowerCase().replace(/[^a-z0-9]+/g, '-');
    
    // 3. Validasi jika fasilitas sudah ada di daftar
    if ($('#fac-' + internalId).length > 0) {
        alert('Fasilitas ini sudah terdaftar di daftar pilihan.');
        inputField.val('');
        
        // Tutup modal secara aman dengan memicu klik pada tombol batal
        $('#btn-close-modal-facility').click();
        return;
    }

    // 4. Struktur HTML komponen baru (Sama persis dengan komponen bawaanmu)
    let newFacilityHtml = `
        <div class="col-6 col-md-4 col-lg-3 facility-item-wrapper">
            <input type="checkbox" name="facilities[]" value="${facilityName}" class="btn-check" id="fac-${internalId}" checked>
            <label class="btn btn-outline-light text-dark border shadow-sm w-100 py-3 d-flex flex-column align-items-center gap-2 rounded-4 facility-label" for="fac-${internalId}">
                <span class="fw-bold text-center">${facilityName}</span>
            </label>
        </div>
    `;
    
    // 5. Sisipkan elemen baru tepat SEBELUM tombol "+ Fasilitas Lainnya"
    $('#btn-add-facility-wrapper').before(newFacilityHtml);
    
    // 6. RESET INPUT FIELD DI DALAM MODAL
    inputField.val('');
    
    // 7. AMAN: Simulasikan klik tombol batal untuk menutup modal tanpa memicu error 'backdrop'
    $('#btn-close-modal-facility').click();
    
    // 8. Pembersihan sisa backdrop secara asinkronus jika animasi fade Bootstrap melambat
    setTimeout(function() {
        if ($('.modal-backdrop').length > 0) {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('overflow', '');
        }
    }, 150);
}

$(document).on('click', '.btn-delete-old-image', function(e) {
    e.preventDefault();
    
    let imageId = $(this).data('image-id');
    
    // 1. Hilangkan visual box foto secara halus
    $(`#image-card-${imageId}`).fadeOut(300, function() {
        $(this).remove();
    });
    
    // 2. Append input hidden baru ke form sebagai penanda ke backend
    $('#deleted-images-inputs').append(`
        <input type="hidden" name="deleted_images[]" value="${imageId}">
    `);
});

// Validasi saat Form dikirim
$(document).on('click', '.btn-save', function(e) {
    let lat = $('#latitude-input').val();
    let lon = $('#longitude-input').val();

    if (!lat || !lon || !isSelected) {
        e.preventDefault(); // Batalkan submit
        $('#address-search').addClass('is-invalid');
        $('#address-search').focus();
        return;
    }

    var rulesHtml = quill.root.innerHTML;
    
    // Jika isinya kosong atau hanya menyisakan tag kosong bawaan Quill
    if (quill.getText().trim().length === 0) {
        rulesHtml = '';
    }
    
    // Inject HTML ke input hidden sebelum form terkirim
    $('#rules-input').val(rulesHtml);
});

$(document).on('change', '#jenis_deposit', function(){
    let val = $(this).val();

    if (val === "persen") {
        $('#wrapper-persen').show();
        $('#wrapper-nominal').hide();
        $('input[name="deposit_nominal"]').val(0); 
    } else {
        $('#wrapper-persen').hide();
        $('#wrapper-nominal').show();
        $('input[name="deposit_percent"]').val(0);
    }
});

$(document).on('change', '#jenis_harga', function() {
    $('#satuan_min_order').html($(this).val());
})

$(document).on('change', '#image-input', function() {
    const files = this.files;
    const $previewContainer = $('#preview-container');
    
    $previewContainer.empty(); 

    if (files.length > 0) {
        // Konversi FileList ke Array agar looping lebih lancar
        Array.from(files).forEach(file => {
            // Validasi sederhana: hanya gambar
            if (!file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                const previewHtml = `
                    <div class="preview-item text-center">
                        <img src="${e.target.result}" class="rounded shadow-sm border" 
                             style="width: 120px; height: 90px; object-fit: cover;">
                        <div class="small text-muted mt-1 text-truncate" style="max-width: 120px;">
                            ${file.name}
                        </div>
                    </div>
                `;
                $previewContainer.append(previewHtml);
            }
            reader.readAsDataURL(file);
        });
    }
});