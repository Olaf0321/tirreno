const addDays = (date, days) => {
    const dateCopy = new Date(date);
    dateCopy.setDate(date.getDate() + days);

    return dateCopy;
};

const addHours = (date, hours) => {
    hours = hours * 60 * 60 * 1000;

    const dateCopy = new Date(date);
    dateCopy.setTime(date.getTime() + hours);

    return dateCopy;
};

//https://stackoverflow.com/a/12550320
const padZero = (n, s = 2) => {
    return (s > 0) ? ('000'+n).slice(-s) : (n+'000').slice(0, -s);
};

const notificationTime = () => {
    const dt        = new Date();
    const day       = padZero(dt.getDate());
    const month     = padZero(dt.getMonth() + 1);
    const year      = padZero(dt.getFullYear(), 4);
    const hours     = padZero(dt.getHours());
    const minutes   = padZero(dt.getMinutes());
    const seconds   = padZero(dt.getSeconds());

    return `[${day}/${month}/${year} ${hours}:${minutes}:${seconds}]`;
};

const format_ymdThis = (dt, useTime) => {
    let m = dt.getMonth() + 1;
    let d = dt.getDate();
    let y = dt.getFullYear();
    let h = useTime ? dt.getHours()   : 0;
    let i = useTime ? dt.getMinutes() : 0;
    let s = useTime ? dt.getSeconds() : 0;

    m = padZero(m);
    d = padZero(d);
    y = padZero(y, 4);
    h = padZero(h);
    i = padZero(i);
    s = padZero(s);

    const dateStr = `${y}-${m}-${d}T${h}:${i}:${s}`;
    return dateStr;
};

export {format_ymdThis, notificationTime, padZero, addDays, addHours};
