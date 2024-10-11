import React from 'react';
import { Button } from 'reactstrap';
import { FaBars } from 'react-icons/fa';

interface MobileNavbarToggleProps {
    onToggle: () => void;
}

const MobileNavbarToggle: React.FC<MobileNavbarToggleProps> = ({ onToggle }) => {
    const handleButtonClick = React.useCallback((e: React.MouseEvent) => {
        onToggle();
    }, [onToggle])

    return (
        <Button color='link' className="d-md-none rounded-circle me-3" onClick={handleButtonClick}>
            <FaBars />
        </Button>
    );

};

export default MobileNavbarToggle;
