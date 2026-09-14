<!-- Toast Manager -->
<div
    x-data="toastManager({ position: 'top-right', max: 4 })"
    x-init="init()"
    class="fixed z-50 space-y-3 pointer-events-none"
    :class="{
        'top-6 right-6': position === 'top-right',
        'bottom-6 left-1/2 -translate-x-1/2': position === 'bottom-center'
    }"
>
    <template x-for="toast in list" :key="toast.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"

            class="pointer-events-auto relative w-80 rounded-lg shadow-xl border-l-4 overflow-hidden"
            :class="toastClasses(toast.type)"
        >
            <!-- Barra progreso -->
            <div
                x-show="toast.duration"
                class="absolute top-0 left-0 h-1 bg-white/40"
                :style="`animation: toast-progress ${toast.duration}ms linear forwards;`"
            ></div>

            <div class="flex gap-3 p-4">
                <!-- Icono -->
                <div class="pt-0.5" x-html="icon(toast.type)"></div>

                <!-- Contenido -->
                <div class="flex-1">
                    <p class="text-sm font-medium leading-snug" x-text="toast.message"></p>

                    <!-- Confirm -->
                    <template x-if="toast.confirm">
                        <div class="flex justify-end gap-2 mt-3">
                            <button
                                @click="toast.onConfirm()"
                                class="px-3 py-1 text-xs font-semibold rounded bg-green-500 text-white hover:bg-green-600"
                            >
                                Aceptar
                            </button>
                            <button
                                @click="remove(toast.id)"
                                class="px-3 py-1 text-xs font-semibold rounded bg-gray-300 text-gray-800 hover:bg-gray-400"
                            >
                                Cancelar
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Cerrar -->
                <template x-if="!toast.confirm">
                    <button
                        @click="remove(toast.id)"
                        class="absolute top-2 right-2 text-white/70 hover:text-white"
                    >
                        ✕
                    </button>
                </template>
            </div>
        </div>
    </template>
</div>

<script>
function toastManager(options = {}) {
    return {
        position: options.position || 'top-right',
        max: options.max || 4,
        list: [],
        initialized: false,

        init() {
            if (this.initialized) return;
            this.initialized = true;

            // Toast simple
            window.addEventListener('livewire:alert', (event) => {
                const d = event.detail?.[0] ?? event.detail;

                if (this.hasConfirm()) return;
                if (!d?.message) return;

                this.show(d.message, d.type || 'info', d.persistent ? null : 3000);
            });

            // Confirm
            window.addEventListener('livewire:confirm', (event) => {
                const d = event.detail?.[0] ?? event.detail;

                if (this.hasConfirm()) return;
                if (!d?.message || !d?.onConfirm || !d?.componentId) return;

                const id = this.uuid();

                this.list.push({
                    id,
                    message: d.message,
                    type: 'warning',
                    confirm: true,
                    onConfirm: () => {
                        const c = Livewire.find(d.componentId);
                        if (c) c.call(d.onConfirm);
                        this.remove(id);
                    }
                });
            });
        },

        show(message, type = 'info', duration = 3000) {
            const id = this.uuid();
            const toast = { id, message, type, duration };

            // límite
            if (this.list.length >= this.max) this.list.shift();

            type === 'error'
                ? this.list.unshift(toast)
                : this.list.push(toast);

            if (type === 'error') this.sound();

            if (duration) {
                setTimeout(() => this.remove(id), duration);
            }
        },

        remove(id) {
            this.list = this.list.filter(t => t.id !== id);
        },

        hasConfirm() {
            return this.list.some(t => t.confirm);
        },

        toastClasses(type) {
            return {
                info: 'bg-blue-500 text-white border-blue-600',
                success: 'bg-green-500 text-white border-green-600',
                warning: 'bg-yellow-100 text-yellow-800 border-yellow-400',
                error: 'bg-red-500 text-white border-red-600'
            }[type];
        },

        icon(type) {
            return {
                info: 'ℹ️',
                success: '✅',
                warning: '⚠️',
                error: '⛔'
            }[type];
        },

        sound() {
            const audio = new Audio('/sounds/error.mp3');
            audio.volume = 0.4;
            audio.play().catch(() => {});
        },

        normalize(detail) {
            return Array.isArray(detail) ? detail[0] : detail;
        },

        uuid() {
            return crypto.randomUUID?.() ?? 't-' + Date.now() + Math.random();
        }
    };
}
</script>
