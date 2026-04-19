$(document).on('click', '.hotel-card', function(){
    window.location.href = "/room";
})

$(document).on('click', '.btn-favorite', function(){
    // Ambil icon di dalam button yang diklik
    let icon = $(this).find('i');
    let roomId = $(this).data('id');
    // Logic Toggle
    if (icon.hasClass('bi-heart')) {
        // PROSES: Tambah ke Favorite
        icon.removeClass('bi-heart').addClass('bi-heart-fill text-danger');
        
        console.log("Room " + roomId + " ditambahkan ke favorit.");
    } else {
        // PROSES: Hapus dari Favorite
        icon.removeClass('bi-heart-fill text-danger').addClass('bi-heart');
        
        console.log("Room " + roomId + " dihapus dari favorit.");
    }
})