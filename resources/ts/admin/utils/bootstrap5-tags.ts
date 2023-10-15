/**
 * Bootstrap 5 (and 4!) tags
 *
 * Turns your select[multiple] into nice tags lists
 *
 * Required Bootstrap 5 styles:
 * - badge
 * - background-color utility
 * - margin-end utility
 * - float-start utility
 * - forms
 * - dropdown
 */

import React from "react";

const ACTIVE_CLASS = "is-active";
const ACTIVE_CLASSES = ["is-active", "bg-primary", "text-white"];
const VALUE_ATTRIBUTE = "data-value";

// Static map will minify very badly as class prop, so we use an external co_selectElementnstant
const INSTANCE_MAP = new WeakMap<HTMLElement, Tags>();

export interface IConfigurationOptions {
    allowNew: boolean;
    showAllSuggestions: boolean;
    badgeStyle: string;
    allowClear: boolean;
    clearEnd: boolean;
    server: string;
    liveServer: boolean;
    serverParams: Record<string, string>;
    selected: string;
    suggestionsThreshold: number;
    validationRegex: string | false;
    separator: string;
    max: number | false;
    placeholder: string;
    clearLabel: string;
    searchLabel: string;
    valueField: string;
    labelField: string;
    keepOpen:  boolean;
    fullWidth: boolean;
    debounceTime: number;
    baseClass: string;
}

interface IInternalOptions {
    allowNew: boolean;
    showAllSuggestions: boolean;
    badgeStyle: string;
    allowClear: boolean;
    clearEnd: boolean;
    server: string | false;
    liveServer: boolean;
    serverParams: Record<string, string>;
    selected: string[];
    suggestionsThreshold: number;
    validationRegex: string | false;
    separator: string[];
    max: number | null;
    placeholder: string | null;
    clearLabel: string;
    searchLabel: string;
    valueField: string;
    labelField: string;
    keepOpen:  boolean;
    fullWidth: boolean;
    debounceTime: number;
    baseClass: string;
}

type TSuggestion = Record<string, string | Record<string, string>>;

type TDebounceFunc = (...args: any[]) => void;

export default class Tags {
    private _selectElement: HTMLSelectElement;

    private _opts: IInternalOptions;

    private _keyboardNavigation: boolean;
    private _fireEvents: boolean;
    private _searchFunc: TDebounceFunc;
    private overflowParent: HTMLElement | null;
    private parentForm: HTMLElement | null;
    private _holderElement: HTMLDivElement;
    private _containerElement: HTMLDivElement;
    private _dropElement: HTMLUListElement;
    private _searchInput: HTMLInputElement;
    private _abortController: AbortController | null;

