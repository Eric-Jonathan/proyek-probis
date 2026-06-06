$(document).on('click', '.btn-submit-assign', function(e) {
    e.preventDefault();
    
    let roomId = $(this).data('room-id');
    // Cari select yang berada dalam satu baris d-flex dengan tombol yang diklik
    let selectedSurveyor = $(this).siblings('.select-surveyor').val();
    if (!selectedSurveyor) {
        alert('Mohon pilih salah satu surveyor terlebih dahulu!');
        return;
    }
    // Set value ke form bayangan global
    $('#hidden-surveyor-id').val(selectedSurveyor);
    
    // Atur action url secara dinamis mengarah ke rute admin kamu
    let actionUrl = `/admin/outsource/assign/${roomId}`;
    $('#hidden-global-assign-form').attr('action', actionUrl);
    // Tembak data!
    $('#hidden-global-assign-form').submit();
});