<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{csrf_token()}}" />
    <title>Get BitCV & DOGE</title>
    <meta name=viewport content="width=device-width,minimum-scale=1,maximum-scale=1,user-scalable=no">
    <link rel="stylesheet" href="/static/css/style.css" />
    <style type="text/css">
    #divresult>div {
        padding: 80px 0px 20px 0px;
    }
    #divresult div div{
        padding: 10px 50px 0px 50px;
        display: inline-block;
    }
    #divresult p {
        line-height: 50px;
    }
    /*
    #doc {
        background-image: url(/images/bg.png);
        background-repeat: no-repeat;
        background-size: 100% auto;
    }
    */
    </style>
</head>

<body>
<div id="doc">

    <div class="header">
    </div>

    <div class="lang-wrap" data-lang="ZH">
        <div class="container">
            <h1 class="logo">
                <a href>
                    <img src="{{$proj['logo_url']}}">
                </a>
            </h1>
            <div class="intro">
                <div class="join"></div>
                <p>{{$leftbcv}}0 thousands BCV left, {{$leftdoge}}0 thousands DOGE left</p>
            </div>
            <fieldset id="info">
                <div id="verifyCode">
                    <input type="text" class="ipt-txt ipt-address" id="mobile" placeholder="Mobile"/>
                    <input type="text" class="ipt-txt ipt-address" style="width:49%" id="vcode" placeholder="Code"/>
                    <input type="button" class='ipt-btn' style="width:49%" id="btnvcode" value="Get Verify Code">
                    <input type="submit" value="SUBMIT" class='ipt-btn' id="code-btn"/>
                </div>

                <div style="display:none;" id="addAddress">
                    <input type="text" class="ipt-txt ipt-address" id="address" placeholder="Enter your Ethereum wallet address"/>
                    <input type="submit" value="SUBMIT" class='ipt-btn' id="address-btn"/>
                </div>
            </fieldset>

            <div class="intro" style="display:none" id="result">
                <div class="join"></div>
                <p id="tips" style="color:#FF6276">Successfully applied. Invite friends to participate for additional rewards</p>
                <div id="divresult">
                    <div>
                        <div>
                            <p style="margin-top:0px;font-size:50px;" id="num">0</p>
                            <p style="color:#4A4A4A">Total Invitations</p>
                        </div>
                        <div style="border-left:1px solid #ccc">    
                            <p style="margin-top:0px;font-size:50px;color:#FF6276" id="total">0</p>
                            <p style="color:#4A4A4A">Cumulative Rewards</p>
                        </div>
                    </div>
                </div>
                <input type="text" id="inviteurl" class="ipt-txt ipt-address" style="width:49%">
                <input type="button" id="btncopy" data-clipboard-target="#inviteurl" class='ipt-btn' style="width:49%" data-clipboard-target='address' value="Copy to invite friends">
                <p>Telegram: <a href="">https://t.me/bcvfuli</a></p>
            </div>
        </div>
        <div class="footer">
            <div class="" style="text-align: center;padding-top: 60px;font-size: 14px;padding-bottom: 40px;">
                <p style="margin-bottom:20px" id="tipphone"><img src="/static/image/alert.png">Each phone can only apply once</p>
            </div>

        </div>
    </div>
</div>

