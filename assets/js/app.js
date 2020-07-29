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
    l.nextElementSibling.setAttribute('placeholder', l.textContent);
});

