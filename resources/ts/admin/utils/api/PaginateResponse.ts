export default class<T = unknown> {
    private readonly _response: IPaginateResponse<T>;

    constructor(response: IPaginateResponse<T>) {
        this._response = response;
    }

    public get response() {
        return this._response;
    }

    public get hasPages() {
        return this._response.last_page > 1;
    }

    public get onFirstPage() {
        return this._response.current_page === 1;
    }

    public get previousPageUrl() {
        return this._response.prev_page_url;
    }

    public get nextPageUrl() {
        return this._response.next_page_url;
    }

    public get hasMorePages() {
        return this._response.next_page_url !== null;
    }

    public get firstItem() {
        // If from is null, the collection is empty
        return this._response.from ?? 0;
    }

    public get lastItem() {
        return this._response.to ?? 0;
    }

    public get total() {
        return this._response.total;
    }

    public get elements() {
        return this._response.links.filter(({ label }) => !label.includes('&laquo;') && !label.includes('&raquo;'));
    }
}
