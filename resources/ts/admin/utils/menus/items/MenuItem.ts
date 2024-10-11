import { IconType } from 'react-icons';

interface MenuItemOptions {
    readonly icon?: IconType;
    readonly roles?: {
        roles: string[];
        oneOf?: boolean;
    } | string[];
}

abstract class MenuItem {
    constructor(
        public readonly text: string,
        public readonly options?: MenuItemOptions,
    ) { }
}

export default MenuItem;
export { MenuItemOptions };
