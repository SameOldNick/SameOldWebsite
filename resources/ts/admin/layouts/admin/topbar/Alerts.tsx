import React from 'react';
import { IconContext } from 'react-icons';
import { FaBell, FaFileAlt, FaDonate, FaExclamationTriangle } from 'react-icons/fa';
import { Dropdown, DropdownToggle, DropdownMenu, DropdownItem, Badge } from 'reactstrap';
import classNames from 'classnames';

interface IProps {

}

interface IAlert {
    link: string;
    icon: React.ReactNode;
    iconBackground?: string;
    message: React.ReactNode;
    dateTime: string;
}

const Alerts: React.FC<IProps> = ({ }) => {
    const [open, setOpen] = React.useState(false);

    const alerts = React.useMemo<IAlert[]>(() => [
        {
            link: '#',
            icon: <FaFileAlt />,
            iconBackground: 'primary',
            message: <span className="fw-bold">A new monthly report is ready to download!</span>,
            dateTime: 'December 12, 2019'
        },
        {
            link: '#',
            icon: <FaDonate />,
            iconBackground: 'success',
            message: '$290.29 has been deposited into your account!',
            dateTime: 'December 7, 2019'
        },
        {
            link: '#',
            icon: <FaExclamationTriangle />,
            iconBackground: 'warning',
            message: 'Spending Alert: We\'ve noticed unusually high spending for your account.',
            dateTime: 'December 12, 2019'
        }
    ], []);

    return (
        <>
            <Dropdown nav className='no-arrow mx-1' isOpen={open} toggle={() => setOpen(!open)}>
                <DropdownToggle nav tag='a' href='#' id="alertsDropdown">
                    <span className='position-relative'>
                        <FaBell className='fa-fw' />
                        {/* Counter - Alerts */}
                        <Badge pill color='danger' className='position-absolute top-0 start-100 translate-middle'>3</Badge>
                    </span>
                </DropdownToggle>

                {/* Dropdown - User Information */}
                <DropdownMenu end className='shadow animated--grow-in'>
                    <DropdownItem header>Alerts Center</DropdownItem>

                    <IconContext.Provider value={{ className: 'text-white' }}>
                        {alerts.map((alert, index) => (
                            <DropdownItem key={index} href={alert.link} className='d-flex align-items-center my-2'>
                                <div className="me-3">
                                    <div className={classNames('icon-circle', alert.iconBackground ? `bg-${alert.iconBackground}` : undefined)}>
                                        {alert.icon}
                                    </div>
                                </div>
                                <div>
                                    <div className="small text-gray-500">{alert.dateTime}</div>
                                    {alert.message}
                                </div>
                            </DropdownItem>
                        ))}
                    </IconContext.Provider>

                    <DropdownItem className='text-center small text-gray-500 mt-2' href='#'>
                        Show All Alerts
                    </DropdownItem>
                </DropdownMenu>
            </Dropdown>
        </>
    );
}

export default Alerts;
