/**
 * Created by Splinter on 18/04/2016.
 */

var validator = {
    errorHilight: function (dom) {
        $(dom).addClass('wrong-field');
    },
    removeErrorHilight: function (dom) {
        $(dom).removeClass('wrong-field');
    }
};

function scrollToZone(target) {
    $('html, body').animate({
        scrollTop: $(target).offset().top + 'px'
    }, 'slow');
}

function initSelect(select, options) {
    $(select).html("");
    addOptionsToSelect(select, options);
}

function addOptionsToSelect(select, options) {
    $.each(options, function (key, value) {
        addOptionToSelect(select, value);
    })
}

function addOptionToSelect(select, option) {
    $(select).append("<option value='" + option.value + "'>" + option.label + "</option>");
}

function isInt(value) {
    return !isNaN(value) &&
        parseInt(Number(value)) == value &&
        !isNaN(parseInt(value, 10));
}

function floatToString(value,n){
    var p ;
    if (n != undefined){
        p = Math.pow(10,n);
    }else{
        p = 100;
    }

    value = Math.round(value*p)/p;
    value = value.toString();
    value = value.replace('.',',');
    return value;
}

function initDataTable(selector,options){
    var defaultOptions = {
        language : {
            url : "/app/js/French.json"
        }
    } ;
    var finalOptions = $.extend({},defaultOptions,options);

    return $(selector).dataTable(finalOptions);
}

function showDefaultModal(title,body,footer,width,heigth){

    if (width != undefined && width != null){
        $("#default-modal").css('width',width);
    }

    if (heigth != undefined && heigth != null){
        $("#default-modal").css('heigth',width);
    }

    $("#default-modal").find('.modal-title').html(title);
    $("#default-modal").find('.modal-body').html(body);
    $("#default-modal").find('.modal-footer').html(footer);

    $("#default-modal").modal('show');
}

function closeDefaultModal(){

    $("#default-modal").find('.modal-title').html('');
    $("#default-modal").find('.modal-body').html('');
    $("#default-modal").find('.modal-footer').html('');
    $("#default-modal").modal('hide');
}


function showNoFlightsMsg(date){
    showDefaultModal('',"<div class='alert alert-danger'>Aucun vol pour la date de <b>"+date+"</b></div>");
    loader.hideGlobalLoader();
}

function compareDates(date1String,date2String,format){

    if (format == undefined){
        format = 'DD-MM-YYYY';
    }

    var date1 = moment(date1String,format);
    var date2 = moment(date2String,format);

    var dateF1 = date1.format('YMMDD');
    var dateF2 = date2.format('YMMDD');

    if( dateF1 < dateF2){
        return -1;
    } else if (dateF1 == dateF2){
        return 0 ;
    }else{
        return 1;
    }
}

function validateDateRetourDateAlle(roundTripSelector,valid){
    $('.date-error-msg').remove();
    if ($(roundTripSelector).is(':checked')){
        if (compareDates($('.date1').val(),$('.date2').val()) >= 0 ){
            validator.errorHilight($('.date2'));
            $('.date2').after("<br class='date-error-msg'><div class='alert alert-danger date-error-msg'>" + "La date du retour doit être ultérieure à la date de l'aller.</div>")
            return false;
        }
    }
    return valid ;
}


function substractMinutesToTime(ch, minutes) {

    var h = parseInt(ch.split(':')[0]);
    var m = parseInt(ch.split(':')[1]);

    while (minutes >= 60) {
        h--;
        minutes -= 60;
    }

    m -= minutes;

    if (m < 0) {
        h--;
        m = m + 60;
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

/** *** Common Document Ready JS *** **/
$(function(){
    $('[data-toggle="tooltip"]').tooltip();
});