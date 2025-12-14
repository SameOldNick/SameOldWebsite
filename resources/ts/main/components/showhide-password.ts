$(() => {
    $('.showhide-password').each(function () {
        const $container = $(this);
        const $input = $('input', this);
        const $button = $('a.ibc-button, button.ibc-button', this);

        if ($input.length === 0 || $button.length === 0) {
            return;
        }

        $button.on('click', function (e) {
            e.preventDefault();

            const show = $input.attr('type') === 'password';

            $container.toggleClass('showing-password', show);

            $input.attr('type', show ? 'text' : 'password');
        });
    });
});
