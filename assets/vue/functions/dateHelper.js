/**
  * Convertit le format de la fonction php date() en un format pour dayjs.
  * Exemple: d/m/Y H:i => DD/MM/YYYY HH:mm
  * @return {string}
  */
export function convertDateFormatToJsFormat(format) {
    const formatAvailables = {
        d: 'DD', //01 à 31
        D: 'ddd', // Mon à Sun
        j: 'D', // 1 à 31
        l: 'dddd', //Sunday à Saturday
        N: 'd',  // ISO 8601 du jour de la semaine	1 (pour Lundi) à 7 (pour Dimanche)
        w: 'd',  // Jour de la semaine au format numérique	0 (pour dimanche) à 6 (pour samedi)
        F: 'MMMM', // January à December,
        m: 'MM', // 01 à 12
        M: 'MMM', // Jan à Dec
        n: 'M', // 1 à 12
        X: 'YYYY', // année complète -0055, +0787, +1999, +10191
        x: 'YYYY', // année complète -0055, 0787, 1999, +10191,
        Y: 'YYYY', // -0055, 0787, 1999, 2003, 10191
        y: 'YY', // sur deux chiffre, 99 ou 03
        a: 'a', // am ou pm
        A: 'A', //  AM ou PM
        B: '', // Heure Internet Swatch	000 à 999
        g: 'h', //Heure, au format 12h, 1 à 12
        G: 'H', //Heure, au format 24h,	0 à 23
        h: 'hh', //Heure, au format 12h,	01 à 12
        H: 'HH', //Heure, au format 24h,	00 à 23
        i: 'mm', //Minutes avec les zéros initiaux	00 à 59
        s: 's', //Secondes avec zéros initiaux	00 à 59
        v: 'SSS', // Millisecondes
        O: 'ZZ', // +05:00	The offset from UTC, ±HH:mm
        P: 'Z' // +0500	The offset from UTC, ±HHmm
    }

    let formatted = '';

    for (let i = 0; i < format.length; i++) {
        const char = formatAvailables[format[i]] ? formatAvailables[format[i]] : format[i];
        formatted += char;
    };

    return formatted;
}