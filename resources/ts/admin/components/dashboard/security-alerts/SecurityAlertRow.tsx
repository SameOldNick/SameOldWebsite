import React from 'react';
import { FaExternalLinkAlt } from 'react-icons/fa';
import { Badge, Button } from 'reactstrap';

import moment from 'moment';
import S from 'string';

import SecurityNotification from '@admin/utils/api/models/notifications/SecurityNotification';

interface ISecurityAlertRowProps {
    badgeColor: string;
    notification: SecurityNotification;
    onViewClicked: () => void;
}

const SecurityAlertRow: React.FC<ISecurityAlertRowProps> = ({ badgeColor, notification, onViewClicked }) => {
    const { issue } = notification.getData();

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
