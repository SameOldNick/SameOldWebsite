import React from 'react';
import { FaCaretDown, FaHome, FaKey, FaSignOutAlt, FaUser } from 'react-icons/fa';
import { connect, ConnectedProps } from 'react-redux';
import { Dropdown, DropdownItem, DropdownMenu, DropdownToggle } from 'reactstrap';
import { IconContext } from 'react-icons';
import { bindActionCreators } from 'redux';

import classNames from 'classnames';

import Avatar from '@admin/components/UserAvatar';
import LogoutModal from '@admin/components/modals/LogoutModal';

import accountSlice from '@admin/store/slices/account';
import { createAuthRequest } from '@admin/utils/api/factories';
import awaitModalPrompt from '@admin/utils/modals';

interface IProps {

}

const connector = connect(
    ({ account }: RootState) => ({ account }),
    (dispatch) => bindActionCreators({ dispatchAuthStage: accountSlice.actions.authStage }, dispatch)
);

type TProps = ConnectedProps<typeof connector> & IProps;

const User: React.FC<TProps> = ({ account: { user, stage }, dispatchAuthStage }) => {
    const [open, setOpen] = React.useState(false);

    const logout = React.useCallback(async () => {
        await createAuthRequest().post('logout', {});

        dispatchAuthStage({ stage: 'none' });
    }, [dispatchAuthStage]);

    const handleLogoutButtonClicked = React.useCallback(async () => {
        try {
            await awaitModalPrompt(LogoutModal);

            await logout();
        } catch (err) {

        }

    }, [logout]);

    React.useEffect(() => {
        if (stage.stage === 'none') {
            // Redirect to home page (externally)
            window.location.href = '/';
        }
    }, [stage]);

    return (
        <>
            <Dropdown nav className='no-arrow me-md-3' isOpen={open} toggle={() => setOpen((prev) => !prev)}>
                <DropdownToggle nav tag='a' href='#' id="userDropdown">
                    <Avatar user='current' />
                    <span className={classNames("ms-2 d-none d-lg-inline text-gray-600 small", { placeholder: user === undefined })}>
                        {user !== undefined && <>{user.email} <FaCaretDown /></>}
                    </span>
                </DropdownToggle>

                {/* Dropdown - User Information */}
                <DropdownMenu end className='shadow animated--fade-in'>
                    <IconContext.Provider value={{ className: 'fa-sm fa-fw me-2 text-gray-400' }}>
                        <DropdownItem href='/' target='_blank'>
                            <FaHome />
                            Main Site
                        </DropdownItem>
                        <DropdownItem href='/user' target='_blank'>
                            <FaUser />
                            Profile
                        </DropdownItem>
                        <DropdownItem href='/user/password' target='_blank'>
                            <FaKey />
                            Change Password
                        </DropdownItem>
                        <DropdownItem href='#' onClick={handleLogoutButtonClicked}>
                            <FaSignOutAlt />
                            Logout
                        </DropdownItem>
                    </IconContext.Provider>
                </DropdownMenu>
            </Dropdown>
        </>
    );
}

export default connector(User);
