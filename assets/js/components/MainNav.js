/* global hoverintent:readonly */

export class MainNav {
    constructor() {
    }

    expandSubNav(mainNavItem, subNav) {
        if (mainNavItem) {
            subNav.classList.add('expanded');
            mainNavItem.classList.add('selected');
        }
    }

    collapseSubNav(mainNavItem, subNav) {
        if (mainNavItem) {
            subNav.classList.remove('expanded');
            mainNavItem.classList.remove('selected');
        }
    }

    toggleSubNav(mainNavItem) {
        const section = mainNavItem.dataset.section;
        const subNav = document.querySelector(`.subnav[data-parent="${section}"]`);

        if (subNav) {
            subNav.classList.toggle('expanded');
            mainNavItem.classList.toggle('selected');
        }
    }

    handleClick(event) {
        const link = this.getEventLink(event);

        if (link && link.parentElement.dataset.section) {
            event.preventDefault();
            this.toggleSubNav(link.parentElement);
        }
    }

    getEventLink(clickEvent) {
        if (this.container.contains(clickEvent.target) === false) {
            return null;
        }

        const el = clickEvent.target;

        if (el.nodeName === 'A') {
            return el;
        }

        if (el.parentElement.nodeName === 'A') {
            return el.parentElement;
        }

        return null;
    }

    hoverintent() {
        if (!this.container) {
            throw "Could not find the main nav element.";
        }

        for (const subNav of document.querySelectorAll('.subnav[data-parent]')) {
            const mainNavItem = this.container.querySelector(`li[data-section="${subNav.dataset.parent}"]`);
            hoverintent(mainNavItem, () => this.expandSubNav(mainNavItem, subNav), () => this.collapseSubNav(mainNavItem, subNav));
        }
    }

    init() {
        this.container = document.querySelector('#header__nav');

        if (!this.container) {
            throw "Could not find the main nav element.";
        }

        document.addEventListener('click', e => this.handleClick(e));
    }
}
