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