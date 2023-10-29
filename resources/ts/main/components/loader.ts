import $ from "jquery";

const clearLoaderCallback = () => $('.loader').removeClass('show');

let loaderTimeout: ReturnType<typeof setTimeout> | undefined = setTimeout(() => {
	clearLoaderCallback();
	loaderTimeout = undefined;
}, 7 * 1000);

$(window).on('load', () => {
	if (loaderTimeout) {
		clearTimeout(loaderTimeout);
		loaderTimeout = undefined;
	}

	clearLoaderCallback();
});
