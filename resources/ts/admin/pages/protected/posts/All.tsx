import React from 'react';
import { Helmet } from 'react-helmet';

import Heading from '@admin/layouts/admin/Heading';

interface IProps {

}

interface IState {
}

export default class extends React.Component<IProps, IState> {
    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
        };
    }

    public render() {
        const { } = this.props;
        const { } = this.state;

        return (
            <>
                <Helmet>
                    <title>All Posts</title>
                </Helmet>

                <Heading title='All Posts' />
            </>
        );
    }
}
