'use strict';

const numbersOnly = document.querySelectorAll('.numbers-only'),
    gmmDateFormat = document.querySelectorAll('.gmm-date-format');

if (numbersOnly.length > 0) {
    numbersOnly.forEach(function (inputValue) {
        new Cleave(inputValue, {
            delimiter: '',
            numeral: true
        });
    });
}

if (gmmDateFormat.length > 0) {
    gmmDateFormat.flatpickr({
        // monthSelectorType: 'static',
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd - M - Y'
    });
}


function blockArea(elementToBlock) {
    elementToBlock.block({
        message: '<div class="spinner-border text-white" role="status"></div>',
        css: {
            backgroundColor: 'transparent',
            color: '#fff',
            border: '0'
        },
        overlayCSS: {
            opacity: 0.5,
            backgroundColor: '#934583'
        }
    });
}

function unBlockArea(elementToBlock) {
    elementToBlock.unblock();
}


function updateSelectBox(selectBoxToBeUpdated, contentToBeUpdated) {

    selectBoxToBeUpdated.selectpicker('destroy');
    selectBoxToBeUpdated.html(contentToBeUpdated);
    selectBoxToBeUpdated.selectpicker();

}

function dismissOffcanvas(offCanvasElementID) {
    var offcanvasElement = document.getElementById(offCanvasElementID);
    var offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
    offcanvas.hide();
}

function showOffcanvas(offCanvasElementID) {
    var offcanvasElement = document.getElementById(offCanvasElementID);
    var offcanvas = new bootstrap.Offcanvas(offcanvasElement);
    offcanvas.show();
}


function populateStateForSelectedCountry(selectedCountryID, stateSelectBoxID, citySelectBoxID = "") {

    const stateSelectBox = $("#" + stateSelectBoxID);
    const citySelectBox = $("#" + citySelectBoxID);
    updateSelectBox(stateSelectBox, '<option value="">Loading...</option>');
    if (citySelectBox !== "") updateSelectBox(citySelectBox, '<option value="">Loading...</option>');


    var formData = new FormData();
    formData.append("selectedCountryID", selectedCountryID);

    $.ajax({
        url: '/ajax/get_states.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (data, status) {
            console.log(data);
            updateSelectBox(stateSelectBox, data);
            if (citySelectBox !== '') updateSelectBox(citySelectBox, '<option value="">Select a state first</option>');
        },

        error: function (error) {

            console.log(error);
        }
    });


}


function populateCityForSelectedState(selectedStateID, citySelectBoxID) {
    const citySelectBox = $("#" + citySelectBoxID);
    updateSelectBox(citySelectBox, '<option value="">Loading...</option>');

    var formData = new FormData();
    formData.append("selectedStateID", selectedStateID);

    $.ajax({
        url: '/ajax/get_cities.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (data, status) {
            updateSelectBox(citySelectBox, data);
        },

        error: function (error) {

            console.log(error);
        }
    });


}


function getRepeaterRowIndex(jQueryElement) {
    // console.log($(jQueryElement).attr('name'));
    const nameAttr = jQueryElement.attr('name');
    // Use a regular expression to extract the index from the name
    const match = nameAttr.match(/\[([0-9]+)\]/);

    // Check if a match was found
    if (match && match[1]) {
        const index = parseInt(match[1], 10); // Convert the extracted value to a number
        return index;
    } else {
        console.log('Index not found in name attribute');
        return false;
    }
}


function getElementByIndexAndName(index, name) {
    const element = $('[name="line-item[' + index + '][' + name + ']"]');
    return element;
}

function getRepeaterRowCount() {
    // Count all elements with the 'data-repeater-item' attribute
    const rowCount = $('[data-repeater-item]').length;
    console.log('Total rows in repeater:', rowCount);
    return rowCount;
}

function toTwoDecimal(value) {
    // Ensure the input is a valid number
    // if (isNaN(value)) {
    //     alert("Decimal Error");
    //     throw new Error("Input must be a number");
    // }

    // Return the value rounded to two decimal places
    return parseFloat(value).toFixed(2);
}

