/**
 * Created by Splinter on 22/05/2016.
 */

function getVolList(){
    var dir = 'from';
    if ($(".vols-btn.arrive").hasClass('active')){
        dir = 'to';
    }
    $.ajax({
        url : Routing.generate('flight_list'),
        data : {
            date : $("#vol-date").val(),
            dir : dir
        },
        dataType : 'json',
        success : function(data){
            $("#zone-vols-table").html(data.html);
            if($('#vol-date').val()!=moment().format('DD/MM/YYYY'))
            {
                $('.flight-details').removeAttr('onclick');
                $('.flight-details').css('color','lightgray');
                $('.flight-details').css('cursor','default');
                $('.flight-details').attr('title','Indisponible');
                $('[data-toggle="tooltip"]').tooltip();
            }
        }
    })
}

$(function(){

    var today = moment();
    $("#vol-date").datepicker({
        dateFormat: "dd/mm/yy",
        dayNames: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
        dayNamesMin: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
        monthNames: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Decembre"],
        minDate:0,
        beforeShowDay: function (date) {
            var string = jQuery.datepicker.formatDate('yy-mm-dd', date);

            if (string < today) {
                return [false];
            } else {
                return [true];
            }

        }
    });

    $("#vol-date").on('change',function(){
        getVolList();
    });

    $(".vols-btn").on('click',function(){
        if ($(this).hasClass('active')){
            $(this).removeClass('active');
            $(this).siblings('.vols-btn').addClass('active');
        }else{
            $(this).addClass('active');
            $(this).siblings('.vols-btn').removeClass('active');
            getVolList();
        }

    })


});
