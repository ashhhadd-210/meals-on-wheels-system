function customMessage(message, type = 2, force = false, autoClose = false) {
    var body = $('body');

    var bg = $('<div>', {
        id: 'cm-customMessageBg'
    })

    if(autoClose){
        bg.addClass('timeout');
    }

    bg.css({
        'position': 'fixed',
        'top': '0px',
        'left': '0px',
        'display': 'flex',
        'justify-content': 'center',
        'width': '100vw',
        'height': '100vh',
        'z-index': '10000000',
    })

    if (force) {
        body.css({
            'overflow-y': 'hidden',
            'margin-right': '17px'
        })
        bg.css({
            'background-color': 'rgba(0,0,0,0.6',
            'backdrop-filter': 'blur(3px)',
            '-webkit-backdrop-filter': 'blur(3px)',
        })
    }

    if(autoClose){
        bg.css({
            'width' : '400px',
            'height' : '130px',
            'left' : '50%',
            'transform' : 'translateX(-50%)'
        })
    }

    var contentWrap = $('<div>', {
        id: 'cm-messageContentWrap'
    })

    contentWrap.css({
        'background-color': 'white',
        'box-shadow': '0px 0px 35px rgba(0,0,0,0.4)',
        'width': '95%',
        'max-width': '400px',
        'display': 'flex',
        'align-items': 'center',
        'border-radius': '3px',
        'padding-right': '10px',
        'gap': '10px',
        'margin-top': '20px',
        'height': '100px',
        'position': 'relative',
        'top': '-150px'
    })

    var iconWrap = $('<div>', {
        id: 'cm-iconWrap'
    })

    const types = ['', 'Success', 'Warning', 'Error'];
    const colors = ['', '#44BD32', '#FBC531', '#E84118'];

    iconWrap.css({
        'background-color': colors[type],
        'border-radius': '3px',
        'height': '100%',
        'width': '50px',
        'display': 'flex',
        'justify-content': 'center',
        'align-items': 'center'
    })

    var icon = $('<img>', {
        src: '/js/customMessage/' + types[type].toLowerCase() + 'Icon.png',
        id: 'cm-messageIcon'
    })

    icon.css({
        'width': '30px'
    })

    var messageBody = $('<div>', {
        id: 'cm-messageBody'
    })

    messageBody.css({
        'display': 'flex',
        'flex-direction': 'column',
        'gap': '5px',
        'flex-grow': '1',
        'margin-bottom': '10px',
        'overflow-y': 'auto'
    })

    var headerMessage = $('<span>', {
        id: 'cm-messageHeader',
        text: types[type] + '!'
    })

    headerMessage.css({
        'color': colors[type],
        'font-size': '20px'
    })

    var messageText = $('<span>', {
        id: 'cm-message',
        text: message
    })

    var close = $('<img>', {
        src: '/js/customMessage/closeIcon.png',
        id: 'cm-close',
        onclick: 'customMessageClear()'
    })

    close.css({
        'cursor': 'pointer'
    })

    iconWrap.append(icon);
    contentWrap.append(iconWrap);
    messageBody.append(headerMessage);
    messageBody.append(messageText);
    contentWrap.append(messageBody);
    if(!autoClose){
        contentWrap.append(close);
    }
    bg.append(contentWrap);
    body.append(bg);
}

$(document).on('DOMNodeInserted', function (e) {
    if ($(e.target).is('#cm-customMessageBg')) {
        $('#cm-messageContentWrap').animate({
            top: '+=150px'
        }, 'slow')
        if($(e.target).hasClass('timeout')){
            setTimeout(() => {
                customMessageClear()
            }, 3000);
        }
    }
});

$(document).ready(function () {
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' || e.key === 'Esc' || e.keyCode === 27) {
            customMessageClear();
        }
    });
});

function customMessageClear() {
    $('#cm-customMessageBg').fadeOut('200', () => {
        $('#cm-customMessageBg').remove();
        $('body').css({
            'overflow-y': 'scroll',
            'margin-right': '0px'
        })
    })
}