    /**
     * @param {HTMLSelectElement} el
     * @param {Object} globalOpts
     */
    constructor(el: HTMLSelectElement, globalOpts: Partial<IConfigurationOptions> = {}) {
        // Hide the select element and register a tags attr
        el.style.display = "none";
        INSTANCE_MAP.set(el, this);
        this._selectElement = el;

        // Handle options, using global settings first and data attr override
        const opts = { ...globalOpts, ...el.dataset };

        this._opts = {
            allowNew: opts.allowNew || false,
            showAllSuggestions: opts.showAllSuggestions || false,
            badgeStyle: opts.badgeStyle || "primary",
            allowClear: opts.allowClear || false,
            clearEnd: opts.clearEnd || false,
            server: opts.server || false,
            liveServer: opts.liveServer || false,
            serverParams: opts.serverParams || {},
            selected: opts.selected ? opts.selected.split(",") : [],
            suggestionsThreshold: opts.suggestionsThreshold !== undefined ? opts.suggestionsThreshold : 1,
            validationRegex: opts.validationRegex || "",
            separator: opts.separator ? opts.separator.split("|") : [],
            max: typeof opts.max === 'number' ? opts.max : null,
            clearLabel: opts.clearLabel || "Clear",
            searchLabel: opts.searchLabel || "Type a value",
            valueField: opts.valueField || "value",
            labelField: opts.labelField || "label",
            keepOpen: opts.keepOpen || false,
            fullWidth: opts.fullWidth || false,
            debounceTime: opts.debounceTime || 300,
            baseClass: opts.baseClass || "",
            placeholder: opts.placeholder || this._getPlaceholder()
        };

        this._keyboardNavigation = false;
        this._fireEvents = true;

        this._searchFunc = Tags.debounce(() => this._loadFromServer(true), this._opts.debounceTime);

        this.overflowParent = null;
        this.parentForm = el.parentElement;
        while (this.parentForm) {
            if (this.parentForm.style.overflow === "hidden") {
                this.overflowParent = this.parentForm;
            }
            this.parentForm = this.parentForm.parentElement;
            if (this.parentForm && this.parentForm.nodeName === "FORM") {
                break;
            }
        }

        this.reset = this.reset.bind(this);

        if (this.parentForm) {
            this.parentForm.addEventListener("reset", this.reset);
        }

        // Create elements
        this._holderElement = document.createElement("div"); // this is the one holding the fake input and the dropmenu
        this._containerElement = document.createElement("div"); // this is the one for the fake input (labels + input)
        this._dropElement = document.createElement("ul");
        this._searchInput = document.createElement("input");

        this._holderElement.appendChild(this._containerElement);
        this._containerElement.appendChild(this._searchInput);
        this._holderElement.appendChild(this._dropElement);
        // insert after select
        this._selectElement.parentNode?.insertBefore(this._holderElement, this._selectElement.nextSibling);

        this._abortController = null;

        // Configure them
        this._configureHolderElement();
        this._configureDropElement();
        this._configureContainerElement();
        this._configureSearchInput();
        this.resetState();

        if (this._opts.server && !this._opts.liveServer) {
            this._loadFromServer();
        } else {
            this.resetSuggestions();
        }
    }

    /**
     * Attach to all elements matched by the selector
     * @param {string} selector
     * @param {Object} opts
     */
    public static init(selector: string = "select[multiple]", opts: Partial<IConfigurationOptions> = {}) {
        const list = document.querySelectorAll<HTMLSelectElement>(selector);
        for (let i = 0; i < list.length; i++) {
            if (Tags.getInstance(list[i])) {
                continue;
            }
            new Tags(list.item(i), opts);
        }
    }

    /**
     * @param {HTMLSelectElement} el
     */
    public static getInstance(el: HTMLSelectElement) {
        return INSTANCE_MAP.has(el) ? INSTANCE_MAP.get(el) : undefined;
    }

    /**
     * @param {Function} func
     * @param {number} timeout
     * @returns {Function}
     */
    public static debounce(func: TDebounceFunc, timeout = 300) {
        let timer: Timeout;
        return (...args: any[]) => {
            clearTimeout(timer);
            timer = setTimeout(() => {
                func.apply(this, args);
            }, timeout);
        };
    }

    public dispose() {
        INSTANCE_MAP.delete(this._selectElement);
        this._selectElement.style.display = "block";
        this._holderElement.parentNode?.removeChild(this._holderElement);
        if (this.parentForm) {
            this.parentForm.removeEventListener("reset", this.reset);
        }
    }

    public resetState() {
        if (this.isDisabled()) {
            this._holderElement.setAttribute("readonly", "");
            this._searchInput.setAttribute("disabled", "");
        } else {
            if (this._holderElement.hasAttribute("readonly")) {
                this._holderElement.removeAttribute("readonly");
            }
            if (this._searchInput.hasAttribute("disabled")) {
                this._searchInput.removeAttribute("disabled");
            }
        }
    }

    public resetSuggestions() {
        const suggestions = Array.from(this._selectElement.querySelectorAll("option"))
            .filter((option) => !option.disabled)
            .map<TSuggestion>((option) => ({
                value: option.getAttribute("value") || '',
                label: option.textContent || '',
                data: {}
            }));

        this._buildSuggestions(suggestions);
    }

    /**
     * @param {boolean} show
     */
    private _loadFromServer(show: boolean = false) {
        if (this._abortController) {
            this._abortController.abort();
        }
        this._abortController = new AbortController();

        this._opts.serverParams.query = this._searchInput.value;
        const params = new URLSearchParams(this._opts.serverParams).toString();

        fetch(this._opts.server + "?" + params, { signal: this._abortController.signal })
            .then((r) => r.json())
            .then((suggestions) => {
                const data = suggestions.data || suggestions;
                this._buildSuggestions(data);
                this._abortController = null;
                if (show) {
                    this._showSuggestions();
                }
            })
            .catch((e) => {
                if (e.name === "AbortError") {
                    return;
                }
                console.error(e);
            });
    }

