let timeout = null;
let isSelected = false; // Flag untuk menandai apakah user sudah klik hasil

$(document).ready(function() {
    $('#jenis_deposit').trigger('change');
    $('#jenis_harga').trigger('change');
});

$('#address-search').on('input', function() {
    clearTimeout(timeout);
    $('#address-search').removeClass('is-invalid');
    
    // Setiap kali user mengetik ulang, anggap belum valid/belum pilih
    isSelected = false; 
    $('#lat, #lon, #full-address').val(''); 
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
                            $('#lat').val(item.lat);
                            $('#lon').val(item.lon);
                            
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
            [{ 'list': 'ordered'}, { 'list': 'bullet' }] // Hanya munculkan opsi list
        ]
    },
    placeholder: 'Masukkan peraturan ruangan...'
});

quill.on('text-change', function(delta, oldDelta, source) {
    if (source === 'user') {
        const selection = quill.getSelection();
        if (selection) {
            // Ambil format pada posisi kursor saat ini
            const [line, offset] = quill.getLine(selection.index);
            const formats = line.formats();

            // Jika baris saat ini tidak memiliki format 'list'
            if (!formats.list) {
                // Paksa baris tersebut menjadi bullet list secara otomatis
                quill.formatLine(selection.index, 1, 'list', 'bullet');
            }
        }
    }
});

setTimeout(() => {
    quill.formatLine(0, 1, 'list', 'bullet');
}, 100);

// Validasi saat Form dikirim
$(document).on('click', '.btn-save', function(e) {
    let lat = $('#lat').val();
    let lon = $('#lon').val();

    if (!lat || !lon || !isSelected) {
        e.preventDefault(); // Batalkan submit
        $('#address-search').addClass('is-invalid');
        $('#address-search').focus();
    }

    var rulesHtml = quill.root.innerHTML;
    // Jika isinya hanya <p><br></p> (kosong), kosongkan saja agar kena validasi 'required'
    if (quill.getText().trim().length === 0) {
        rulesHtml = '';
    }
    $('#rules-hidden').val(rulesHtml);
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