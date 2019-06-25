/**
 * Created by Splinter on 16/04/2016.
 */

/*** Selectors ****/
var formSelector = "form[name='transfer']";
var validateFirstPartBtn = '#validate-transfer-information';
var modifyFirstPartBtn = '#modify-first-part-information';
var formPart2 = '#form-part-2';
var unitPrice = '#unit-price-zone';
var volTime = '#heure-vol';
var firstPartZone = "#first-part-zone";
var qtySelector = '#transfer_qty';
var qtyChildSelector = '#transfer_qtyChild';
var qtyBabySelector = '#transfer_qtyBaby';
var vols = [];
var rdv = "#rdv";
var rdvLieu = "#rdv-lieu"
var today = moment().format('YYYY-MM-DD');
var rdvLieuMsg = "#rdv-lieu-msg";
var rdvPlace;
var duration = null;

var orderSubmitBtn = '#submit-btn'

var zipCodeChalon = 'g1';
var zipCodeReims = 'g2';
var min_date_param=3;
$.ajax({
    async: false,
    url: Routing.generate('get_calendar_param'),
    method: 'POST',
    dataType: 'json',
    data: {typetransfert: 'gare'},
    success: function (receivedData) {
        min_date_param=receivedData;
    }
});

function validateFirstPart() {

    var valid = true;

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

    var testBlock = function (dateSelector, destSelector, numSelector) {

        validator.removeErrorHilight(dateSelector);
        validator.removeErrorHilight(destSelector);
        validator.removeErrorHilight(numSelector);

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

        return test;
    }

    var valid = testBlock('#transfer_date', '#vol-destination', '#flight-select');

    if ($("#transfer_roundTrip").is(':checked')){

        var valid2 = testBlock('#transfer_date2', '#vol-destination-2', '#flight-select-2');
        valid = valid && valid2 ;

    }
    valid =  validateDateRetourDateAlle("#transfer_roundTrip",valid);
    return valid;
}

function disableFirstPartInput() {
    $(firstPartZone).find('input, select').attr('disabled', 'disabled');
}

function enableFirstPartInput() {
    $(firstPartZone).find('input, select').removeAttr('disabled');
}

