function cleanNumbers() {
    var inputs = $('.customNumberInput')
    inputs.each(function (index, element) {
        $(element).on('input', function () {
            $(element).val($(element).val().replace(/[^\d.,]/g, ''));
        })
    })
}

function monthInputs() {
    $('.monthSelector').each(function () {
        const $input = $(this);
        if ($input.hasClass('triggered') == false) {

            const inputValue = $input.val() || '';
            const [currentYear, currentMonth] = inputValue ? inputValue.split('-') : [null, null];

            const monthSelectorHTML = `
            <div class="month-selector">
            <div class="month-display">
            <span>${currentYear ? `${new Date(currentYear, currentMonth - 1).toLocaleString('default', { month: 'long' })} ${currentYear}` : 'Select Month'}</span>
                    <span><svg style="width:20px" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" /></svg></span>
                    </div>
                    <div class="month-dropdown">
                    <div class="year-control">
                        <svg class="prev-year month-ui-year-select" style="width:30px" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
                        <span class="current-year">${currentYear || new Date().getFullYear()}</span>
                        <svg class="next-year month-ui-year-select" style="width:30px" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                        </div>
                        <div class="months">
                        ${['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                            .map((m, i) => `<div class="month" data-month="${i + 1}">${m}</div>`)
                            .join('')}
                            </div>
                            <button type="button" class="clearMonthSelect">Clear</button>
                            </div>
                            </div>
                            `;



            $input.hide().after(monthSelectorHTML);
            $input.addClass('triggered');
            const $selector = $input.next('.month-selector');
            const $dropdown = $selector.find('.month-dropdown');
            const $display = $selector.find('.month-display');
            const $clearBtn = $selector.find('.clearMonthSelect');

            if (currentYear && currentMonth) {
                const initialMonth = parseInt(currentMonth, 10);
                $selector.find(`.month[data-month="${initialMonth}"]`).addClass('active');
            }

            $clearBtn.on('click', () => {
                $input.val('');
                $display.find('span').first().text('Select Month');
                $dropdown.find('.month').removeClass('active');
            })

            $display.on('click', function () {
                $dropdown.toggle();
            });

            $selector.find('.prev-year').on('click', function () {
                const $year = $selector.find('.current-year');
                $year.text(+$year.text() - 1);
            });
            $selector.find('.next-year').on('click', function () {
                const $year = $selector.find('.current-year');
                $year.text(+$year.text() + 1);
            });

            $selector.find('.month').on('click', function () {
                const $year = $selector.find('.current-year').text();
                const $month = $(this).data('month');
                const monthName = new Date($year, $month - 1).toLocaleString('default', {
                    month: 'long'
                });

                $display.find('span:first').text(`${monthName} ${$year}`);
                $selector.find('.month').removeClass('active');
                $(this).addClass('active');
                $input.val(`${$year}-${String($month).padStart(2, '0')}`);
                $dropdown.hide();
            });
            $(document).on('click', function (e) {
                if (!$(e.target).closest($selector).length) {
                    $dropdown.hide();
                }
            });

            $input[0].monthSelector = {
                set: (year, month) => {
                    const monthName = new Date(year, month - 1).toLocaleString('default', {
                        month: 'long'
                    });
                    $display.find('span:first').text(`${monthName} ${year}`);
                    $input.val(`${year}-${String(month).padStart(2, '0')}`);
                },
                get: () => {
                    const [year, month] = $input.val().split('-');
                    return {
                        year: parseInt(year, 10),
                        month: parseInt(month, 10),
                    };
                },
                reset: () => {
                    $display.find('span:first').text('Select Month');
                    $dropdown.find('.month').removeClass('active');
                    $input.val('');
                }
            };
        }

    });
    appendMonthInputStyles();
}

var stylesAppended = false;

