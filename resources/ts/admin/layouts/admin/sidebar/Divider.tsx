import React from 'react';

interface DividerProps {

}

const Divider: React.FC<DividerProps> = ({ }) => {
    return (
        <hr className="sidebar-divider" />
    );
}

export default Divider;
export { DividerProps };
