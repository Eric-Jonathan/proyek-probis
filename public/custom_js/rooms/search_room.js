$(document).on('click', '.hotel-card', function(e){
    let roomId = $(this).data('id');
    window.location.href = "/rooms/" + roomId;
});