function getTarif() {
    loader.showGlobalLoader();
    $.ajax({
        url: Routing.generate('calculate_tarif_gare'),
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

                rdvPlace = receivedData.rdv;
                $('#tarif-amount').html(floatToString(receivedData.prix));
                $('#tarif-adult-amount').html(floatToString(receivedData.prixAdulte));
                $('#tarif-child-amount').html(floatToString(receivedData.prixEnfant));

                // on affiche les prix unitaire
                $(unitPrice).show();
                $("#tarif-row").show('slow', function () {
                    scrollToZone("#tarif-row");
                });
            } else {

                //Todo show msg error
            }
        }
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
    reset('#transfer_date2');

    reset('#vol-destination', true);
    reset('#vol-destination-2', true);

    reset('#flight-select', true);
    reset('#flight-select-2', true);

    reset(volTime, true);
    reset('#transfer_flight');
    reset('#transfer_flight2');

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


function initDetailsBox(datePicker, volDestSelect, flightSelect, flightHiddenSelector, volTimeInput,zipCodeSelected, direction,rdvMsgContainer,index) {

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
        minDate: min_date_param
      /*  beforeShowDay: function (date) {
            var string = jQuery.datepicker.formatDate('yy-mm-dd', date);

            if (string < today) {
                return [false];
            } else {
                return [true];
            }

        }*/
    });

    // changement de la date
    $(datePicker).on('change', function () {
        var self = $(this);
        $(flightHiddenSelector).val('');
        $(flightSelect).attr('disabled', 'disabled');
        $(flightSelect).html('');
        $(volTimeInput).val('');

        if ($(this).val().trim() == '') {
            $(volDestSelect).attr('disabled', 'disabled');
            $(volDestSelect).html('');
            return;
        }

        var url = null;
        if (direction == 'to') {
            url = Routing.generate("get_destinations",{ 'type' : 'gare' });
        } else {
            url = Routing.generate("get_provenances",{ 'type' : 'gare' })
        }

        loader.showGlobalLoader();
        $.ajax({
            url: url,
            data: {
                date: self.val().replace(/\//g, '-')
            },
            dataType: 'json',
            success: function (receivedData) {
                vols[index] = receivedData;

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

                if (vols[index].length > 0) {
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
                    $(volTimeInput).val('');
                }
                loader.hideGlobalLoader();

            }
        })
    }); //on date change

    $(volDestSelect).on('change', function () {
        $(volTimeInput).val('');
        $(flightHiddenSelector).val('');
        if ($(volDestSelect).val() == '') {
            $(flightSelect).attr('disabled', 'disabled');
            $(flightSelect).html('');
            $(volTimeInput).val('');
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

            $.each(vols[index], function (key, value) {
                callback(value);
            });
            initSelect(flightSelect, optionsVols);
            $(flightSelect).removeAttr('disabled');
        }
    });

    $(flightSelect).on('change', function () {
        $(flightHiddenSelector).val($(flightSelect).val());
        if ($(flightSelect).val() == '') {
            $(volTimeInput).val('');
        } else {
            $(rdv).removeAttr('hidden');
            $(rdvLieu).removeAttr('hidden');

            var volDetails = null;
            $.each(vols[index], function (key, value) {
                if (value.id == $(flightSelect).val()) {
                    volDetails = value;
                }
            });
            var exist = true;
            if (direction == 'from') {
                //A partir de l'aeropport Paris Vatry et vol To Paris Vatry
                if (volDetails.pickUpTime > '20:00' || volDetails.pickUpTime < '08:00') {
                    exist = true;
                }
            } else {
                //Navette A destination de l'aeroport Paris vatry et Vol From Vatry

                if (volDetails.pickUpTime < '10:30' || volDetails.pickUpTime > '22:30') {
                    exist = true;
                }
            }

            if (!exist){
                showErrorMsg("Il n'y a pas des navettes associées au vol choisi");
                return ;
            }

            $(volTimeInput).val(volDetails.pickUpTime);
            var rdvDate = '';
            //Test ON THE DIRECTION AND THE GARE
            if (zipCodeSelected == zipCodeChalon){
                //C'est chalon
                console.log('sfsdf')
                if (direction == 'from' ){
                    rdvDate = addMinutesToTime(volDetails.pickUpTime,35);
                    $('#flight-warning').show()
                    $('#flight-warning-2').hide()
                }else{
                    rdvDate = substractMinutesToTime(volDetails.pickUpTime,120 + duration);
                    $('#flight-warning').hide()
                    $('#flight-warning-2').show()
                }

            }else if (zipCodeSelected == zipCodeReims){
                //C'est reim
                if (direction == 'from' ){
                    rdvDate = addMinutesToTime(volDetails.pickUpTime,35);
                }else{
                    rdvDate = substractMinutesToTime(volDetails.pickUpTime,120 + duration);
                }
            }
            $('#transfer_time').val(rdvDate);
            var msg = "L'heure de prise en charge est à : <strong>"+rdvDate+"</strong>";

            showRdvMsg(rdvMsgContainer,msg);

        }
    });
    if (zipCodeSelected == zipCodeChalon){
        //C'est chalon
        if (direction == 'to' ){
            //rdvDate = addMinutesToTime(volDetails.pickUpTime,35);
            $('#flight-warning').show()
            $('#flight-warning-2').hide()
        }else{
            //rdvDate = substractMinutesToTime(volDetails.pickUpTime,140);
            $('#flight-warning').hide()
            $('#flight-warning-2').show()
        }

    }
}

function showRdvMsg(container,msg){
    $(container).find('.rdv-msg').html(msg);
    $(container).show();
}

function hideRdvMsg(container){
    $(container).hide();
    $(container).find('.rdv-msg').html('');
}

function setLieuRdvLieuDepot(){

    var gareDepotLocation1 ;
    var gareDepotLocation2 ;
    if ($("#transfer_location option[value='"+$('#transfer_location').val()+"']").attr('zipcode') == zipCodeChalon){
        gareDepotLocation1 = "En face de la Gare de Châlons-en-Champagne";
        gareDepotLocation2 = "A la sortie de la Gare de Châlons-en-Champagne";
    }else{
        gareDepotLocation1 = "En face de la Gare de Reims";
        gareDepotLocation2 = "A la sortie de la Gare de Reims";
    }

    $("#rdv-lieu-msg").html("");
    $("#rdv-lieu-msg2").html("");
    $("#depot-lieu").html("");
    $("#depot-lieu-2").html("");


    if ($("#transfer_direction").val() == 'to_vatry') {
        //Lieu depot
        $("#rdv-lieu-msg").html(gareDepotLocation2);
        $("#depot-lieu").html("A la sortie de l’aérogare des passagers");

        $("#rdv-lieu-msg2").html("En face de l’aérogare des passagers");
        $("#depot-lieu-2").html(gareDepotLocation1);
    }else{
        //Lieu depot
        $("#rdv-lieu-msg2").html(gareDepotLocation2);
        $("#depot-lieu-2").html("A la sortie de l’aérogare des passagers");

        $("#rdv-lieu-msg").html("En face de l’aérogare des passagers");
        $("#depot-lieu").html(gareDepotLocation1);
    }
}

