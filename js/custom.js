require(["jquery"],function($) {
    $('.leeloo_ar_cert').on('click', function(e){
        e.preventDefault();
        var modal = $(this).attr('data-target');
        console.log(modal);
        $(modal+' .modal-body').html('<iframe class="leeloo_ar_frame" src="'+$(this).attr('href')+'"></iframe>');
    });
    $('.leeloo_paid_ar_modal').on('hidden.bs.modal', function () {
        location.reload();
    });
});