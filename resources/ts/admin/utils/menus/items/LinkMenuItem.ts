import MenuItem, { MenuItemOptions } from './MenuItem';

class LinkMenuItem extends MenuItem {
    constructor(
        text: string,
        public readonly href: string,
        options?: MenuItemOptions,
    ) {
        super(text, options);
    }
}

export default LinkMenuItem;
