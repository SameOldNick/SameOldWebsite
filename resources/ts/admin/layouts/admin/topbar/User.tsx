import React from 'react';
import { FaCaretDown, FaKey, FaSignOutAlt, FaUser } from 'react-icons/fa';
import { connect, ConnectedProps } from 'react-redux';
import { Dropdown, DropdownItem, DropdownMenu, DropdownToggle } from 'reactstrap';
import { IconContext } from 'react-icons';
import { bindActionCreators } from 'redux';

import classNames from 'classnames';

import Avatar from '@root/admin/components/Avatar';
import LogoutModal from '@root/admin/components/LogoutModal';

import accountSlice  from '@admin/store/slices/account';
import { createAuthRequest } from '@admin/utils/api/factories';
import CurrentAvatar from '@admin/components/hoc/CurrentAvatar';

interface IProps {

}

interface IState {
    open: boolean;
    logoutModal: boolean;
}

const connector = connect(
    ({ account }: RootState) => ({ account }),
    (dispatch) => bindActionCreators({ dispatchAuthStage: accountSlice.actions.authStage }, dispatch)
);

type TProps = ConnectedProps<typeof connector> & IProps;

export default connector(class User extends React.Component<TProps, IState> {
    constructor(props: Readonly<TProps>) {
        super(props);

        this.state = {
            open: false,
            logoutModal: false
        };

        this.closeLogoutModal = this.closeLogoutModal.bind(this);
        this.logout = this.logout.bind(this);
    }

    public componentDidUpdate(props: Readonly<TProps>) {
        const { account } = this.props;

        if (props.account.stage !== account.stage && account.stage.stage === 'none') {
            // Redirect to home page (externally)
            window.location.href = '/';
        }
    }

    private closeLogoutModal() {
        this.setState({ logoutModal: false });
    }

    private async logout() {
        const response = await createAuthRequest().post('logout', {});

        this.props.dispatchAuthStage({ stage: 'none' });
    }

    public render() {
        const { account: { user } } = this.props;
        const { open, logoutModal } = this.state;

        return (
            <>
                <Dropdown nav className='no-arrow me-md-3' isOpen={open} toggle={() => this.setState((prevState) => ({ open: !prevState.open }))}>
                    <DropdownToggle nav tag='a' href='#' id="userDropdown">
                        <CurrentAvatar />
                        <span className={classNames("ms-2 d-none d-lg-inline text-gray-600 small", { placeholder: user === undefined })}>
                            {user !== undefined && <>{user.email} <FaCaretDown /></>}
                        </span>
                    </DropdownToggle>

                    {/* Dropdown - User Information */}
                    <DropdownMenu end className='shadow animated--fade-in'>
                        <IconContext.Provider value={{ className: 'fa-sm fa-fw me-2 text-gray-400' }}>
                            <DropdownItem href='#'>
                                <FaUser />
                                Profile
                            </DropdownItem>
                            <DropdownItem href='#'>
                                <FaKey />
                                Change Password
                            </DropdownItem>
                            <DropdownItem href='#' onClick={() => this.setState({ logoutModal: true })}>
                                <FaSignOutAlt />
                                Logout
                            </DropdownItem>
                        </IconContext.Provider>
                    </DropdownMenu>
                </Dropdown>

                <LogoutModal show={logoutModal} onLogout={this.logout} onCancel={this.closeLogoutModal} />
            </>
        );
    }
});
