/**
 * Build a skeleton for an element
 * @param {HTMLElement} elem 
 * @returns {HTMLElement}
 */
export default function skeleton(elem) {
    elem.classList.add('loading');
    
    /**
     * 
     * @param {Promise} promise 
     * @returns 
     */
    elem.stopLoadingOnFulfilled = (promise) => {
        return elem;
    }

    elem.classList.remove()
    return elem;
}