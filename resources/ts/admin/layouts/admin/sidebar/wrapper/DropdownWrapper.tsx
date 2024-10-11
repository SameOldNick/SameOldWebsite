import React from 'react';

import DropdownMenuItem from '@admin/utils/menus/items/DropdownMenuItem';
import Dropdown from '../Dropdown';
import ItemWrapper from './ItemWrapper';

interface IProps {
    item: DropdownMenuItem;
}

const DropdownWrapper: React.FC<IProps> = ({ item }) => {
    const icon = React.useMemo(() => item.options?.icon ? React.createElement(item.options?.icon) : undefined, [item.options]);

    return (
        <>
            <Dropdown text={item.text} icon={icon}>
                {item.items.map((item, index) => <ItemWrapper key={index} item={item} inDropdown />)}
            </Dropdown>

        </>
    );

};

export default DropdownWrapper;
