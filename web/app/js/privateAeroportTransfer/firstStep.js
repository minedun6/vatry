/**
 * Created by Splinter on 16/04/2016.
 */

/*** Selectors ****/
var formSelector = "form[name='private_aeroport_transfer_first_step']";
var locationSelector = '#private_aeroport_transfer_first_step_location';
var roundTripCheckBoxSelector = '#private_aeroport_transfer_first_step_roundTrip';
var returnZone = '.return-zone';
var validateFirstPartBtn = '#validate-transfer-information';
var modifyFirstPartBtn = '#modify-first-part-information';
var formPart2 = '#form-part-2';
var firstPartZone = "#first-part-zone";
var qtySelector ='#private_aeroport_transfer_first_step_qty';
var qtyChildSelector ='#private_aeroport_transfer_first_step_qtyChild';
var qtyBabySelector ='#private_aeroport_transfer_first_step_qtyBaby';
var vols = [];
var vol2s = [];
var rdv="#rdv-time";
var rdv2="#rdv-time2";
var min_date_param=4;
var orderSubmitBtn = '#order_submit_btn';

var goPriceNet = '#goPriceNet';
var goPriceExtra = '#goExtra';
var returnPriceNet = '#returnPriceNet';
var returnPriceExtra = '#returnExtra';

$.ajax({
    async: false,
    url: Routing.generate('get_calendar_param'),
    method: 'POST',
    dataType: 'json',
    data: {typetransfert: 'private'},
    success: function (receivedData) {
        min_date_param=receivedData;
    }
});
var today = moment().format('YYYY-MM-DD');
function validateFirstPart() {

    var testLocationName = function (loc) {
        var test = true;
        validator.removeErrorHilight($("#private_aeroport_transfer_first_step_location").siblings('.selectize-control').find('.selectize-input'));

        if ($("#private_aeroport_transfer_first_step_location").val() == ''){
            test = false;
            validator.errorHilight($("#private_aeroport_transfer_first_step_location").siblings('.selectize-control').find('.selectize-input'));
        }
        return test;
    };

    var valid = testLocationName( locationSelector);

    validator.removeErrorHilight(qtySelector);
    if ($(qtySelector).val().trim() == '' || ! isInt($(qtySelector).val())  ){
        validator.errorHilight(qtySelector);
        valid = false;
    }else{
        if (parseInt($(qtySelector).val())<=0){
            validator.errorHilight(qtySelector);
            valid = false;
        }
    }

    validator.removeErrorHilight(qtyChildSelector);
    if ($(qtyChildSelector).val().trim() != '' && ! isInt($(qtyChildSelector).val()) ){
        validator.errorHilight(qtyChildSelector);
        valid = false;
    }

    validator.removeErrorHilight(qtyBabySelector);
    if ($(qtyBabySelector).val().trim() != '' && ! isInt($(qtyBabySelector).val()) ){
        validator.errorHilight(qtyBabySelector);
        valid = false;
    }

    return valid;
}

