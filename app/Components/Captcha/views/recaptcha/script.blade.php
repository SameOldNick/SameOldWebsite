<script src="{{ sprintf('%s?render=%s', $jsUrl, $siteKey) }}"></script>

@if ($jsCallback)
    <script>
        function {{ $jsCallerName ?? 'prepareRecaptcha' }}() {
            grecaptcha.ready(function() {
                grecaptcha.execute('{{ $siteKey }}', @json([
                    'action' => $jsCallbackAction ?? 'submit',
                ])).then({{ $jsCallback }});
            });
        }

        @if ($jsAutoCall)
            {{ $jsCallerName ?? 'prepareRecaptcha' }}();
        @endif
    </script>
@endif
