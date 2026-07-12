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
    
    // 1. Hitung foto lama yang tersisa di halaman (elemen card visual yang belum di-delete)
    // Asumsi: Box foto lamamu memiliki class '.old-image-card' atau sejenisnya
    let totalOldImages = $('.facility-item-wrapper, .preview-item').length; 
    // ^ TIPS: Sesuaikan selector di atas dengan class pembungkus foto lama yang ada di edit form-mu saat ini!

    // 2. Hitung jumlah foto baru yang lolos seleksi di input file
    const imageInput = $('#image-input')[0];
    let totalNewFiles = imageInput && imageInput.files ? imageInput.files.length : 0;

    // 3. Hitung Total Akumulasi
    let totalPhotosAccumulated = totalOldImages + totalNewFiles;

    // Eksekusi Validasi Batas Minimal 5 Foto Secara Kumulatif
    if (totalPhotosAccumulated < 5) {
        e.preventDefault(); // Gagalkan submit form ke backend
        $('#image-input').addClass('is-invalid');
        alert(`Form gagal dikirim! Total foto ruangan saat ini baru ${totalPhotosAccumulated} foto. Anda wajib mengunggah minimal 5 foto secara keseluruhan.`);
        $('#image-input').focus();
        return;
    }

    // Validasi alamat koordinat maps terdahulu
    if (!lat || !lon || !isSelected) {
        e.preventDefault(); 
        $('#address-search').addClass('is-invalid');
        $('#address-search').focus();
        return;
    }

    var rulesHtml = quill.root.innerHTML;
    if (quill.getText().trim().length === 0) {
        rulesHtml = '';
    }
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

// EVENT LISTENER PERUBAHAN DROPDOWN HARGA PER
$(document).on('change', '#jenis_harga', function() {
    let selectedValue = $(this).val(); // Mengambil nilai mentah: 'pax', 'hari', 'jam', 'pax_jam'

    const labelMapping = {
        'pax': 'Pax',
        'pax_hari': 'Pax / Hari',
        'hari': 'Hari',
        'jam': 'Jam',
        'pax_jam': 'Pax / Jam'
    };

    let cleanLabel = labelMapping[selectedValue] || selectedValue;
    $('#addon-minimal-order').text(cleanLabel);
});

// Pemicu otomatis saat halaman pertama kali dijalankan (Taruh ini di dalam $(document).ready Anda)
$('#jenis_harga').trigger('change');

let selectedFilesArray = [];

$(document).on('change', '#image-input', function() {
    const files = this.files;
    const $previewContainer = $('#preview-container');
    const $inputField = $(this);
    
    // Jangan kosongkan container jika user ingin mencicil upload (UX lebih baik)
    // $previewContainer.empty(); 
    $inputField.removeClass('is-invalid');

    const maxSizeBytes = 2 * 1024 * 1024; // 2MB
    let hasLargeFile = false;

    if (files.length > 0) {
        // Gabungkan berkas baru ke dalam array penampung
        Array.from(files).forEach(file => {
            if (!file.type.startsWith('image/')) return;

            if (file.size > maxSizeBytes) {
                hasLargeFile = true;
                return; 
            }

            selectedFilesArray.push(file);
        });

        if (hasLargeFile) {
            alert('Beberapa foto dilewati karena ukurannya melebihi 2MB.');
        }

        // Sinkronisasi ulang isi input file dengan array penampung kita
        refreshInputFileElements();
        // Render ulang seluruh kotak preview
        renderImagePreviews();
    }
});

// Fungsi untuk merender ulang seluruh komponen kotak preview gambar baru
function renderImagePreviews() {
    // Hapus placeholder teks bawaan blade jika ada
    $('#gallery-container .placeholder-text').remove();
    
    // Hapus preview foto baru yang lama agar tidak duplikat saat upload bertahap
    $('#gallery-container .new-image').remove();

    selectedFilesArray.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewHtml = `
                <div class="image-wrapper new-image text-center position-relative border rounded p-1 bg-white shadow-sm" style="width: 120px;">
                    <button type="button" class="btn-remove-new-image btn btn-danger btn-sm p-0 position-absolute rounded-circle shadow" 
                            style="top: -8px; right: -8px; width: 22px; height: 22px; line-height: 18px; z-index: 10; font-size: 11px; font-weight: bold; border: none;" 
                            data-index="${index}">
                        &times;
                    </button>
                    <img src="${e.target.result}" class="rounded w-100" style="height: 85px; object-fit: cover;">
                    <div class="small text-success mt-1 text-truncate px-1" style="font-size: 11px; font-weight: 500;">
                        ${file.name}
                    </div>
                </div>
            `;
            $('#gallery-container').append(previewHtml);
        }
        reader.readAsDataURL(file);
    });
}

// Fungsi krusial untuk menyuntikkan isi array ke dalam FileList input HTML
function refreshInputFileElements() {
    const dataTransfer = new DataTransfer();
    selectedFilesArray.forEach(file => {
        dataTransfer.items.add(file);
    });
    // Timpa berkas asli di input file dengan berkas hasil filter kita
    $('#image-input')[0].files = dataTransfer.files; 
}

// Handler saat tombol silang (X) pada preview gambar baru diklik
$(document).on('click', '.btn-remove-new-image', function(e) {
    e.preventDefault();
    
    // Ambil indeks array dari properti data
    const targetIndex = $(this).data('index');
    
    // Hapus 1 item dari array penampung
    selectedFilesArray.splice(targetIndex, 1);
    
    // Sinkronisasikan ulang berkas ke input HTML dan render ulang tampilannya
    refreshInputFileElements();
    renderImagePreviews();
});