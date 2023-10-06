import React from 'react';
import { Provider } from 'react-redux';
import { Helmet } from 'react-helmet';
import { BrowserRouter as Router } from "react-router-dom";
import { IconContext } from "react-icons";

import store from '@admin/store/index';
import Pages from '@admin/pages';

interface IProps {

}

const App: React.FC<IProps> = ({ }) => {
    return (
        <>
            <Helmet titleTemplate='%s | Same Old Nick' />

            <Provider store={store}>
                <IconContext.Provider value={{ className: 'react-icons' }}>
                    <Router>
                        <Pages />
                    </Router>
                </IconContext.Provider>
            </Provider>
        </>
    );
}

export default App;
