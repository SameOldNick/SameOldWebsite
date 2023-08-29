declare global {
    export interface IJsonWebToken {
        access_token: string;
        token_type: 'bearer';
        expires_in: number;
        expires_at: string;
    }
}

export default { };
