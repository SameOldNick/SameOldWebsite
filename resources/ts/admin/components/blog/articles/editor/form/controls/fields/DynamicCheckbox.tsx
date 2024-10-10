import React from 'react';
import { InputProps, Input } from 'reactstrap';

import DynamicField from './DynamicField';

type DynamicCheckboxProps = Omit<InputProps, 'name' | 'checked'> & {
    name: string;
    checked: boolean;
    onChange: (checked: boolean) => void;
};

const DynamicCheckbox: React.FC<DynamicCheckboxProps> = ({ name, checked, onChange, ...props }) => {
    return (
        <DynamicField
            as={Input}
            type='checkbox'
            name={name}
            checked={checked}
            onClick={() => onChange(!checked)}
            {...props}
        />
    );
}

export default DynamicCheckbox;
