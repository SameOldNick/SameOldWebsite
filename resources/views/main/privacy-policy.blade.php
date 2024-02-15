<x-main.layout>
    <x-slot:title>{{ __('Privacy Policy') }}</x-slot:title>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <div class="card card-container shadow-lg bg-light mt-3">
                    <div class="card-header card-header-banner">
                        <h2 class="text-center">{{ __('Privacy Policy') }}</h2>
                    </div>
                    <div class="card-body">
                        <p>Last Updated: October 29, 2023</p>

                        <h3>{{ __('1. Introduction') }}</h3>

                        <p>Same Old Nick" ("I," "me," or "my") respects your privacy and is committed to protecting your personal information. This Privacy Policy explains how I collect, use, and safeguard the data you provide when using my website. By accessing or using my website, you consent to the practices described in this Privacy Policy.</p>

                        <h3>{{ __('2. Information I Collect') }}</h3>

                        <p><strong>Personal Information:</strong> I may collect personal information, such as your name, email address, and other contact details when you voluntarily provide it.</p>

                        <p><strong>Usage Data:</strong> I may collect information about your usage of the website, including your IP address, browser type, operating system, and other technical details.</p>

                        <p><strong>Cookies:</strong> I use cookies to enhance your experience on my website. You can adjust your browser settings to refuse cookies or be notified when they are being used.</p>

                        <h3>{{ __('3. How I Use Your Information') }}</h3>

                        <p>The information collected is used for the following purposes:</p>

                        <ul>
                            <li>To provide and maintain the website.</li>
                            <li>To respond to your inquiries and requests.</li>
                            <li>To improve my website and services.</li>
                            <li>To communicate with you and send relevant updates.</li>
                            <li>To monitor the usage of the website and analyze trends.</li>
                        </ul>

                        <h3>{{ __('4. Data Sharing') }}</h3>

                        <p>I will not sell, trade, or rent your personal information to third parties. However, I may share your information with service providers who assist me in operating my website, conducting my business, or servicing you. These third parties are obligated to protect your information.</p>

                        <h3>{{ __('5. Security') }}</h3>

                        <p>I take reasonable measures to protect your personal information. However, no method of data transmission over the internet is entirely secure, and I cannot guarantee the security of your data.</p>

                        <h3>{{ __('6. Links to Other Websites') }}</h3>

                        <p>My website may contain links to third-party websites that are not operated by me. I have no control over the content or privacy practices of these websites and encourage you to review their privacy policies.</p>

                        <h3>{{ __('7. Changes to this Privacy Policy') }}</h3>

                        <p>I may update this Privacy Policy from time to time to reflect changes in my practices or for other operational, legal, or regulatory reasons. I will post the updated Privacy Policy on this page with the date of the last update.</p>

                        <h3>{{ __('8. Contact Me') }}</h3>

                        <p>If you have any questions or concerns about this Privacy Policy or your personal information, please <a href="{{ route('contact') }}">contact me</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main.layout>


