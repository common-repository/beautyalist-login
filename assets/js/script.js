(function ($) {
    $(document).ready(function () {
        // open popup
        if ($('#bl_login_popup').length) {
            $('#bl_login_popup').modal('show');
        }

        $('*[data-element="bl_login_create_page"]').click(function (event) {
            $('#bl_login_page_popup').modal('show');
        })

        $('*[data-element="bl_login_page"]').click(function (event) {
            $('#wpbody').addClass('overlay preloader');

            var page = $('#bl_login_page_popup input[name="page"]').get(0),
                nonce = $('#bl_login_page_popup input[name="nonce"]').get(0);

            var data = {
                page: $(page).val(),
                nonce: $(nonce).val(),
                action: 'bl_login_create_page'
            }

            $.ajax({
                cache: false,
                dataType: 'json',
                url: $(this).attr('data-href'),
                type: 'POST',
                data: data,
                success: function (data) {
                    if (!data.success) {
                        $('*[data-element="bl_login_page_error"]').html(data.message);
                        $('*[data-element="bl_login_page_error"]').show();
                    } else {
                        location.reload();
                    }
                    $('#wpbody').removeClass('overlay preloader')
                },
                error: function (err) {
                    console.log(err)
                    $('#wpbody').removeClass('overlay preloader')
                }
            })
        })

        $('*[data-element="bl_login_button_copy"]').click(async function (e) {
            var $temp = $("<input>"),
                el = $(this).attr('data-target'),
                text = $('*[data-element="' + el + '"]').val();

            $(this).text('Copied');

            $("body").append($temp);
            $temp.val(text).select();
            document.execCommand("copy");
            $temp.remove();

            await blDelay(10000);
            $(this).text('Copy');
        })

        $('*[data-element="bl_login_login"]').click(function (event) {
            $('#wpbody').addClass('overlay preloader');

            var email = $('#bl_login_popup input[name="email"]').get(0),
                pass = $('#bl_login_popup input[name="password"]').get(0),
                nonce = $('#bl_login_popup input[name="nonce"]').get(0);

            var data = {
                action: 'bl_login_login',
                email: $(email).val(),
                pass: $(pass).val(),
                nonce: $(nonce).val()
            }

            $.ajax({
                cache: false,
                dataType: 'json',
                url: $(this).attr('data-href'),
                type: 'POST',
                data: data,
                success: function (data) {
                    if (data.success) {
                        $('*[data-element="bl_login_login_error"]').html('');
                        location.search = '';
                        // location.reload();
                    } else {
                        if (data.hasOwnProperty('message')) {
                            $('*[data-element="bl_login_login_error"]').html(data.message);
                            $('*[data-element="bl_login_login_error"]').show();
                        }
                    }
                    $('#wpbody').removeClass('overlay preloader')
                },
                error: function (err) {
                    $('*[data-element="bl_login_login_error"]').html('Error: Please refresh page and try again');
                    $('*[data-element="bl_login_login_error"]').show();

                    $('#wpbody').removeClass('overlay preloader')
                }
            });
        });

        $('*[data-element="bl_login_save_role"]').click(function (event) {
            $('#wpbody').addClass('overlay preloader')
            var data = {
                action: 'bl_login_save_role',
                role: $('*[data-element="role').val(),
                nonce: $(this).attr('data-nonce'),
            }

            $.ajax({
                cache: false,
                dataType: 'json',
                url: $(this).attr('data-href'),
                type: 'POST',
                data: data,
                success: function (data) {
                    if (data.success) {
                        $('*[data-element="bl_login_save_role_message"] div').show().html(data.message).delay(5000).fadeOut('slow');
                    } else {
                        if (data.hasOwnProperty('message')) {
                            $('*[data-element="bl_login_save_role_message"] div').show().html(data.message).delay(5000).fadeOut('slow');
                        }
                    }
                    $('#wpbody').removeClass('overlay preloader')
                },
                error: function (err) {
                    console.log(err)
                    $('#wpbody').removeClass('overlay preloader')
                }
            });
        });

        $('*[data-element="bl_login_save_key"]').click(function (event) {
            $('#wpbody').addClass('overlay preloader')
            var data = {
                action: 'bl_login_save_key',
                key: $('*[data-element="key').val(),
                id: $('*[data-element="id').val(),
                page: $('*[data-element="page').val(),
                nonce: $(this).attr('data-nonce'),
            }

            $.ajax({
                cache: false,
                dataType: 'json',
                url: $(this).attr('data-href'),
                type: 'POST',
                data: data,
                success: function (data) {
                    if (data.success) {
                        $('*[data-element="bl_login_save_key_message"] div').html(data.message);
                        location.reload();
                    } else {
                        if (data.hasOwnProperty('message')) {
                            $('*[data-element="bl_login_save_key_message"] div').html(data.message);
                        }
                    }
                    $('#wpbody').removeClass('overlay preloader')
                },
                error: function (err) {
                    console.log(err)
                    $('#wpbody').removeClass('overlay preloader')
                }
            });
        });
    })
})(jQuery);

const blDelay = ms => new Promise(res => setTimeout(res, ms));