    /**
     * @returns {string}
     */
    private _getPlaceholder() {
        // Use placeholder and data-placeholder in priority
        if (this._selectElement.hasAttribute("placeholder")) {
            return this._selectElement.getAttribute("placeholder");
        }
        if (this._selectElement.dataset.placeholder) {
            return this._selectElement.dataset.placeholder;
        }
        // Fallback to first option if no value
        const firstOption = this._selectElement.querySelector("option");
        if (!firstOption) {
            return "";
        }
        if (firstOption.hasAttribute("selected")) {
            firstOption.removeAttribute("selected");
        }
        return !firstOption.value ? firstOption.textContent : "";
    }

    private _configureDropElement() {
        this._dropElement.classList.add("dropdown-menu", "p-0");
        this._dropElement.style.maxHeight = "280px";
        if (!this._opts.fullWidth) {
            this._dropElement.style.maxWidth = "360px";
        }
        this._dropElement.style.overflowY = "auto";

        // If the mouse was outside, entering remove keyboard nav mode
        this._dropElement.addEventListener("mouseenter", (event) => {
            this._keyboardNavigation = false;
        });
    }

    private _configureHolderElement() {
        this._holderElement.classList.add("form-control", "dropdown");
        if (this._selectElement.classList.contains("form-select-lg")) {
            this._holderElement.classList.add("form-control-lg");
        }
        if (this._selectElement.classList.contains("form-select-sm")) {
            this._holderElement.classList.add("form-control-sm");
        }
        // If we don't have an overflow parent, we can simply inherit styles
        // If we have an overflow parent, it needs a relatively positioned element
        if (this.overflowParent) {
            this._holderElement.style.position = "inherit";
        }
        if (this._getBootstrapVersion() === 4) {
            // Prevent fixed height due to form-control
            this._holderElement.style.height = "auto";
        }
    }

    private _configureContainerElement() {
        this._containerElement.addEventListener("click", (event) => {
            if (this.isDisabled()) {
                return;
            }
            if (this._searchInput.style.visibility !== "hidden") {
                this._searchInput.focus();
            }
        });

        // add initial values
        // we use selectedOptions because single select can have a selected option
        // without a selected attribute if it's the first value
        const initialValues = this._selectElement.selectedOptions;

        for (let j = 0; j < initialValues.length; j++) {
            const initialValue = initialValues.item(j);
            if (!initialValue || !initialValue.value) {
                continue;
            }
            // track initial values for reset
            initialValue.dataset.init = '1';
            this.addItem(initialValue.textContent || '', initialValue.value);
        }
    }

    private _isInputEvent(ev: any): ev is InputEvent {
        return typeof ev.data === 'string';
    }

