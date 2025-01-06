import React from 'react';
import { Dropdown, DropdownItem, DropdownMenu, DropdownToggle } from 'reactstrap';
import { FaBan, FaExternalLinkAlt, FaRegCheckCircle, FaRegTimesCircle, FaToolbox, FaTrash } from 'react-icons/fa';

import S from 'string';
import { DateTime } from 'luxon';

import ContactMessage from '@admin/utils/api/models/ContactMessage';

interface IRowProps extends Omit<React.HTMLAttributes<HTMLTableRowElement>, 'children'> {
    selected: boolean;
    message: ContactMessage;
    onSelected: () => void;
    onViewClicked: () => void;
    onMarkUnconfirmedClicked: () => void;
    onMarkConfirmedClicked: () => void;
    onDenyNameClicked: () => void;
    onDenyEmailClicked: () => void;
    onRemoveClicked: () => void;
}

const MessageRow: React.FC<IRowProps> = ({
    selected,
    message,
    onSelected,
    onViewClicked,
    onMarkUnconfirmedClicked,
    onMarkConfirmedClicked,
    onDenyNameClicked,
    onDenyEmailClicked,
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
                        {!['unconfirmed', 'flagged'].includes(message.status) && <DropdownItem onClick={onMarkUnconfirmedClicked}><FaRegTimesCircle />{' '}Mark Unconfirmed</DropdownItem>}
                        {!['confirmed', 'flagged'].includes(message.status) && <DropdownItem onClick={onMarkConfirmedClicked}><FaRegCheckCircle />{' '}Mark Confirmed</DropdownItem>}
                        <DropdownItem onClick={onDenyNameClicked}><FaBan />{' '}Ban Name</DropdownItem>
                        <DropdownItem onClick={onDenyEmailClicked}><FaBan />{' '}Ban E-mail</DropdownItem>
                        <DropdownItem onClick={onRemoveClicked}><FaTrash />{' '}Remove</DropdownItem>
                    </DropdownMenu>
                </Dropdown>
            </td>
        </tr>
    );
}

export default MessageRow;
