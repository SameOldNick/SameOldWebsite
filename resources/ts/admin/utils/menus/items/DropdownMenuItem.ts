import { IconType } from "react-icons";

import MenuItem, { MenuItemOptions } from "./MenuItem";

class DropdownMenuItem extends MenuItem {
    constructor(
        text: string,
        public readonly items: MenuItem[],
        options?: MenuItemOptions,
    ) {
        super(text, options);
    }
}

export default DropdownMenuItem;