    private _configureSearchInput() {
        this._searchInput.type = "text";
        this._searchInput.autocomplete = "off";
        this._searchInput.spellcheck = false;
        this._searchInput.style.backgroundColor = "transparent";
        this._searchInput.style.border = '0';
        this._searchInput.style.outline = '0';
        this._searchInput.style.maxWidth = "100%";
        this._searchInput.ariaLabel = this._opts.searchLabel;
        this._resetSearchInput(true);


        this._searchInput.addEventListener("input", (event) => {
            // Add item if a separator is used
            // On mobile or copy paste, it can pass multiple chars (eg: when pressing space and it formats the string)
            if (this._isInputEvent(event)) {
                const lastChar = event.data ? event.data.slice(-1) : '';
                if (this._opts.separator.length && this._searchInput.value && this._opts.separator.includes(lastChar)) {
                    // Remove separator even if adding is prevented
                    this._searchInput.value = this._searchInput.value.slice(0, -1);
                    const text = this._searchInput.value;
                    this._add(text, null);
                    return;
                }
            }

            // Adjust input width to current content
            this._adjustWidth();

            // Check if we should display suggestions
            if (this._searchInput.value.length >= this._opts.suggestionsThreshold) {
                if (this._opts.liveServer) {
                    this._searchFunc();
                } else {
                    this._showSuggestions();
                }
            } else {
                this._hideSuggestions();
            }
        });

        this._searchInput.addEventListener("focus", (event) => {
            if (this._searchInput.value.length >= this._opts.suggestionsThreshold) {
                this._showSuggestions();
            }
        });

        this._searchInput.addEventListener("focusout", (event) => {
            this._hideSuggestions();
            if (this._opts.keepOpen) {
                this._resetSearchInput();
            }
        });

        // keypress doesn't send arrow keys, so we use keydown
        this._searchInput.addEventListener("keydown", (event) => {
            // Keycode reference : https://css-tricks.com/snippets/javascript/javascript-keycodes/
            const key = event.keyCode || event.key;

            // Keyboard keys
            switch (key) {
                case 13:
                case "Enter": {
                    event.preventDefault();
                    const selection = this.getActiveSelection();
                    if (selection) {
                        selection.click();
                    } else {
                        // We use what is typed if not selected and not empty
                        if (this._opts.allowNew && this._searchInput.value) {
                            const text = this._searchInput.value;
                            this._add(text, null);
                        }
                    }
                    break;
                }
                case 38:
                case "ArrowUp": {
                    event.preventDefault();
                    this._keyboardNavigation = true;
                    const newSelection = this._moveSelectionUp();
                    // If we use arrow up without input and there is no new selection, hide suggestions
                    if (this._searchInput.value.length === 0 && this._dropElement.classList.contains("show") && !newSelection) {
                        this._hideSuggestions();
                    }
                    break;
                }
                case 40:
                case "ArrowDown": {
                    event.preventDefault();
                    this._keyboardNavigation = true;
                    this._moveSelectionDown();
                    // If we use arrow down without input, show suggestions
                    if (this._searchInput.value.length === 0 && !this._dropElement.classList.contains("show")) {
                        this._showSuggestions();
                    }
                    break;
                }

                case 8:
                case "Backspace": {
                    if (this._searchInput.value.length === 0) {
                        this.removeLastItem();
                        this._adjustWidth();
                        this._hideSuggestions();
                    }
                    break;
                }
                case 27:
                case "Escape": {
                    // We may wish to not use the suggestions
                    this._hideSuggestions();
                    break;
                }
            }
        });
    }

    /**
     * @param {string} text
     * @param {string} value
     * @param {object} data
     */
    private _add(text: string, value: string | null = null, data = {}) {
        if (!this.canAdd(text, value)) {
            return;
        }
        this.addItem(text, value, data);
        if (this._opts.keepOpen) {
            this._showSuggestions();
        } else {
            this._resetSearchInput();
        }
    }

    /**
     * @returns {HTMLElement}
     */
    private _moveSelectionUp() {
        const active = this.getActiveSelection();
        if (active) {
            let prev = active.parentNode as HTMLElement;
            while (prev && prev.style.display === "none") {
                prev = prev.previousSibling as HTMLElement;
            }

            if (!prev) {
                return null;
            }
            active.classList.remove(...ACTIVE_CLASSES);
            prev.querySelector("a")?.classList.add(...ACTIVE_CLASSES);
            // Don't use scrollIntoView as it scrolls the whole window
            (prev.parentNode as HTMLElement).scrollTop = prev.offsetTop - (prev.parentNode as HTMLElement).offsetTop;
            return prev;
        }
        return null;
    }

    /**
     * @returns {HTMLElement}
     */
    private _moveSelectionDown() {
        const active = this.getActiveSelection();
        let next: HTMLElement | null = null;
        if (active) {
            next = active.parentNode as HTMLElement;
            while (next && next.style.display === "none") {
                next = next.nextSibling as HTMLElement;
            }
            if (!next) {
                return null;
            }
            active.classList.remove(...ACTIVE_CLASSES);
            next.querySelector("a")?.classList.add(...ACTIVE_CLASSES);
            // This is the equivalent of scrollIntoView(false) but only for parent node
            if (next.offsetTop > (next.parentNode as HTMLElement).offsetHeight - next.offsetHeight) {
                (next.parentNode as HTMLElement).scrollTop += next.offsetHeight;
            }
            return next;
        }
        return next;
    }

