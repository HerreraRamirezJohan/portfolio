/*
| Number formatting shared by the public pages.
*/

const formatters = new Map();

/**
 * 15 USD -> "$15", 275 MXN -> "$275". Pesos use the Mexican format so both
 * currencies print with a plain "$"; the page writes the currency code next
 * to the number.
 */
export function formatMoney(amount, currency) {
    if (! formatters.has(currency)) {
        formatters.set(
            currency,
            new Intl.NumberFormat(currency === 'MXN' ? 'es-MX' : 'en-US', { style: 'currency', currency, maximumFractionDigits: 0 }),
        );
    }

    return formatters.get(currency).format(amount);
}
