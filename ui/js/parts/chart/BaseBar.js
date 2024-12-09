import {BaseChart}  from './BaseChart.js?v=2';
import {COLOR_GREEN} from '../utils/Constants.js?v=2';

export class BaseBarChart extends BaseChart {

    getOptions() {
        // save/restore cursor and tooltip state across re-inits
        let cursLeft = -10;
        let cursTop = -10;

        const cursorMemo = {
            set: (left, top) => {
                cursLeft = left;
                cursTop = top;
            },
            get: () => ({
                left: cursLeft,
                top: cursTop,
                y: false,

                drag: {
                    x: false,
                    y: false
                }
            })
        };

        const tooltipsPlugin = this.tooltipsPlugin({cursorMemo});

        const { linear, stepped, bars, spline, spline2 } = uPlot.paths;

        const lineInterpolations = {
            linear:     0,
            stepAfter:  1,
            stepBefore: 2,
            spline:     3,
        //	spline2:    4,
        };

        const drawStyles = {
            line:      0,
            bars:      1,
            points:    2,
            barsLeft:  3,
            barsRight: 4,
        };

        // generate bar builder with 60% bar (40% gap) & 100px max bar width
        const _bars60_100   = bars({size: [0.6, 100]});
        const _bars100Left  = bars({size: [1], align:  1});
        const _bars100Right = bars({size: [1], align: -1});
        const _stepBefore   = stepped({align: -1}); //, ascDesc: true
        const _stepAfter    = stepped({align:  1}); //, ascDesc: true
        const _linear       = linear();
        const _spline       = spline();
        //	const _spline2      = spline2();

        function paths(u, seriesIdx, idx0, idx1, extendGap, buildClip) {
            let s = u.series[seriesIdx];
            let style = s.drawStyle;
            let interp = s.lineInterpolation;

            let renderer = (
                style == drawStyles.line ? (
                    interp == lineInterpolations.linear     ? _linear :
                        interp == lineInterpolations.stepAfter  ? _stepAfter :
                            interp == lineInterpolations.stepBefore ? _stepBefore :
                                interp == lineInterpolations.spline     ? _spline :
                                //	interp == lineInterpolations.spline2    ? _spline2 :
                                    null
                ) :
                    style == drawStyles.bars ? (
                        _bars60_100
                    ) :
                        style == drawStyles.barsLeft ? (
                            _bars100Left
                        ) :
                            style == drawStyles.barsRight ? (
                                _bars100Right
                            ) :
                                style == drawStyles.points ? (
                                    () => null
                                ) : () => null
            );

            return renderer(u, seriesIdx, idx0, idx1, extendGap, buildClip);
        }

        let cfg = {
            drawStyle: drawStyles.bars,
            lineInterpolation: null,
            stroke: '#2b2a3d'
        };

        let xAxeConig = {
            scale: 'DAY',
            stroke: '#8180a0',
            grid: {
                width: 1 / devicePixelRatio,
                stroke: '#2b2a3d',
            },
            ticks: {
                width: 1 / devicePixelRatio,
                stroke: '#2b2a3d',
            },
            values: [
                //Copied from https://github.com/leeoniya/uPlot/tree/master/docs#axis--grid-opts
                // tick incr          default           year                             month    day                        hour     min                sec       mode
                [3600 * 24,         '{D}/{M}',        '\n{YYYY}',                      null,    null,                      null,    null,              null,        1]
            ]
        };

        const opts = {
            width: 995,
            height: 200,
            tzDate: ts => uPlot.tzDate(new Date(ts * 1e3), 'Etc/UTC'),

            legend: {
                show: false
            },

            cursor: cursorMemo.get(),
            plugins: [tooltipsPlugin],

            scales: {
                x: {
                    time: false,
                },
            },
            axes: [
                xAxeConig,
                {
                    stroke: '#8180a0',
                    values: (u, vals, space) => vals.map(v => this.formatKiloValue(u, v)),
                    grid: {
                        width: 1 / devicePixelRatio,
                        stroke: '#2b2a3d',
                    },
                    ticks: {
                        width: 1 / devicePixelRatio,
                        stroke: '#2b2a3d',
                    }
                },
            ],


            series: [
                {
                    label: 'Day',
                    scale: 'DAY',
                    value: '{YYYY}-{MM}-{DD}'
                },
                Object.assign({
                    label: 'Total events',
                    width: -1 / devicePixelRatio,
                    drawStyle: drawStyles.points,
                    lineInterpolation: null,
                    paths,
                }, {
                    drawStyle:         cfg.drawStyle,
                    lineInterpolation: cfg.lineInterpolation,
                    stroke: COLOR_GREEN,
                    fill: COLOR_GREEN
                }),
            ]
        };

        return opts;
    }

