Event.observe(window, 'load', mailer_modifyconfig_init, false);

function mailer_modifyconfig_init()
{
     Event.observe('mailer_queuetype', 'change', mailer_queue_onchange, false);
     Event.observe('mailer_mailertype', 'change', mailer_transport_onchange, false);
     Event.observe('mailer_smtpauth', 'click', mailer_smtpauth_onchange, false);
     mailer_queue_onchange();
     mailer_transport_onchange();
     mailer_smtpauth_onchange();
}

function mailer_transport_onchange()
{
    var mailtransport = $('mailer_mailertype')

    if ( mailtransport.value == '4') {
        $('mailer_smtpsettings').show();
    } else {
        $('mailer_smtpsettings').hide();
    }
    if ( mailtransport.value == '2') {
        $('mailer_sendmailsettings').show();
    } else {
        $('mailer_sendmailsettings').hide();
    }
}

function mailer_queue_onchange()
{
    var queue = $('mailer_queuetype')

    if ( queue.value == '1') {
        $('mailer_queue_frequency').hide();
        $('mailer_queue_settings').hide();
    } else {
        $('mailer_queue_settings').show();
        $('mailer_queue_frequency').show();
    }
    if ( queue.value == '2') {
        $('mailer_queue_cronpwd').show();
    } else {
        $('mailer_queue_cronpwd').hide();
    }
}

function mailer_smtpauth_onchange()
{
    var mailer_smtpauth = $('mailer_smtpauth')

    if ( mailer_smtpauth.checked == true) {
        $('mailer_smtp_authentication').show();
    } else {
        $('mailer_smtp_authentication').hide();
    }
}