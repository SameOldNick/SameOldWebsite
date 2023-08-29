import React from 'react';
import { Helmet } from "react-helmet";
import { FaBars } from 'react-icons/fa';
import { Button } from 'reactstrap';

import classNames from 'classnames';

interface IProps {

}

const SidebarToggle: React.FC<IProps> = ({ }) => {
    const [toggled, setToggled] = React.useState(false);

    const onToggle = (e: React.MouseEvent) => {
        e.preventDefault();

        setToggled((prevState) => !prevState);
    }

    return (
        <>
            <Helmet>
                <body className={classNames({ 'sidebar-hidden': toggled })} />
            </Helmet>

            <Button color='link' className="d-md-none rounded-circle me-3" onClick={onToggle}>
                <FaBars />
            </Button>
        </>
    );
}

export default SidebarToggle;
