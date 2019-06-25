/**
 * Created by Splinter on 16/04/2016.
 */

/*** Selectors ****/
var formSelector = "form[name='transfer']";
var cpSelector = '#transfer_cp';
var locationSelector = '#transfer_location';
var validateFirstPartBtn = '#validate-transfer-information';
var modifyFirstPartBtn = '#modify-first-part-information';
var formPart2 = '#form-part-2';
var firstPartZone = "#first-part-zone";
var qtySelector = '#transfer_qty';
var qtyChildSelector = '#transfer_qtyChild';
var qtyBabySelector = '#transfer_qtyBaby';
var vols = [];
var unitPrice = '#unit-price-zone';
var volTime = '#heure-vol';
var rdv = "#rdv";
var rdvMsg = "#rdv-msg";
var rdvLieu = "#rdv-lieu"
var rdvLieuMsg = "#rdv-lieu-msg";
var end_date = new Date('2016-09-15');
var orderSubmitBtn = '#order_submit_btn';


var today = moment().format('YYYY-MM-DD');
var zipCodes = [];
var noZipCode = "<div class='alert alert-warning'>Le code postal que vous avez saisi (%1%) ne correspond pas à l’une des villes desservies par la prestation %2% </div>";
var selectedZipCode = null;
var prestation = "Navette partagée de Paris";
var min_date_param=3;

$.ajax({
    async: false,
    url: Routing.generate('get_calendar_param'),
    method: 'POST',
    dataType: 'json',
    data: {typetransfert: 'paris'},
    success: function (receivedData) {
        min_date_param=receivedData;
    }
});


function validateFirstPart() {

    var testCpAndLocationName = function (cp, loc) {
        var test = true;
        validator.removeErrorHilight($(cp).siblings('.selectize-control').find('.selectize-input'));
        validator.removeErrorHilight($(loc + "Name").siblings('.selectize-control').find('.selectize-input'));

        if ($(cp).val().trim() == '') {
            test = false;
            validator.errorHilight($(cp).siblings('.selectize-control').find('.selectize-input'));
        }

        if ($(loc + "Name").val().trim() == '') {
            test = false;
            validator.errorHilight($(loc + "Name").siblings('.selectize-control').find('.selectize-input'));
        }

        if ($(cp).val().trim() != '' && $(loc + "Name").val().trim() != '' && $(loc).val().trim() == '') {
            validator.errorHilight($(cp).siblings('.selectize-control').find('.selectize-input'));
            validator.errorHilight($(loc + "Name").siblings('.selectize-control').find('.selectize-input'));
            test = false;
        }
        return test;
    };

    var valid = testCpAndLocationName(cpSelector, locationSelector);

    validator.removeErrorHilight(qtySelector);
    if ($(qtySelector).val().trim() == '' || !isInt($(qtySelector).val())) {
        validator.errorHilight(qtySelector);
        valid = false;
    } else {
        if (parseInt($(qtySelector).val()) <= 0) {
            validator.errorHilight(qtySelector);
            valid = false;
        }
    }

    validator.removeErrorHilight(qtyChildSelector);
    if ($(qtyChildSelector).val().trim() != '' && !isInt($(qtyChildSelector).val())) {
        validator.errorHilight(qtyChildSelector);
        valid = false;
    }

    validator.removeErrorHilight(qtyBabySelector);
    if ($(qtyBabySelector).val().trim() != '' && !isInt($(qtyBabySelector).val())) {
        validator.errorHilight(qtyBabySelector);
        valid = false;
    }

    return valid;
}

