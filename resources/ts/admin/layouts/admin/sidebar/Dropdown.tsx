import React from 'react';
import { Collapse, NavItem, NavLink } from 'reactstrap';
import { FaAngleDown, FaAngleUp } from 'react-icons/fa';

import classNames from 'classnames';

import { viewportSize } from '@admin/utils';
import Icon from './Icon';

interface DropdownProps extends React.PropsWithChildren {
    icon?: React.ReactNode;
    text: string;
}

interface IDropdownContext {
    inDropdown: boolean;
    hasActive: boolean;
    setHasActive: (active: boolean) => void;
}

const DropdownContext = React.createContext<IDropdownContext>({ inDropdown: false, hasActive: false, setHasActive: () => null });

const Dropdown: React.FC<DropdownProps> = ({ icon, text, children }) => {
    const [hasActive, setHasActive] = React.useState(false);
    const [isOpen, setIsOpen] = React.useState(false);

    React.useEffect(() => {
        if (hasActive && !isOpen)
            setIsOpen(true);
    }, [hasActive]);

    const onResize = React.useCallback(() => {
        const { width } = viewportSize();

        // Close any open menu accordions when window is resized below 768px
        if (width < 768 && isOpen) {
            setIsOpen(false);
        }
    }, [isOpen]);

    React.useEffect(() => {
        window.addEventListener('resize', onResize);

        return () => window.removeEventListener('resize', onResize);
    }, [onResize]);

    return (
        <DropdownContext.Provider value={{ hasActive, setHasActive, inDropdown: true }}>
            <NavItem>
                <NavLink href='#' className={classNames('d-flex justify-content-between', { collapsed: !isOpen })} role='button' onClick={() => setIsOpen(!isOpen)}>
                    <span>
                        <Icon>
                            {icon}
                        </Icon>

                        <span style={{ verticalAlign: 'middle' }}>
                            {text}
                        </span>
                    </span>

                    <span>
                        {isOpen ? <FaAngleUp /> : <FaAngleDown />}
                    </span>
                </NavLink>
                <Collapse isOpen={isOpen}>
                    <ul className="py-2 collapse-inner">
                        {children}
                    </ul>
                </Collapse>
            </NavItem>
        </DropdownContext.Provider>

    );
}

export default Dropdown;
export { DropdownProps, DropdownContext };
