declare global {
	interface Window {
		accessToken?: IJsonWebToken;
		refreshToken?: IJsonWebToken;
	}
}

export default { };
