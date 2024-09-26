import React from 'react';
import { AlertProps } from 'reactstrap';
import Alert from './Alert';

type TAlertExtraProps = Omit<AlertProps, 'children' | 'color' | 'key' | 'isOpen' | 'toggle'>;

interface IAlertsProps extends TAlertExtraProps {
    alerts: IAlert[];
    limit?: number;
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