function appendMonthInputStyles() {
    const style = `
    .month-selector {
        position: relative;
        display: inline-block;
        width: 200px;
        width: 170px !important;
    }
    .month-display {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        cursor: pointer;
        background-color: #fff;
    }
    .month-display span {
        font-size: 16px;
    }
    .month-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
        z-index: 1000;
    }
    .month-dropdown .year-control {
        display: flex;
        justify-content: space-between;
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }
    .month-dropdown .months {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        padding: 10px;
        gap: 2px;
    }
    .month-dropdown .month {
        padding: 8px 0;
        text-align: center;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.2s ease;
    }
    .month-dropdown .month:hover {
        background-color: #f0f0f0;
    }
    .month-dropdown .month.active {
        background-color: #000;
        color: #fff;
    }
    .month-ui-year-select {
        cursor: pointer;
    }
        .current-year {
	display: flex;
	justify-content: center;
	align-items: center;
}
    .clearMonthSelect {
	background-color: transparent;
	border: none;
	width: 100%;
	padding: 7px;
	cursor: pointer;
	font-family: inherit;
}
`;
    if (!stylesAppended) {
        $('<style>').text(style).appendTo('head');
        stylesAppended = true;
    }
}


function priceInputs() {
    var inputs = $('.customPrice'); // Use . to select elements with class 'customPrice'
    inputs.each(function (index, element) {
        var originalLength = $(element).val().length;
        var originalPosition = element.selectionStart;
        var value = $(element).val().replace(/\D/g, '');

        if (value.length === 0) {
            value = '0';
        }

        value = value.padStart(3, '0');
        value = String(parseInt(value, 10)).padStart(3, '0');

        var integerPart = value.slice(0, -2);
        var decimalPart = value.slice(-2);
        var formattedIntegerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

        $(element).val(formattedIntegerPart + '.' + decimalPart);

        // Cursor position adjustment
        var newPosition = originalPosition + ($(element).val().length - originalLength);
        if (newPosition > $(element).val().length) {
            newPosition = $(element).val().length;
        }
        element.setSelectionRange(newPosition, newPosition);
    });
}

function alterCheckboxes() {
    var green = '#519348';
    var inputs = $('input[type="checkbox"].customCheckbox')
    inputs.each(function (index, element) {
        if (!$(element).hasClass('altered')) {
            var customCheckboxWrap = $('<div>', {
                class: 'customCheckboxWrap'
            })
            var toggleWrap = $('<div>', {
                class: 'customCheckboxToggleWrap',
            })

            toggleWrap.css({
                'width': '60px',
                'height': '35px',
                'background-color': 'transparent',
                'border': '2px solid #c6c6c6',
                'border-radius': '100px',
                'padding': '1px',
                'position': 'relative',
                'cursor': 'pointer',
                'transition': 'all 150ms cubic-bezier(0.77, 0, 0.18, 1)'
            })
            var toggleBtn = $('<div>', {
                class: 'customCheckboxToggleBtn'
            })

            var clone = $(element).clone(true)
            clone.addClass('altered')
            clone.css({
                'display': 'none'
            })
            var checked = false;
            if (clone.prop('checked')) {
                toggleWrap.addClass('checked')
                toggleBtn.addClass('checked')
                checked = true;
            }

            toggleBtn.css({
                'width': '27px',
                'height': '27px',
                'border-radius': '100%',
                'position': 'absolute',
                'top': '2px',
                'transition': 'all 150ms cubic-bezier(0.77, 0, 0.18, 1)'
            })


            if (checked) {
                toggleBtn.css({
                    'left': '27px',
                    'background-color': 'white',
                })
                toggleWrap.css({
                    'background-color': green
                })
            } else {
                toggleBtn.css({
                    'left': '2px',
                    'background-color': '#bdbdbd',
                })
            }



            toggleWrap.append(toggleBtn)
            customCheckboxWrap.append(clone)
            customCheckboxWrap.append(toggleWrap)
            $(element).replaceWith(customCheckboxWrap)

            toggleWrap.on('click', function () {
                if ($(this).hasClass('checked')) {
                    $(this).removeClass('checked');
                    toggleBtn.removeClass('checked')
                    toggleBtn.css({
                        'left': '2px',
                        'background-color': '#bdbdbd',
                    })
                    toggleWrap.css({
                        'background-color': 'transparent'
                    })
                    $(clone).prop('checked', false).trigger('change')
                } else {
                    $(this).addClass('checked');
                    toggleBtn.addClass('checked')
                    toggleBtn.css({
                        'left': '27px',
                        'background-color': 'white',
                    })
                    toggleWrap.css({
                        'background-color': green
                    })
                    $(clone).prop('checked', true).trigger('change')
                }
            })

            $(element).addClass('altered');
        }
    })
}

