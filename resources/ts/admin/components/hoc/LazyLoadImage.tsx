import React from 'react';

interface IProps extends Omit<React.ImgHTMLAttributes<HTMLImageElement>, 'placeholder' | 'onError'> {
    placeholder?: JSX.Element;
    onError?: (e: ErrorEvent) => void;
    retry: number;
}

interface IState {
    loaded: boolean;
    tried: number;
}

export default class LazyLoadImage extends React.Component<IProps, IState> {
    private _img?: HTMLImageElement;

    public static defaultProps: Partial<IProps> = {
        retry: 1
    };

    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
            loaded: false,
            tried: 0
        };

        this.onImgLoaded = this.onImgLoaded.bind(this);
        this.onImgError = this.onImgError.bind(this);
    }

    public componentDidMount() {
        this.loadImage();
    }

    public componentDidUpdate(prevProps: Readonly<IProps>) {
        const { src } = this.props

        if (src !== prevProps.src)
            this.loadImage();
    }

    private onImgLoaded(e: Event) {
        this.setState({ loaded: true });
    }

    private onImgError(e: ErrorEvent) {
        const { onError, retry } = this.props
        const { tried } = this.state

        if (retry === 0 || tried < retry)
            this.loadImage();

        if (onError)
            onError(e);
    }

    public loadImage() {
        const { src } = this.props;

        this.setState(
            (prevState) => ({ loaded: false, tried: prevState.tried + 1 }),
            () => {
                if (this._img !== undefined) {
                    this._img.removeEventListener('load', this.onImgLoaded);
                    this._img.removeEventListener('error', this.onImgError);
                }

                this._img = new Image();

                this._img.addEventListener('load', this.onImgLoaded);
                this._img.addEventListener('error', this.onImgError)

                this._img.src = src || '';
            }
        );
    }

    public render() {
        const { src, placeholder, onError, ...props } = this.props;
        const { loaded } = this.state;

        if (loaded)
            return (
                <img src={src} {...props} />
            );
        else
            return (
                <>
                    {placeholder}
                </>
            );
    }
}
