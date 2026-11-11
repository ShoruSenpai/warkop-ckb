export function ckbColor(name) {
    const color = getComputedStyle(document.documentElement)
        .getPropertyValue(`--color-ckb-${name}`)
        .trim();

    if (!color) {
        throw new Error(`CKB design color is not defined: ${name}`);
    }

    return color;
}

export function ckbColorWithAlpha(name, opacity) {
    const hex = ckbColor(name).replace("#", "");

    if (!/^[0-9a-f]{6}$/i.test(hex)) {
        throw new Error(`CKB design color must use a six-digit hex value: ${name}`);
    }

    const [red, green, blue] = hex.match(/.{2}/g).map((channel) =>
        Number.parseInt(channel, 16),
    );

    return `rgba(${red}, ${green}, ${blue}, ${opacity})`;
}
