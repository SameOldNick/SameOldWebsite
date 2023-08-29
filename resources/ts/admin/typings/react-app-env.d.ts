/// <reference types="react-scripts" />
declare namespace NodeJS {
	interface ProcessEnv {
		NODE_ENV: 'development' | 'production' | 'test';
		PUBLIC_URL: string;
		API_URL: string;
	}
}