function validateSecondPart(){

    var testBlock = function(dateSelector,destSelector,numSelector,timeSelector){

        validator.removeErrorHilight(dateSelector);
        validator.removeErrorHilight(destSelector);
        validator.removeErrorHilight(numSelector);
        validator.removeErrorHilight(timeSelector);

        var test = true;
        if ($(dateSelector).val() == '' ){
            validator.errorHilight(dateSelector);
            test = false;
        }

        if ($(destSelector).val() == '' ){
            validator.errorHilight(destSelector);
            test = false;
        }

        if ($(numSelector).val() == '' ){
            validator.errorHilight(numSelector);
            test = false;
        }

        if ($(timeSelector).val() == '' ){
            validator.errorHilight(timeSelector);
            test = false;
        }else{
            var t = $(timeSelector).val().trim();

            if (t.length != 5 ){
                validator.errorHilight(timeSelector);
                test = false;
            }

            var tt = t.split(':');
            var h = tt[0];
            var m = tt[1];

            if (!isInt(h) || !isInt(m)){
                validator.errorHilight(timeSelector);
                test = false;
            }

            if (parseInt(h)<0 || parseInt(h)>23){
                validator.errorHilight(timeSelector);
                test = false;
            }

            if (parseInt(m)<0 || parseInt(m)>59){
                validator.errorHilight(timeSelector);
                test = false;
            }

        }
        return test;
    }

    var valid = testBlock('#private_aeroport_transfer_first_step_date', '#vol-destination','#flight-select','#private_aeroport_transfer_first_step_time');
    valid = valid && (
    !$(roundTripCheckBoxSelector).is(':checked') ||
    ($(roundTripCheckBoxSelector).is(':checked') &&
    testBlock('#private_aeroport_transfer_first_step_date2', '#vol-destination-2','#flight-select-2','#private_aeroport_transfer_first_step_time2')
    )
    );
    valid =  validateDateRetourDateAlle(roundTripCheckBoxSelector,valid);
    return valid;

}

function disableSecondPart() {
    $(formPart2).find('select, input').attr('disabled', 'disabled');
}
function enableSecondPart() {
    $(formPart2).find('select, input').removeAttr('disabled', 'disabled');
}
function disableFirstPartInput() {
    $(firstPartZone).find('input, select').attr('disabled', 'disabled');
    $("#private_aeroport_transfer_first_step_location").selectize()[0].selectize.disable();
}

function enableFirstPartInput() {
    $(firstPartZone).find('input, select').removeAttr('disabled');
    $("#private_aeroport_transfer_first_step_location").selectize()[0].selectize.enable();
}