    /**
     * @param {string} text
     * @param {string} size
     * @returns {Number}
     */
    private _calcTextWidth(text: string, size: string | null = null) {
        const span = document.createElement("span");
        document.body.appendChild(span);
        span.style.fontSize = size || "inherit";
        span.style.height = "auto";
        span.style.width = "auto";
        span.style.position = "absolute";
        span.style.whiteSpace = "no-wrap";
        span.innerHTML = text;
        const width = Math.ceil(span.clientWidth) + 8;
        document.body.removeChild(span);
        return width;
    }

    /**
     * Adjust the field to fit its content and show/hide placeholder if needed
     */
    private _adjustWidth() {
        if (this._searchInput.value) {
            this._searchInput.size = this._searchInput.value.length;
        } else {
            // Show the placeholder only if empty
            if (this.getSelectedValues().length) {
                this._searchInput.placeholder = "";
                this._searchInput.size = 1;
            } else {
                this._searchInput.size = this._opts.placeholder && this._opts.placeholder.length > 0 ? this._opts.placeholder.length : 1;
                this._searchInput.placeholder = this._opts.placeholder || '';
            }
        }

        // If the string contains ascii chars or strange font, input size may be wrong
        const v = this._searchInput.value || this._searchInput.placeholder;
        const computedFontSize = window.getComputedStyle(this._holderElement).fontSize;
        const w = this._calcTextWidth(v, computedFontSize);
        this._searchInput.style.minWidth = w + "px";
    }

    /**
     * Add suggestions to the drop element
     * @param {array} suggestions
     */
    private _buildSuggestions(suggestions: TSuggestion[]) {
        while (this._dropElement.lastChild) {
            this._dropElement.removeChild(this._dropElement.lastChild);
        }

        for (const suggestion of suggestions) {
            const label = suggestion[this._opts.labelField];
            const value = suggestion[this._opts.valueField];
            if (typeof label !== 'string' || typeof value !== 'string') {
                continue;
            }

            // initial selection
            if (suggestion.selected || this._opts.selected.includes(value)) {
                this._add(label, value, suggestion.data);
                continue; // no need to add as suggestion
            }

            const newChild = document.createElement("li");
            const newChildLink = document.createElement("a");

            newChild.append(newChildLink);

            newChildLink.classList.add("dropdown-item", "text-truncate");
            newChildLink.setAttribute(VALUE_ATTRIBUTE, value);
            newChildLink.setAttribute("href", "#");
            newChildLink.textContent = label;

            if (suggestion.data) {
                for (const [key, value] of Object.entries(suggestion.data)) {
                    newChildLink.dataset[key] = value;
                }
            }
            this._dropElement.appendChild(newChild);

            // Hover sets active item
            newChildLink.addEventListener("mouseenter", (event) => {
                // Don't trigger enter if using arrows
                if (this._keyboardNavigation) {
                    return;
                }
                this.removeActiveSelection();
                newChild.querySelector("a")?.classList.add(...ACTIVE_CLASSES);
            });
            // Moving the mouse means no longer using keyboard
            newChildLink.addEventListener("mousemove", (event) => {
                this._keyboardNavigation = false;
            });

            newChildLink.addEventListener("mousedown", (event) => {
                // Otherwise searchInput would lose focus and close the menu
                event.preventDefault();
            });
            newChildLink.addEventListener("click", (event) => {
                event.preventDefault();

                if (newChildLink.textContent)
                    this._add(newChildLink.textContent, newChildLink.getAttribute(VALUE_ATTRIBUTE), newChildLink.dataset);
            });
        }
    }

    public reset() {
        this.removeAll();

        // Reset doesn't fire change event
        this._fireEvents = false;
        const initialValues = this._selectElement.querySelectorAll<HTMLOptionElement>("option[data-init]");
        for (let j = 0; j < initialValues.length; j++) {
            const initialValue = initialValues.item(j);
            this.addItem(initialValue.textContent || '', initialValue.value);
        }
        this._adjustWidth();
        this._fireEvents = true;
    }

