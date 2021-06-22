/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.css';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';
import { runWhen } from './utilities/run-when.js';

const $$ = (selector, context = document) => Array.from(context.querySelectorAll(selector));

runWhen(
    () => {
        return ('LazyLoad' in window) && ('IntersectionObserver' in window);
    },
    () => {
        new LazyLoad();
    }
);

$$('#form label').forEach(l => {
    l.parentElement.querySelector('input, textarea').setAttribute('placeholder', l.textContent);
});

const subnavMainNavPairs = $$('.subnav').map(subnav => [subnav, document.querySelector(`#header__nav li[data-section="${subnav.dataset.parent}"]`)]);

subnavMainNavPairs.forEach(([subnav, navItem]) => {
    if (!navItem) {
        console.error('Invalid submenu item.', subnav);
        return;
    }

    navItem.querySelector('a').addEventListener('click', e => {
        e.preventDefault();
        subnav.classList.toggle('expanded');
    });
});

runWhen(
    () => {
        return ('hoverintent' in window);
    },
    () => {
        subnavMainNavPairs.forEach(([subnav, navItem]) => {
            if (!navItem) {
                return;
            }

            hoverintent(
                navItem,
                () => subnav.classList.add('expanded'),
                () => subnav.classList.remove('expanded')
            );
        });
    }
);

function applyLazyLoadedStyles()
{
    for (let link of document.head.querySelectorAll('link[rel="preload"]')) {
        if (link.href.endsWith('css')) {
            link.rel = 'stylesheet';
        }
    }
}

function onLoad()
{
    applyLazyLoadedStyles();
}

document.readyState === 'complete' ? onLoad : window.addEventListener('load', loadEvent => onLoad());
