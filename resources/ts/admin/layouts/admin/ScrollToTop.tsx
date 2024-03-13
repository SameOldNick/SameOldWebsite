import React from 'react';
import { FaAngleUp } from 'react-icons/fa';
import animateScrollTo from 'animated-scroll-to';

interface IProps {
    scrollTo: string;
}

const shouldDisplayButton = () => window.scrollY > 100;

const ScrollToTop: React.FC<IProps> = ({ scrollTo }) => {
    const [show, setShow] = React.useState(shouldDisplayButton());

    const onScroll = React.useCallback(() => {
        const shouldDisplay = shouldDisplayButton();

        if (shouldDisplay && !show) {
            setShow(true);
        } else if (!shouldDisplay && show) {
            setShow(false);
        }
    }, [show]);

    React.useEffect(() => {
        document.addEventListener('scroll', onScroll);

        return () => document.removeEventListener('scroll', onScroll);
    });

    const onClick = React.useCallback((e: React.MouseEvent<HTMLAnchorElement>) => {
        e.preventDefault();

        const el = document.getElementById(scrollTo);
        const opts = { speed: 350 };

        if (el !== null)
            animateScrollTo(el, opts);
        else
            animateScrollTo(0, opts);
    }, [scrollTo]);

    return (
        <>
            <a
                className="scroll-to-top rounded animated--fade-in"
                href={`#${scrollTo}`}
                onClick={onClick}
                style={{ opacity: show ? 1 : 0 }}
            >
                <FaAngleUp />
            </a>
        </>
    );
}

export default ScrollToTop;
