import { AxiosResponse } from "axios";

export type TFormatterCallback<T = any> = (response: AxiosResponse<T>) => string | null;
export type TFormatter = TFormatterCallback | string;

/**
 * Parses a response and uses it to format a message
 *
 * @export
 * @class ResponseFormatter
 */
export default class ResponseFormatter {
    private formattersStack: TFormatterCallback[];
    private fallbackFormatters: TFormatterCallback[];

    /**
     * The message to be displayed after all formatter callbacks returned null.
     *
     * @type {string}
     * @memberof ResponseFormatter
     */
    public defaultMessage: string;

    /**
     * Creates an instance of ResponseFormatter.
     *
     * @param {TFormatter[]} [formatters=[]] Formatters (empty array by default)
     * @param {TFormatter[]} [fallbackFormatters=[]] Fallback formatters (empty array by default)
     * @memberof ResponseFormatter
     */
    constructor(formatters: TFormatter[] = [], fallbackFormatters: TFormatter[] = []) {
        this.formattersStack = this.transformFormattersToCallbacks(formatters);
        this.fallbackFormatters = this.transformFormattersToCallbacks(fallbackFormatters);
        this.defaultMessage = 'An unknown error occurred. Please try again.';
    }

    /**
     * Adds formatter callback.
     *
     * @param {TFormatter} formatter String or callback that returns either string or null (if it doesn't know how to handle it).
     * @returns {ResponseFormatter} ResponseFormatter instance
     * @memberof ResponseFormatter
     */
    public addFormatter(formatter: TFormatter): ResponseFormatter {
        this.formattersStack.push(this.transformFormatterToCallback(formatter));

        return this;
    }

    /**
     * Adds a formatter for the specified status code(s).
     *
     * @param {(number|number[])} statusCodes
     * @param {TFormatter} formatter Callback or string to set message to if status code is reached.
     * @returns {ResponseFormatter} ResponseFormatter instance
     * @memberof ResponseFormatter
     */
    public addFormatterForStatusCode(statusCodes: number|number[], formatter: TFormatter): ResponseFormatter {
        const statusCodesAry = typeof statusCodes === 'number' ? [statusCodes] : statusCodes;

        this.formattersStack.push((response) => statusCodesAry.includes(response.status) ? this.transformFormatterToCallback(formatter)(response) : null);

        return this;
    }

    /**
     * Adds formatter callbacks.
     *
     * @param {TFormatter[]} formatters
     * @returns {ResponseFormatter} ResponseFormatter instance
     * @memberof ResponseFormatter
     */
    public addFormatters(formatters: TFormatter[]): ResponseFormatter {
        for (const formatter of formatters) {
            this.addFormatter(formatter);
        }

        return this;
    }

    /**
     * Adds fallback formatter (called after all other formatters returned null)
     *
     * @param {TFormatterCallback} formatter Callback that returns either string or null (if it doesn't know how to handle it).
     * @returns {ResponseFormatter} ResponseFormatter instance
     * @memberof ResponseFormatter
     */
    public addFallbackFormatter(formatter: TFormatter): ResponseFormatter {
        this.fallbackFormatters.push(this.transformFormatterToCallback(formatter));

        return this;
    }

    /**
     * Adds failback formatter callbacks.
     *
     * @param {TFormatterCallback[]} formatters
     * @returns {ResponseFormatter} ResponseFormatter instance
     * @memberof ResponseFormatter
     */
    public addFallbackFormatters(formatters: TFormatter[]): ResponseFormatter {
        for (const formatter of formatters) {
            this.addFallbackFormatter(formatter);
        }

        return this;
    }

    /**
     * Determines message from response.
     *
     * @param {*} response
     * @returns {string}
     * @memberof ResponseFormatter
     */
    public parse(response?: AxiosResponse): string {
        if (response !== undefined) {
            // Treat callbacks as stack
            const stack = Array.from(this.formattersStack).reverse();
            const fallbacks = Array.from(this.fallbackFormatters);

            // Merge arrays by placing fallbacks after formatters
            for (const formatter of stack.concat(fallbacks)) {
                const message = formatter(response);
                if (message !== null)
                    return message;
            }
        }

        return this.defaultMessage;
    }

    /**
     * Transforms a TFormatter to a TFormatterCallback.
     *
     * @private
     * @param {TFormatter} formatter
     * @returns {TFormatterCallback}
     * @memberof ResponseFormatter
     */
    private transformFormatterToCallback(formatter: TFormatter): TFormatterCallback {
        return typeof formatter === 'string' ? () => formatter : formatter;
    }

    /**
     * Transform TFormatters to TFormatterCallbacks.
     *
     * @private
     * @param {TFormatter[]} formatters
     * @returns {TFormatterCallback[]}
     * @memberof ResponseFormatter
     */
    private transformFormattersToCallbacks(formatters: TFormatter[]): TFormatterCallback[] {
        return formatters.map(this.transformFormatterToCallback);
    }
}
