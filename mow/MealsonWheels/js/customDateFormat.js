$(document).ready(function () {
    customDate();
});

function customDate(){
    $('.customDate').each(function (index, element) {
        var dateInit = $(element);
        var customDate = $('<div>', {
            id: 'customDateShell' + index,
            // ... other properties
        });

        $('.customDate').hide();

        var isRequired = dateInit.prop('required');

        var computedStyles = window.getComputedStyle(dateInit[0]);
        customDate.css({
            'background-color': computedStyles.backgroundColor,
            'color': computedStyles.color,
            'padding': computedStyles.padding,
            'border': computedStyles.border,
            'font-family': computedStyles.fontFamily,
            'font-size': computedStyles.fontSize,
            'border-radius': computedStyles.borderRadius,
            'display': 'flex',
            'gap': '5px',
            'width': '160px',
            'justify-content': 'center',
            'align-items': 'center'
        });

        var month = $('<input>', {
            id: 'monthInput' + index,
            class: 'customDateInput',
            required: isRequired,
            placeholder: 'MM',
            type: 'text',
            pattern: '[0-9]*',
            maxlength: '2',
        });

        month.css({
            'border': 'none',
            'padding': '0px',
            'width': '30px',
            'outline': '0px'
        })

        var day = $('<input>', {
            id: 'dayInput' + index,
            class: 'customDateInput',
            required: isRequired,
            placeholder: 'DD',
            type: 'text',
            pattern: '[0-9]*',
            maxlength: '2',
        });

        day.css({
            'border': 'none',
            'padding': '0px',
            'width': '30px',
            'outline': '0px'
        })

        var year = $('<input>', {
            id: 'yearInput' + index,
            class: 'customDateInput',
            required: isRequired,
            placeholder: 'YYYY',
            type: 'text',
            pattern: '[0-9]*',
            maxlength: '4',
        });

        year.css({
            'border': 'none',
            'padding': '0px',
            'width': '45px',
            'outline': '0px'
        })

        customDate.append(month);
        customDate.append('<span>/</span>');
        customDate.append(day);
        customDate.append('<span>/</span>');
        customDate.append(year);
        dateInit.after(customDate);

        $('#monthInput' + index).on('input', function () {
            if ($(this).val().length === 2) {
                $(this).nextAll('input').first().focus();
            }
        });

        $('#dayInput' + index).on('input', function () {
            if ($(this).val().length === 2) {
                $(this).nextAll('input').first().focus();
            }
        });

        $(document).on('input', '#customDateShell' + index + ' .customDateInput', function () {
            var clean = $(this).val().replace(/\D/g, '');
            $(this).val(clean);
            var yearVal = $('#yearInput' + index).val();
            var monthVal = $('#monthInput' + index).val();
            var dayVal = $('#dayInput' + index).val();

            if (yearVal !== '' && monthVal !== '' && dayVal !== '') {
                dateInit.val(yearVal + '-' + monthVal + '-' + dayVal);
            }
        });

        $('.customDateInput').on('keydown', function (e) {
            if (e.which === 8 && $(this).val() == '') {
                $(this).prevAll('input').first().focus();
                dateInit.val('');
            }
        });
    });
}