$(document).ready(function (){
    $('.sprint-load').click(function() {
        $('.container-sprint-send').show();
        $('.container-sprint-send').css('display', 'flex');
        $('html, body').css({
            overflow: 'hidden',
            height: '100%'
        });
        const text = $(this).data('sprint-text');
        $('.container-sprint-send .text-insert').html(text);
    });
})