function validateSecondPart(forTarif) {

    var testBlock = function (dateSelector, destSelector, numSelector, timeSelector, addressSelector) {

        validator.removeErrorHilight(dateSelector);
        validator.removeErrorHilight(destSelector);
        validator.removeErrorHilight(numSelector);
        validator.removeErrorHilight(timeSelector);

        var test = true;
        if ($(dateSelector).val() == '') {
            validator.errorHilight(dateSelector);
            test = false;
        }

        if ($(destSelector).val() == '') {
            validator.errorHilight(destSelector);
            test = false;
        }

        if ($(numSelector).val() == '') {
            validator.errorHilight(numSelector);
            test = false;
        }

        if ($(addressSelector).val() == '') {
            validator.errorHilight(addressSelector);
            test = false;
        }

        if ($(timeSelector).val() == '') {
            validator.errorHilight(timeSelector);
            test = false;
        } else {
            var t = $(timeSelector).val().trim();

            if (t.length != 5) {
                validator.errorHilight(timeSelector);
                test = false;
            }

            var tt = t.split(':');
            var h = tt[0];
            var m = tt[1];

            if (!isInt(h) || !isInt(m)) {
                validator.errorHilight(timeSelector);
                test = false;
            }

            if (parseInt(h) < 0 || parseInt(h) > 23) {
                validator.errorHilight(timeSelector);
                test = false;
            }

            if (parseInt(m) < 0 || parseInt(m) > 59) {
                validator.errorHilight(timeSelector);
                test = false;
            }

        }
        return test;
    }
    var valid;
    if(forTarif){
        valid = testBlock('#transfer_date', '#vol-destination', '#flight-select', '#transfer_time');
        valid = valid && (
                !$('').is(':checked') ||
                ($('#transfer_roundTrip').is(':checked') &&
                    testBlock('#transfer_date2', '#vol-destination-2', '#flight-select-2', '#transfer_time2')
                )
            );
    }else{
        valid = testBlock('#transfer_date', '#vol-destination', '#flight-select', '#transfer_time', '#transfer_address');
        valid = valid && (
                !$('').is(':checked') ||
                ($('#transfer_roundTrip').is(':checked') &&
                    testBlock('#transfer_date2', '#vol-destination-2', '#flight-select-2', '#transfer_time2', '#transfer_address2')
                )
            );
    }


    valid =  validateDateRetourDateAlle('#transfer_roundTrip',valid);
    return valid;
}

function disableFirstPartInput() {
    $(firstPartZone).find('input, select').attr('disabled', 'disabled');
    $(locationSelector + "Name").selectize()[0].selectize.disable();
}

function enableFirstPartInput() {
    $(firstPartZone).find('input, select').removeAttr('disabled');
    $(locationSelector + "Name").selectize()[0].selectize.enable();
}

function getTarif() {
    loader.showGlobalLoader();
    $.ajax({
        url: Routing.generate('calculate_paris_transfer'),
        method: 'POST',
        data: $(formSelector).serialize(),
        dataType: 'json',
        success: function (receivedData) {
            loader.hideGlobalLoader();
            if (receivedData.prix != null) {

                disableFirstPartInput();
                $(validateFirstPartBtn).hide();
                $(modifyFirstPartBtn).show();
                $(orderSubmitBtn).show();

                $('#tarif-amount').html(floatToString(receivedData.prix));
                if ($('#transfer_roundTrip').is(':checked')) {
                    $(formPart2).find('.return-zone').show();
                } else {
                    $(formPart2).find('.return-zone').hide();
                }

                $('#tarif-adult-amount').html(floatToString(receivedData.prixUnitaire));
                $('#tarif-child-amount').html(floatToString(receivedData.prixUnitaire));

                // on affiche les prix unitaire
                $(unitPrice).show();
                $("#tarif-row").show('slow', function () {
                    scrollToZone('#tarif-row');
                });
            } else {
                alert('pas de réponse');
                //Todo show msg error
            }
        }
    });
}

function initOnChangeCp(cp, locationInput) {
    $(locationInput).val('');
    if (cp == '') {
        $(locationInput + "Name").selectize()[0].selectize.destroy();
        $(locationInput + "Name").find('option').remove();
        $(locationInput + "Name").attr('disabled', 'disabled');
        $(locationInput + "Name").selectize();
    } else {
        loader.showGlobalLoader();
        $.ajax({
            url: Routing.generate('get_paris_locations'),
            data: {
                zipCode: cp
            },
            dataType: 'json',
            success: function (receivedData) {
                $(locationInput + "Name").removeAttr('disabled');
                $(locationInput + "Name").selectize()[0].selectize.destroy();
                initSelect(locationInput + "Name", [{value: '', label: 'Veuillez sélectionner une ville'}]);
                $.each(receivedData, function (key, value) {
                    addOptionToSelect(locationInput + "Name", {value: value.id, label: value.name});
                });
                loader.hideGlobalLoader();
                $(locationInput + "Name").selectize();
            }
        });
    }
}

function initLocationNameChange(locationInput) {
    $(locationInput + "Name").on('change', function () {
        $(locationInput).val($(locationInput + "Name").val());
    });
}

function resetPart2() {

    var reset = function (x, disable) {
        $(x).val('');
        $(x).find('option').remove();
        if (disable != undefined) {
            $(x).attr('disabled', 'disabled');
        }
    };

    reset('#transfer_date');

    reset('#vol-destination', true);

    reset('#flight-select', true);

    reset('#transfer_flight');

    reset('#transfer_time', true);

    reset('#heure-vol', true);
    reset('#rdv', true);
    reset('#transfer_address');


}

