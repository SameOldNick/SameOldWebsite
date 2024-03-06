import { Logger } from '@admin/utils/logger';

declare global {
    // These values are automatically filled in using the Webpack DefinePlugin
    // They need to be var and not const so they can be set for jest.
    // Prepend global. or globalThis. to access these variables in Jest. Do not do this outside of jest.
    // Solution to make these variables work with Jest: https://stackoverflow.com/a/66229206/533242
    // var __ENV__: string;
    // var __DEBUG__: boolean;
    // var __NAME__: string;
    // var __URL__: string;
    // var __API_URL__: string;
    // var __RECAPTCHA_SITE_KEY__: string;
    // var __BUILD_DATE__: string;
    // var __WEBPACK_VERSION__: string;

    //var __webpack_nonce__: string;

    const logger: Logger;

    export interface Window {
        logger: Logger;
    }
}

// This empty export is needed so TypeScript detects this as a definitions file.
export { };
