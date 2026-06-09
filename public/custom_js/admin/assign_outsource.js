$(document).on('click', '.btn-submit-assign', function(e) {
    e.preventDefault();
    
    let roomId = $(this).data('room-id');
    // Cari select yang berada dalam satu baris d-flex dengan tombol yang diklik
    let selectedOutsource = $(this).siblings('.select-surveyor').val();
    if (!selectedOutsource) {
        alert('Mohon pilih salah satu mitra outsource terlebih dahulu!');
        return;
    }
    // Set value ke form bayangan global
    $('#hidden-outsource-id').val(selectedOutsource);
    
    // Atur action url secara dinamis mengarah ke rute admin kamu
    let actionUrl = `/admin/outsource/assign/${roomId}`;
    $('#hidden-global-assign-form').attr('action', actionUrl);
    // Tembak data!
    $('#hidden-global-assign-form').submit();
});