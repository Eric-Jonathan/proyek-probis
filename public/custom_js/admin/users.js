$(document).ready(function(){
    init();
})

function init(){
    $('#tableUser').DataTable({
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        searching: true,
        ordering: true,
        responsive: true,
        order: [],
        dom: 'rt<"dt-footer"lp>', 
        
        language: {
            lengthMenu: "Show _MENU_ entries",
            info: "", 
            paginate: {
                next: '<i class="bi bi-chevron-right"></i>',
                previous: '<i class="bi bi-chevron-left"></i>'
            }
        }
    });
}

$(document).on('click', '.btn-view-user', function() {
    let modal = $('#modalViewUser');
    modal.find('.field-username').text($(this).data('username'));
    modal.find('.field-email').text($(this).data('email'));
    modal.find('.field-phone').text($(this).data('phone'));
    modal.find('.field-role').text($(this).data('role'));
    modal.find('.field-company').text($(this).data('company'));
    modal.modal('show');
});