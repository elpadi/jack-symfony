export function lazyLoadCss()
{
    for (let link of document.head.querySelectorAll('link[rel="preload"]')) {
        if (link.href.endsWith('css')) {
            link.rel = 'stylesheet';
        }
    }
}
