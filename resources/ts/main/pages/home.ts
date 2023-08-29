const scrollspyEl = document.querySelector('#sectionsScrollspyContainer');

scrollspyEl?.addEventListener('activate.bs.scrollspy', (e) => {
    // Get the active link item
    const activeLinkEl = document.querySelector<HTMLAnchorElement>('.sections-submenu a.active');
    const targetEl = document.querySelector<HTMLSpanElement>('.sections-submenu-current');

    if (targetEl && activeLinkEl)
        targetEl.innerHTML = activeLinkEl.innerText;
});
