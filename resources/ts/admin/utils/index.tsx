/**
 * Checks path name matches expected
 * @param pathname Path name (with or without leading /)
 * @param expected Expected path name
 * @returns True if they match
 */
export const matchesPathName = (pathname: string, expected: string) => {
    const actual = !pathname.startsWith('/') ? `/${pathname}` : pathname;

    return actual.toLowerCase() === expected.toLowerCase();
}

/**
 * Checks if provide value is a Promise instance.
 * As per https://stackoverflow.com/a/28133130/533242, x instanceof Promise only works with native NodeJS.
 * @param value Value to check
 * @returns True if value is probably a Promise.
 */
export const isPromise = (value: any): value is Promise<any> => typeof value === 'object' && typeof value.then === 'function';

/**
 * Creates object with specified keys included
 * @param obj Object
 * @param included Array of keys to include
 * @returns Object with only keys included
 */
export const includeInObject = <TObject extends Record<string, any>, TObjectKeys extends Array<keyof TObject>>(obj: TObject, included: TObjectKeys): Pick<TObject, TObjectKeys[number]> => {
    Object.keys(obj)
        .filter((key) => !included.includes(key))
        .forEach((key) => delete obj[key]);

    return obj;
}

/**
 * Creates object with specified keys excluded
 * @param obj Object to remove keys from
 * @param excluded Keys to exclude
 * @returns Object with keys excluded
 */
export const excludeFromObject = <TObject extends Record<string, any>>(obj: TObject, excluded: string[]) => {
    Object.keys(obj)
        .filter((key) => excluded.includes(key))
        .forEach((key) => delete obj[key]);

    return obj;
}

/**
 * Converts bytes to a human readable string
 * Source: https://stackoverflow.com/a/28896535/533242
 * @param bytes Number of bytes
 * @returns string
 */
export const humanReadableFileSize = (bytes: number) => {
    const sizes = [' Bytes', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB'];

    for (let i = 1; i < sizes.length; i++) {
        if (bytes < Math.pow(1024, i))
            return (Math.round((bytes / Math.pow(1024, i - 1)) * 100) / 100) + sizes[i - 1];
    }

    return String(bytes);
}

/**
 * Get the size of the viewport
 * @returns Object with width and hieght
 */
export const viewportSize = () => {
    return {
        width: Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0),
        height: Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0)
    };
}

/**
 * Checks that two objects are equal (using JSON.stringify)
 *
 * @export
 * @param {object} a
 * @param {object} b
 * @returns True if objects are equal.
 */
export function areEqual<A, B>(a: A, b: B) {
    return JSON.stringify(a) === JSON.stringify(b);
}

/**
 * Creates base64 url from file contents
 *
 * @export
 * @param {File} file
 * @returns Promise with base64 url string when resolved
 */
export function createBase64UrlFromFile(file: File) {
    return new Promise<string>((resolve, reject) => {
        const reader = new FileReader();

        reader.onload = (ev: ProgressEvent<FileReader>) => {
            if (!ev.target || !ev.target.result) {
                reject('The event is missing a target.');
                return;
            }

            let src: string;

            if (typeof ev.target.result !== 'string') {
                const uint8array = new Uint8Array(ev.target.result);
                src = new TextDecoder('utf-8').decode(uint8array);
            } else {
                src = ev.target.result;
            }

            resolve(src);
        }

        reader.readAsDataURL(file);
    });
}

/**
 * Formats number for the appropriate locale.
 * This is the equivalent of the number_format function in PHP (https://www.php.net/manual/en/function.number-format.php)
 * @param num Number to format
 * @param decimals Number of decimals to display (default: 0)
 * @param decimalSeparator What to use for decimals separator (default: .)
 * @param thousandsSeparator  What to use to separate thousands (default: ,)
 * @returns Formatted number
 */
export const numberFormat = (num: number, decimals: number = 0, decimalSeparator: string = '.', thousandsSeparator: string = ',') => {
    return num.toFixed(decimals).replace('.', decimalSeparator).replace(/(?=(?:\d{3})+$)(?!^)/g, thousandsSeparator);
}

/**
 * Delays calling a function
 * @param func Function to delay calling
 * @param timeout Minimum amount of time to wait until calling function
 * @returns Function that clears timeout and creates new timeout.
 */
export const debounce = (func: (...args: any) => void, timeout: number = 300) => {
    let timer: Timeout;

    return (...args: any[]) => {
        clearTimeout(timer);
        timer = setTimeout(() => func.apply(this, args), timeout);
    }
}

/**
 * Applies maximum amount of time for async function to finish
 * @param callback Async function
 * @param maxTimeMs Max. time to wait
 * @returns Promise
 */
export function awaitAtMost<T>(callback: () => Promise<T>, maxTimeMs: number) {
    return new Promise((resolve, reject) => {
        const timer = setTimeout(() => reject('Max. execution time reached'), maxTimeMs);

        callback()
            .then((value) => resolve(value))
            .catch((reason) => reject(reason))
            .finally(() => clearTimeout(timer));
    });
}

/**
 * Transform Cron alias (@yearly, @monthly, etc.) to actual expression
 * @param expression
 * @returns Transformed expression
 * @exports
 */
export const transformCronExpression = (expression: string) => {
    const presets: Record<string, string> = {
        '@yearly': '0 0 1 1 *',
        '@annually': '0 0 1 1 *',
        '@monthly': '0 0 1 * *',
        '@weekly': '0 0 * * 0',
        '@daily': '0 0 * * *',
        '@midnight': '0 0 * * *',
        '@hourly': '0 * * * *',
    };

    return expression in presets ? presets[expression] : expression;
}

/**
 * Parses Cron expression
 * @param expression Cron expression
 * @param transformFromPresets If true, transforms alias to expression
 * @returns {object} Object with minute, hour, dayOfMonth, month, and dayOfWeek
 * @throws {Error} Thrown if expression cannot be parsed
 * @exports
 */
export const parseCronExpression = (expression: string, transformFromPresets = true) => {
    if (transformFromPresets) {
        expression = transformCronExpression(expression);
    }

    const split = expression.trim().split(/\s+/).filter((part) => part !== '');

    if (!Array.isArray(split)) {
        throw new Error(`"${expression}" is not a valid CRON expression`);
    }

    const notEnoughParts = split.length < 5;

    const questionMarkInInvalidPart =
        (split[0] === '?') ||
        (split[1] === '?') ||
        (split[3] === '?');

    const tooManyQuestionMarks = (split[2] === '?') && (split[4] === '?');

    if (notEnoughParts || questionMarkInInvalidPart || tooManyQuestionMarks) {
        throw new Error(`"${expression}" is not a valid CRON expression`);
    }

    return {
        minute: split[0],
        hour: split[1],
        dayOfMonth: split[2],
        month: split[3],
        dayOfWeek: split[4]
    };
}