    /**
     * @param {bool} init Pass true during init
     */
    private _resetSearchInput(init = false) {
        this._searchInput.value = "";
        this._adjustWidth();

        if (!init) {
            this._hideSuggestions();
            // Trigger input even to show suggestions if needed
            this._searchInput.dispatchEvent(new Event("input"));
        }

        // We use visibility instead of display to keep layout intact
        if (this._opts.max !== null && this.getSelectedValues().length >= this._opts.max) {
            this._searchInput.style.visibility = "hidden";
        } else if (this._searchInput.style.visibility === "hidden") {
            this._searchInput.style.visibility = "visible";
        }

        if (this.isSingle() && !init) {
            (document.activeElement as HTMLElement).blur();
        }
    }

    /**
     * @returns {array}
     */
    public getSelectedValues() {
        // option[selected] is used rather that selectedOptions as it works more consistently
        const selected = this._selectElement.querySelectorAll<HTMLOptionElement>("option[selected]");
        return Array.from(selected).map((el) => el.value);
    }

    /**
     * The element create with buildSuggestions
     */
    private _showSuggestions() {
        if (this._searchInput.style.visibility === "hidden") {
            return;
        }

        // Get search value
        const search = this._searchInput.value.toLocaleLowerCase();

        // Get current values
        const values = this.getSelectedValues();

        // Filter the list according to search string
        const list = this._dropElement.querySelectorAll("li");
        let found = false;
        let firstItem: HTMLLIElement | null = null;
        let hasPossibleValues = false;

        for (let i = 0; i < list.length; i++) {
            const item = list.item(i);
            const text = item.textContent ? item.textContent.toLocaleLowerCase() : '';
            const link = item.querySelector("a");

            // Remove previous selection
            link?.classList.remove(...ACTIVE_CLASSES);

            // Hide selected values
            if (link !== null && values.indexOf(link.getAttribute(VALUE_ATTRIBUTE) || '') !== -1) {
                item.style.display = "none";
                continue;
            }

            hasPossibleValues = true;

            // Check search length since we can trigger dropdown with arrow
            const isMatched = search.length === 0 || text.indexOf(search) !== -1;
            if (this._opts.showAllSuggestions || this._opts.suggestionsThreshold === 0 || isMatched) {
                item.style.display = "list-item";
                found = true;
                if (!firstItem && isMatched) {
                    firstItem = item;
                }
            } else {
                item.style.display = "none";
            }
        }

        if (firstItem || this._opts.showAllSuggestions) {
            this._holderElement.classList.remove("is-invalid");
            // Always select first item
            if (firstItem) {
                firstItem.querySelector("a")?.classList.add(...ACTIVE_CLASSES);

                if (firstItem.parentNode)
                    (firstItem.parentNode as HTMLElement).scrollTop = firstItem.offsetTop;
            }
        } else {
            // No item and we don't allow new items => error
            if (!this._opts.allowNew && !(search.length === 0 && !hasPossibleValues)) {
                this._holderElement.classList.add("is-invalid");
            } else if (this._opts.validationRegex && this.isInvalid()) {
                this._holderElement.classList.remove("is-invalid");
            }
        }

        // Remove dropdown if not found or to show validation message
        if (!found || this.isInvalid()) {
            this._dropElement.classList.remove("show");
        } else {
            // Or show it if necessary
            this._dropElement.classList.add("show");

            if (this._opts.fullWidth) {
                // Use full input width
                this._dropElement.style.left = -1 + "px";
                this._dropElement.style.width = this._holderElement.offsetWidth + "px";
            } else {
                // Position next to search input
                let left = this._searchInput.offsetLeft;

                // Overflow right
                const w = document.body.offsetWidth - 1; // avoid rounding issues
                const scrollbarOffset = 30; // scrollbars are not taken into account
                const wdiff = w - (left + this._dropElement.offsetWidth) - scrollbarOffset;

                // If the dropdowns goes out of the viewport, remove the diff from the left position
                if (wdiff < 0) {
                    left = left + wdiff;
                }
                this._dropElement.style.left = left + "px";

                // Overflow bottom
                const h = document.body.offsetHeight;
                const bottom = this._searchInput.getBoundingClientRect().y + window.pageYOffset + this._dropElement.offsetHeight;
                const hdiff = h - bottom;
                if (hdiff < 0) {
                    // We display above input
                    this._dropElement.style.transform = "translateY(calc(-100% - " + scrollbarOffset + "px))";
                } else {
                    this._dropElement.style.transform = "none";
                }
            }
        }
    }

