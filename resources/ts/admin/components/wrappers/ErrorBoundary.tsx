import { Component, ErrorInfo, PropsWithChildren } from "react";

interface FallbackErrorProps {
    error?: any;
    resetErrorBoundary?: () => void;
}

interface ErrorBoundaryProps extends PropsWithChildren {
    fallback: React.ComponentType<FallbackErrorProps>;
}

interface State {
    error?: any;
}

class ErrorBoundary extends Component<ErrorBoundaryProps, State> {
    constructor(props: Readonly<ErrorBoundaryProps>) {
        super(props);

        this.state = {};

        this.resetErrorBoundary = this.resetErrorBoundary.bind(this);
    }

    static getDerivedStateFromError(error: any) {
        // Update state so the next render will show the fallback UI.
        return { error };
    }

    componentDidCatch(error: Error, info: ErrorInfo) {
        // Example "componentStack":
        //   in ComponentThatThrows (created by App)
        //   in ErrorBoundary (created by App)
        //   in div (created by App)
        //   in App
        logger.error('An uncaught error occurred.', [error, info.componentStack]);
    }

    resetErrorBoundary() {
        this.setState({ error: undefined });
    }

    render() {
        if (this.state.error) {
            const FallbackComponent = this.props.fallback;

            return <FallbackComponent error={this.state.error} resetErrorBoundary={this.resetErrorBoundary} />;
        }

        return this.props.children;
    }
}

export default ErrorBoundary;
export type { ErrorBoundaryProps, FallbackErrorProps };