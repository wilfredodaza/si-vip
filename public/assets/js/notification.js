$(document).ready(function(){
    $('.notification-active').click(function(){
        const URL = localStorage.getItem('url');
        const id = $(this).data('id');
        $(this).hide();
        fetch(`${URL}/notification/view/${id}`)
            .then(function (response) {
                return response.json();
            })
            .then(function (myJson) {
                location.href =  `${URL}/notification/index?nota=${id}`;
            });
    });
});