    /**
     * The element create with buildSuggestions
     */
    private _hideSuggestions() {
        this._dropElement.classList.remove("show");
        this._holderElement.classList.remove("is-invalid");
        this.removeActiveSelection();
    }

    /**
     * @returns {Number}
     */
    private _getBootstrapVersion() {
        const ver = 5;

        // Source: https://stackoverflow.com/a/57808392/533242

        /* Causes JS file to not be rendered by browser ??? */
        /* We'll just assume it's Bootstrap 5 */
        //if (typeof bootstrap.Tooltip.VERSION === 'string')
            //ver = Number(bootstrap.Tooltip.VERSION.charAt(0));

        // If we have jQuery and the tooltip plugin for BS4
        /*if (window.jQuery && $.fn.tooltip !== undefined && $.fn.tooltip.Constructor != undefined) {
            ver = parseInt($.fn.tooltip.Constructor.VERSION.charAt(0));
        }*/

        return ver;
    }

    /**
     * Find if label is already selected (based on attribute)
     * @param {string} text
     * @returns {boolean}
     */
    private _isSelected(text: string) {
        const opt = Array.from(this._selectElement.querySelectorAll("option")).find((el) => el.textContent === text);
        if (opt && opt.getAttribute("selected")) {
            return true;
        }
        return false;
    }

    /**
     * Checks if value matches a configured regex
     * @param {string} value
     * @returns {boolean}
     */
    private _validateRegex(value: string) {
        const regex = new RegExp(this._opts.validationRegex ? this._opts.validationRegex.trim() : '');
        return regex.test(value);
    }

    /**
     * @returns {HTMLElement}
     */
    public getActiveSelection() {
        return this._dropElement.querySelector<HTMLAnchorElement>("a." + ACTIVE_CLASS);
    }

    public removeActiveSelection() {
        const selection = this.getActiveSelection();
        if (selection) {
            selection.classList.remove(...ACTIVE_CLASSES);
        }
    }

    public removeAll() {
        const items = this.getSelectedValues();
        items.forEach((item) => {
            this.removeItem(item, true);
        });
        this._adjustWidth();
    }

    /**
     * @param {boolean} noEvents
     */
    public removeLastItem(noEvents: boolean = false) {
        const items = this._containerElement.querySelectorAll("span");
        if (!items.length) {
            return;
        }

        const lastItemValue = items.item(items.length - 1).getAttribute(VALUE_ATTRIBUTE);

        if (lastItemValue)
            this.removeItem(lastItemValue, noEvents);
    }

    /**
     * @returns {boolean}
     */
    public isDisabled() {
        return this._selectElement.hasAttribute("disabled") || this._selectElement.disabled || this._selectElement.hasAttribute("readonly");
    }

    /**
     * @returns {boolean}
     */
    public isInvalid() {
        return this._holderElement.classList.contains("is-invalid");
    }

    /**
     * @returns {boolean}
     */
    public isSingle() {
        return !this._selectElement.hasAttribute("multiple");
    }

    /**
     * @param {string} text
     * @param {string} value
     * @returns {boolean}
     */
    public canAdd(text: string, value: string | null = null) {
        if (!value) {
            value = text;
        }
        // Check invalid input
        if (!text) {
            return false;
        }
        // Check disabled
        if (this.isDisabled()) {
            return false;
        }
        // Check already selected input (single will replace)
        if (!this.isSingle() && this._isSelected(text)) {
            return false;
        }
        // Check for max
        if (this._opts.max && this.getSelectedValues().length >= this._opts.max) {
            return false;
        }
        // Check for regex
        if (this._opts.validationRegex && !this._validateRegex(text)) {
            this._holderElement.classList.add("is-invalid");
            return false;
        }
        return true;
    }

