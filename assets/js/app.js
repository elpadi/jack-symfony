/* global LazyLoad:readonly */

import '../css/app.css';

import { runWhen } from './utilities/runWhen.js';
import { MainNav } from './components/MainNav.js';
import { onLoad } from './onLoad.js';

const $$ = (selector, context = document) => Array.from(context.querySelectorAll(selector));

runWhen(() => ('LazyLoad' in window) && ('IntersectionObserver' in window), () => new LazyLoad());

$$('#form label').forEach(l => {
    l.parentElement.querySelector('input, textarea').setAttribute('placeholder', l.textContent);
});

const mainNav = new MainNav();
mainNav.init();

runWhen(() => 'hoverintent' in window, () => mainNav.hoverintent());

document.readyState === 'complete' ? onLoad : window.addEventListener('load', () => onLoad());
