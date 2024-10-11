import React from 'react';

import MenuItem from '@admin/utils/menus/items/MenuItem';
import DropdownMenuItem from '@admin/utils/menus/items/DropdownMenuItem';
import LinkMenuItem from '@admin/utils/menus/items/LinkMenuItem';

import DropdownWrapper from './DropdownWrapper';
import LinkItemWrapper from './LinkItemWrapper';
import AuthorizedWrapper from './AuthorizedWrapper';
import DropdownLinkItemWrapper from './DropdownLinkItemWrapper';

interface ItemWrapperProps {
    item: MenuItem;
    inDropdown?: boolean;
}

const ItemWrapper: React.FC<ItemWrapperProps> = ({ item, inDropdown }) => {
    const children = React.useMemo(() => {
        if (item instanceof DropdownMenuItem) {
            return <DropdownWrapper item={item} />;
        } else if (item instanceof LinkMenuItem) {
            return inDropdown ? <DropdownLinkItemWrapper item={item} /> : <LinkItemWrapper item={item} />;
        } else {
            logger.error(`Unknown menu item`);

            return null;
        }
    }, [item]);

    return (
        <AuthorizedWrapper roles={item.options?.roles}>
            {children}
        </AuthorizedWrapper>
    )
};

export default ItemWrapper;
