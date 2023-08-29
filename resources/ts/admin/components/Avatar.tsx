import React from 'react';
import { ClipLoader } from 'react-spinners';

import { DateTime } from 'luxon';

interface IProps extends Omit<React.HTMLProps<HTMLImageElement>, 'ref'> {
    innerRef?: React.Ref<HTMLImageElement>;
}

interface IState {
    lastRefreshed: DateTime;
}

export default class Avatar extends React.Component<IProps, IState> {
    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
            lastRefreshed: DateTime.now()
        };
    }

    public refresh() {
        this.setState({ lastRefreshed: DateTime.now() });
    }

    render() {
        const { src, innerRef, ...props } = this.props;
        const { lastRefreshed } = this.state;

        if (src === undefined) {
            return (
                <ClipLoader color='#858796' className='img-profile' />
            );
        } else {
            const url = `${src}${src.includes('?') ? '&' : '?'}t=${lastRefreshed.toUnixInteger()}`;

            return (
                <img ref={innerRef} className="img-profile rounded-circle" src={url} {...props} />
            );
        }
    }
}
