import React from 'react';
import { Alert as ReactstrapAlert, AlertProps } from 'reactstrap';

export type TAlertTypes = 'success' | 'warning' | 'danger' | 'info';

export interface IAlert extends Omit<AlertProps, 'children'> {
    type: TAlertTypes;
    message: string | JSX.Element;
    dismissable?: boolean;
}


type TAlertExtraProps = Omit<AlertProps, 'children' | 'color' | 'key' | 'isOpen' | 'toggle'>;

interface IAlertsProps extends TAlertExtraProps {
    alerts: IAlert[];
    limit?: number;
}

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

/**
 * Displays Bootstrap alerts from either the redux store or properties.
 */
const Alerts: React.FC<IAlertsProps> = ({ alerts, limit, ...props }) => {
    return React.useMemo(() =>
        alerts
            .slice(0, limit)
            .map((alert, key) => <Alert key={key} alert={alert} {...props} />),
        [limit, alerts]
    );
}

export default Alerts;
