/**
 * @template T
 * @typedef Skeleton
 * @prop { <D>(promise: Promise<D>, callback: (elem: T, data: D) => void) => Skeleton } onFulfilled
 */

/**
 * Build a skeleton
 * @template {HTMLElement} T
 * @param {T} elem 
 * @returns {Skeleton<T>}
 */
export default function Skeleton(elem) {
    elem.classList.add('loading');

    elem.onFulfilled = (promise, callback) => {
        promise.then(data => {
            callback(elem, data);
            elem.classList.remove('loading')
        });

        return elem;
    }

    return elem;
}