const debounce = callback => {
    let timeout;

    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => callback.apply(this, args), 500);
    };
};

export {debounce};
