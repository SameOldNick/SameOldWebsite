import React from 'react';
import { Dropdown, DropdownItem, DropdownMenu, DropdownToggle } from 'reactstrap';
import { FaBan, FaExternalLinkAlt, FaRegCheckCircle, FaRegTimesCircle, FaToolbox, FaTrash } from 'react-icons/fa';

import S from 'string';

import ContactMessage from '@admin/utils/api/models/ContactMessage';
import { DateTime } from 'luxon';

interface IRowProps extends Omit<React.HTMLAttributes<HTMLTableRowElement>, 'children'> {
    selected: boolean;
    message: ContactMessage;
    onSelected: () => void;
    onViewClicked: () => void;
    onMarkUnconfirmedClicked: () => void;
    onMarkConfirmedClicked: () => void;
    onDenyClicked: () => void;
    onRemoveClicked: () => void;
}

const MessageRow: React.FC<IRowProps> = ({
    selected,
    message,
    onSelected,
    onViewClicked,
    onMarkUnconfirmedClicked,
    onMarkConfirmedClicked,
    onDenyClicked,
    onRemoveClicked,
    ...props
}) => {
    const [actionDropdown, setActionDropdown] = React.useState(false);

    return (
        <tr {...props}>
            <td>
                <input
                    type="checkbox"
                    checked={selected}
                    onChange={onSelected}
                />
            </td>
            <td>{message.message.uuid}</td>
            <td>{message.displayName}</td>
            <td>{S(message.message.message).truncate(75).s}</td>
            <td title={message.createdAt.toLocaleString(DateTime.DATETIME_FULL)}>
                {message.createdAt.toRelative()}
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
                        <DropdownItem onClick={onDenyClicked}><FaBan />{' '}Deny E-mail</DropdownItem>
                        <DropdownItem onClick={onRemoveClicked}><FaTrash />{' '}Remove</DropdownItem>
                    </DropdownMenu>
                </Dropdown>
            </td>
        </tr>
    );
}

export default MessageRow;
