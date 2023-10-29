$(() => {
    $('.showhide-password').each(function() {
        const $input = $('input', this);
        const $button = $('a.ibc-button, button.ibc-button', this);

        if ($input.length === 0 || $button.length === 0) {
            return;
        }

        $button.on('click', function(e) {
            e.preventDefault();

            $input.attr('type', $input.attr('type') === 'password' ? 'text' : 'password');
        });
    });
});
