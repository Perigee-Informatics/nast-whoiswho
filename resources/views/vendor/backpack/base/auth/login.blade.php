
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{trans("तराई-मधेश समृद्धि कार्यक्रम")}} :: {{'LOGIN'}}</title>
    <meta name='robots' content='noindex,nofollow'/>
    
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <!-- Font Awesome Icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <!-- Theme style -->
    <link href="{{asset('css/AdminLTE.min.css')}}" rel="stylesheet" type="text/css"/>

    <style>
      
      .login-page {
            background:url("{{asset('img/banner.jpg')}}") no-repeat center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
            font-family: 'Times New Roman', Times, serif;
            /* font-family: cursive; */

        }
     
        .login-box-body{
            box-shadow: 0px 0px 50px rgba(0, 0, 0, 1);
            background: #66ebff;
            border: solid 5px lightgray;
            border-radius:20px;
            color: black;
            position:relative;
            padding:10px 20px 20px 20px;
            margin-left:auto;
            margin-right:auto;
            margin-top:12%;
            height:400px;
            width:420px;
        }
        .login-box-msg{
            margin-top:7%;
            padding-bottom:10px;
            text-align:center;
            color:red;  
        }
        .login-header{
            margin-top:-80px;
            margin-bottom:30px;
            background: #66ebff;
            box-shadow: 0px 0px 30px 10px rgba(0, .5, .5, .5);
            border-radius:15px;
            padding-bottom:20px;
        }
        .rights{
            font-size:12px;
            margin-left:0px;
            font-size:13px;
        }
        .invalid-feedback{
            color:red;
            font-weight:bold;
        }
        .login-logo{
            float:left;
            margin-top:5%;
            margin-left:2%;
        }
        .login-flag{
            float:right;
            margin-right:5%;
            margin-top:5%;
            position:relative;
        }
        .header{
            margin-top:5px;
            margin-left:17%;
            position:absolute;
            font-weight:bold;
            text-align:center;
        }
        .form-check{
            margin:0px 5px 0px 3px;
        }
        #toggle,.form-check label{
            cursor: pointer;
        }
        .btn-signin{
            color:black;
            font-size:15px;
            box-shadow: 10px 10px 30px rgba(100, 120, 120, 40);
        }
        .btn.btn-flat{
            border-radius: 10px;
        }
      
        .app_owner{
            font-size:12px;
            margin:10px 0px;
            text-align:center;
            font-weight:bold;
        }

        .wrap-input100 {
            position: relative;
            width: 100%;
            z-index: 100;
        }

        .input100 {
        font-size: 14px;
        line-height: 1;
        color: black;
        display: block;
        background: #fff;
        height: 35px;
        width:100%;
        border-radius: 10px;
        padding: 0 30px 0 53px;
        margin-bottom: 15px;
        border: none;
        }


        /*------------------------------------------------------------------
        [ Focus ]*/
        .focus-input100 {
        display: block;
        position: absolute;
        border-radius: 10px;
        bottom: 0;
        left: 0;
        z-index: -1;
        width: 100%;
        height: 100%;
        box-shadow: 0px 0px 0px 0px;
        color: rgba(0,91,234, 0.6);
        border:none;
        }

        .input100:focus + .focus-input100 {
        -webkit-animation: anim-shadow 0.5s ease-in-out forwards;
        animation: anim-shadow 0.5s ease-in-out forwards;
        border-radius: 10px;
        }

        @-webkit-keyframes anim-shadow {
        to {
            box-shadow: 0px 0px 80px 30px;
            opacity: 0;
            border: none;
        }
        }

        @keyframes anim-shadow {
        to {
            box-shadow: 0px 0px 80px 30px;
            opacity: 0;
            border: none;
        }
        }

        .symbol-input100 {
        font-size: 15px;
        color: black;

        display: -webkit-box;
        display: -webkit-flex;
        display: -moz-box;
        display: -ms-flexbox;
        display: flex;
        align-items: center;
        position: absolute;
        border-radius: 10px;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100%;
        padding-left: 30px;
        pointer-events: none;

        -webkit-transition: all 0.4s;
        -o-transition: all 0.4s;
        -moz-transition: all 0.4s;
        transition: all 0.4s;
        }

        .input100:focus + .focus-input100 + .symbol-input100 {
        color: #00c6fb;
        padding-left: 23px;
        border:none;
        text-decoration: none;
        }
        .app_owner1 {
        border: 1px solid green;
        background-color: yellow;
        }

    </style>
