// Klaro Configuration
window.klaroConfig = {
    elementID: 'klaro',
    privacyPolicy: '/datenschutz', // Link zur Datenschutzseite
    cookieName: 'klaro-consent', // Name des Consent-Cookies
    lang: 'de', // Sprache
    acceptAll: true,
    callback: function(consent, service) {
        console.log('Consent changed for service', service.name, consent);
        if (service.name === 'matomo' && consent === true) {
            console.log('Matomo accepted');
        }
    },

    translations: {
        de: {
            consentModal: {
                title: 'Cookie-Einstellungen',
                description: 'Hier kannst du auswählen, welche Cookies du erlauben möchtest.',
            },
            consentNotice: {
                changeDescription: 'Es gab Änderungen seit deinem letzten Besuch.',
                description: 'Ich verwende Cookies und andere Technologien, um meine Website sicher und zuverlässig bereitzustellen, die Leistung zu analysieren und Ihre Nutzererfahrung mit relevanten Inhalten zu verbessern. Mit einem Klick auf „Alles akzeptieren" stimmen Sie der Verwendung dieser Technologien zur Verarbeitung Ihrer Daten zu. Wenn Sie dies nicht möchten, nutze ich nur die notwendigen Cookies. Weitere Informationen finden Sie unter „Mehr".',
                learnMore: 'Mehr erfahren',
            },
            acceptAll: 'Alle akzeptieren',
            acceptSelected: 'Auswahl akzeptieren',
            decline: 'Ablehnen',
            ok: 'Alle akzeptieren',
            close: 'Schließen',
            service: {
                disableAll: {
                    title: 'Alle deaktivieren',
                    description: 'Nutze diesen Schalter, um alle Dienste zu aktivieren oder zu deaktivieren.',
                },
                matomo: {
                    description: 'Matomo hilft uns, die Nutzung der Website anonym zu analysieren.',
                },
            },
        }
    },

    purposes: {
        analytics: {
            title: 'Analytics',
            description: 'Diese Dienste verarbeiten personenbezogene Informationen, um die Nutzung unseres Dienstes zu analysieren.',
        }
    },
    services: [
        {
            name: 'matomo',
            title: 'Matomo Analytics',
            description: 'Matomo wird verwendet, um anonymisierte Nutzungsstatistiken zu erfassen.',
            cookies: ['_pk_ref', '_pk_cvar', '_pk_id', '_pk_ses'],
            required: false,
            optOut: false,
            onlyOnce: true,
            purposes: ['analytics'],
            onDecline: `
                var scripts = document.querySelectorAll("script[data-name='matomo']");
                scripts.forEach(function(element) {
                    element.type = "text/plain";
                });
            `
        }
    ]
};
