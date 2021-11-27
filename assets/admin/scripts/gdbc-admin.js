/*!
 Mailchimp Ajax Submit
 jQuery Plugin
 Author: Siddharth Doshi

 Use:
 ===
 $('#form_id').ajaxchimp(options);

 - Form should have one <input> element with attribute 'type=email'
 - Form should have one label element with attribute 'for=email_input_id' (used to display error/success message)
 - All options are optional.

 Options:
 =======
 options = {
 language: 'en',
 callback: callbackFunction,
 url: 'http://blahblah.us1.list-manage.com/subscribe/post?u=5afsdhfuhdsiufdba6f8802&id=4djhfdsh99f'
 }

 Notes:
 =====
 To get the mailchimp JSONP url (undocumented), change 'post?' to 'post-json?' and add '&c=?' to the end.
 For e.g. 'http://blahblah.us1.list-manage.com/subscribe/post-json?u=5afsdhfuhdsiufdba6f8802&id=4djhfdsh99f&c=?',
 */

(function ($) {
    'use strict';

    $.ajaxChimp = {
        responses: {
            'Thanks! We have sent you a confirmation email'        : 0,
            'Please enter a valid email address'                   : 1,
            'An email address must contain a single @'             : 2,
            'The domain portion of the email address is invalid'   : 3,
            'The username portion of the email address is invalid' : 4,
            'This email address looks fake or invalid'             : 5
        },
        translations: {
            'en': null
        },
        init: function (selector, options) {
            $(selector).ajaxChimp(options);
        }
    };

    $.fn.ajaxChimp = function (options) {
        $(this).each(function(i, elem) {
            var form = $(elem);
            var email = form.find('input[type=email]');
            var label = form.find('label[for=' + email.attr('id') + ']');

            var settings = $.extend({
                'url': form.attr('action'),
                'language': 'en'
            }, options);

            var url = settings.url.replace('/post?', '/post-json?').concat('&c=?');

            form.attr('novalidate', 'true');
            email.attr('name', 'EMAIL');

            form.submit(function () {
                var msg;
                function successCallback(resp) {
                    if (resp.result === 'success') {
                        msg = "Thanks! We've sent a confirmation email";
                        label.removeClass('error').addClass('valid');
                        email.removeClass('error').addClass('valid');
                    }
                    else
                    {
                        email.removeClass('valid').addClass('error');
                        label.removeClass('valid').addClass('error');
                        var index = -1;
                        try {
                            var parts = resp.msg.split(' - ', 2);
                            if (parts[1] === undefined) {
                                msg = resp.msg;
                            } else {
                                var i = parseInt(parts[0], 10);
                                if (i.toString() === parts[0]) {
                                    index = parts[0];
                                    msg = parts[1];
                                } else {
                                    index = -1;
                                    msg = resp.msg;
                                }
                            }
                        }
                        catch (e) {
                            index = -1;
                            msg = resp.msg;
                        }
                    }

                    // Translate and display message
                    if (
                        settings.language !== 'en'
                        && $.ajaxChimp.responses[msg] !== undefined
                        && $.ajaxChimp.translations
                        && $.ajaxChimp.translations[settings.language]
                        && $.ajaxChimp.translations[settings.language][$.ajaxChimp.responses[msg]]
                    ) {
                        msg = $.ajaxChimp.translations[settings.language][$.ajaxChimp.responses[msg]];
                    }
                    label.html(msg);

                    label.show(2000);
                    if (settings.callback) {
                        settings.callback(resp);
                    }
                }

                var data = {};
                var dataArray = form.serializeArray();
                $.each(dataArray, function (index, item) {
                    data[item.name] = item.value;
                });

                $.ajax({
                    url: url,
                    data: data,
                    success: successCallback,
                    dataType: 'jsonp',
                    error: function (resp, text) {
                        console.log('mailchimp ajax submit error: ' + text);
                    }
                });

                // Translate and display submit message
                var submitMsg = 'Submitting...';
                if(
                    settings.language !== 'en'
                    && $.ajaxChimp.translations
                    && $.ajaxChimp.translations[settings.language]
                    && $.ajaxChimp.translations[settings.language]['submit']
                ) {
                    submitMsg = $.ajaxChimp.translations[settings.language]['submit'];
                }
                label.html(submitMsg).show(2000);

                return false;
            });
        });
        return this;
    };
})(jQuery);

jQuery( document ).ready(function($) {
    var maxLogsDaysElm = $('#gdbcsettings-settings-MaxLogsDays');

    if(maxLogsDaysElm.length !== 0) {
        if(maxLogsDaysElm.val() == 0) {
            maxLogsDaysElm.parent().children('p').first().toggle(false);
            maxLogsDaysElm.parent().children('p').last().toggle(true);
        }

        maxLogsDaysElm.change(function () {
            if ($(this).val() != 0) {
                $(this).parent().children('p').first().toggle(true);
                $(this).parent().children('p').last().toggle(false);
            }
            else {
                $(this).parent().children('p').first().toggle(false);
                $(this).parent().children('p').last().toggle(true);
            }

        });
    }

    $('div.mch-admin-notice.is-dismissible').each(function(){
        var noticeElm = $(this);
        noticeElm.on('click', '.notice-dismiss', function(event){

            jQuery.ajax({
                type : "post",
                cache: false,
                dataType : "json",
                url : GdbcAdmin.ajaxUrl,
                data : {
                    action: 'gdbc-dismiss-' + noticeElm.prop('id'),
                    ajaxRequestNonce: GdbcAdmin.ajaxRequestNonce
                }
            });

        })

    });

    $('#gdbc-subscribe-frm').ajaxChimp({
        url: '//wpbruiser.us12.list-manage.com/subscribe/post?u=5a2f4e669c2e2427b7e6d8ad9&id=5da2802c23',
        callback: function(resp){
            if (resp.result !== 'success')
                return;
            jQuery.ajax({
                type : "post",
                cache: false,
                dataType : "json",
                url : GdbcAdmin.ajaxUrl,
                data : {
                    action: 'gdbc-user-subscribed-newsletter' ,
                    ajaxRequestNonce: GdbcAdmin.ajaxRequestNonce
                }
            });

        }
    });

});