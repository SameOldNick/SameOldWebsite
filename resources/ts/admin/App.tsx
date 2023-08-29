import React from 'react';
import { Provider } from 'react-redux';
import { Helmet } from 'react-helmet';
import { BrowserRouter as Router } from "react-router-dom";

import store from '@admin/store/index';
import Pages from '@admin/pages';

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
        return (
            <>
                <Helmet titleTemplate='%s | Same Old Nick' />

                <Provider store={store}>
                    <Router>
                        <Pages />
                    </Router>
                </Provider>
            </>
        );
    }
}
