import Chart from "chart.js/auto";

export { Chart };

export function ckbChartColors() {
    const styles = getComputedStyle(document.documentElement);
    const getColor = (name, fallback) =>
        styles.getPropertyValue(`--color-ckb-${name}`).trim() || fallback;

    return {
        primary: getColor("primary", "#142747"),
        accent: getColor("accent", "#496b9a"),
        surface: getColor("surface", "#fffdf4"),
        muted: getColor("on-surface-variant", "#596579"),
        grid: getColor("outline-variant", "#d2d5d8"),
    };
}
