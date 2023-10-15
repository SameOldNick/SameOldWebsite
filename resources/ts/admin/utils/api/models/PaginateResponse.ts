import { excludeFromObject } from "@admin/utils";

export default class PaginateResponse<T = unknown> {
    /**
     * Creates an instance of PaginateResponse.
     * @param {(IPaginateResponse<T> | IPaginateResponseCollection<T>)} _response
     * @memberof PaginateResponse
     */
    constructor(
        private readonly _response: IPaginateResponse<T> | IPaginateResponseCollection<T>
    ) {
    }

    /**
     * Gets the response.
     *
     * @readonly
     */
    public get response() {
        return this._response;
    }

    /**
     * Checks if more than one page exist.
     *
     * @readonly
     */
    public get hasPages() {
        const lastPage = this.isResource(this._response) ? this._response.last_page : this._response.meta.last_page;

        return lastPage > 1;
    }

    /**
     * Checks if on first page.
     *
     * @readonly
     */
    public get onFirstPage() {
        const currentPage = this.isResource(this._response) ? this._response.current_page : this._response.meta.current_page;

        return currentPage === 1;
    }

    /**
     * Gets URL to previous page.
     *
     * @readonly
     */
    public get previousPageUrl() {
        return this.isResource(this._response) ? this._response.prev_page_url : this._response.links.prev;
    }

    /**
     * Gets URL to next page.
     *
     * @readonly
     */
    public get nextPageUrl() {
        return this.isResource(this._response) ? this._response.next_page_url : this._response.links.next;
    }

    /**
     * Checks if there's more pages to navigate.
     *
     * @readonly
     */
    public get hasMorePages() {
        return this.nextPageUrl !== null;
    }

    /**
     * Gets the ID of the first item in this page.
     *
     * @readonly
     */
    public get firstItem() {
        const firstItem = this.isResource(this._response) ? this._response.from : this._response.meta.from;

        // If from is null, the collection is empty
        return firstItem ?? 0;
    }

    /**
     * Gets the ID of the last item in this page.
     *
     * @readonly
     */
    public get lastItem() {
        const to = this.isResource(this._response) ? this._response.to : this._response.meta.to;

        // If to is null, the collection is empty
        return to ?? 0;
    }

    /**
     * Gets total number of items.
     *
     * @readonly
     */
    public get total() {
        return this.isResource(this._response) ? this._response.total : this._response.meta.total;
    }

    /**
     * Gets meta-data.
     *
     * @readonly
     */
    public get meta() {
        if (this.isResource(this._response)) {
            return excludeFromObject(this._response, ['data']);
        } else {
            return this._response.meta;
        }
    }

    /**
     * Gets links
     *
     * @readonly
     */
    public get elements() {
        const links = this.isResource(this._response) ? this._response.links : this._response.meta.links;

        return links.filter(({ label }) => !label.includes('&laquo;') && !label.includes('&raquo;'));
    }

    /**
     * Checks if resource response (derived from Illuminate\Http\Resources\Json\ResourceResponse instance)
     *
     * @private
     * @template T
     * @param {unknown} response
     * @returns {response is IPaginateResponse<T>}
     */
    private isResource<T>(response: unknown): response is IPaginateResponse<T> {
        return typeof response === 'object' && response !== null && !('meta' in response && typeof response.meta === 'object');
    }

    /**
     * Checks if collection response (derived from Illuminate\Http\Resources\Json\ResourceCollection instance)
     *
     * @private
     * @template T
     * @param {unknown} response
     * @returns {response is IPaginateResponseCollection<T>}
     */
    private isCollection<T>(response: unknown): response is IPaginateResponseCollection<T> {
        return typeof response === 'object' && response !== null && 'meta' in response && typeof response.meta === 'object';
    }
}
