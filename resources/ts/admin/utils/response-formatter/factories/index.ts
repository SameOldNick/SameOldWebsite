import { AxiosResponse } from 'axios';
import { DateTime } from 'luxon';

import ResponseFormatter, { TFormatterCallback } from '..';


interface IValidationResponse {
    errors: {
        [key: string]: string[];
    };
}

/**
 * Checks if a response is a validation response.
 *
 * @param {*} obj
 * @returns {obj is IValidationResponse}
 */
const isValidationResponse = (response: AxiosResponse): response is AxiosResponse<IValidationResponse> => {
    if (response.status !== 422 || typeof response.data.errors !== 'object')
        return false;

    for (const key of Object.keys(response.data.errors)) {
        if (!Array.isArray(response.data.errors[key]))
            return false;
    }

    return true;
};

/**
 * Gets the default formatters.
 * @param {string|undefined} defaultMessage Default message for when no formatter is found. If undefined, default message in ResponseFormatter is used. (Default is undefined)
 * @returns ResponseFormatter instance
 */
const defaultFormatter = (defaultMessage?: string): ResponseFormatter => {
    const formatters: TFormatterCallback[] = [];

    formatters.push(({ status }) => status === 401 ? 'You are not authorized to perform this action.' : null);
    formatters.push(({ status }) => status === 500 ? 'An error occurred contacting the website. Please try again.' : null);
    formatters.push(({ status }) => status === 503 ? 'The website is down for maintenance. Please try again later.' : null);

    formatters.push(({ status, headers }) => {
        if (status !== 429)
            return null;

        let message = 'You have exceeded the maximum number of attempts.';

        // Get # of seconds until the throttle resets
        const retryAfter = parseInt(headers['retry-after'], undefined);

        if (!isNaN(retryAfter)) {
            const expiresAt = DateTime.now().plus({ seconds: retryAfter }).toLocaleString(DateTime.DATETIME_MED);

            message += ` Please wait until ${expiresAt} and try again.`;
        } else {
            message += ` Please wait and try again.`;
        }

        return message;
    });

    formatters.push((response) => isValidationResponse(response) ? Object.values(response.data.errors)[0][0] : null);

    formatters.push((response) => response.data.message && typeof response.data.message === 'string' ? response.data.message : null);

    const formatter = new ResponseFormatter([], formatters);

    if (defaultMessage)
        formatter.defaultMessage = defaultMessage;

    return formatter;
};

export { defaultFormatter };
