import MenuItem, { MenuItemOptions } from './MenuItem';

class LinkMenuItem extends MenuItem {
    constructor(
        content: string,
        public readonly href: string,
        options?: MenuItemOptions,
    ) {
        super(content, options);
    }
}

export default LinkMenuItem;