function getTarif() {
    loader.showGlobalLoader();
    $.ajax({
        url: Routing.generate('calculate_tarif_aeroport'),
        method: 'POST',
        data: $(formSelector).serialize(),
        dataType: 'json',
        success: function (receivedData) {
            loader.hideGlobalLoader();
            if (receivedData != null) {

                disableFirstPartInput();
                disableSecondPart();
                $(validateFirstPartBtn).hide();
                $(modifyFirstPartBtn).show();
                $(orderSubmitBtn).show();

                $('#tarif-amount').html(floatToString(receivedData.price));
                if ($(roundTripCheckBoxSelector).is(':checked')) {
                    $(formPart2).find(returnZone).removeClass('hidden');
                } else {
                    $(formPart2).find(returnZone).addClass('hidden');
                }


                $(goPriceNet).text(receivedData.aller.netAller + ' €')
                if(receivedData.aller.extraAller != 0){
                    $(goPriceExtra).text(', majoration : '+receivedData.aller.extraAller+' €) ').show()
                }else{
                    $(goPriceExtra).hide()
                }

                $('#tarif-details-aller').show();

                $(returnPriceNet).text(receivedData.retour.netRetour+ ' €')
                if(receivedData.retour.extraRetour != 0) {
                    $(returnPriceExtra).text(', majoration : ' + receivedData.retour.extraRetour + ' €').show()
                }else{
                    $(returnPriceExtra).hide()
                }
                if(receivedData.retour.netRetour != '0'){
                    $('#tarif-details-return').show();
                }else{
                    $('#tarif-details-return').hide();
                }

                if(receivedData.extra != 0){
                    $('#nighty-charges-warning').show();
                }else{
                    $('#nighty-charges-warning').hide();
                }

                // on affiche les prix unitaire
                $("#tarif-row").show('slow', function () {
                    scrollToZone('#tarif-row');
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

    reset('#private_aeroport_transfer_first_step_date');
    reset('#private_aeroport_transfer_first_step_date2');

    reset('#vol-destination', true);
    reset('#vol-destination-2', true);

    reset('#flight-select', true);
    reset('#flight-select-2', true);

    reset('#private_aeroport_transfer_first_step_flight');
    reset('#private_aeroport_transfer_first_step_flight2');

    reset('#private_aeroport_transfer_first_step_time', true);
    reset('#private_aeroport_transfer_first_step_time2', true);


    reset('#rdv-time',true);
    reset('#rdv-time2',true);

    reset('#heure-vol',true);
    reset('#heure-vol2',true);

    reset('#rdv',true);
    reset('#rdv2',true);

}

function addMinutesToTime(ch,minutes){

    var h = parseInt(ch.split(':')[0]);
    var m = parseInt(ch.split(':')[1]);

   while(minutes>=60){
       h++;
       minutes -= 60 ;
   }

   m += minutes;

    if (m>=60){
        h++;
        m = m - 60 ;
    }

    h = h % 24 ;

    if (h.toString().length<2){
        h = '0'+h;
    }

    if (m.toString().length<2){
        m = '0'+m;
    }

    return h+":"+m;
}

function initDetailsBox(datePicker, volDestSelect, flightSelect, flightHiddenSelector, volTimeInput, flightTimeInput, rdvTimeShow, rdvMsgShow,rdvLieuShow, rdvLieuMsgShow, volDistBloc, direction, numBlock) {

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
        minDate: min_date_param,
        /*beforeShowDay: function(date){
            var string = jQuery.datepicker.formatDate('yy-mm-dd', date);

            if (string < today){
                return [false];
            }else{
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

    // on va modifier ici le traitement
    $(flightSelect).on('change', function () {
        $(flightHiddenSelector).val($(flightSelect).val());
        if ($(flightSelect).val() == '') {
            $(volTimeInput).attr('disabled', 'disabled');
            $(volTimeInput).val('');
        } else {
            var volList = [];

            $(rdvLieuShow).show();
            $(rdv).show();
            $(volDistBloc).show();

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
            if ($(volTimeInput).hasClass('free-type')) {
                $(volTimeInput).removeAttr('disabled');
                $(volTimeInput).focus();

                lieuMsg = " Aerogare ";

                var msg = "Prévoyer d'être à l'aéroport <strong>2 heures</strong> avant l'arrivé du vol prévu à <strong>"+volDetails.pickUpTime+"</strong>";
                $(rdvMsgShow).html(msg);
                $(rdvLieuMsgShow).html(lieuMsg);
            } else
            {
                lieuMsg = "Sortie des bagages à l’intérieur de l’aérogare de l’aéroport Paris Vatry";
                $(rdvLieuMsgShow).html(lieuMsg);
                $(flightTimeInput).removeAttr('disabled');
                // $(volTimeInput).mTimePicker('setTime',addMinutesToTime(volDetails.pickUpTime,35));
                $(volTimeInput).val(addMinutesToTime(volDetails.pickUpTime,35));
                $(rdvLieuMsgShow).html(lieuMsg);
                $(rdvMsgShow).html("");
            }


            $(rdvLieuMsgShow).show();
            $(flightTimeInput).val(volDetails.pickUpTime);
        }
    });

}

function initDirection() {

    if ($("#private_aeroport_transfer_first_step_direction").val() == 'to_vatry') {

        $('#vatry_direction_block_2').show('slow');
        $('#vatry_direction_block_1').hide('slow');
        $($('#label_location_direction label')[1]).hide();
        $($('#label_location_direction label')[0]).show();

        $("label[for='vol-destination']").html("Destination du Vol");
        $("label[for='vol-destination-2']").html("Provenance du Vol");

        initDetailsBox('#private_aeroport_transfer_first_step_date', "#vol-destination", "#flight-select", "#private_aeroport_transfer_first_step_flight", "#private_aeroport_transfer_first_step_time","#heure-vol","#rdv-time","#rdv-msg","#rdv-lieu","#rdv-lieu-msg","#vol-distant", 'to', 1);
        initDetailsBox('#private_aeroport_transfer_first_step_date2', "#vol-destination-2", "#flight-select-2", "#private_aeroport_transfer_first_step_flight2", "#private_aeroport_transfer_first_step_time2","#heure-vol2","#rdv-time2","#rdv-msg2","#rdv-lieu2","#rdv-lieu-msg2","#vol-distant2",'from', 2);
    } else {

        $('#vatry_direction_block_2').hide('slow');
        $('#vatry_direction_block_1').show('slow');
        $($('#label_location_direction label')[1]).show();
        $($('#label_location_direction label')[0]).hide();

        $("label[for='vol-destination']").html("Provenance du Vol");
        $("label[for='vol-destination-2']").html("Destination du Vol");

        initDetailsBox('#private_aeroport_transfer_first_step_date', "#vol-destination", "#flight-select", "#private_aeroport_transfer_first_step_flight", "#private_aeroport_transfer_first_step_time","#heure-vol","#rdv-time","#rdv-msg","#rdv-lieu","#rdv-lieu-msg","#vol-distant", 'from', 1);
        initDetailsBox('#private_aeroport_transfer_first_step_date2', "#vol-destination-2", "#flight-select-2", "#private_aeroport_transfer_first_step_flight2", "#private_aeroport_transfer_first_step_time2","#heure-vol2","#rdv-time2","#rdv-msg2","#rdv-lieu2","#rdv-lieu-msg2","#vol-distant2", 'to', 2);
    }


    //Direction vers vatry, alors il saisit manuellement le temps
    $('#private_aeroport_transfer_first_step_time').removeClass('free-type');
    $('#private_aeroport_transfer_first_step_time2').removeClass('free-type');
    if ($('#private_aeroport_transfer_first_step_direction').val() == 'to_vatry') {
        $('#private_aeroport_transfer_first_step_time').addClass('free-type');
    } else {
        $('#private_aeroport_transfer_first_step_time2').addClass('free-type');
    }
}


var x1,x2,x3,x4;

$(function () {


    $('#private_aeroport_transfer_first_step_location').selectize();

    $(roundTripCheckBoxSelector).on('change', function () {
        if ($(this).is(':checked')) {
            $(returnZone).show('slow');
        } else {
            $(returnZone).hide('slow');
        }
    });

    $(validateFirstPartBtn).on('click', function () {
        if (validateFirstPart() && validateSecondPart()) {
            getTarif();
        }
    });


    $(modifyFirstPartBtn).on('click', function () {
        $(orderSubmitBtn).hide();
        $("#tarif-row").hide('slow');
        // resetPart2();
        scrollToZone('html');
        enableFirstPartInput();
        enableSecondPart();
        $(this).hide('slow');
        $(validateFirstPartBtn).show('slow');
    });


    initDirection();

    $('#private_aeroport_transfer_first_step_direction').on('change', function () {
        initDirection();
    });

    $(formSelector).on('submit',function(){

        var ok =  validateFirstPart() && validateSecondPart() ;

        if (ok){
            loader.showGlobalLoader();
            $(formSelector).find('input, select').removeAttr('disabled');
            return true;
        }else{
            return false;
        }

    });

    $(formSelector)
        .find('#private_aeroport_transfer_first_step_date , #private_aeroport_transfer_first_step_date2')
        .on('keydown',function(event){
            $(this).blur();
        });

    $("#order-btn button").on('click', function () {
        if (validateFirstPart()) {
            $("#order-btn").fadeOut();
            $(modifyFirstPartBtn).fadeOut();
            $(validateFirstPartBtn).fadeIn();
            $(formPart2).show('slow');
        }

    })

    x1 = $("#private_aeroport_transfer_first_step_time").attr('readonly', true).clockpicker({autoclose: true});
    x2 = $("#private_aeroport_transfer_first_step_time2").attr('readonly', true).clockpicker({autoclose: true});
    x3 = $("#private_aeroport_transfer_first_step_externalFlightTime").attr('readonly', true).clockpicker({autoclose: true});
    x4 = $("#private_aeroport_transfer_first_step_externalFlightTime2").attr('readonly', true).clockpicker({autoclose: true});


});


