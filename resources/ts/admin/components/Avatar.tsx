import React from 'react';
import { ClipLoader } from 'react-spinners';

import { DateTime } from 'luxon';

import { createAuthRequest, createUrl } from '@admin/utils/api/factories';
import LazyLoadImage from './hoc/LazyLoadImage';

interface ICurrentUserAvatarProps {
    current: true;
}

interface IUserAvatarProps {
    current: undefined;
    user: IUser;
}

interface ISharedProps extends Omit<React.HTMLProps<HTMLImageElement>, 'ref' | 'src' | 'placeholder' | 'onError'> {
}

type TAvatarProps = (ICurrentUserAvatarProps | IUserAvatarProps) & ISharedProps;

interface IState {
    src?: string;
    lastRefreshed: DateTime;
}

export default class Avatar extends React.Component<TAvatarProps, IState> {
    constructor(props: Readonly<TAvatarProps>) {
        super(props);

        this.state = {
            lastRefreshed: DateTime.now()
        };
    }

    componentDidMount(): void {
        this.fetchAvatarSrc();
    }

    private async fetchAvatarSrc() {
        try {
            let response;

            if (this.props.current === true) {
                response = await createAuthRequest().get<IUser>(`/user`);
            } else {
                response = await createAuthRequest().get<IUser>(`/users/${this.props.user.id}`);
            }

            this.setState({ src: response.data.avatar_url, lastRefreshed: DateTime.now() });
        } catch (err) {
            console.error(err);
        }
    }

    /**
     * Refreshes the avatar.
     *
     * @memberof Avatar
     */
    public refresh() {
        this.fetchAvatarSrc();
    }

    private get src() {
        const { src, lastRefreshed } = this.state;

        if (src !== undefined) {
            return `${src}${src.includes('?') ? '&' : '?'}t=${lastRefreshed.toUnixInteger()}`;
        } else {
            return '';
        }
    }

    render() {
        const { current, ...props } = this.props;

        return (
            <LazyLoadImage
                placeholder={<ClipLoader color='#858796' className='img-profile' />}
                className="img-profile rounded-circle"
                src={this.src}
                {...props}
            />
        );
    }
}
