document.addEventListener("DOMContentLoaded", function () {
    const header = document.querySelector<HTMLElement>("header");

    if (!header) {
        console.error('Unable to find top header.');
        return;
    }

    const headerHeight = header.offsetHeight;

    const spacer = document.querySelector<HTMLElement>('header + #headerSpacer');

    if (spacer) {
        spacer.style.paddingTop = headerHeight + "px";
    } else {
        const spacerEl = document.createElement('div');

        spacerEl.id = 'headerSpacer';
        spacerEl.style.paddingTop = headerHeight + "px";

        header.after(spacerEl);
    }
});
