import React from 'react';
import ReactClient from 'react-dom/client';

export interface IPromptModalProps<TResult = void> {
    onSuccess: (result: TResult) => Promise<void>;
    onCancelled: (reason?: any) => void;
}

export default function awaitModalPrompt<TResult, TModalProps extends IPromptModalProps<TResult> = IPromptModalProps<TResult>>(
    Component: React.FC<IPromptModalProps<TResult>> | React.FC<TModalProps>,
    extraProps?: Omit<TModalProps, keyof IPromptModalProps>
) {
    return new Promise<TResult>((resolve, reject) => {
        const hideModal = () => {
            root.unmount();
            el.remove();
        }

        const handleSuccess = (result: TResult) => {
            hideModal();

            resolve(result);
        }

        const handleCancelled = (reason?: any) => {
            hideModal();

            reject(reason);
        }

        const el = document.createElement('div');

        document.body.appendChild(el);

        const root = ReactClient.createRoot(el);

        const props = { onSuccess: handleSuccess, onCancelled: handleCancelled, ...extraProps } as TModalProps;

        root.render(<Component {...props} />);
    });
}

