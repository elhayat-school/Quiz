/**
 *
 * @param {string} href
 */
const softRedirect = (href) => {
    history.pushState({}, "", href);
    const softRedirectEvent = new PopStateEvent("popstate");
    window.dispatchEvent(softRedirectEvent);
};

export default softRedirect;
