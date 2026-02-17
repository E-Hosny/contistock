import { createI18n } from 'vue-i18n';
import ar from './locales/ar.json';
import en from './locales/en.json';

const LOCALE_KEY = 'contistock_locale';

function getSavedLocale() {
    try {
        return localStorage.getItem(LOCALE_KEY) || document.documentElement.lang?.slice(0, 2) || 'en';
    } catch {
        return 'en';
    }
}

export const i18n = createI18n({
    legacy: false,
    locale: getSavedLocale(),
    fallbackLocale: 'en',
    messages: { ar, en },
});

export function setLocale(locale) {
    i18n.global.locale.value = locale;
    try {
        localStorage.setItem(LOCALE_KEY, locale);
    } catch {}
    const html = document.documentElement;
    html.setAttribute('lang', locale === 'ar' ? 'ar' : 'en');
    html.setAttribute('dir', locale === 'ar' ? 'rtl' : 'ltr');
}

export function getLocale() {
    return i18n.global.locale.value;
}
