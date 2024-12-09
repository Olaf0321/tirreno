const fireEvent = (name, data) => {
    const details = {detail: data};
    const event = new CustomEvent(name, details);

    dispatchEvent(event);
};

export {fireEvent};
