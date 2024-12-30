/// <reference types="luxon" />

declare module 'luxon' {
    export interface TSSettings {
        throwOnInvalid: true;
    }
}

export default {}