        $(function () {
            var url,
                user_referrals_modal = $('#user-referrals'),
                user_referrals_modal_body = $('.modal-body', user_referrals_modal);

            $(document).on('click', '#view-referrals', function (e) {
                e.preventDefault();
                url = $(this).attr('href');
                user_referrals_modal.modal();
                user_referrals_modal_body.html('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>');

                $.ajax({
                    url: url,
                    success: function (response) {
                        user_referrals_modal_body.html(response);
                    },
                    error: function () {
                        user_referrals_modal_body.html($('<div class="alert alert-danger" />').text('Some errors'));
                    }
                });
            });
        });
