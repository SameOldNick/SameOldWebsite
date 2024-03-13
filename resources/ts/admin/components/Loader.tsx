import React from 'react';
import { BounceLoader, RotateLoader } from 'react-spinners';

import classNames from 'classnames';

interface IDisplayPage {
    type: 'page';
    show: boolean;
}

interface IDisplayOverElement {
    type: 'over-element';
    wrapper?: boolean;
}

type TDisplays = IDisplayPage | IDisplayOverElement;

interface IProps {
    display: TDisplays;
}

const Loader: React.FC<IProps> = ({ display }) => {
    const renderPage = React.useCallback(({ show }: IDisplayPage) => {
        return (
            <div className={classNames('loader', { show })}>
                <div className="loader-content">
                    <span className="visually-hidden">Loading...</span>
                    <RotateLoader color='white' role="status" />
                </div>
            </div>
        );
    }, []);

    const renderOverElement = React.useCallback(({ wrapper }: IDisplayOverElement) => {
        const inner = (
            <div className='position-absolute top-50 start-50 translate-middle' style={{ zIndex: 9999 }}>
                <BounceLoader />
            </div>
        );

        if (wrapper) {
            return (
                <div className='position-relative'>
                    {inner}
                </div>
            );
        } else {
            return (
                <>
                    {inner}
                </>
            );
        }
    }, []);

    if (display.type === 'page')
        return renderPage(display);
    else
        return renderOverElement(display);
}

export default Loader;
