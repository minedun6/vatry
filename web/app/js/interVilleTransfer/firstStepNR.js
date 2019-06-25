/**
 * Created by Splinter on 16/04/2016.
 */

/*** Selectors ****/
var formSelector = "form[name='inter_ville_transfer_first_step']";
var cpSelector = '#inter_ville_transfer_first_step_cp';
var locationSelector = '#inter_ville_transfer_first_step_location';
var validateFirstPartBtn = '#validate-transfer-information';
var modifyFirstPartBtn = '#modify-first-part-information';
var formPart2 = '#form-part-2';
var unitPrice = '#unit-price-zone';
var volTime = '#heure-vol';
var firstPartZone = "#first-part-zone";
var qtySelector = '#inter_ville_transfer_first_step_qty';
var qtyChildSelector = '#inter_ville_transfer_first_step_qtyChild';
var qtyBabySelector = '#inter_ville_transfer_first_step_qtyBaby';
var vols = [];
var rdv = "#rdv";
var rdvLieu = "#rdv-lieu"
var today = moment().format('YYYY-MM-DD');
var rdvMsg = "#rdv-msg";
var rdvLieuMsg = "#rdv-lieu-msg";
var rdvPlace;

var today = moment().format('YYYY-MM-DD');
var zipCodes = [];
var noZipCode = "<div class='alert alert-warning'>Le code postal que vous avez saisi (%1%) ne correspond pas à l’une des villes desservies par la prestation %2% </div>";
var selectedZipCode = null;
var prestation = "Navette partagée inter-ville";
var towns = [];
var duration = null;

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

