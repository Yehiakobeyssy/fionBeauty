// js/login.js
// Requires jQuery (already included in login.php)
$(document).ready(function(){

    // UI: switch tabs
    $('.tab-btn').on('click', function(){
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');
        var tgt = $(this).data('target');
        $('.auth-form').addClass('hidden');
        $(tgt).removeClass('hidden');
        // hide messages
        hideMsg();
    });
    $('.switch-tab').on('click', function(e){
        e.preventDefault();
        var tgt = $(this).data('target');
        $('.auth-form').addClass('hidden');
        $(tgt).removeClass('hidden');
        $('.tab-btn').removeClass('active');
        $('.tab-btn[data-target="'+tgt+'"]').addClass('active');
        hideMsg();
    });

    // message box helpers
    function showMsg(text, type){
        var $box = $('#msg-box');
        $box.removeClass('hidden').removeClass('error success');
        $box.addClass(type === 'success' ? 'success' : 'error');
        $('#msg-text').text(text);
    }
    function hideMsg(){ $('#msg-box').addClass('hidden'); $('#msg-text').text(''); }

    $('#msg-close').on('click', function(){ hideMsg(); });

    // ---------- SIGNUP: check passwords client-side ----------
    $('#clientPassword, #conformPassword').on('input', function(){
        var p = $('#clientPassword').val();
        var c = $('#conformPassword').val();
        var $note = $('#pw-note');
        if(!p && !c){ $note.text(''); return; }
        if(p !== c){
            $note.text('Passwords do not match').css('color','#ffdddd');
        } else {
            $note.text('Passwords match').css('color','#ddffdd');
        }
    });

    // ---------- SIGNUP: check email existence on blur ----------
    var emailTimer = null;
    $('#clientEmail').on('blur input', function(){
        var email = $(this).val().trim();
        var $note = $('#email-check');

        clearTimeout(emailTimer);
        if(!email) { $note.text(''); return; }

        // debounce
        emailTimer = setTimeout(function(){
            $.ajax({
                url: 'ajax/check_email.php',
                method: 'POST',
                data: { email: email },
                dataType: 'json'
            }).done(function(res){
                if(res.exists){
                    $note.text('Email already exists').css('color', '#ffdddd');
                } else {
                    $note.text('Email available').css('color', '#ddffdd');
                }
            }).fail(function(){
                $note.text('Could not verify email').css('color', '#ffdddd');
            });
        }, 400);
    });

    // ---------- SIGNUP: submit via AJAX ----------
    $('#signup-form').on('submit', function(e){
        e.preventDefault();

        // client-side password match check
        var p = $('#clientPassword').val();
        var c = $('#conformPassword').val();
        if(p !== c){
            showMsg('Passwords do not match', 'error');
            return;
        }

        // check email quickly before sending
        var email = $('#clientEmail').val().trim();
        if(!email){
            showMsg('Please provide an email', 'error');
            return;
        }

        // build FormData for file upload
        var fd = new FormData(this);
        $.ajax({
            url: 'ajax/signup.php',
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function(){
                $('#signup-submit').prop('disabled', true).text('Creating...');
                hideMsg();
            }
        }).done(function(res){
            if(res.success){
                showMsg(res.message || 'Account created. Redirecting...', 'success');
                setTimeout(function(){ window.location.href = 'login.php'; }, 1200);
            } else {
                showMsg(res.message || 'Could not create account', 'error');
            }
        }).fail(function(){
            showMsg('Signup request failed', 'error');
        }).always(function(){
            $('#signup-submit').prop('disabled', false).text('Create Account');
        });
    });

    // ---------- LOGIN: submit via AJAX ----------
$('#login-form').on('submit', function(e){
    e.preventDefault();
    var fd = new FormData(this);

    $.ajax({
        url: 'ajax/login.php',
        method: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        dataType: 'json',
        beforeSend: function(){
            $('#login-submit').prop('disabled', true).text('Signing in...');
            hideMsg();
        }
    }).done(function(res){
        if(res.success){
            window.location.href = res.redirect || 'index.php';
        } else {
            showMsg(res.message || 'Username or Password incorrect!', 'error');
        }
    }).fail(function(jqXHR, textStatus){
        console.log("Fail response:", jqXHR.responseText);
        showMsg('Login request failed: ' + textStatus, 'error');
    }).always(function(){
        $('#login-submit').prop('disabled', false).text('Sign in');
    });
});



    

});
 