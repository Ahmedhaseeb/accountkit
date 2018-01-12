<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="assets/css/custom.css">
        <script src="jquery-3.0.0.min.js"></script>
        <script src="https://sdk.accountkit.com/en_US/sdk.js"></script>
        <style>
            body{
                font-family: Raleway-Regular;
            }
            button{
              background-color: #4267B2;
              border:none;
              border-radius: 2px;
              color:#fff;
              font-size: 16px;
              padding: 10px;
              box-shadow: 1px 1px 10px rgba(0,0,0,1);
            }
            input[type="text"]{
              padding: 10px;

            }
            .message {
                box-shadow: 1px 1px 10px rgba(0,0,0,1);
                background-color: #000;
                border: 1px solid #dcdcdc;
                color: #20c20e;
                font-family: Raleway-Regular;
                margin-top: 84px;
                min-height: 250px;
                padding: 2px 45px;
                text-align: left;
                width: 50%;
                word-wrap: break-word;
            }
            b{
              color:#fff;
            }
        </style>
    </head>
    <body>
        <div align="center">
            <input type="text" value="+92" id="country_code" />
            <input type="text" placeholder="phone number" id="phone_number"/>
            <button onclick="smsLogin();">Login via SMS</button>
            <h2>OR</h2>
            <input type="text" placeholder="email" id="email"/>
            <button onclick="emailLogin();">Login via Email</button>
            <div class="message">
                <p><h2 align="center" style="box-shadow: 1px 1px 10px rgba(255,255,255,1);">Message Board</h2></p>
            </div>
        </div>
        <script>
          //https://developers.facebook.com/docs/accountkit/webjs
          $(".message").append("<p><b>initialized Account Kit.</b></p>");
          // initialize Account Kit with CSRF protection
          AccountKit_OnInteractive = function(){
            AccountKit.init(
              {
                appId:"APP_ID", // you facebook app id here
                state:"CSRF", // type any charcters here for protection
                version:"v1.0", //v1.0
                debug:true,
                fbAppEventsEnabled:true
              }
            );
          };
          // login callback
          function loginCallback(response) {
            if (response.status === "PARTIALLY_AUTHENTICATED") {
              var code = response.code;
              var csrf = response.state;
              document.write(code);
            }
            else if (response.status === "NOT_AUTHENTICATED") {
              // handle authentication failure
            }
            else if (response.status === "BAD_PARAMS") {
              // handle bad parameters
            }
          }

          // login callback
          function loginCallback(response) {
            if (response.status === "PARTIALLY_AUTHENTICATED") {
              var code = response.code;
              var csrf = response.state;
                $(".message").append("<p><b>Received auth token from facebook</b> -  "+ code +"</p>");
                $(".message").append("<p><b>Triggering AJAX for server-side validation.</b></p>");
                
                $.post("verify.php", { code : code, csrf : csrf }, function(result){
                    $(".message").append( "<p><b>Server response : </b>" + result + "</p>" );
                });
                
            }
            else if (response.status === "NOT_AUTHENTICATED") {
              // handle authentication failure
                $(".message").append("<p>( Error ) NOT_AUTHENTICATED status received from facebook, something went wrong.</p>");
            }
            else if (response.status === "BAD_PARAMS") {
              // handle bad parameters
                $(".message").append("<p>( Error ) BAD_PARAMS status received from facebook, something went wrong.</p>");
            }
          }
            
            
          // phone form submission handler
          function smsLogin() {
            var countryCode = document.getElementById("country_code").value;
            var phoneNumber = document.getElementById("phone_number").value;
            $(".message").append("<p>Triggering phone validation.</p>");
            AccountKit.login(
              'PHONE', 
              {countryCode: countryCode, phoneNumber: phoneNumber},
              loginCallback
            );
          }


          // email form submission handler
          function emailLogin() {
            var emailAddress = document.getElementById("email").value;
            $(".message").append("<p><b>Triggering email validation.</b></p>");
            AccountKit.login(
              'EMAIL',
              {emailAddress: emailAddress},
              loginCallback
            );
          }
        </script>    
    </body>
</html>
