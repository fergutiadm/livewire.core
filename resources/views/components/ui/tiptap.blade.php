{{-- resources/views/components/ui/tiptap.blade.php --}}
@props([
    'model' => 'contenido',
    'placeholder' => 'Escriba el contenido del artículo...',
])

<div
    x-data="{
        contenido: $wire.entangle(@js($model)),
        ultimoHTML: null,

        init() {
            const Editor = window.Tiptap.Editor;
            const StarterKit = window.Tiptap.StarterKit;

            const editor = new Editor({
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
                        placeholder: @js($placeholder),
                    },
                },
                onUpdate: ({ editor }) => {
                    const html = editor.getHTML();

                    this.ultimoHTML = html;
                    this.contenido = html;
                },
            });

            // Mantener la instancia fuera del objeto reactivo de Alpine.
            this.$refs.editor._tiptapEditor = editor;

            // Sincronizar cambios externos a TipTap.
            this.$watch('contenido', (value) => {
                const editor = this.$refs.editor._tiptapEditor;

                if (!editor) return;

                const html = value || '';

                if (html === this.ultimoHTML) return;

                queueMicrotask(() => {
                    const editor = this.$refs.editor._tiptapEditor;

                    if (!editor) return;
                    if ((this.contenido || '') !== html) return;

                    if (editor.getHTML() !== html) {
                        editor.commands.setContent(html, {
                            emitUpdate: false,
                        });
                    }

                    this.ultimoHTML = html;
                });
            });
        },

        destroy() {
            const editor = this.$refs.editor._tiptapEditor;

            editor?.destroy();

            delete this.$refs.editor._tiptapEditor;
        },

        ejecutar(comando) {
            const editor = this.$refs.editor._tiptapEditor;

            if (!editor) return;

            const chain = editor.chain().focus();

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

        <button
            type="button"
            x-on:click="ejecutar('bold')"
            class="rounded-lg border px-3 py-1 font-bold hover:bg-slate-200"
            title="Negrita"
        >
            B
        </button>

        <button
            type="button"
            x-on:click="ejecutar('italic')"
            class="rounded-lg border px-3 py-1 italic hover:bg-slate-200"
            title="Cursiva"
        >
            I
        </button>

        <button
            type="button"
            x-on:click="ejecutar('bulletList')"
            class="rounded-lg border px-3 py-1 hover:bg-slate-200"
            title="Lista con viñetas"
        >
            • Lista
        </button>

        <button
            type="button"
            x-on:click="ejecutar('orderedList')"
            class="rounded-lg border px-3 py-1 hover:bg-slate-200"
            title="Lista numerada"
        >
            1. Lista
        </button>

        <button
            type="button"
            x-on:click="ejecutar('undo')"
            class="rounded-lg border px-3 py-1 hover:bg-slate-200"
            title="Deshacer"
        >
            ↶
        </button>

        <button
            type="button"
            x-on:click="ejecutar('redo')"
            class="rounded-lg border px-3 py-1 hover:bg-slate-200"
            title="Rehacer"
        >
            ↷
        </button>

    </div>

    {{-- Editor --}}
    <div
        x-ref="editor"
        class="min-h-[260px] text-sm text-slate-800"
    ></div>
</div>
