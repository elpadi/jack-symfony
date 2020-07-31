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

runWhen(
    () => {
        return ('hoverintent' in window);
    },
    () => {
        $$('.subnav').forEach(subnav => {
            const navItem = document.querySelector(`#header__nav li[data-section="${subnav.dataset.parent}"]`);
            hoverintent(
                navItem,
                () => subnav.classList.add('expanded'),
                () => subnav.classList.remove('expanded')
            );
        });
    }
);

