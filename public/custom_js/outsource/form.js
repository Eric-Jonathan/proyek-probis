let selectedPhotosArray = [];

$(document).ready(function() {
    // 1. Menangani Penambahan Fasilitas Kustom
    $(document).on('click', '#btn-confirm-add-facility', function(e) {
        e.preventDefault();
        executeAddFacility();
    });

    $(document).on('keypress', '#custom-facility-input', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            executeAddFacility();
        }
    });

    function executeAddFacility() {
        let inputField = $('#custom-facility-input');
        let facilityName = inputField.val().trim();
        
        if (facilityName === '') {
            inputField.addClass('is-invalid');
            return;
        }
        inputField.removeClass('is-invalid');
        
        let internalId = 'custom-' + facilityName.toLowerCase().replace(/[^a-z0-9]+/g, '-');
        
        if ($('#fac-' + internalId).length > 0) {
            alert('Fasilitas sudah ditambahkan.');
            inputField.val('');
            $('#btn-close-modal-facility').click();
            return;
        }

        let newFacilityHtml = `
            <div class="col-6 col-md-4 col-lg-3 facility-item-wrapper">
                <input type="checkbox" name="facilities[]" value="${facilityName}" class="btn-check" id="fac-${internalId}" checked>
                <label class="btn btn-outline-light text-dark border shadow-sm w-100 py-3 d-flex flex-column align-items-center gap-2 rounded-4 facility-label" for="fac-${internalId}">
                    <span class="fw-bold text-center" style="font-size: 0.8rem;">${facilityName}</span>
                </label>
            </div>
        `;
        
        $('#btn-add-facility-wrapper').before(newFacilityHtml);
        inputField.val('');
        $('#btn-close-modal-facility').click();
    }

    // 2. Akumulasi Unggahan Foto (Mencegah Override)
    $('#foto-input').on('change', function() {
        const files = this.files;
        const maxSizeBytes = 2 * 1024 * 1024; // 2MB
        let hasLargeFile = false;

        if (files.length > 0) {
            Array.from(files).forEach(file => {
                if (!file.type.startsWith('image/')) return;

                if (file.size > maxSizeBytes) {
                    hasLargeFile = true;
                    return; 
                }

                // Masukkan ke array penampung global agar bisa di-upload mencicil
                selectedPhotosArray.push(file);
            });

            if (hasLargeFile) {
                alert('Beberapa foto dilewati karena ukurannya melebihi 2MB.');
            }

            // Sinkronisasikan berkas ke input file element asli
            refreshInputFileElements();
            // Render ulang pratinjau gambar di container
            renderImagePreviews();
        }
    });

    function renderImagePreviews() {
        const container = $('#foto-preview-container');
        
        // Bersihkan pratinjau sebelumnya
        container.find('.new-preview').remove();
        $('#foto-placeholder').remove();

        if (selectedPhotosArray.length > 0) {
            selectedPhotosArray.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewHtml = `
                        <div class="preview-card new-preview">
                            <button type="button" class="btn-remove-new-photo btn-remove" data-index="${index}">&times;</button>
                            <img src="${e.target.result}">
                        </div>
                    `;
                    container.append(previewHtml);
                }
                reader.readAsDataURL(file);
            });
        } else {
            container.append(`
                <div class="text-center w-100 text-muted" id="foto-placeholder">
                    <i class="bi bi-images fs-3 d-block mb-1"></i>
                    <span class="small">Belum ada foto yang dipilih</span>
                </div>
            `);
        }
    }

    function refreshInputFileElements() {
        const dataTransfer = new DataTransfer();
        selectedPhotosArray.forEach(file => {
            dataTransfer.items.add(file);
        });
        $('#foto-input')[0].files = dataTransfer.files; 
    }

    // Menghapus foto tertentu dari pratinjau mencicil
    $(document).on('click', '.btn-remove-new-photo', function(e) {
        e.preventDefault();
        const targetIndex = $(this).data('index');
        
        selectedPhotosArray.splice(targetIndex, 1);
        
        refreshInputFileElements();
        renderImagePreviews();
    });

    // 3. Pratinjau Video & Fitur Pembatalan Video
    $('#video-input').on('change', function() {
        const file = this.files[0];
        const container = $('#video-preview-container');
        const video = $('#video-preview')[0];
        
        if (file) {
            if (!file.type.startsWith('video/')) {
                alert('Silakan pilih berkas video yang valid.');
                clearVideoSelection();
                return;
            }
            if (file.size > 10 * 1024 * 1024) { // 10MB
                alert('Ukuran video melebihi batas 10MB.');
                clearVideoSelection();
                return;
            }

            const videoUrl = URL.createObjectURL(file);
            $(video).attr('src', videoUrl);
            container.removeClass('d-none');
        } else {
            clearVideoSelection();
        }
    });

    // Tombol silang pembatalan video
    $(document).on('click', '#btn-remove-video', function(e) {
        e.preventDefault();
        clearVideoSelection();
    });

    function clearVideoSelection() {
        const video = $('#video-preview')[0];
        $(video).attr('src', '');
        $('#video-input').val('');
        $('#video-preview-container').addClass('d-none');
    }

    // 4. Validasi Form pada Submit
    $('#btn-submit-report').on('click', function(e) {
        if (selectedPhotosArray.length < 3) {
            e.preventDefault();
            alert('Gagal mengirim! Anda harus mengunggah minimal 3 foto hasil cek lapangan.');
            $('#foto-input').focus();
            return;
        }
    });
});
