/**
 * Created by Splinter on 27/04/2016.
 */

function submitRegisterFormBindEvent(callbackPre,callBackSuccess,callBackFailure){

    $(document).on('click','#register-submit-btn',function(event){
        if (callbackPre != undefined && callbackPre != null){
            callbackPre();
        }
        $.ajax({
            url : Routing.generate('registration'),
            method : 'POST',
            dataType : 'json',
            data : $('#registration-form').serialize(),
            success: function(receivedData){
                if (receivedData.status == true){
                    if (callBackSuccess != undefined && callBackSuccess != null){
                        callBackSuccess(receivedData);
                    }
                }else{
                    if (callBackFailure != undefined && callBackFailure != null){
                        callBackFailure(receivedData);
                    }
                }
            }
        });

    });

}

function loginSubmitBtn(callbackPre,callBackSuccess,callBackFailure){
    $(document).on('click','#login-btn' ,function(event){
        if (callbackPre != undefined && callbackPre != null){
            callbackPre();
        }
        $.ajax({
            url : Routing.generate('login'),
            method : 'POST',
            dataType : 'json',
            data : $('#login-form').serialize(),
            success: function(receivedData){
                if (receivedData.success == true){
                    if (callBackSuccess != undefined && callBackSuccess != null){
                        callBackSuccess(receivedData);
                    }else{
                        window.location.reload();
                    }
                }else{
                    if (callBackFailure != undefined && callBackFailure != null){
                        callBackFailure(receivedData);
                    }
                }
            }
        });
    });
}