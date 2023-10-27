const updateTogglerMenu = () => {
    // Get the active link item
    const activeLinkEl = document.querySelector<HTMLAnchorElement>('.sections-submenu a.active');
    const targetEl = document.querySelector<HTMLSpanElement>('.sections-submenu-current');

    if (targetEl && activeLinkEl)
        targetEl.innerHTML = activeLinkEl.innerText;
}

const scrollToElementWithOffset = (element: Element, offset: number) => {
    const rect = element.getBoundingClientRect();
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const targetTop = rect.top + scrollTop - offset;

    window.scrollTo({
        top: targetTop,
        behavior: 'smooth'
    });
}

// Find element with x, y coords closest to 0, 0
function findClosestElementToZero(elements: Element[]) {
    if (elements.length === 0) {
        return null; // No elements to check
    }

    // Initialize variables to track the closest element and its distance
    let closestElement = elements[0];
    let closestDistance = getDistanceFromOrigin(closestElement);

    // Iterate through the elements and update the closest element if needed
    for (let i = 1; i < elements.length; i++) {
        const currentElement = elements[i];
        const currentDistance = getDistanceFromOrigin(currentElement);

        if (currentDistance < closestDistance) {
            closestElement = currentElement;
            closestDistance = currentDistance;
        }
    }

    return closestElement;
}

function getDistanceFromOrigin(element: Element) {
    // Get the X and Y coordinates of the element
    const rect = element.getBoundingClientRect();
    const x = rect.left;
    const y = rect.top;

    // Calculate the Euclidean distance from (0, 0)
    return Math.sqrt(x * x + y * y);
}

$(() => {
    if ($('.page-home').length === 0)
        return;

    const menuLinkEls: Record<string, HTMLAnchorElement[]> = {};
    const targetEls: Element[] = [];

    $<HTMLAnchorElement>('a', '.page-home .navbar.sections-submenu').each(function () {
        const href = this.getAttribute('href');

        if (!href || !href.startsWith('#'))
            return;

        if (href in menuLinkEls) {
            menuLinkEls[href].push(this);
        } else {
            menuLinkEls[href] = [this];
        }

        const targetEl = document.querySelector(href);

        if (targetEl)
            targetEls.push(targetEl);
    });

    const setActiveLink = (href: string, associatedEls: HTMLAnchorElement[]) => {
        const allLinkEls = Object.values(menuLinkEls).flat();

        allLinkEls.forEach((linkEl) => linkEl.classList.remove('active'));

        associatedEls.forEach((linkEl) => linkEl.classList.add('active'));

        updateTogglerMenu();
    }

    let updateMenuTimeout: Timeout | null;

    $(window).on('DOMContentLoaded load resize scroll', (e) => {
        if (updateMenuTimeout) {
            clearTimeout(updateMenuTimeout);
            updateMenuTimeout = null;
        }

        updateMenuTimeout = setTimeout(() => {
            const visibleEl = findClosestElementToZero(targetEls);

            let foundActiveLink = false;

            const menuLinkEntries = Object.entries(menuLinkEls);

            for (const [href, els] of menuLinkEntries) {
                const targetEl = document.querySelector(href);

                if (targetEl && visibleEl?.isEqualNode(targetEl)) {
                    setActiveLink(href, els);
                    foundActiveLink = true;
                    break;
                }
            }

            if (!foundActiveLink) {
                const [href, els] = menuLinkEntries[0];

                setActiveLink(href, els);
            }
        }, 100);
    });

    // Handles scrolling to element when sub-menu item is clicked
    const menuHeightOffset = $('nav#topNavbar').outerHeight(true) || 0;

    $<HTMLAnchorElement>('a', '.page-home .navbar.sections-submenu').each(function () {
        const href = this.getAttribute('href');

        if (!href || !href.startsWith('#'))
            return;

        const targetEl = document.querySelector(href);

        if (targetEl) {
            $(this).on('click', (e) => {
                e.preventDefault();

                scrollToElementWithOffset(targetEl, menuHeightOffset);
            })
        }
    });
});