//SWAL
function showSuccessMessage(message, callbackFunction = null, ...callbackParams) {
    Swal.fire({
        title: 'Success!',
        icon: 'success',
        text: message,
        type: 'success',
        customClass: {
            confirmButton: 'btn btn-primary'
        },
        showClass: {
            popup: 'animate__animated animate__bounce'
        },
        buttonsStyling: false
    }).then(function () {
        if (typeof callbackFunction === 'function') {
            callbackFunction(...callbackParams);
        }
    });
}

function showErrorMessage(message, callbackFunction = null, ...callbackParams) {
    Swal.fire({
        title: 'Error!',
        icon: 'error',
        text: message,
        type: 'error',
        customClass: {
            confirmButton: 'btn btn-primary'
        },
        showClass: {
            popup: 'animate__animated animate__shakeX'
        },
        buttonsStyling: false
    }).then(function () {
        if (typeof callbackFunction === 'function') {
            callbackFunction(...callbackParams);
        }
    });
}


function gotoPage(url) {
    window.location.href = url;
}

function reloadPage() {
    location.reload();
}

$(document).on('changed.bs.select', '#companySelectionDropDown', function(){
    const selectedValue = $(this).val();
    console.log('Changed value:', selectedValue);
    callPhpAPI(
        '/ajax/ajax_switch_company.php',
        'POST',
        { companyID: selectedValue },
        (response) => {
            console.log(response.message);
            console.log("Session companyID set to: ", response.companyID);
        },
        (error) => {
            console.error("Error:", error);
        }
    );
    gotoPage("/dashboard");
})



/**
 * A generic helper function (jQuery-based) to call an API endpoint.
 *
 * @param {string} endpoint        - The URL or endpoint for the PHP API.
 * @param {string} method          - The HTTP method to use (e.g., 'GET', 'POST', etc.).
 * @param {Object|null} data       - The data to send with the request (if applicable).
 * @param {Function} onSuccess     - Callback for success response.
 * @param {Function} onError       - Callback for error handling.
 */
function callPhpAPI(endpoint, method = 'GET', data = null, onSuccess = () => {}, onError = () => {}) {
    // Prepare ajax config
    const ajaxConfig = {
        url: endpoint,
        type: method,                 // e.g. 'GET', 'POST', 'PUT', etc.
        processData: (method === 'GET'),
        // We won't rely on jQuery to parse the response automatically;
        // we’ll handle parsing manually in `success`.
        dataType: 'text',

        success: function (response, textStatus, jqXHR) {
            // Check the "Content-Type" from response headers.
            // If JSON, parse it; otherwise treat it as text.
            const contentType = jqXHR.getResponseHeader('Content-Type') || '';
            let parsedData = response;

            if (contentType.includes('application/json')) {
                try {
                    parsedData = JSON.parse(response);
                } catch (parseError) {
                    // If parsing fails, pass the error to onError
                    onError(parseError);
                    return;
                }
            }

            // If the request succeeded, call the onSuccess callback
            onSuccess(parsedData);
        },

        error: function (jqXHR, textStatus, errorThrown) {
            // jQuery triggers 'error' if the status is not in [200..299],
            // or if there's a parsing/network error
            onError(errorThrown || textStatus);
        }
    };

    // For GET: jQuery will convert the "data" object to a query string if processData is true
    // For non-GET: we send JSON in the body
    if (method.toUpperCase() !== 'GET' && data) {
        ajaxConfig.contentType = 'application/json; charset=UTF-8';
        ajaxConfig.data = JSON.stringify(data);
    } else if (method.toUpperCase() === 'GET' && data) {
        // Just pass the object. jQuery will transform it into ?key=value automatically.
        ajaxConfig.data = data;
    }

    // Execute AJAX call
    $.ajax(ajaxConfig);
}


function formDataToJson(formData) {
    const obj = {};
    formData.forEach((value, key) => {
        // Handle multiple entries with the same key (e.g., checkboxes)
        if (obj[key]) {
            if (Array.isArray(obj[key])) {
                obj[key].push(value);
            } else {
                obj[key] = [obj[key], value];
            }
        } else {
            obj[key] = value;
        }
    });
    return JSON.stringify(obj);
}