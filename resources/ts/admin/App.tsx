import React from 'react';
import { Provider } from 'react-redux';
import { Helmet } from 'react-helmet';
import { BrowserRouter as Router } from "react-router-dom";
import { IconContext } from "react-icons";

import Pages from '@admin/pages';
import EchoProvider from '@admin/components/echo/Provider';

import store from '@admin/store/index';
import echoFactory from '@admin/utils/echo/echo';

interface IProps {

}

const App: React.FC<IProps> = ({ }) => {
    return (
        <>
            <Helmet titleTemplate='%s | Same Old Nick' />

            <Provider store={store}>
                <EchoProvider factory={echoFactory}>
                    <IconContext.Provider value={{ className: 'react-icons' }}>
                        <Router>
                            <Pages />
                        </Router>
                    </IconContext.Provider>
                </EchoProvider>
            </Provider>
        </>
    );
}

export default App;
