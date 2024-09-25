import React from 'react';
import { FaEnvelope } from 'react-icons/fa';
import { Dropdown, DropdownToggle, Badge, DropdownMenu, DropdownItem } from 'reactstrap';

import { ConnectedProps, connect } from 'react-redux';
import classNames from 'classnames';
import md5 from 'blueimp-md5';

import { fetchMessages } from '@admin/store/slices/notifications';

import user from '@images/user.png';

const connector = connect(
    ({ notifications: { messages } }: RootState) => ({ stored: messages }),
    { fetchMessages }
);

type TProps = ConnectedProps<typeof connector>;

interface IMessageProps {
    message: IMessage;
}

interface IMessage {
    link: string;
    image: string;
    status?: string;
    text: string;
    author: string;
    timeAgo: string;
    read: boolean;
}

const Message: React.FC<IMessageProps> = ({ message }) => {
    return (
        <DropdownItem href={message.link} className='d-flex align-items-center my-2'>
            <div className="flex-shrink-0">
                <img className="rounded-circle w-rem-2 h-rem-2" src={message.image} alt={message.author} />
                <div className={classNames("status-indicator", message.status ? `bg-${message.status}` : undefined)}></div>
            </div>
            <div className={classNames('flex-grow-1 ms-3', { 'fw-bold': !message.read })}>
                <div className="text-truncate">
                    {message.text}
                </div>
                <div className="small text-gray-500">{`${message.author} · ${message.timeAgo}`}</div>
            </div>
        </DropdownItem>
    );
}

const Messages: React.FC<TProps> = ({ stored, fetchMessages }) => {
    const [open, setOpen] = React.useState(false);

    const buildGravatarUrl = React.useCallback((email: string) => `https://www.gravatar.com/avatar/${md5(email.trim().toLowerCase())}`, []);

    React.useEffect(() => {
        fetchMessages();
    }, []);

    const messages = React.useMemo(() => stored.map((message) => {
        const data = message.getData();
        const address = data.addresses.replyTo.length > 0 ? data.addresses.replyTo[0].address : null;
        const imageUrl = address !== null ? buildGravatarUrl(address) : user;

        return {
            link: '/admin/contact/messages',
            author: address ?? 'Unknown',
            image: imageUrl,
            text: data.subject,
            timeAgo: message.createdAt.toRelative() ?? 'unknown',
            read: message.readAt !== null
        };
    }), [stored]);

    const unreadCount = React.useMemo(() => messages.filter(({ read }) => !read).length, [messages]);

    return (
        <>
            <Dropdown nav className='no-arrow mx-1' isOpen={open} toggle={() => setOpen(!open)}>
                <DropdownToggle nav tag='a' href='#' id="messagesDropdown">
                    <span className='position-relative'>
                        <FaEnvelope className='fa-fw' />
                        {/* Counter - Messages */}

                        {unreadCount > 0 && (
                            <Badge pill color='danger' className='position-absolute top-0 start-100 translate-middle'>
                                {unreadCount}
                            </Badge>
                        )}

                    </span>
                </DropdownToggle>

                {/* Dropdown - User Information */}
                <DropdownMenu end className='shadow animated--grow-in'>
                    <DropdownItem header>Message Center</DropdownItem>

                    {messages.map((message, index) => (
                        <Message key={index} message={message} />
                    ))}

                    <DropdownItem className='text-center small text-gray-500 mt-2' href='/admin/contact/messages'>
                        Read More Messages
                    </DropdownItem>
                </DropdownMenu>
            </Dropdown>
        </>
    );
}

export default connector(Messages);
