export function runWhen(conditionalFn, callback, tries = 5, timeout = 100) {
    const attempt = function() {
        if (conditionalFn()) {
            return callback();
        }
        tries--;
        if (tries > 0) {
            setTimeout(attempt, timeout);
        }
    };
    setTimeout(attempt, timeout);
}
