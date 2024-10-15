declare global {
    interface INotification<TType extends string = string> {
        id: string;
        type: TType;
        notifiable_id: number;
        notifiable_type: string;
        data: string | object;
        created_at: string;
        updated_at: string;
        read_at: string | null;
    }
}

export { };
