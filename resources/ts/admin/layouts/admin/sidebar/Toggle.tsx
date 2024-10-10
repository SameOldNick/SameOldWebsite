import React from 'react';

import { FaAngleLeft, FaAngleRight } from 'react-icons/fa';

interface ToggleProps {
    onToggle: () => void;
    toggled: boolean;
}

const Toggle: React.FC<ToggleProps> = ({ toggled, onToggle }) => {
    const toggle = React.useCallback((e: React.MouseEvent) => {
        e.preventDefault();

        onToggle();
    }, [onToggle]);

    return (
        <div className="text-center d-none d-md-inline">
            <button id="sidebarToggle" className="rounded-circle border-0" onClick={toggle}>
                {toggled ? <FaAngleRight /> : <FaAngleLeft />}
            </button>
        </div>
    );
}

export default Toggle;
export { ToggleProps };
