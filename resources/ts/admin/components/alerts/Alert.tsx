import React from 'react';
import { Alert as ReactstrapAlert, AlertProps } from 'reactstrap';

type TAlertExtraProps = Omit<AlertProps, 'children' | 'color' | 'key' | 'isOpen' | 'toggle'>;

interface IAlertProps extends TAlertExtraProps {
    alert: IAlert;
}

const Alert: React.FC<IAlertProps> = ({ alert, ...props }) => {
    const [hidden, setHidden] = React.useState(false);

    if (alert.dismissable) {
        const toggleHidden = () => setHidden(!hidden);

        return <ReactstrapAlert color={alert.type} isOpen={!hidden} toggle={toggleHidden} {...props}>{alert.message}</ReactstrapAlert>;
    } else {
        return <ReactstrapAlert color={alert.type}>{alert.message}</ReactstrapAlert>;
    }
}

export default Alert;
