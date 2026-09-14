import './bootstrap';
import '@wotz/livewire-sortablejs';
import flatpickr from "flatpickr";
import { Spanish } from "flatpickr/dist/l10n/es.js";
import "flatpickr/dist/flatpickr.min.css";

import Swiper from 'swiper';
import 'swiper/css';
import 'swiper/css/navigation';

import './common';

function initFlatpickr() {
    ['#fecha_inicio', '#fecha_fin'].forEach(selector => {
        const el = document.querySelector(selector);
        if (el && !el._flatpickr) {
            flatpickr(el, { dateFormat: "d-m-Y", locale: Spanish, allowInput: true });
        }
    });
}

function initSwipers() {
    document.querySelectorAll('.mySwiper').forEach(swiperEl => {
        if (!swiperEl.swiper) {
            new Swiper(swiperEl, {
                modules: [Swiper.Navigation],
                slidesPerView: 1,
                spaceBetween: 16,
                navigation: {
                    nextEl: swiperEl.querySelector('.swiper-button-next'),
                    prevEl: swiperEl.querySelector('.swiper-button-prev'),
                },
                breakpoints: {
                    640: { slidesPerView: 2 },
                    768: { slidesPerView: 3 },
                    1024: { slidesPerView: 4 },
                },
            });
        }
    });
}

// document.addEventListener('livewire:load', () => {
//     initFlatpickr();
//     initSwipers();

//     Livewire.hook('message.processed', () => {
//         initFlatpickr();
//         initSwipers();
//     });
// });

// document.addEventListener('livewire:init', () => {
//     Livewire.on('open-window', (event) => {
//         window.open(event.url, '_blank');
//     });
// });

document.addEventListener('livewire:init', () => {
    // 1. Inicialización en la carga inicial de la página
    initFlatpickr();
    initSwipers();

    // 2. Eventos personalizados de Livewire 3 (Reemplaza tu Livewire.on anterior)
    Livewire.on('open-window', (event) => {
        // En Livewire 3, los parámetros viajan dentro de un array o un objeto.
        // Si pasas los datos como un array ordenado, se accede con event[0].url o event.url según cómo lo envíes en PHP.
        const url = event.url || (event[0] && event[0].url);
        if (url) window.open(url, '_blank');
    });

    // 3. Reemplazo de 'message.processed' en Livewire 3
    // Este hook se ejecuta cada vez que Livewire termina de actualizar el DOM (después de un renderizado)
    Livewire.hook('morph.updated', ({ el }) => {
        initFlatpickr();
        initSwipers();
    });
});


document.addEventListener('input', function (event) {
    limpiarErrorCampo(event.target);
});

document.addEventListener('change', function (event) {
    limpiarErrorCampo(event.target);
});

function limpiarErrorCampo(field) {
    if (!field.matches('input, textarea, select')) {
        return;
    }

    let container = field.parentElement;

    while (container && container !== document.body) {

        const error = Array.from(container.children)
            .find(child => child.classList.contains('panel-form-error'));

        if (error) {
            error.remove();
            return;
        }

        container = container.parentElement;
    }
}

// 🔥 COPIAR AL PORTAPAPELES (Expuesta globalmente para Alpine.js)
window.copiarAlPortapapeles = function(texto) {
    console.log('Intentando copiar el texto: ' + texto);

    // 1. API Moderna (Clipboard API)
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(texto)
            .then(() => {
                console.log('Copiado (API moderna): ' + texto);
                if (typeof alertify !== 'undefined') alertify.log('Copiado Código: ' + texto);
            })
            .catch(err => {
                console.error('Error clipboard API, usando fallback:', err);
                fallbackCopiar(texto);
            });
        return;
    }

    // 2. Fallback antiguo (Corregido a Vanilla JS sin requerir jQuery $)
    fallbackCopiar(texto);
}

// Función auxiliar de respaldo
function fallbackCopiar(texto) {
    const temp = document.createElement("textarea");
    temp.value = texto;
    // Evitar scroll visual al añadir el elemento
    temp.style.top = "0";
    temp.style.left = "0";
    temp.style.position = "fixed";

    document.body.appendChild(temp);
    temp.focus();
    temp.select();

    try {
        const exitoso = document.execCommand("copy");
        if (exitoso) {
            console.log('Copiado (fallback): ' + texto);
            if (typeof alertify !== 'undefined') alertify.log('Copiado Código: ' + texto);
        } else {
            console.error('No se pudo copiar en el fallback');
        }
    } catch (e) {
        console.error('Error en el fallback de copia:', e);
    }

    document.body.removeChild(temp);
}
