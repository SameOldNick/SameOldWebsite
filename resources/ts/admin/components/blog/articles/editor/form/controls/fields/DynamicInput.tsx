import React from 'react';

import { InputProps, Input } from 'reactstrap';
import DynamicField from './DynamicField';

type DynamicInputProps = Omit<InputProps, 'name' | 'onChange' | 'onBlur'> & {
    name: string;
    onChange: (value: string) => void;
};

const DynamicInput: React.FC<DynamicInputProps> = ({ name, onChange, ...props }) => {
    const handleChange = React.useCallback((e: React.ChangeEvent<HTMLInputElement>) => {
        onChange(e.target.value);
    }, [onChange]);

    const handleBlur = React.useCallback((e: React.ChangeEvent<HTMLInputElement>) => {
        onChange(e.target.value);
    }, [onChange]);

    return (
        <DynamicField
            as={Input}
            name={name}
            onChange={handleChange}
            onBlur={handleBlur}
            {...props}
        />
    );
}

export default DynamicInput;