</head>

<body class="login-page">
<div class ="col-lg-12">
    <div class="box-container col-md-push-4">
        <div class="login-box-body">
            <div class = "login-header">
                <div class ="row">
                    <div class="col-md-2 login-logo">
                            <img src="{{ asset('img/nepal_govt_logo.png') }}" style="max-height:60px; height:60px;"/> 
                    </div>
                    <div class="col-md-8 header">
                        <h4 style ="font-weight:bold;">नेपाल सरकार</h4>
                        <h4 style ="font-weight:bold;">संघीय मामिला तथा सामान्य प्रशासन मन्त्रालय</h4>
                        <h4 style ="font-weight:bold; color:red">तराई-मधेश समृद्धि कार्यक्रम</h4>
                    </div>


                    <div class="col-md-2 login-flag">
                            <img src="{{ asset('img/nepal_flag.gif') }}" style="max-height:60px; height: 60px;"/> 
                    </div>
                </div>    
            </div>
              
            {{-- <p class='login-box-message'>{{trans("login_message")}}</p> --}}
            <form autocomplete='off' action="{{ route('backpack.auth.login') }}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>

                <div class="wrap-input100 m-b-8" data-validate="Email is required">
                    <input class="input100" type="text" name="email" placeholder="E-mail">
                    <span class="focus-input100"></span>
                    <span class="symbol-input100"><i class="fa fa-user"></i></span>
                </div>
                    <div class="wrap-input100  m-b-8" data-validate="Password is required">
                    <input class="input100" type="password" name="password" id="password" placeholder="Password">
                    <span class="focus-input100"></span>
                    <span class="symbol-input100"><i class="fa fa-lock"></i></span>
                </div>

                    <div class="form-group row">
                        <div class="col-md-6 offset-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="toggle" id="toggle">
                                <label class="form-check-label" for="toggle">{{('Show Password') }}</label>
                            </div>
                        </div>
                    </div>
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <center><strong>{{ $message }}</strong></center>
                    </span>
                    @enderror

                    <div class='col-xs-12'>
                        <button type="submit" class="btn btn-signin btn-primary btn-block btn-flat"><i class='fa fa-lock' style="font-weight:bold;"> {{trans("Sign in")}}</i></button>
                    </div>

                    <div class='app_owner col-xs-12'>
                        <span>सर्वाधिकार &copy; २०७७,<br/> नेपाल सरकार, <br/>संघीय मामिला तथा सामान्य प्रशासन मन्त्रालय</span>
                    </div>
                    <div class='app_owner1 col-xs-12'>
                        <center><strong><span style ="font-weight:bold; color:red; text-decoration:underline">पासवर्ड को लागि सम्पर्क:</span></strong></center>
                        <p><b>Ishwor Ghimire:</b>9849338499, <b>Bibek Pandit:</b>9810399525</p>
                    </div>
                   
            </form>
        </div><!-- /.login-box-body -->
    </div>
</div>    

<!-- jQuery 2.1.3 -->
<script src="{{asset('js/jquery-3.3.1.min.js')}}" type="text/javascript"></script>
<script src="{{asset('js/bootstrap.min.js')}}" type="text/javascript"></script>

<script>
$('#toggle').click(function(){
  var input = document.getElementById("password");
  if (input.type === "password") {
    input.type = "text";
  } else {
    input.type = "password";
  }
});
</script>

</body>
</html>