function alterCheckboxWrap() {
    var inputs = $('.checkboxRow');

    inputs.each(function (index, element) {

    })
}

function alterPhones() {
    var inputs = $('.customPhone');
    inputs.each(function () {
        $(this).on('input', function () {
            var value = $(this).val().replace(/\D/g, '');
            $(this).val(value);

            this.setCustomValidity('');

            if (value.length < 10) {
                this.setCustomValidity('Please enter a 10-digit phone number.');
                this.reportValidity();
            } else if (value.length == 11) {
                $(this).val(value.slice(0, 10));
            }
        });
    });
}

function alterForms() {
    var inputs = $('.inputRow > input, .inputRow > textarea');

    inputs.each(function (index, element) {
        var types = ['text', 'tel', 'email', 'password', 'number'];
        if ((types.indexOf($(element).prop('type')) !== -1 || $(element).prop('tagName') == 'TEXTAREA') && $(element).parent().css('display') != 'none') {
            var label = $(element).siblings('label')[0];
            if (label != undefined) {
                if ($(element).prop('tagName') == 'TEXTAREA') {
                    var height = parseInt($(element).css('padding-top').replace('px', ''));
                    var moveHeight = height + $(label).outerHeight() + 1;
                } else {
                    var height = $(element).outerHeight();
                    var moveHeight = (height - $(label).outerHeight() / 2) + 0.5;
                }
                var pad = $(element).css('padding-left');
                $(element).attr('placeholder', '');
                if ($(element).val() == '' && $(element).html() == '') {
                    $(label).css({
                        'cursor': 'text',
                        'font-size': $(element).css('font-size'),
                        'opacity': '0.6',
                        'transform': `translate(${pad}, ${moveHeight}px)`
                    })
                }
                setTimeout(() => {
                    $(label).css({
                        'transition': 'all 100ms ease-in-out',
                    })
                }, 100);
                $(label).on('click', function () {
                    $(element).focus();
                })
                $(element).on('focus', function () {
                    $(label).css({
                        'cursor': 'default',
                        'opacity': '1',
                        'transform': `translate(${0}, ${0}px)`
                    })
                })
                $(element).on('blur', function () {
                    if ($(element).val() == '' && $(element).html() == '') {
                        $(label).css({
                            'cursor': 'text',
                            'opacity': '0.6',
                            'transform': `translate(${pad}, ${moveHeight}px)`
                        })
                    }
                })
            }

        }

    })
}

function alterPasswords() {
    var passwordsInputs = $('input[type="password"]');
    passwordsInputs.each(function (index, element) {
        var parent = $(element).parent();
        var label = parent.find('label').first();

        label.css({
            'position': 'relative',
            'z-index': '2'
        })
        var wrap = $('<div>', {
            id: 'passwordInputWrap' + index
        })
        wrap.css({
            'background-color': $(element).css('background-color'),
            'border': $(element).css('border'),
            'border-radius': $(element).css('border-radius'),
            'position': 'relative',
            'display': 'flex',
            'align-items': 'center'
        })
        $(element).prop('id', 'passwordInput' + index)
        $(element).css({
            'width': '100%',
            'position': 'relative',
            'z-index': '1',
            'border': '0px solid transparent',
            'background-color': 'transparent',
            'outline': 'none',
            'left': '0px'
        })

        var img = $('<img>', {
            src: '/media/icons/dark/visible.png',
            class: 'passwordToggleBtn',
            onclick: 'togglePassword(this,' + index + ')'
        })

        img.css({
            'width': '20px',
            'position': 'absolute',
            'right': '10px',
            'z-index': '3',
            'cursor': 'pointer'
        })

        wrap.append(img)
        wrap.append($(element));
        parent.append(wrap);

    })
}