function validateSecondPart() {

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

        /* if ($(addressSelector).val() == '' ){
         validator.errorHilight(addressSelector);
         test = false; alert('adresse');
         }*/

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

    var valid = testBlock('#inter_ville_transfer_first_step_date', '#vol-destination', '#flight-select', '#inter_ville_transfer_first_step_time', '#inter_ville_transfer_first_step_address');

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
        url: Routing.generate('calculate_tarif_inter_ville'),
        method: 'POST',
        data: $(formSelector).serialize(),
        dataType: 'json',
        success: function (receivedData) {
            loader.hideGlobalLoader();
            if (receivedData.prix != null) {

                duration = receivedData.duration;

                disableFirstPartInput();
                $(validateFirstPartBtn).hide();
                $(modifyFirstPartBtn).show();

                rdvPlace = receivedData.rdv;
                $('#tarif-amount').html(floatToString(receivedData.prix));
                $('#tarif-adult-amount').html(floatToString(receivedData.prixAdulte));
                $('#tarif-child-amount').html(floatToString(receivedData.prixEnfant));

                // on affiche les prix unitaire
                $(unitPrice).show();
                $("#tarif-row").show('slow', function () {
                    scrollToZone(modifyFirstPartBtn);
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
        $(locationInput + "Name").removeAttr('disabled');
        $(locationInput + "Name").selectize()[0].selectize.destroy();
        initSelect(locationInput + "Name", [{value: '', label: 'Veuillez sélectionner une ville'}]);
        $.each(towns, function (key, value) {
            if (value.zipCode == cp){
                addOptionToSelect(locationInput + "Name", {value: value.idLocation, label: value.town});
            }
        });
        $(locationInput + "Name").selectize();
        initLocationNameChange(locationInput);
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

    reset('#inter_ville_transfer_first_step_date');
    reset('#vol-destination', true);
    reset('#flight-select', true);
    reset('#heure-vol', true);
    reset('#inter_ville_transfer_first_step_flight');
    reset('#inter_ville_transfer_first_step_time', true);
    reset('#rdv', true);

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

function initDetailsBox(datePicker, volDestSelect, flightSelect, flightHiddenSelector, volTimeInput, direction, numBlock) {

    //Destroy datepickers
    //Destroyevents
    $(datePicker).datepicker("destroy");

    $(datePicker).off('change');
    $(volDestSelect).off('change');
    $(flightSelect).off('change');

    /** INIT **/
    $(datePicker).datepicker({
        dateFormat: "dd-mm-yy",
        dayNames: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
        dayNamesMin: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
        monthNames: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Decembre"],
// a commenter pour ne plus avoir de restrictions
       // minDate: 4,
        beforeShowDay: function (date) {
            var string = jQuery.datepicker.formatDate('yy-mm-dd', date);

            if (string < today) {
                return [false];
            } else {
                return [true];
            }
        }
    });

    // changement de la date
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
            url = Routing.generate("get_destinations");
        } else {
            url = Routing.generate("get_provenances")
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
            $(rdv).show();
            $(rdvLieu).show();
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
            var HRDV = '';
            if ($(volTimeInput).hasClass('flag')) {
                HRDV = substractMinutesToTime(volDetails.pickUpTime, 100 + duration);
            } else {
                HRDV = addMinutesToTime(volDetails.pickUpTime, 30);
            }
            $(volTimeInput).removeAttr('disabled');
            $(volTimeInput).val(HRDV);
            $(rdvMsg).html("");
            $('#time-block').show();
            $(volTime).val(volDetails.pickUpTime);

            var hDepot = '';
            if (direction == 'to') {
                hDepot = addMinutesToTime(HRDV,duration);
            }else{
                hDepot = addMinutesToTime(volDetails.pickUpTime,35+ duration);
            }
            $('#depot-heure').val(hDepot);
        }
    });

}

function setLieuRdvLieuDepot(){
    $("#rdv-lieu-msg").html("");
    $("#depot-lieu").html("");
    var vi = $("#inter_ville_transfer_first_step_locationName option:selected").text();
    var zc = $("#inter_ville_transfer_first_step_cp").val();
    var place = '';
    $.each(towns,function(key,value){
        if (value.zipCode == zc){
            place = value.rdv;
        }
    });
    var l = place+", "+ vi+", "+zc;

    if ($("#inter_ville_transfer_first_step_direction").val() == 'to_vatry') {
        $("#rdv-lieu-msg").html(l);
        $("#depot-lieu").html("En face de l’aérogare des passagers");
    }else{
        $("#rdv-lieu-msg").html("En face de l’aérogare des passagers");
        $("#depot-lieu").html(l);
    }
}

function initDirection() {

    $("#rdv-lieu-msg").html("");
    $("#depot-lieu").html("");
    if ($("#inter_ville_transfer_first_step_direction").val() == 'to_vatry') {

        $("label[for='vol-destination']").html("Destination du Vol");
        initDetailsBox('#inter_ville_transfer_first_step_date', "#vol-destination", "#flight-select", "#inter_ville_transfer_first_step_flight", "#inter_ville_transfer_first_step_time", 'to', 1);
    } else {

        $("label[for='vol-destination']").html("Provenance du Vol");
        initDetailsBox('#inter_ville_transfer_first_step_date', "#vol-destination", "#flight-select", "#inter_ville_transfer_first_step_flight", "#inter_ville_transfer_first_step_time", 'from', 1);
    }


    $('#inter_ville_transfer_first_step_time').removeClass('free-type');
    if ($('#inter_ville_transfer_first_step_direction').val() == 'to_vatry') {
        $('#inter_ville_transfer_first_step_time').addClass('flag');
        $('#vatry_direction_block_2').show('slow');
        $('#vatry_direction_block_1').hide('slow');
        $($('#label_location_direction label')[1]).hide();
        $($('#label_location_direction label')[0]).show();
    }
    else {
        $('#vatry_direction_block_2').hide('slow');
        $('#vatry_direction_block_1').show('slow');
        $($('#label_location_direction label')[1]).show();
        $($('#label_location_direction label')[0]).hide();
        $('#inter_ville_transfer_first_step_time').removeClass('flag');
        $('#time-block').show();
    }
}

$(function () {

    loader.showGlobalLoader();

    $(locationSelector + "Name").selectize();


    $.ajax({
            url: Routing.generate('get_zip_codes', {type: 'inter_ville'}),
            dataType: 'json',
            success: function (receivedData) {
                $.each(receivedData, function (key, value) {
                    zipCodes.push(value.zipCode);
                });
                loader.hideGlobalLoader();
                $(cpSelector).autocomplete({
                    source: function(request, response) {
                        var results = $.ui.autocomplete.filter(zipCodes, request.term);

                        if (!results.length) {
                            results = [ { value : request.term , label : "Aucun code postale trouvé"} ];
                        }

                        response(results);
                    },
                    select: function(item,ui){
                        if (ui.item.label === "Aucun code postale trouvé") {
                            showDefaultModal('',noZipCode.replace("%1%",ui.item.value).replace("%2%",prestation),'');
                            $(this).val('');
                            event.preventDefault();
                        }else{
                            selectedZipCode = ui.item.value ;
                            initOnChangeCp(ui.item.value,locationSelector)
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

    $(cpSelector).on('blur',function(){
        if ($(this).val() != '' && selectedZipCode== null){
            showDefaultModal('',noZipCode.replace("%1%",$(this).val()).replace("%2%",prestation),'');
            $(this).val('');
        }else{
            $(this).val(selectedZipCode);
        }
    });

    initLocationNameChange(locationSelector);

    $(validateFirstPartBtn).on('click', function () {
        if (validateFirstPart()) {
            getTarif();
        }
    });

    $(modifyFirstPartBtn).on('click', function () {
        $("#tarif-row").hide('slow');
        resetPart2();
        scrollToZone('html');
        enableFirstPartInput();
        $(this).hide('slow');
        $(validateFirstPartBtn).show('slow');
        $('#form-part-2').hide('slow');
    });

    initDirection();

    $('#inter_ville_transfer_first_step_direction').on('change', function () {
        initDirection();
    });

    $(formSelector).on('submit', function () {
        var ok = validateFirstPart() && validateSecondPart();

        if (ok) {
            loader.showGlobalLoader();
            $(formSelector).find('input, select').removeAttr('disabled');
            return true;
        } else {
            return false;
        }

    });

    $(formSelector)
        .find('#inter_ville_transfer_first_step_date')
        .on('keydown', function (event) {
            $(this).blur();
        });
    $('#uniform-inter_ville_transfer_first_step_roundTrip').hide();

    $("#order-btn button").on('click', function () {
        setLieuRdvLieuDepot();
        $("#order-btn").fadeOut();
        $(modifyFirstPartBtn).fadeOut();
        $(formPart2).show('slow');

    })

    $('#inter_ville_transfer_first_step_time').mTimePicker();
    $('#depot-heure').mTimePicker();

    $.ajax({
        url : Routing.generate('get_interville_locations'),
        dataType : 'json',
        success : function(data) {
            towns = data;
        }});
});