function initDirection() {
    var zipCodeSelected = $("#transfer_location option[value='"+$('#transfer_location').val()+"']").attr('zipcode');
    var zipCodeSelected2 = $("#transfer_location option[value='"+$('#transfer_location').val()+"']").attr('zipcode');

    if ($("#transfer_direction").val() == 'to_vatry') {

        $('#vatry_direction_block_2').show('slow');
        $('#vatry_direction_block_1').hide('slow');
        $($('#label_location_direction label')[1]).hide();
        $($('#label_location_direction label')[0]).show();

        $("label[for='vol-destination']").html("Destination du Vol");
        $("label[for='vol-destination-2']").html("Provenance du Vol");

        initDetailsBox('#transfer_date', "#vol-destination", "#flight-select", "#transfer_flight","#heure-vol",zipCodeSelected, 'to',"#rdv",0);
        initDetailsBox('#transfer_date2', "#vol-destination-2", "#flight-select-2", "#transfer_flight2","#heure-vol2",zipCodeSelected2, 'from',"#rdv2",1);

    } else {

        $('#vatry_direction_block_2').hide('slow');
        $('#vatry_direction_block_1').show('slow');
        $($('#label_location_direction label')[1]).show();
        $($('#label_location_direction label')[0]).hide();

        $("label[for='vol-destination']").html("Provenance du Vol");
        $("label[for='vol-destination-2']").html("Destination du Vol");

        initDetailsBox('#transfer_date', "#vol-destination", "#flight-select", "#transfer_flight","#heure-vol",zipCodeSelected, 'from',"#rdv",0);
        initDetailsBox('#transfer_date2', "#vol-destination-2", "#flight-select-2", "#transfer_flight2","#heure-vol2",zipCodeSelected2, 'to',"#rdv2",1);
    }

}

function showErrorMsg(msg,classDiv){

    if (classDiv == undefined){
        classDiv = "alert-info";
    }

    var html = "<div class='alert "+classDiv+"'><span class='glyphicon glyphicon-warning-sign'></span> "+msg+"</div>";
    showDefaultModal('',html,'');
}

$(function () {

    $('#transfer_roundTrip').on('change',function(){
        if ( $(this).is(':checked') ){
            $('.return-zone').show('slow');
        }else{
            $('.return-zone').hide('slow');
        }
    });

    $(validateFirstPartBtn).on('click', function () {
        if (validateFirstPart() && validateSecondPart()) {
            getTarif();
        }
    });

    $("#order-btn button").on('click', function () {
        if (validateFirstPart()) {
            $.ajax({
                url: Routing.generate('calculate_tarif_gare'),
                method: 'POST',
                data: $(formSelector).serialize(),
                dataType: 'json',
                success: function (receivedData) {
                    //console.log($(cpSelector).val());
                    loader.hideGlobalLoader();
                    if (receivedData.prix != null) {

                        duration = receivedData.duration;

                    }
                }
            });
            $("#order-btn").fadeOut();
            $(modifyFirstPartBtn).fadeOut();
            $(validateFirstPartBtn).fadeIn();
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
        setLieuRdvLieuDepot();

    });

    $("#submit-btn").on('click', function () {
        var ok = validateFirstPart() && validateSecondPart();

        if (ok) {
            loader.showGlobalLoader();
            $(formSelector).find('input, select').removeAttr('disabled');
            $(formSelector).submit();
        } else {
            return false;
        }

    });

    $(formSelector)
        .find('#transfer_date , #transfer_date2')
        .on('keydown', function (event) {
            $(this).blur();
        });

    setLieuRdvLieuDepot();

});