<script src="/js/libs/jquery.min.js"></script>
<!--script src="/static/js/jquery.zclip.js"></script-->
<script src="/js/libs/clipboard.min.js"></script>
<script>
    $.ajaxSetup(
        {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }
    );
    var wait=60;
    function time(o) {
        if (wait == 0) {
            o.css('background-color', '');
            o.attr("disabled", false);
            o.val("Get Verify Code");
            wait = 60;
        } else {
            o.css('background-color', '#C0C0C0');
            o.attr("disabled", true);
            o.val("Resend(" + wait + ")");
            wait--;
            setTimeout(function() {
                    time(o)
                },
                1000)
        }
    }

    // 提交表单
    $(function() {
        $('#btnvcode').click(function() {
            var mobile = $('#mobile').val();
            //var pat = /^(((13[0-9]{1})|(17[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
            //if (!pat.test(mobile)) {
            if (mobile.length < 11) {
                alert('please enter a valid phone number');
                return false;
            }
            time($(this));
            $.post('/invite/vcode/'+mobile, '', function(ret) {
                if (ret.retcode == 200) {
                    //alert('验证码已发送，请在5分钟内输入');
                } else {
                    alert(ret.msg);
                }
            })
        });

        //注册手机号码
        $('#code-btn').click(function () {
            var mobile = $('#mobile').val();
            var pat = /^(((13[0-9]{1})|(17[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
            if (!pat.test(mobile)) {
                alert('please enter a valid phone number');
                return false;
            }

            var vcode = $('#vcode').val();
            if (vcode.length != 6) {
                alert('Please enter the correct verify code');
                return false;
            }

            $.post(
                '/invite/verifyCode',
                {
                    'mobile': mobile,
                    'vcode': vcode,
                    'code': '{{$code}}'
                },

                function(ret) {
                    if (ret.retcode == 200) {
                        $('#verifyCode').hide();
                        $('#addAddress').show();
                    } else if (ret.retcode == 202) {
                        window.location.reload();

                        $('#inviteurl').val(ret.data['url']);
                        $('#total').html(ret.data['total_bcv_num']+'BCV<br>'+ ret.data['total_doge_num']+'DOGE');
                        $('#num').html(ret.data['num']);
                        $('#tips').html('Congratulations, you\'ve got'+ret.data['bcv_num']+' BCV,'+ret.data['doge_num']+' DOGE,invite friends to get more rewards');
                        $('#verifyCode').hide();
                        $('#result').show();
                    } else {
                        alert(ret.msg);
                    }
                }
            );
        });

        $('#address-btn').click(function() {
            var address = $('#address').val();
            var pattern = /[0-9a-zA-Z]{30,50}/;
            //验证长度，字母数字，长度30-50
            if (!pattern.test(address)) {
                alert('Please enter the correct format wallet address!！');
                return false;
            }

            $.post(
                '/invite/add',
                {
                    'address' : address,
                    'code': '{{$code}}'
                },
                function(ret) {
                    if (ret.retcode == 200 || ret.retcode == 201) {
                        if (ret.retcode == 201) {
                            $('#tips').html(' Your phone number or wallet address has been applied,invite friends to participate for more rewards');
                            $('#tipphone').html('<img src="/static/image/alert.png">Each phone can only apply once, you can withdraw from BitCV later');
                        }
                        $('#inviteurl').val(ret.data);
                        $('#addAddress').hide();
                        $('#result').show();
                    } else {
                        alert(ret.msg);
                    }
                }
            );
        });
/*
        $('#btncopy').zclip({
            path: "/static/js/ZeroClipboard.swf",
            copy: function(){
                return $('#inviteurl').val();
            }
        });
*/
        var clipboard = new Clipboard('#btncopy');
        clipboard.on('success', function(e) {
            alert('Copy successful. Invite friends to participate for additional rewards');
        });
    });

    @if (isset($user['id']))
        $('#inviteurl').val("{{$user['url']}}");
        $('#total').html("{{$user['bcv_num']}} BCV<br>{{$user['doge_num']}} DOGE");
        $('#num').html("{{$user['num']}}");

        $('#tips').html('Congratulations, you\'ve got {{$user['bcv_num']}} BCV,{{$user['doge_num']}} DOGE,invite friends to get more rewards');
        $('#tipphone').html('<img src="/static/image/alert.png">Each phone can only apply once, you can withdraw from BitCV later');
        $('#verifyCode').hide();
        $('#result').show();
    @endif
    
$(function(){
    if (navigator.userAgent.match(/MicroMessenger/i)) {
        var weixinShareLogo = 'https://bitcv.saas.lianbi.io/images/token_500*500.png';
        $('body').prepend('<div style=" overflow:hidden; width:0px; height:0; margin:0 auto; position:absolute; top:-800px;"><img src="'+ weixinShareLogo +'"></div>');
    }
});
</script>
</body>

</html>




