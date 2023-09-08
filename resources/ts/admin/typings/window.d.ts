declare global {
	interface Window {
		accessToken?: IJsonWebToken;
		refreshToken?: IJsonWebToken;
	}

    type Timeout = ReturnType<typeof window.setTimeout> | ReturnType<typeof setTimeout>;
    type Interval = ReturnType<typeof window.setInterval> | ReturnType<typeof setInterval>;
}

export default { };
