/**
 * Created by Splinter on 16/04/2016.
 */

function submitRegisterFormBindEvent(){

    $('#register-submit-btn').on('click',function(event){
        $.ajax({
            url : Routing.generate('registration'),
            method : 'POST',
            dataType : 'json',
            data : $('#registration-form').serialize(),
            success: function(receivedData){
                if (receivedData.status == true){
                    alert('Registration OK')
                }
            }
        });

    });

}

function loginSubmotBtn(){
    $('#login-btn').on('click',function(event){

        $.ajax({
            url : Routing.generate('login'),
            method : 'POST',
            dataType : 'json',
            data : $('#login-form').serialize(),
            success: function(receivedData){
                if (receivedData.success == true){
                    window.location.reload();
                }
            }
        });

    });
}