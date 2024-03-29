import React from 'react';
import { Dropdown, DropdownItem, DropdownMenu, DropdownToggle } from 'reactstrap';
import { FaExternalLinkAlt, FaRegCheckCircle, FaRegTimesCircle, FaToolbox, FaTrash } from 'react-icons/fa';

import S from 'string';

import ContactMessage from '@admin/utils/api/models/ContactMessage';

interface IRowProps {
    message: ContactMessage;
    onViewClicked: () => void;
    onMarkUnconfirmedClicked: () => void;
    onMarkConfirmedClicked: () => void;
    onRemoveClicked: () => void;
}

const MessageRow: React.FC<IRowProps> = ({ message, onViewClicked, onMarkUnconfirmedClicked, onMarkConfirmedClicked, onRemoveClicked }) => {
    const [actionDropdown, setActionDropdown] = React.useState(false);

    return (
        <tr>
            <td>{message.message.uuid}</td>
            <td>{message.displayName}</td>
            <td>{S(message.message.message).truncate(75).s}</td>
            <td title={message.createdAt.toISOString()}>
                {message.createdAt.fromNow()}
            </td>
            <td>{S(message.status).humanize().s}</td>
            <td>
                <Dropdown group toggle={() => setActionDropdown((prev) => !prev)} isOpen={actionDropdown}>
                    <DropdownToggle caret color='primary'>
                        <FaToolbox />{' '}
                        Actions
                    </DropdownToggle>
                    <DropdownMenu>
                        <DropdownItem onClick={onViewClicked}><FaExternalLinkAlt />{' '}View</DropdownItem>
                        {message.status !== 'unconfirmed' && <DropdownItem onClick={onMarkUnconfirmedClicked}><FaRegTimesCircle />{' '}Mark Unconfirmed</DropdownItem>}
                        {message.status !== 'confirmed' && <DropdownItem onClick={onMarkConfirmedClicked}><FaRegCheckCircle />{' '}Mark Confirmed</DropdownItem>}
                        <DropdownItem onClick={onRemoveClicked}><FaTrash />{' '}Remove</DropdownItem>
                    </DropdownMenu>
                </Dropdown>
            </td>
        </tr>
    );
}

export default MessageRow;