    /**
     * You might want to use canAdd before to ensure the item is valid
     * @param {string} text
     * @param {string} value
     * @param {object} data
     */
    public addItem(text: string, v: string | null = null, data: Partial<Record<'badgeStyle' | 'badgeClass', string>> = {}) {
        const value = v || text;

        // Single items remove first
        if (this.isSingle() && this.getSelectedValues().length) {
            this.removeLastItem(true);
        }

        const bver = this._getBootstrapVersion();
        let opt = this._selectElement.querySelector<HTMLOptionElement>('option[value="' + value + '"]');
        if (opt) {
            data = opt.dataset;
        }

        // create span
        let html = text;
        const span = document.createElement("span");
        let classes = ["badge"];
        let badgeStyle = this._opts.badgeStyle;
        if (data.badgeStyle) {
            badgeStyle = data.badgeStyle;
        }
        if (data.badgeClass) {
            classes.push(...data.badgeClass.split(" "));
        }
        if (this._opts.baseClass) {
            // custom style
            bver === 5 ? classes.push("me-2") : classes.push("mr-2");
            classes.push(...this._opts.baseClass.split(" "));
        } else if (bver === 5) {
            //https://getbootstrap.com/docs/5.1/components/badge/
            classes = [...classes, ...["me-2", "bg-" + badgeStyle, "mw-100"]];
        } else {
            // https://getbootstrap.com/docs/4.6/components/badge/
            classes = [...classes, ...["mr-2", "badge-" + badgeStyle]];
        }
        span.classList.add(...classes);
        span.setAttribute(VALUE_ATTRIBUTE, value);

        if (this._opts.allowClear) {
            const closeClass = classes.includes("text-dark") ? "btn-close" : "btn-close-white";
            let btnMargin;
            let btnFloat;
            if (this._opts.clearEnd) {
                btnMargin = bver === 5 ? "ms-2" : "ml-2";
                btnFloat = bver === 5 ? "float-end" : "float:right;";
            } else {
                btnMargin = bver === 5 ? "me-2" : "mr-2";
                btnFloat = bver === 5 ? "float-start" : "float:left;";
            }
            const btn =
                bver === 5
                    ? '<button type="button" style="font-size:0.65em" class="' +
                    btnMargin +
                    " " +
                    btnFloat +
                    " btn-close " +
                    closeClass +
                    '" aria-label="' +
                    this._opts.clearLabel +
                    '"></button>'
                    : '<button type="button" style="font-size:1em;' +
                    btnFloat +
                    'text-shadow:none;color:currentColor;transform:scale(1.2)" class="' +
                    btnMargin +
                    ' close" aria-label="' +
                    this._opts.clearLabel +
                    '"><span aria-hidden="true">&times;</span></button>';
            html = btn + html;
        }

        span.innerHTML = html;
        this._containerElement.insertBefore(span, this._searchInput);

        if (this._opts.allowClear) {
            const el = span.querySelector("button");


            if (el) {
                el.addEventListener("click", (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    if (!this.isDisabled()) {
                        this.removeItem(value);

                        el.blur();

                        this._adjustWidth();
                    }
                });
            }
        }

        // we need to create a new option
        if (!opt) {
            opt = document.createElement("option");
            opt.value = value;
            opt.textContent = text; // innerText is not well supported by jsdom
            // Pass along data provided
            for (const [key, value] of Object.entries(data)) {
                opt.dataset[key] = value;
            }
            this._selectElement.appendChild(opt);
        }

        // update select, we need to set attribute for option[selected]
        opt.setAttribute("selected", "selected");
        opt.selected = true;

        // Fire change event
        if (this._fireEvents) {
            this._selectElement.dispatchEvent(new Event("change", { bubbles: true }));
        }
    }

    /**
     * @param {string} value
     * @param {boolean} value
     */
    public removeItem(value: string, noEvents: boolean = false) {
        const item = this._containerElement.querySelector("span[" + VALUE_ATTRIBUTE + '="' + value + '"]');
        if (!item) {
            return;
        }
        item.remove();

        // update select
        const opt = this._selectElement.querySelector<HTMLOptionElement>('option[value="' + value + '"]');
        if (opt) {
            opt.removeAttribute("selected");
            opt.selected = false;

            // Fire change event
            if (this._fireEvents && !noEvents) {
                this._selectElement.dispatchEvent(new Event("change", { bubbles: true }));
            }
        }

        // Make input visible
        if (this._searchInput.style.visibility === "hidden" && this._opts.max !== null && this.getSelectedValues().length < this._opts.max) {
            this._searchInput.style.visibility = "visible";
        }
    }
}
