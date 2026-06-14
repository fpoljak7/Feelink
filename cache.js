const FEELINK_CACHE_KEY = 'feelink_offline_unosi';

function spremiOffline(unos) {
    const postojeci = dohvatiOfflineUnoce();
    postojeci.push(unos);
    localStorage.setItem(FEELINK_CACHE_KEY, JSON.stringify(postojeci));
}

function dohvatiOfflineUnoce() {
    const podaci = localStorage.getItem(FEELINK_CACHE_KEY);
    return podaci ? JSON.parse(podaci) : [];
}

function sinkronizirajOfflineUnoce() {
    const unosi = dohvatiOfflineUnoce();
    if (unosi.length === 0) return;

    const obecanja = unosi.map(unos => {
        const podaci = new FormData();
        podaci.append('emoji_vrijednost', unos.emoji_vrijednost);
        podaci.append('biljeska', unos.biljeska);

        return fetch('../backend/checkin.php', {
            method: 'POST',
            body: podaci
        }).then(r => r.json());
    });

    Promise.all(obecanja)
        .then(() => {
            localStorage.removeItem(FEELINK_CACHE_KEY);
            console.log('Offline unosi sinkronizirani');
        })
        .catch(err => console.log('Sinkronizacija nije uspjela:', err));
}

// pokušaj sinkronizirati čim se veza uspostavi
window.addEventListener('online', sinkronizirajOfflineUnoce);

// provjeri pri učitavanju stranice
if (navigator.onLine) {
    sinkronizirajOfflineUnoce();
}