    tooltipsPlugin(opts) {
        let seriestt;

        function init(u, opts, data) {
            let over = u.over;

            let tt = document.createElement('div');
            tt.className = 'tooltipline';
            tt.textContent = '';
            tt.style.pointerEvents = 'none';
            tt.style.position = 'absolute';
            tt.style.background = 'rgba(0,0,0,0.5)';
            over.appendChild(tt);
            seriestt = tt;

            over.addEventListener('mouseleave', () => {
                if (!u.cursor._lock) {
                    tt.style.display = 'none';
                }
            });

            over.addEventListener('mouseenter', () => {
                const display = u.data.length > 1 ? null : 'none';

                tt.style.display = display;
            });

            if (u.cursor.left < 0)
                tt.style.display = 'none';
            else
                tt.style.display = null;
        }

        function setCursor(u) {
            const {left, idx} = u.cursor;

            if(opts && opts.cursorMemo) {
                opts.cursorMemo.set(left, top);
            }

            if (left >= 0) {
                let xVal = u.data[0][idx];

                let dt = (new Date(xVal * 1e3));
                if(dt instanceof Date && !isNaN(dt)) {
                    dt = dt.toLocaleDateString();
                } else {
                    dt = '';
                }

                let top;
                let html = [];

                if(u.data.length > 1) {
                    let s1 = u.series[1];
                    let yVal1 = u.data[1][idx];
                    yVal1 = (yVal1 !== null && yVal1 != undefined) ? yVal1 : '0';

                    let color = u.series[1].stroke();

                    html.push(`<span style="border-radius: 3px; color:#131220; padding: 2px 3px; background: ${color}">${s1.label}: ${yVal1}</span>`);
                    top = u.valToPos(yVal1, s1.scale);
                }

                if(u.data.length > 2) {
                    let s2 = u.series[2];
                    let yVal2 = u.data[2][idx];
                    yVal2 = (yVal2 !== null && yVal2 != undefined) ? yVal2 : '0';

                    let color = u.series[2].stroke();

                    html.push(`<span style="border-radius: 3px; color:#131220; padding: 2px 3px; background: ${color}">${s2.label}: ${yVal2}</span>`);
                }

                if(u.data.length > 3) {
                    let s3 = u.series[3];
                    let yVal3 = u.data[3][idx];
                    yVal3 = (yVal3 !== null && yVal3 != undefined) ? yVal3 : '0';

                    let color = u.series[3].stroke();

                    html.push(`<span style="border-radius: 3px; color:#131220; padding: 2px 3px; background: ${color}">${s3.label}: ${yVal3}</span>`);
                }

                if(u.data.length > 4) {
                    let s4 = u.series[4];
                    let yVal4 = u.data[4][idx];
                    yVal4 = (yVal4 !== null && yVal4 != undefined) ? yVal4 : '0';

                    let color = u.series[4].stroke();

                    html.push(`<span style="border-radius: 3px; color:#131220; padding: 2px 3px; background: ${color}">${s4.label}: ${yVal4}</span>`);
                }

                if(html.length) {
                    html.push(dt.replace(/\./g, '/'));
                    seriestt.innerHTML = html.join('<br>');

                    let left = u.valToPos(xVal, 'DAY');
                    seriestt.style.top = Math.round(top) + 'px';
                    seriestt.style.left = Math.round(left) + 'px';
                    seriestt.style.display = null;
                } else {
                    seriestt.style.display = 'none';
                }
            } else {
                seriestt.style.display = 'none';
            }
        }

        return {
            hooks: {
                init,
                setCursor
            }
        };
    }
}
