import React from 'react';
import { Button } from 'reactstrap';
import { FaEnvelope, FaEnvelopeOpen, FaExternalLinkAlt } from 'react-icons/fa';

import S from 'string';

interface IRowProps {
    notification: TMessageNotification;
    onViewClicked: () => void;
    onMarkReadClicked: () => void;
    onMarkUnreadClicked: () => void;
}

const MessageRow: React.FC<IRowProps> = ({ notification, onViewClicked, onMarkReadClicked, onMarkUnreadClicked }) => {
    const address = notification.data.addresses.replyTo.length > 0 ? notification.data.addresses.replyTo[0].address : '(unknown)';
    const message = S(notification.data.view.text).truncate(75).s;

    const isRead = React.useMemo(() => notification.read_at !== null, [notification]);
    const styles: React.CSSProperties = React.useMemo(() => !isRead ? { fontWeight: 'bold' } : {}, [isRead]);

    return (
        <tr>
            <td style={styles}>{notification.id}</td>
            <td style={styles}>{address}</td>
            <td style={styles}>{message}</td>
            <td>
                <Button color='primary' className='me-1' onClick={onViewClicked}>
                    <FaExternalLinkAlt />
                </Button>
                {
                    !isRead ? (
                        <Button color='primary' title='Mark as unread' onClick={onMarkReadClicked}>
                            <FaEnvelopeOpen />
                        </Button>
                    ) : (
                        <Button color='primary' title='Mark as read' onClick={onMarkUnreadClicked}>
                            <FaEnvelope />
                        </Button>
                    )
                }

            </td>

        </tr>
    );
}

export default MessageRow;