function addMinutesToTime(ch, minutes) {

    var h = parseInt(ch.split(':')[0]);
    var m = parseInt(ch.split(':')[1]);

    while (minutes >= 60) {
        h++;
        minutes -= 60;
    }

    m += minutes;

    if (m >= 60) {
        h++;
        m = m - 60;
    }

    h = h % 24;

    if (h.toString().length < 2) {
        h = '0' + h;
    }

    if (m.toString().length < 2) {
        m = '0' + m;
    }

    return h + ":" + m;
}

function initDetailsBox(datePicker, volDestSelect, flightSelect, flightHiddenSelector,
                        volTimeInput, flightTimeInput, rdvTimeShow, rdvMsgShow, rdvLieuShow, rdvLieuMsgShow, direction, numBlock) {

    //Destroy datepickers
    //Destroyevents
    $(datePicker).datepicker("destroy");

    $(datePicker).off('change');
    $(volDestSelect).off('change');
    $(flightSelect).off('change');
    $(volTimeInput).hide();


    /** INIT **/
    $(datePicker).datepicker({
        dateFormat: "dd-mm-yy",
        dayNames: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
        dayNamesMin: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
        monthNames: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Decembre"],
        minDate: min_date_param,
        maxDate: end_date,
       /* beforeShowDay: function (date) {
            var string = jQuery.datepicker.formatDate('yy-mm-dd', date);

            if (string < today) {
                return [false];
            } else {
                return [true];
            }

        }*/
    });

    $(datePicker).on('change', function () {
        var self = $(this);
        $(flightHiddenSelector).val('');
        if ($(this).val().trim() == '') {
            $(volDestSelect).attr('disabled', 'disabled');
            $(volDestSelect).html('');
            $(flightSelect).attr('disabled', 'disabled');
            $(flightSelect).html('');
            $(volTimeInput).attr('disabled', 'disabled');
            $(volTimeInput).val('');
            return;
        }

        var url = null;
        if (direction == 'to') {
            url = Routing.generate("get_destinations",{ 'dest' : 'Algerie' });
        } else {
            url = Routing.generate("get_provenances",{ 'provenance' : 'Algerie' });
        }

        loader.showGlobalLoader();
        $.ajax({
            url: url,
            data: {
                date: self.val().replace(/\//g, '-')
            },
            dataType: 'json',
            success: function (receivedData) {
                var volList = receivedData;
                if (numBlock == 1) {
                    vols = receivedData;
                } else {
                    vol2s = receivedData;
                }

                if (receivedData.length == 0){
                    showNoFlightsMsg(self.val());
                    return ;
                }

                var optionsVols = [];
                var places = [];

                var callback = null;
                if (direction == 'to') {
                    callback = function (value) {
                        if ($.inArray(value.to, places) < 0) {
                            places.push(value.to);
                            optionsVols.push({
                                value: value.to,
                                label: value.to
                            });
                        }
                    }
                } else {
                    callback = function (value) {
                        if ($.inArray(value.from, places) < 0) {
                            places.push(value.from);
                            optionsVols.push({
                                value: value.from,
                                label: value.from
                            });
                        }
                    }
                }

                if (volList.length > 0) {
                    optionsVols.push({
                        value: '',
                        label: ''
                    });
                    $.each(receivedData, function (key, value) {
                        callback(value);
                    });
                    initSelect(volDestSelect, optionsVols);
                    $(volDestSelect).removeAttr('disabled');

                } else {
                    $(volDestSelect).attr('disabled', 'disabled');
                    $(volDestSelect).html('');
                    $(flightSelect).attr('disabled', 'disabled');
                    $(flightSelect).html('');
                    $(volTimeInput).attr('disabled', 'disabled');
                    $(volTimeInput).val('');
                }
                loader.hideGlobalLoader();

            }
        })
    }); //on date change

    $(volDestSelect).on('change', function () {
        $(volTimeInput).attr('disabled', 'disabled');
        $(volTimeInput).val('');
        $(flightHiddenSelector).val('');
        if ($(volDestSelect).val() == '') {
            $(flightSelect).attr('disabled', 'disabled');
            $(flightSelect).html('');
        } else {
            var optionsVols = [];
            optionsVols.push({
                value: '',
                label: ''
            });

            var callback = null;
            if (direction == 'to') {
                callback = function (value) {
                    if (value.to == $(volDestSelect).val()) {
                        optionsVols.push({
                            value: value.id,
                            label: value.num
                        });
                    }
                }
            } else {
                callback = function (value) {
                    if (value.from == $(volDestSelect).val()) {
                        optionsVols.push({
                            value: value.id,
                            label: value.num
                        });
                    }
                }
            }

            var volList = [];
            if (numBlock == 1) {
                volList = vols;
            } else {
                volList = vol2s;
            }

            $.each(volList, function (key, value) {
                callback(value);
            });
            initSelect(flightSelect, optionsVols);
            $(flightSelect).removeAttr('disabled');
        }
    });

    $(flightSelect).on('change', function () {
        $(flightHiddenSelector).val($(flightSelect).val());
        if ($(flightSelect).val() == '') {
            $(volTimeInput).attr('disabled', 'disabled');
            $(volTimeInput).val('');
        } else {
            var volList = [];

            $(rdvTimeShow).show();
            $(rdvLieuShow).show();

            if (numBlock == 1) {
                volList = vols;
            } else {
                volList = vol2s;
            }

            var volDetails = null;
            $.each(volList, function (key, value) {
                if (value.id == $(flightSelect).val()) {
                    volDetails = value;
                }
            });

            var lieuMsg;
            if (direction == 'to') {
                $(volTimeInput).val('00:00');
                $(volTimeInput).hide();

                var msg = " L'heure de prise en charge vous sera communiqué <strong>ultérieurement </strong>";
                $(rdvMsgShow).html(msg);
                $(rdvMsgShow).show();
            } else {
                // a partir de Vatry
                lieuMsg = "Sortie des bagages à l’intérieur de l’aérogare de l’aéroport Paris Vatry";
                $(volTimeInput).val(addMinutesToTime(volDetails.pickUpTime, 35));
                $(volTimeInput).show();
            }


            $(rdvLieuMsgShow).show();
            $(flightTimeInput).val(volDetails.pickUpTime);
        }
    });

}

function initDirection() {


    if ($("#transfer_direction").val() == 'to_vatry') {

        $('#vatry_direction_block_2').show('slow');
        $('#vatry_direction_block_1').hide('slow');
        $($('#label_location_direction label')[1]).hide();
        $($('#label_location_direction label')[0]).show();

        $("label[for='vol-destination']").html("Destination du Vol");
        $("label[for='vol-destination-2']").html("Provenance du Vol");
//      initDetailsBox(datePicker, volDestSelect, flightSelect, flightHiddenSelector, volTimeInput, flightTimeInput, rdvTimeShow, rdvMsgShow,rdvLieuShow, rdvLieuMsgShow,direction, numBlock) {

        initDetailsBox('#transfer_date', "#vol-destination", "#flight-select", "#transfer_flight", "#transfer_time", "#heure-vol", "#rdv-time", "#rdv-msg", "#rdv-lieu", "#rdv-lieu-msg", 'to', 1);
        initDetailsBox('#transfer_date2', "#vol-destination-2", "#flight-select-2", "#transfer_flight2", "#transfer_time2", "#heure-vol2", "#rdv-time2", "#rdv-msg2", "#rdv-lieu2", "#rdv-lieu-msg2", 'from', 2);
    } else {

        $('#vatry_direction_block_2').hide('slow');
        $('#vatry_direction_block_1').show('slow');
        $($('#label_location_direction label')[1]).show();
        $($('#label_location_direction label')[0]).hide();

        $("label[for='vol-destination']").html("Provenance du Vol");
        $("label[for='vol-destination-2']").html("Destination du Vol");

        initDetailsBox('#transfer_date', "#vol-destination", "#flight-select", "#transfer_flight", "#transfer_time", "#heure-vol", "#rdv-time", "#rdv-msg", "#rdv-lieu", "#rdv-lieu-msg", 'from', 1);
        initDetailsBox('#transfer_date2', "#vol-destination-2", "#flight-select-2", "#transfer_flight2", "#transfer_time2", "#heure-vol2", "#rdv-time2", "#rdv-msg2", "#rdv-lieu2", "#rdv-lieu-msg2", 'to', 2);
    }


    $('#transfer_time').removeClass('free-type');
    if ($('#transfer_direction').val() == 'to_vatry') {
        $('#transfer_time').addClass('flag');
    }
    else {
        $('#transfer_time').removeClass('flag');
    }
}

function setLieuDepot(){
    var a1 = $("#transfer_address");
    var a2 = $("#transfer_address2");
    a1.appendTo("#a1-container");
    a2.appendTo("#a2-container");
    $("#rdv-lieu-msg").html("");
    $("#rdv-lieu-msg2").html("");
    $("#depot-lieu").html("");
    $("#depot-lieu-2").html("");
    if ($("#transfer_direction").val() == 'to_vatry') {

        //Lieu depot
        $("#transfer_address").appendTo("#rdv-lieu-msg");
        $("#depot-lieu").html("En face de l’aérogare des passagers");
        $("#transfer_address2").appendTo("#depot-lieu-2");
        $("#rdv-lieu-msg2").html("En face de l’aérogare des passagers");
    }else{
        //Lieu depot
        $("#transfer_address").appendTo("#depot-lieu");
        $("#rdv-lieu-msg").html("À l’intérieur de l’aérogare des passagers, face à la sortie des bagages ");
        $("#transfer_address2").appendTo("#rdv-lieu-msg2");
        $("#depot-lieu-2").html("À l’intérieur de l’aérogare des passagers, face à la sortie des bagages ");
    }
    var ville_cp = "<div>"+$("#transfer_locationName option:selected").text()+","+$('#transfer_cp').val()+"</div>";
    $("#transfer_address, #transfer_address2")
        .after(ville_cp);
}

function setLieuRdvLieuDepot(){

    $("#transfer_address").appendTo("#a-container");
    $("#rdv-lieu-msg").html("");
    $("#depot-lieu").html("");


    if ($("#transfer_direction").val() == 'to_vatry') {
        $("#transfer_address").appendTo("#rdv-lieu-msg");
        $("#depot-lieu").html("En face de l’aérogare des passagers");
    }else{
        $("#transfer_address").appendTo("#depot-lieu");
        $("#rdv-lieu-msg").html("A l’intérieur de l’aérogare des passagers, à coté du comptoir de Navette de Vatry");
    }
}

$(function () {

    loader.showGlobalLoader();

    $(locationSelector + "Name").selectize();


    $.ajax({
        url: Routing.generate('get_zip_codes', {type: 'paris'}),
        dataType: 'json',
        success: function (receivedData) {
            $.each(receivedData, function (key, value) {
                zipCodes.push(value.zipCode);
            });
            loader.hideGlobalLoader();
            $(cpSelector).autocomplete({
                source: function (request, response) {
                    var results = $.ui.autocomplete.filter(zipCodes, request.term);

                    if (!results.length) {
                        results = [{value: request.term, label: "Aucun code postale trouvé"}];
                    }

                    response(results);
                },
                select: function (item, ui) {
                    if (ui.item.label === "Aucun code postale trouvé") {
                        showDefaultModal('', noZipCode.replace("%1%", ui.item.value).replace("%2%", prestation), '');
                        $(this).val('');
                        event.preventDefault();
                    } else {
                        selectedZipCode = ui.item.value;
                        initOnChangeCp(ui.item.value, locationSelector)
                    }

                },
                focus: function (event, ui) {
                    if (ui.item.label === "Aucun code postale trouvé") {
                        event.preventDefault();
                    }
                }
            });
        }
    });

    $(cpSelector).on('blur', function () {
        if ($(this).val() != '' && selectedZipCode == null) {
            showDefaultModal('', noZipCode.replace("%1%", $(this).val()).replace("%2%", prestation), '');
            $(this).val('');
        } else {
            $(this).val(selectedZipCode);
        }
    });


    initLocationNameChange(locationSelector);

    $(validateFirstPartBtn).on('click', function () {
        if (validateFirstPart() && validateSecondPart(true)) {
            getTarif();
        }
    });

    $("#order-btn button").on('click', function () {
        if (validateFirstPart()) {
            setLieuDepot()
            $("#order-btn").fadeOut();
            $(modifyFirstPartBtn).fadeOut();
            $(formPart2).show('slow');
        }

    })

    $(modifyFirstPartBtn).on('click', function () {
        $(orderSubmitBtn).hide();
        $("#tarif-row").hide('slow');
        // resetPart2();
        scrollToZone('html');
        enableFirstPartInput();
        $(this).hide('slow');
        $(validateFirstPartBtn).show('slow');
    });


    initDirection();

    $('#transfer_direction').on('change', function () {
        initDirection();
        setLieuDepot()
    });

    $(formSelector).on('submit', function () {
        var ok = validateFirstPart() && validateSecondPart(false);

        if (ok) {
            loader.showGlobalLoader();
            $(formSelector).find('input, select').removeAttr('disabled');
            return true;
        } else {
            return false;
        }

    });

    $(formSelector)
        .find('#transfer_date')
        .on('keydown', function (event) {
            $(this).blur();
        });

    setLieuDepot();
});


