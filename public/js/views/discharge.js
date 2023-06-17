$(document).ready(function() {

    $('.edit').click(function () {
        var url = localStorage.getItem('url');
        var id = $(this).data('id');
        $('#form').attr('action', `${url}/discharge/store/${id}`);
        $('#name_customer').html($(this).data('customer'));
    });


    $('.help').click(function () {
        localStorage.removeItem('active_tour_wallet');
        $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
        $('body').addClass('shepherd-active');
        tourInit();
    });

    if(!localStorage.getItem('active_tour_wallet')) {
        $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
        tourInit();
    }

});
