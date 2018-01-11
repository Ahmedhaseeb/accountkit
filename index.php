<!DOCTYPE html>
<html>
    <head>
        <script src="jquery-3.0.0.min.js"></script>
        <script src="https://sdk.accountkit.com/en_US/sdk.js"></script>
        <style>
            body{
                font-family: tahoma;
            }
            .message {
                background-color: #000;
                border: 1px solid #dcdcdc;
                color: #20c20e;
                font-family: courier new;
                margin-top: 84px;
                min-height: 250px;
                padding: 2px 45px;
                text-align: left;
                width: 50%;
                word-wrap: break-word;
            }
        </style>
    </head>
    <body>
        <div align="center">
            <input value="+1" id="country_code" />
            <input placeholder="phone number" id="phone_number"/>
            <button onclick="smsLogin();">Login via SMS</button>
            <div>OR</div>
            <input placeholder="email" id="email"/>
            <button onclick="emailLogin();">Login via Email</button>
            <div class="message">
                <p><center><h2>Message Board</h2></center></p>
            </div>
        </div>
        <script>
          //https://developers.facebook.com/docs/accountkit/webjs
          $(".message").append("<p>initialized Account Kit.</p>");
          // initialize Account Kit with CSRF protection
          AccountKit_OnInteractive = function(){
            AccountKit.init(
              {
                appId:"FB_APP_ID", // you facebook app id here
                state:"CSRF_TOKEN_HERE", // type any charcters here for protection
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
                $(".message").append("<p>Received auth token from facebook -  "+ code +".</p>");
                $(".message").append("<p>Triggering AJAX for server-side validation.</p>");
                
                $.post("verify.php", { code : code, csrf : csrf }, function(result){
                    $(".message").append( "<p>Server response : " + result + "</p>" );
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
            $(".message").append("<p>Triggering email validation.</p>");
            AccountKit.login(
              'EMAIL',
              {emailAddress: emailAddress},
              loginCallback
            );
          }
        </script>    
    </body>
</html>
