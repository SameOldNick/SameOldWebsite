import React from 'react';
import { Provider } from 'react-redux';
import { Helmet } from 'react-helmet';
import { BrowserRouter as Router } from "react-router-dom";
import { IconContext } from "react-icons";

import Pages from '@admin/pages';
import EchoProvider from '@admin/components/echo/Provider';
import NotificationProvider from '@admin/components/notifications/NotificationProvider';

import storeFactory from '@admin/store/index';
import echoFactory from '@admin/utils/echo/echo';
import { setStore } from '@admin/utils/api/factories';

interface IProps {

}

const App: React.FC<IProps> = ({ }) => {
    const store = storeFactory();

    setStore(store);

    return (
        <>
            <Helmet titleTemplate='%s | Same Old Nick' />

            <Provider store={store}>
                <EchoProvider factory={echoFactory}>
                    <NotificationProvider delay={50000}>
                        <IconContext.Provider value={{ className: 'react-icons' }}>
                            <Router>
                                <Pages />
                            </Router>
                        </IconContext.Provider>
                    </NotificationProvider>
                </EchoProvider>
            </Provider>

        </>
    );
}

export default App;
