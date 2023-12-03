declare global {
    interface INotification<TType extends string = string, TData extends object = Record<string, any>> {
        id: string;
        type: TType;
        notifiable_id: number;
        notifiable_type: string;
        data: TData;
        created_at: string;
        updated_at: string;
        read_at: string | null;
    }

    interface IAddress {
        name: string | null;
        address: string;
    }

    interface IMessageData {
        addresses: Record<'to' | 'cc' | 'bcc' | 'replyTo', IAddress[]>;
        subject: string;
        view: Record<'html' | 'text', string>;
        type: string;
    }

    type TMessageNotification = INotification<'6414fd8c-847a-492b-a919-a5fc539456e8', IMessageData>;
}

export { };
