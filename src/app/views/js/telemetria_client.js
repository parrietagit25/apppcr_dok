/**
 * Telemetría silenciosa: solo datos disponibles sin permisos del usuario.
 * Se ejecuta en segundo plano, una vez por sesión.
 */
(function () {
    'use strict';

    var STORAGE_KEY = 'apppcr_tel_ctx_sent';
    if (sessionStorage.getItem(STORAGE_KEY) === '1') {
        return;
    }

    var endpoint = window.APPPCR_TELEMETRIA_URL;
    if (!endpoint) {
        return;
    }

    function tipoDispositivo() {
        var ua = navigator.userAgent || '';
        var w = Math.min(window.screen.width, window.screen.height);
        if (/iPad|Tablet|PlayBook|Silk/i.test(ua) || (ua.indexOf('Android') > -1 && ua.indexOf('Mobile') === -1)) {
            return 'tablet';
        }
        if (/Mobi|Android|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua) || w < 768) {
            return 'mobile';
        }
        return 'desktop';
    }

    function recolectar() {
        var conn = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
        return {
            dispositivo_tipo: tipoDispositivo(),
            resolucion_pantalla: (window.screen.width || 0) + 'x' + (window.screen.height || 0),
            resolucion_viewport: (window.innerWidth || 0) + 'x' + (window.innerHeight || 0),
            resolucion_disponible: (window.screen.availWidth || 0) + 'x' + (window.screen.availHeight || 0),
            pixel_ratio: window.devicePixelRatio || 1,
            orientacion: (screen.orientation && screen.orientation.type) ? screen.orientation.type : '',
            timezone: (function () {
                try {
                    return Intl.DateTimeFormat().resolvedOptions().timeZone || '';
                } catch (e) {
                    return '';
                }
            })(),
            idioma: navigator.language || '',
            idiomas: (navigator.languages || []).join(','),
            plataforma: navigator.platform || '',
            user_agent_cliente: navigator.userAgent || '',
            nucleos_cpu: navigator.hardwareConcurrency || null,
            memoria_dispositivo_gb: navigator.deviceMemory || null,
            touch_pantalla: (navigator.maxTouchPoints || 0) > 0 ? 1 : 0,
            modo_oscuro: window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 1 : 0,
            referrer: document.referrer || '',
            tipo_conexion: conn ? (conn.effectiveType || conn.type || '') : '',
            conexion_downlink: conn && conn.downlink != null ? conn.downlink : null,
            conexion_rtt: conn && conn.rtt != null ? conn.rtt : null,
            online: navigator.onLine ? 1 : 0,
            cookies_habilitadas: navigator.cookieEnabled ? 1 : 0,
            pagina_url: location.href.substring(0, 500)
        };
    }

    function marcarEnviado() {
        try {
            sessionStorage.setItem(STORAGE_KEY, '1');
        } catch (e) { /* ignore */ }
    }

    function enviar(payload) {
        var json = JSON.stringify(payload);
        try {
            if (navigator.sendBeacon) {
                var blob = new Blob([json], { type: 'application/json' });
                if (navigator.sendBeacon(endpoint, blob)) {
                    marcarEnviado();
                    return;
                }
            }
        } catch (e) { /* fallback fetch */ }

        fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
            keepalive: true,
            body: json
        }).then(function (r) {
            if (r.ok) {
                marcarEnviado();
            }
        }).catch(function () { /* silencioso */ });
    }

    function ejecutar() {
        enviar(recolectar());
    }

    if (window.requestIdleCallback) {
        requestIdleCallback(ejecutar, { timeout: 4000 });
    } else {
        setTimeout(ejecutar, 2000);
    }
})();
