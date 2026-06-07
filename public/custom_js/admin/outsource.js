$(document).on('click', '.btn-detail', function() {
    let modal = $('#modalDetail');
    
    // Suntik data teks dari tombol ke dalam modal target
    modal.find('.id-company').text($(this).data('company'));
    modal.find('.id-type').text($(this).data('type'));
    modal.find('.id-nib').text($(this).data('nib'));
    modal.find('.id-npwp').text($(this).data('npwp'));
    modal.find('.id-address').text($(this).data('address'));
    modal.find('.id-pic').text($(this).data('pic'));
    modal.find('.id-position').text($(this).data('position'));
    modal.find('.id-email').text($(this).data('email'));
    modal.find('.id-phone').text($(this).data('phone'));
    modal.find('.id-bank').text($(this).data('bank'));
    modal.find('.id-account').text($(this).data('account'));
    
    // Tampilkan modal detail ke layar
    modal.modal('show');
});