function togglePassword(btn, i) {
    var passwordsInput = $('#passwordInput' + i);
    var type = passwordsInput.hasClass('shown') ? 'password' : 'text';
    var newInput = passwordsInput.clone(true).prop('type', type);

    if (type === 'text') {
        newInput.addClass('shown');
        btn.src = '/media/icons/dark/invisible.png';
    } else {
        newInput.removeClass('shown');
        btn.src = '/media/icons/dark/visible.png';
    }

    passwordsInput.replaceWith(newInput);
}

function alterSliders() {
    var sliders = $('.cm_slider:not(.triggered)');

    sliders.each(function () {
        var $select = $(this);
        $select.hide();

        var $wrapper = $('<div class="cm-slider-wrapper" style="position: relative;"></div>');
        var $selector = $('<div class="cm-slider-selector"></div>');

        var $buttons = [];

        $select.find('option').each(function (index) {
            var $opt = $(this);
            var $btn = $('<div class="cm-slider-option"></div>').text($opt.text()).css({
                transition: 'color 0.3s ease'
            });

            if ($opt.is(':selected')) {
                $btn.addClass('active');
            }

            $btn.on('click', function () {
                $wrapper.find('.cm-slider-option').removeClass('active');
                $btn.addClass('active');
                $select.prop('selectedIndex', index).trigger('change');
                moveSelector($btn, $selector);
            });

            $buttons.push($btn);
            $wrapper.append($btn);
        });

        // Insert selector at the bottom
        $wrapper.prepend($selector);

        // After DOM is built, move the selector to the active
        setTimeout(() => {
            let $active = $wrapper.find('.cm-slider-option.active');
            if ($active.length) {
                moveSelector($active, $selector);
            }
        }, 10);

        $select.after($wrapper);
        $select.addClass('triggered');
    });
    applySliderStyles();
}

function moveSelector($btn, $selector) {
    $selector.css({
        width: $btn.outerWidth(),
        left: $btn.position().left
    });
}


var appliedSliderStyles = false;
function applySliderStyles() {
    var styles = `
    .cm-slider-wrapper {
	display: flex;
	border: 1px solid #c6c6c6;
	border-radius: 5px;
	overflow: hidden;
	background-color: #0000;
	width: fit-content;
	font-family: sans-serif;
	position: relative;
	padding: 0px;
	align-items: center;
	color: inherit;
}

.cm-slider-selector {
    background-color: #00a651;
    position: absolute;
    top: 0px;
    left: 0px;
    height: 100%;
    border-radius: 3px;
    transition: all 0.3s ease;
    z-index: 0;
}

.cm-slider-option {
    padding: 15px 20px;
    cursor: pointer;
    color: var(--grey);
    z-index: 1;
    position: relative;
    transition: color 0.3s ease;
}

.cm-slider-option.active {
    color: white;
}`;
if (!appliedSliderStyles) {
    $('<style>').text(styles).appendTo('head');
    appliedSliderStyles = true;
}

}

// appendForms() was referenced by siteUpdate() but never defined, which threw an
// uncaught ReferenceError on every page load and halted the rest of siteUpdate()
// (alterPasswords/alterPhones/alterCheckboxes/etc. never ran). Defined as a no-op
// to restore the rest of the form setup. Safe: nothing else relies on it.
function appendForms() {}

function siteUpdate() {
    alterForms();
    appendForms();
    alterPasswords();
    alterPhones();
    alterCheckboxes();
    priceInputs();
    cleanNumbers();
    alterSliders();
}

$(document).ready(function () {
    siteUpdate();
})