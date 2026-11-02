
@props([
    'model' => 'contenido',
    'placeholder' => 'Escriba el contenido del artículo...',
])

<div
    x-data="{
        contenido: $wire.entangle(@js($model)),
        editor: null,

        init() {
            const Editor = window.Tiptap.Editor;
            const StarterKit = window.Tiptap.StarterKit;

            this.editor = new Editor({
                element: this.$refs.editor,
                extensions: [
                    StarterKit.configure({
                        link: {
                            openOnClick: false,
                            autolink: true,
                        },
                    }),
                ],
                content: this.contenido || '',
                editorProps: {
                    attributes: {
                        class: 'min-h-[260px] p-4 focus:outline-none',
                    },
                },
                onUpdate: ({ editor }) => {
                    this.contenido = editor.getHTML();
                },
            });

            this.$watch('contenido', (value) => {
                if (!this.editor) return;

                const html = value || '';

                if (this.editor.getHTML() !== html) {
                    this.editor.commands.setContent(html, {
                        emitUpdate: false,
                    });
                }
            });
        },

        destroy() {
            this.editor?.destroy();
            this.editor = null;
        },

        ejecutar(comando) {
            if (!this.editor) return;

            const chain = this.editor.chain().focus();

            if (comando === 'bold') chain.toggleBold().run();
            if (comando === 'italic') chain.toggleItalic().run();
            if (comando === 'bulletList') chain.toggleBulletList().run();
            if (comando === 'orderedList') chain.toggleOrderedList().run();
            if (comando === 'undo') chain.undo().run();
            if (comando === 'redo') chain.redo().run();
        },
    }"
    wire:ignore
    class="overflow-hidden rounded-xl border border-slate-300 bg-white"
>
    {{-- Barra de herramientas --}}
    <div class="flex flex-wrap gap-2 border-b border-slate-200 bg-slate-50 p-2">

        <button type="button" x-on:click="ejecutar('bold')"
            class="rounded-lg border px-3 py-1 font-bold hover:bg-slate-200"
            title="Negrita">
            B
        </button>

        <button type="button" x-on:click="ejecutar('italic')"
            class="rounded-lg border px-3 py-1 italic hover:bg-slate-200"
            title="Cursiva">
            I
        </button>

        <button type="button" x-on:click="ejecutar('bulletList')"
            class="rounded-lg border px-3 py-1 hover:bg-slate-200"
            title="Lista con viñetas">
            • Lista
        </button>

        <button type="button" x-on:click="ejecutar('orderedList')"
            class="rounded-lg border px-3 py-1 hover:bg-slate-200"
            title="Lista numerada">
            1. Lista
        </button>

        <button type="button" x-on:click="ejecutar('undo')"
            class="rounded-lg border px-3 py-1 hover:bg-slate-200"
            title="Deshacer">
            ↶
        </button>

        <button type="button" x-on:click="ejecutar('redo')"
            class="rounded-lg border px-3 py-1 hover:bg-slate-200"
            title="Rehacer">
            ↷
        </button>

    </div>

    {{-- Editor --}}
    <div
        x-ref="editor"
        class="min-h-[260px] text-sm text-slate-800"
    ></div>
</div>
