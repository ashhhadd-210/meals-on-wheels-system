function alterCC() {
    $('.basicCC').each(function (index, element) {
        var backgroundColor = $(element).css('background-color');
        $(element).attr('type', 'text');
        $(element).attr('maxLength', '16');
        $(element).css('letter-spacing', '5px');
        $(element).css('width', '246px');
        $(element).on('input', function () {
            $(element).val($(element).val().replace(/[^0-9/]/g, ''));
            if ($(element).val().length > 13) {
                if (validCard($(element).val())) {
                    $(element).css('background-color', '#A0DFA0');
                    $(element).removeClass('error');
                } else {
                    $(element).css('background-color', '#D94747');
                    $(element).addClass('error');
                }
            } else {
                $(element).css('background-color', backgroundColor);
                $(element).removeClass('error');
            }
        })
    })
    $('.customCC').each(function (index, element) {
        var ccInit = $(element);
        var customCC = $('<div>', {
            id: 'customCCShell' + index,
        });
        $('.customCC').hide();
        var isRequired = ccInit.prop('required');
        var computedStyles = window.getComputedStyle(ccInit[0]);
        var originalBorderColor = ccInit.css('border-color');
        customCC.css({
            'background-color': computedStyles.backgroundColor,
            'color': computedStyles.color,
            'padding': computedStyles.padding,
            'border': computedStyles.border,
            'font-family': computedStyles.fontFamily,
            'font-size': computedStyles.fontSize,
            'border-radius': computedStyles.borderRadius,
            'display': 'flex',
            'gap': '5px',
            'width': '245px',
            'justify-content': 'center',
            'align-items': 'center'
        });
        var cc1 = $('<input>', {
            id: 'CC1Input' + index,
            class: 'customCCInput',
            required: isRequired,
            placeholder: '0000',
            type: 'text',
            pattern: '[0-9]*',
            maxlength: '4',
        });

        cc1.css({
            'border': 'none',
            'padding': '0px',
            'width': '44px',
            'outline': '0px'
        })

        var cc2 = $('<input>', {
            id: 'CC2Input' + index,
            class: 'customCCInput',
            required: isRequired,
            placeholder: '0000',
            type: 'text',
            pattern: '[0-9]*',
            maxlength: '4'
        });

        cc2.css({
            'border': 'none',
            'padding': '0px',
            'width': '44px',
            'outline': '0px'
        })

        var cc3 = $('<input>', {
            id: 'CC3Input' + index,
            class: 'customCCInput',
            required: isRequired,
            placeholder: '0000',
            type: 'text',
            pattern: '[0-9]*',
            maxlength: '4'
        });

        cc3.css({
            'border': 'none',
            'padding': '0px',
            'width': '44px',
            'outline': '0px'
        })

        var cc4 = $('<input>', {
            id: 'CC4Input' + index,
            class: 'customCCInput',
            required: isRequired,
            placeholder: '0000',
            type: 'text',
            pattern: '[0-9]*',
            maxlength: '4'
        });

        cc4.css({
            'border': 'none',
            'padding': '0px',
            'width': '44px',
            'outline': '0px'
        })

        customCC.append(cc1);
        customCC.append('<span>-</span>');
        customCC.append(cc2);
        customCC.append('<span>-</span>');
        customCC.append(cc3);
        customCC.append('<span>-</span>');
        customCC.append(cc4);
        ccInit.after(customCC);

        $('#CC1Input' + index).on('input', function () {
            if ($(this).val().length === 4) {
                $(this).nextAll('input').first().focus();
            }
        });
        $('#CC2Input' + index).on('input', function () {
            if ($(this).val().length === 4) {
                $(this).nextAll('input').first().focus();
            }
        });
        $('#CC3Input' + index).on('input', function () {
            if ($(this).val().length === 4) {
                $(this).nextAll('input').first().focus();
            }
        });
        $('#CC4Input' + index).on('input', function () {
            if ($(this).val().length === 4) {
                $(this).nextAll('input').first().focus();
            }
        });

        $(document).on('input', '#customCCShell' + index + ' .customCCInput', function () {
            var clean = $(this).val().replace(/\D/g, '');
            $(this).val(clean);
            var cc1Val = $('#CC1Input' + index).val();
            var cc2Val = $('#CC2Input' + index).val();
            var cc3Val = $('#CC3Input' + index).val();
            var cc4Val = $('#CC4Input' + index).val();


            if (cc1Val !== '' && cc2Val !== '' && cc3Val !== '' && cc4Val !== '') {
                ccInit.val(cc1Val + cc2Val + cc3Val + cc4Val);
                if (validCard(cc1Val + cc2Val + cc3Val + cc4Val)) {
                    customCC.css('background-color', '#A0DFA0');
                } else {
                    customCC.css('background-color', '#D94747');
                }
            } else {
                customCC.css('border-color', originalBorderColor);
            }
        });

        $('.customCCInput').on('keydown', function (e) {
            if (e.which === 8 && $(this).val() == '') {
                $(this).prevAll('input').first().focus();
                ccInit.val('');
            }
        });
    });

    function validCard(cardNumber) {
        cardNumber = cardNumber.replace(/\D/g, '');
        var digits = cardNumber.split('').map(Number);
        for (var i = digits.length - 2; i >= 0; i -= 2) {
            digits[i] *= 2;

            if (digits[i] > 9) {
                digits[i] -= 9;
            }
        }
        var sum = digits.reduce(function (acc, digit) {
            return acc + digit;
        }, 0);
        return sum % 10 === 0;
    }

    ///////////////////////////////////


    $('.customCCDate').each(function (index, element) {
        var dateInit = $(element);
        var customDate = $('<div>', {
            id: 'customCCDateShell' + index,
        });

        $('.customCCDate').hide();

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
            'width': '114px',
            'justify-content': 'center',
            'align-items': 'center'
        });

        var month = $('<input>', {
            id: 'monthCCInput' + index,
            class: 'customCCDateInput',
            autoComplete: 'cc-exp-month',
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

        var year = $('<input>', {
            id: 'yearCCInput' + index,
            class: 'customCCDateInput',
            autoComplete: 'cc-exp-year',
            required: isRequired,
            placeholder: 'YYYY',
            type: 'text',
            pattern: '[0-9]*',
            maxlength: '4'
        });

        year.css({
            'border': 'none',
            'padding': '0px',
            'width': '44px',
            'outline': '0px'
        })

        customDate.append(month);
        customDate.append('<span>/</span>');
        customDate.append(year);
        dateInit.after(customDate);

        $('#monthCCInput' + index).on('input', function () {
            if ($(this).val().length === 2) {
                $(this).nextAll('input').first().focus();
            }
        });

        $(document).on('input', '#customCCDateShell' + index + ' .customCCDateInput', function () {
            var clean = $(this).val().replace(/\D/g, '');
            $(this).val(clean);
            var yearVal = $('#yearCCInput' + index).val();
            var monthVal = $('#monthCCInput' + index).val();

            if (yearVal !== '' && monthVal !== '') {
                dateInit.val(yearVal + '/' + monthVal);
            }
        });

        $('.customCCDateInput').on('keydown', function (e) {
            if (e.which === 8 && $(this).val() == '') {
                $(this).prevAll('input').first().focus();
                dateInit.val('');
            }
        });
    });

    var states = [{
            name: 'Alabama',
            abbreviation: 'AL'
        },
        {
            name: 'Alaska',
            abbreviation: 'AK'
        },
        {
            name: 'American Samoa',
            abbreviation: 'AS'
        },
        {
            name: 'Arizona',
            abbreviation: 'AZ'
        },
        {
            name: 'Arkansas',
            abbreviation: 'AR'
        },
        {
            name: 'California',
            abbreviation: 'CA'
        },
        {
            name: 'Colorado',
            abbreviation: 'CO'
        },
        {
            name: 'Connecticut',
            abbreviation: 'CT'
        },
        {
            name: 'Delaware',
            abbreviation: 'DE'
        },
        {
            name: 'District of Columbia',
            abbreviation: 'DC'
        },
        {
            name: 'Federated States of Micronesia',
            abbreviation: 'FM'
        },
        {
            name: 'Florida',
            abbreviation: 'FL'
        },
        {
            name: 'Georgia',
            abbreviation: 'GA'
        },
        {
            name: 'Guam',
            abbreviation: 'GU'
        },
        {
            name: 'Hawaii',
            abbreviation: 'HI'
        },
        {
            name: 'Idaho',
            abbreviation: 'ID'
        },
        {
            name: 'Illinois',
            abbreviation: 'IL'
        },
        {
            name: 'Indiana',
            abbreviation: 'IN'
        },
        {
            name: 'Iowa',
            abbreviation: 'IA'
        },
        {
            name: 'Kansas',
            abbreviation: 'KS'
        },
        {
            name: 'Kentucky',
            abbreviation: 'KY'
        },
        {
            name: 'Louisiana',
            abbreviation: 'LA'
        },
        {
            name: 'Maine',
            abbreviation: 'ME'
        },
        {
            name: 'Marshall Islands',
            abbreviation: 'MH'
        },
        {
            name: 'Maryland',
            abbreviation: 'MD'
        },
        {
            name: 'Massachusetts',
            abbreviation: 'MA'
        },
        {
            name: 'Michigan',
            abbreviation: 'MI'
        },
        {
            name: 'Minnesota',
            abbreviation: 'MN'
        },
        {
            name: 'Mississippi',
            abbreviation: 'MS'
        },
        {
            name: 'Missouri',
            abbreviation: 'MO'
        },
        {
            name: 'Montana',
            abbreviation: 'MT'
        },
        {
            name: 'Nebraska',
            abbreviation: 'NE'
        },
        {
            name: 'Nevada',
            abbreviation: 'NV'
        },
        {
            name: 'New Hampshire',
            abbreviation: 'NH'
        },
        {
            name: 'New Jersey',
            abbreviation: 'NJ'
        },
        {
            name: 'New Mexico',
            abbreviation: 'NM'
        },
        {
            name: 'New York',
            abbreviation: 'NY'
        },
        {
            name: 'North Carolina',
            abbreviation: 'NC'
        },
        {
            name: 'North Dakota',
            abbreviation: 'ND'
        },
        {
            name: 'Northern Mariana Islands',
            abbreviation: 'MP'
        },
        {
            name: 'Ohio',
            abbreviation: 'OH'
        },
        {
            name: 'Oklahoma',
            abbreviation: 'OK'
        },
        {
            name: 'Oregon',
            abbreviation: 'OR'
        },
        {
            name: 'Palau',
            abbreviation: 'PW'
        },
        {
            name: 'Pennsylvania',
            abbreviation: 'PA'
        },
        {
            name: 'Puerto Rico',
            abbreviation: 'PR'
        },
        {
            name: 'Rhode Island',
            abbreviation: 'RI'
        },
        {
            name: 'South Carolina',
            abbreviation: 'SC'
        },
        {
            name: 'South Dakota',
            abbreviation: 'SD'
        },
        {
            name: 'Tennessee',
            abbreviation: 'TN'
        },
        {
            name: 'Texas',
            abbreviation: 'TX'
        },
        {
            name: 'Utah',
            abbreviation: 'UT'
        },
        {
            name: 'Vermont',
            abbreviation: 'VT'
        },
        {
            name: 'Virgin Islands',
            abbreviation: 'VI'
        },
        {
            name: 'Virginia',
            abbreviation: 'VA'
        },
        {
            name: 'Washington',
            abbreviation: 'WA'
        },
        {
            name: 'West Virginia',
            abbreviation: 'WV'
        },
        {
            name: 'Wisconsin',
            abbreviation: 'WI'
        },
        {
            name: 'Wyoming',
            abbreviation: 'WY'
        },
        {
            name: 'Armed Forces - AA',
            abbreviation: 'AA'
        },
        {
            name: 'Armed Forces - AE',
            abbreviation: 'AE'
        },
        {
            name: 'Armed Forces - AP',
            abbreviation: 'AP'
        },
        {
            name: 'Alberta',
            abbreviation: 'AB'
        },
        {
            name: 'British Columbia',
            abbreviation: 'BC'
        },
        {
            name: 'Manitoba',
            abbreviation: 'MB'
        },
        {
            name: 'New Brunswick',
            abbreviation: 'NB'
        },
        {
            name: 'Newfoundland and Labrador',
            abbreviation: 'NL'
        },
        {
            name: 'Northwest Territories',
            abbreviation: 'NT'
        },
        {
            name: 'Nova Scotia',
            abbreviation: 'NS'
        },
        {
            name: 'Nunavut',
            abbreviation: 'NU'
        },
        {
            name: 'Ontario',
            abbreviation: 'ON'
        },
        {
            name: 'Prince Edward Island',
            abbreviation: 'PE'
        },
        {
            name: 'Quebec',
            abbreviation: 'QC'
        },
        {
            name: 'Saskatchewan',
            abbreviation: 'SK'
        },
        {
            name: 'Yukon',
            abbreviation: 'YT'
        }
    ];

    countries = [
        "Afghanistan",
        "Aland Islands",
        "Albania",
        "Algeria",
        "American Samoa",
        "Andorra",
        "Angola",
        "Anguilla",
        "Antarctica",
        "Antigua and Barbuda",
        "Argentina",
        "Armenia",
        "Aruba",
        "Australia",
        "Austria",
        "Azerbaijan",
        "Bahamas, The",
        "Bahrain",
        "Bangladesh",
        "Barbados",
        "Belarus",
        "Belgium",
        "Belize",
        "Benin",
        "Bermuda",
        "Bhutan",
        "Bolivia",
        "Bonaire, Saint Eustatius and Saba",
        "Bosnia and Herzegovina",
        "Botswana",
        "Bouvet Island",
        "Brazil",
        "British Indian Ocean Territory",
        "Brunei Darussalam",
        "Bulgaria",
        "Burkina Faso",
        "Burundi",
        "Cambodia",
        "Cameroon",
        "Canada",
        "Canary Islands",
        "Cape Verde",
        "Cayman Islands",
        "Central African Republic",
        "Chad",
        "Chile",
        "China",
        "Christmas Island",
        "Cocos (Keeling) Islands",
        "Colombia",
        "Comoros",
        "Congo",
        "Congo, The Democratic Republic of the",
        "Cook Islands",
        "Costa Rica",
        "Cote D'ivoire",
        "Croatia",
        "Curaçao",
        "Cyprus",
        "Czech Republic",
        "Denmark",
        "Djibouti",
        "Dominica",
        "Dominican Republic",
        "Ecuador",
        "Egypt",
        "El Salvador",
        "Equatorial Guinea",
        "Eritrea",
        "Estonia",
        "Ethiopia",
        "Falkland Islands (Malvinas)",
        "Faroe Islands",
        "Fiji",
        "Finland",
        "France",
        "French Guiana",
        "French Polynesia",
        "French Southern Territories",
        "Gabon",
        "Gambia, The",
        "Georgia",
        "Germany",
        "Ghana",
        "Gibraltar",
        "Greece",
        "Greenland",
        "Grenada",
        "Guadeloupe",
        "Guam",
        "Guatemala",
        "Guernsey",
        "Guinea",
        "Guinea-Bissau",
        "Guyana",
        "Haiti",
        "Heard Island and the McDonald Islands",
        "Holy See",
        "Honduras",
        "Hong Kong",
        "Hungary",
        "Iceland",
        "India",
        "Indonesia",
        "Iraq",
        "Ireland",
        "Isle of Man",
        "Israel",
        "Italy",
        "Jamaica",
        "Japan",
        "Jersey",
        "Jordan",
        "Kazakhstan",
        "Kenya",
        "Kiribati",
        "Kosovo",
        "Kuwait",
        "Kyrgyzstan",
        "Lao People's Democratic Republic",
        "Latvia",
        "Lebanon",
        "Lesotho",
        "Liberia",
        "Libya",
        "Liechtenstein",
        "Lithuania",
        "Luxembourg",
        "Macao",
        "Macedonia, The Former Yugoslav Republic of",
        "Madagascar",
        "Malawi",
        "Malaysia",
        "Maldives",
        "Mali",
        "Malta",
        "Marshall Islands",
        "Martinique",
        "Mauritania",
        "Mauritius",
        "Mayotte",
        "Mexico",
        "Micronesia, Federated States of",
        "Moldova, Republic of",
        "Monaco",
        "Mongolia",
        "Montenegro",
        "Montserrat",
        "Morocco",
        "Mozambique",
        "Myanmar",
        "Namibia",
        "Nauru",
        "Nepal",
        "Netherlands",
        "Netherlands Antilles",
        "New Caledonia",
        "New Zealand",
        "Nicaragua",
        "Niger",
        "Nigeria",
        "Niue",
        "Norfolk Island",
        "Northern Mariana Islands",
        "Norway",
        "Oman",
        "Pakistan",
        "Palau",
        "Palestinian Territories",
        "Panama",
        "Papua New Guinea",
        "Paraguay",
        "Peru",
        "Philippines",
        "Pitcairn",
        "Poland",
        "Portugal",
        "Puerto Rico",
        "Qatar",
        "Republic of Korea",
        "Reunion",
        "Romania",
        "Russian Federation",
        "Rwanda",
        "Saint Barthelemy",
        "Saint Helena, Ascension and Tristan da Cunha",
        "Saint Kitts and Nevis",
        "Saint Lucia",
        "Saint Martin",
        "Saint Pierre and Miquelon",
        "Saint Vincent and the Grenadines",
        "Samoa",
        "San Marino",
        "Sao Tome and Principe",
        "Saudi Arabia",
        "Senegal",
        "Serbia",
        "Seychelles",
        "Sierra Leone",
        "Singapore",
        "Sint Maarten",
        "Slovakia",
        "Slovenia",
        "Solomon Islands",
        "Somalia",
        "South Africa",
        "South Georgia and the South Sandwich Islands",
        "Spain",
        "Sri Lanka",
        "Suriname",
        "Svalbard and Jan Mayen",
        "Swaziland",
        "Sweden",
        "Switzerland",
        "Taiwan",
        "Tajikistan",
        "Tanzania, United Republic of",
        "Thailand",
        "Timor-leste",
        "Togo",
        "Tokelau",
        "Tonga",
        "Trinidad and Tobago",
        "Tunisia",
        "Turkey",
        "Turkmenistan",
        "Turks and Caicos Islands",
        "Tuvalu",
        "Uganda",
        "Ukraine",
        "United Arab Emirates",
        "United Kingdom",
        "United States",
        "United States Minor Outlying Islands",
        "Uruguay",
        "Uzbekistan",
        "Vanuatu",
        "Venezuela",
        "Vietnam",
        "Virgin Islands, British",
        "Virgin Islands, U.S.",
        "Wallis and Futuna",
        "Western Sahara",
        "Yemen",
        "Zambia",
        "Zimbabwe"
    ]

    $('.stateSelector').each(function (index, element) {
        var option1 = $('<option>', {
            text: 'Select State',
            value: '',
            hidden: 'hidden',
            selected: 'selected'
        })
        $(element).append(option1);
        states.forEach(state => {
            var option = $('<option>', {
                value: state.abbreviation,
                text: state.name + ' (' + state.abbreviation + ')'
            })
            $(element).append(option);
        });
        var initialState = $(element).data('initial-state');
        if (initialState) {
            $(element).val(initialState);
        } else {
            $(element).val('WI');
        }
    })

    $('.countrySelector').each(function (index, element) {
        var option1 = $('<option>', {
            text: 'Select Country',
            value: '',
            hidden: 'hidden',
            selected: 'selected'
        })
        $(element).append(option1);
        countries.forEach(country => {
            var option = $('<option>', {
                value: country,
                text: country
            })
            $(element).append(option);
        });
        $(element).val('United States');
    })
}
$(document).ready(function () {
    alterCC();

});