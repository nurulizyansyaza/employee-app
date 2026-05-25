export function formatCurrency(value, currency = 'USD', locale = undefined) {
    const n = Number(value ?? 0);
    const code = (currency || 'USD').toString().toUpperCase();
    if (!Number.isFinite(n)) return `${code} 0.00`;
    try {
        return new Intl.NumberFormat(locale, {
            style: 'currency',
            currency: code,
        }).format(n);
    } catch (e) {
        return `${code} ${n.toLocaleString(locale)}`;
    }
}

export function formatDate(value) {
    if (!value) return '';
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return value;
    return d.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

export function formatSex(value) {
    return value ? 'Male' : 'Female';
}
