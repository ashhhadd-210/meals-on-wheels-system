function newModal(data, allowMulti = false){
    if(!allowMulti){
        if($('#modalBackground').length > 0){
            return false;
        }
    }

    var body = $('body');
    var modalBackground = $('<div>',{
        id : 'modalBackground',
    })

    

    modalBackground.css({
        'position' : 'fixed',
        'z-index':'100',
        'height' : '100dvh',
        'width' : '100vw',
        'background-color' : 'rgba(0,0,0,0.7)',
        'backdrop-filter' : 'blur(3px)',
        '-webkit-backdrop-filter' : 'blur(3px)',
        'display' : 'flex',
        'flex-direction' : 'column',
        'top' : '0px',
        'left' : '0px',
    })

    var modalBody = $('<div>',{
        id : 'modalBody'
    })

    modalBody.css({
        'background-color' : 'white',
        'width' : '100%',
        'max-width' : '900px',
        'height' : '95svh',
        'overflow-y' : 'scroll',
        'overflow-x' : 'hidden',
        'margin-left' : 'auto',
        'margin-right' : 'auto',
        'border-radius' : '5px',
        'margin-bottom':'10px',
        'flex-grow':'1'
    })
    // if($(window).width() <= 767) {
    //     modalBody.css({
    //         'margin-bottom':'10px'
    //     })
    // }

    var modalHead = $('<div>',{
        id : 'modalHead'
    })

    modalHead.css({
        'display' : 'flex',
        'justify-content' : 'end'
    })

    var modalClose = $('<span>',{
        id: 'mdalClose',
        onclick : 'customModalClear()',
        text : 'Close'
    })

    modalClose.css({
        'padding' : '10px',
        'cursor' : 'pointer',
        'background-color':'rgb(189, 29, 29)',
        'width':'98%',
        'max-width':'900px',
        'margin-left':'auto',
        'margin-right':'auto',
        'margin-bottom':'10px',
        'margin-top':'10px',
        'border-radius':'5px',
        'text-align':'center',
        'color':'white'
    })


    body.css({
        'overflow-y' : 'hidden'
    })

    if(screen.width > 800){
        body.css({
            'margin-right' : '18px'
        })
    }

    modalBackground.append(modalClose);
    // modalBody.append(modalHead);
    if(data != ''){
        $(modalBody).append(data);
    }
    modalBackground.append(modalBody)

    body.append(modalBackground)
}

function modalData(){
    return $('#modalBody').html();
}

function clearModal(){
    customModalClear()
}

function customModalClear(){
    $('#modalBackground').fadeOut(200,()=>{
        $('#modalBackground').remove()
    })
    $('body').css({
        'overflow-y' : 'scroll',
        'margin-right':'0px'
    })
}

$(document).ready(function () {
    $(document).on('keydown', function (e) {
        if(($('#fullscreen-gb').length <= 0) && ($('#cm-customMessageBg').length <= 0)){
            if (e.key === 'Escape' || e.key === 'Esc' || e.keyCode === 27) {
                customModalClear();
            }
        }
    });
    // $(document).on('click', function(e) {
    //     if ($(e.target).closest('#modalBody').length === 0) {
    //         customModalClear();
    //     }
    // });
});