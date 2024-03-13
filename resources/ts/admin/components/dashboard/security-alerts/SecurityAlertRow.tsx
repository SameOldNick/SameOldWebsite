import React from 'react';
import { FaExternalLinkAlt } from 'react-icons/fa';
import { Badge, Button } from 'reactstrap';

import moment from 'moment';
import S from 'string';

import { TSecurityAlertNotification } from '@admin/pages/protected/dashboard/SecurityAlerts';

interface ISecurityAlertRowProps {
    badgeColor: string;
    alert: TSecurityAlertNotification;
    onViewClicked: () => void;
}

const SecurityAlertRow: React.FC<ISecurityAlertRowProps> = ({ badgeColor, alert, onViewClicked }) => {
    const { data: { issue } } = alert;

    const handleViewClicked = React.useCallback((e: React.MouseEvent) => {
        e.preventDefault();

        onViewClicked();
    }, [onViewClicked]);

    return (
        <>
            <tr>
                <td>
                    <Badge color={badgeColor}>
                        {S(issue.severity).humanize().s}
                    </Badge>
                </td>
                <td>{moment(issue.datetime).fromNow()}</td>
                <td>{S(issue.message).truncate(75).s}</td>
                <td>
                    <Button color='link' title='View more information' onClick={handleViewClicked}>
                        <FaExternalLinkAlt />
                    </Button>
                </td>
            </tr>
        </>
    );
}

export default SecurityAlertRow;
